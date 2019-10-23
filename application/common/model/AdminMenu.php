<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/7
 * Time: 19:43
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;
use think\Db;
use think\Exception;

/**
 * 菜单模型
 *
 * Class AdminMenu
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class AdminMenu extends BaseModel
{

    /**
     * 获取节点树
     *
     * @param int $id // 主键ID
     * @param array $container // 容器
     * @return array | string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getNodeTree($id = 0, array &$container = [])
    {
        $model = self::where('pid', $id)->field(['id', 'pid', 'title', 'icon', 'menu_type', 'url_value', 'status'])->select();
        $layer = $model->toArray();
        foreach ($layer as $key => $item) {
            $data = [
                'id' => $item['id'],
                'text' => $item['title'],
                'data' => [
                    'menu_type' => $item['menu_type'],
                    'status' => $item['status']
                ],
                'type' => 'menu',
                'children' => []
            ];

            if (strcasecmp($item['menu_type'], 'function')) {
                $data['state']["opened"] = 'true';
            }

            if ($item['status'] === 0) {
                $data['state']["selected"] = "true";
            }

            $data['state']["disabled"] = "true";
            $container[$key] = $data;
            $this->getNodeTree($item['id'], $container[$key]['children']);
        }

        return $container;
    }

    /**
     * 递归禁用/启用
     *
     * @param $id // 节点ID
     * @param $status // 状态 1 or 0
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function toggle($id, $status)
    {
        $model = self::where([['pid', '=', $id], ['status', '<>', $status]])->field('id')->select();
        $data = $model->toArray();
        foreach ($data as $item) {
            self::quickCreate([
                'id' => $item['id'],
                'status' => (int)$status,
            ], true);
            $this->toggle($item['id'], (int)$status);
        }

        return true;
    }

    /**
     * 删除菜单节点
     *
     * @param $id // 节点ID
     * @return int|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function del($id)
    {
        $check = self::where('pid', $id)->find();
        if (empty($check)) {
            // 删除菜单
            self::where(['id' => $id])->delete();
            // 删除权限表
            AdminAuth::where(['menu_id' => $id])->delete();

            return true;
        }

        return '请先删除其子节点';
    }
}