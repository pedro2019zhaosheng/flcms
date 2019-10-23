<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/28
 * Time: 18:17
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;

/**
 * 管理员模型
 *
 * Class Admin
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Admin extends BaseModel
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
        // 日期过滤
        if (
            isset($where['endDate'])
            && !empty($where['endDate'])
            && isset($where['startDate'])
            && !empty($where['startDate'])
        ) {
            $endWhere[] = ['create_at', 'between time', [$where['startDate'], $where['endDate']]];
        } else {
            if (isset($where['startDate']) && !empty($where['startDate'])) {
                $endWhere[] = ['create_at', '>=', $where['startDate']];
            }

            if (isset($where['endDate']) && !empty($where['endDate'])) {
                $endWhere[] = ['create_at', '<=', $where['endDate']];
            }
        }
        // 用户名过滤
        if (isset($where['username']) && !empty($where['username'])) {
            $endWhere[] = ['username', 'like', '%' . $where['username'] . '%'];
        }

        return $endWhere;
    }

    /**
     * 获取管理员列表
     *
     * @param $where // 条件
     * @param null $order // 排序
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getList($where, $order = 'sort ASC')
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        $where = $this->commonFilter($where);
        $where[] = ['is_delete', '=', 0];
        $paginate = self::where($where)
            ->field([
                'id',
                'username', // 用户名
                'email', // 邮箱
                'login_status', // 登录状态
                'last_login_ip', // 最后登录IP
                'last_login_time', // 最后登录时间
                'nick_name', // 昵称
                'phone', // 手机号
                'signup_ip', // 注册IP
                'sort', // 排序
                'photo', // 手机号
                'frozen', // 是否冻结
                'create_at', // 注册时间
                'role', // 角色ID
                'group_id', // 部门ID
            ])
            ->order($order)
            ->paginate($perPage);

        foreach ($paginate as $k => $item) {
            $paginate[$k]['photo'] = Attach::getPathByAttachId($item['photo']);
            $paginate[$k]['role'] = AdminRole::getRoleNameById($item['role'], 'name');
        }

        return $paginate;
    }

    /**
     * 导出Excel
     *
     * @param $where // 筛选条件
     * @param string $order // 排序规则
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function exportAdmin($where, $order = 'sort ASC')
    {
        $where = $this->commonFilter($where);
        $where[] = ['is_delete', '=', 0];
        $model = self::where($where)
            ->field([
                'id',
                'nick_name', // 昵称
                'username', // 用户名
                'phone', // 手机号
                'email', // 邮箱
                'role', // 角色ID
                'last_login_ip', // 最后登录IP
                'last_login_time', // 最后登录时间
                'signup_ip', // 注册IP
                'frozen', // 是否冻结
                'create_at', // 注册时间
            ])
            ->order($order)
            ->select();

        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        foreach ($data as $k => $item) {
            $data[$k]['role'] = AdminRole::getRoleNameById($item['role'], 'name');
            $data[$k]['frozen'] = $item['frozen'] === 0 ? '正常' : '冻结';
        }

        return $data;
    }

    /**
     * 获取管理员详情
     *
     * @param $adminId
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getOneInfo($adminId)
    {
        $data = self::quickGetOne($adminId);
        $result = [];
        $result['last_login_time'] = $data['last_login_time'];
        $result['nick_name'] = $data['nick_name'] ?: $data['username'];
        $result['photo'] = Attach::getPathByAttachId($data['photo']);
        $result['id'] = $data['id'];

        return $result;
    }

    /**
     * @desc 获取单个管理员的数据
     * @throws \Exception
     * @auther LiBin
     * @date 2019-03-07
     */
    public function getOneAll($where)
    {
        return self::where($where)->find();
    }

    /**
     * 新增后台管理员
     *
     * @param $data
     * @return bool|int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function insertAdmin($data)
    {
        return self::quickCreate($data);
    }

    /**
     * 软删除
     *
     * @param $id // 管理员ID
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function deleteAdmin($id)
    {
        $username = self::where('id', $id)->value('username');
        if (!strcasecmp($username, 'admin')) {
            return '系统内置账号“admin”不能被删除!';
        }

        $result = self::quickSoftDel(['is_delete' => 1], $id);
        return $result ? true : false;
    }

    /**
     * 获取单个值
     *
     * @param $where // 搜索条件
     * @param string $column
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getValue($where, $column = 'id')
    {
        $value = self::where($where)->value($column);
        return $value ?: '';
    }

    /**
     * faker工具，批量插入管理员，用于数据测试
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function faker()
    {
        for ($i = 0; $i < 50; $i++) {
            $this->insertAdmin([
                'username' => Helper::randomStr(5),
                'password' => md5('admin888'),
                'last_login_ip' => Helper::getClientIP(),
                'nick_name' => Helper::randomStr(10),
                'photo' => '/static/lib/images/admin.png',
                'create_at' => Helper::timeFormat(time(), 's'),
                'update_at' => Helper::timeFormat(time(), 's'),
            ]);
        }
    }
}
