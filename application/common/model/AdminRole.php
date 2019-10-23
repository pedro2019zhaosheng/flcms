<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/5
 * Time: 16:19
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;
use think\Db;
use think\Exception;

/**
 * 角色模型
 *
 * Class AdminRole
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class AdminRole extends BaseModel
{
    /**
     * 统一过滤GET接口参数
     *
     * @param $where
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    private function commonFilter($where)
    {
        $endWhere = [];
        // 角色名称过滤
        if (isset($where['roleName']) && !empty($where['roleName'])) {
            $endWhere[] = ['name', 'like', '%' . $where['roleName'] . '%'];
        }

        return $endWhere;
    }

    /**
     * 获取角色列表
     *
     * @param $where
     * @param null $order
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getRoleList($where, $order = null)
    {
        if (!$order) {
            $order = "sort ASC";
        }

        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        // 更改angular分页按钮点击事件
        config('paginate.js_var', 'getRolePage');
        $where = $this->commonFilter($where);
        $paginate = self::where($where)
            ->field([
                'id',
                'name', // 角色名
                'description', // 描述
                'create_time', // 创建时间
                'update_time', // 修改时间
                'status', // 状态
                'sort', // 排序
                'roletype', // 角色类型 1，其他     0，超管
            ])
            ->order($order)
            ->paginate($perPage);

        return $paginate;
    }

    /**
     * 获取所有正常角色
     *
     * @param $status // 状态
     * @return array|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getAll($status = 1)
    {
        $list = self::where('status', $status)
            ->field([
                'id',
                'name',
                'status',
            ])
            ->select();

        return $list->toArray();
    }

    /**
     * 获取角色名
     *
     * @param $id // 角色ID
     * @param $column // 字段
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getRoleNameById($id, $column)
    {
        $role = self::get($id);
        if (empty($role)) {
            return '';
        }

        return $role[$column];
    }

    /**
     * 删除角色
     *
     * @param $id // 角色ID
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function deleteRole($id)
    {
        $one = self::where('id', $id)->find();
        if ($one['roletype'] === 0) {
            return '系统角色“超级管理员”不能被删除!';
        }

        $rows = self::where('id', $id)->delete();
        return $rows ? true : false;
    }

    /**
     * 获取角色权限
     *
     * @param $id
     * @return array|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getRoleAuth($id)
    {
        $data = self::get($id);
        if (!empty($data)) {
            $roleType = $data['roletype'];
            if ($roleType === 0) {
                return '超管默认拥有最高权限!';
            }
        }

        $adminAuth = new AdminAuth();
        $data = $adminAuth->getAuthTree($id);

        return $data;
    }

    /**
     * 保存节点权限
     *
     * @param $data // 节点权限数据
     * @return bool|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function saveAuth($data)
    {
        $roleId = $data['id'];
        $auth = $data['auth'];
        $clicked = $auth['clicked'];
        $undetermined = isset($auth['undetermined']) && is_array($auth['undetermined']) ? $auth['undetermined'] : [];
        Db::startTrans();
        try {
            AdminAuth::where(['role_id' => $roleId])->delete();
            foreach ($clicked as $menuId) {
                $rows = AdminAuth::insertAuth($roleId, $menuId, 'clicked');
                if (!$rows) {
                    throw new Exception("保存权限节点失败");
                }
            }

            foreach ($undetermined as $unMenuId) {
                $rows = AdminAuth::insertAuth($roleId, $unMenuId, 'undetermined');
                if (!$rows) {
                    throw new Exception("保存权限节点失败");
                }
            }

            Db::commit();
            return true;
        } catch (Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 批量插入测试数据(测试用)
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function faker()
    {
        for ($i = 0; $i < 50; $i++) {
            self::quickCreate([
                'pid' => 0,
                'name' => Helper::randomStr(5),
                'description' => Helper::randomStr(15),
                'create_time' => Helper::timeFormat(time(), 's'),
                'update_time' => Helper::timeFormat(time(), 's'),
            ]);
        }
    }
}