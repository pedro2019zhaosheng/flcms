<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/7
 * Time: 15:28
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\controller;

use app\common\Helper;
use app\common\model\AdminMenu;
use app\common\VpController;
use think\Cache;

/**
 * 节点控制器
 *
 * Class Node
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Node extends VpController
{
    /**
     * 初始化控制
     *
     * @return \think\response\Json|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function initialize()
    {
        parent::initialize(); // TODO: Change the autogenerated stub
        $curAction = $this->request->action(); // 非小驼峰
        $accessClearCacheAction = ['add', 'set', 'del', 'toggle'];
        if (in_array($curAction, $accessClearCacheAction, true)) {
            $cacheConfig = config('cache.');
            $cache = new Cache($cacheConfig);
            $cache->clear();
        }
    }

    /**
     * 添加菜单节点
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function add()
    {
        $post = $this->post;
        $validate = $this->validate($post, 'menu.add');
        if ($validate !== true) {
            return $this->asJson(0, 'error', $validate);
        }

        $result = AdminMenu::quickCreate([
            'pid' => $post['pid'],
            'title' => $post['title'],
            'icon' => $post['icon'],
            'module' => $post['module'],
            'controller' => $post['controller'],
            'action' => $post['action'],
            'menu_type' => $post['menu_type'],
            'url_value' => $post['url_value'],
            'sort' => $post['sort'],
            'create_time' => Helper::timeFormat(time(), 's'),
            'update_time' => Helper::timeFormat(time(), 's'),
        ]);

        if ($result) {
            return $this->asJson(1, 'success', '添加成功');
        }

        return $this->asJson(0, 'error', '添加失败');
    }

    /**
     * 获取节点树
     *
     * @return false|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function tree()
    {
        $model = new AdminMenu();
        $trees = $model->getNodeTree();

        return $trees;
    }

    /**
     * 获取详情
     *
     * @param $id
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function info($id)
    {
        $data = AdminMenu::quickGetOne($id);
        $pid = $data['pid'];
        if ((int)$pid === 0) {
            $data['topTitle'] = '顶级节点';
        } else {
            $wrapData = AdminMenu::quickGetOne($pid);
            $data['topTitle'] = $wrapData['title'];
        }

        return $this->asJson(1, 'success', '请求成功', $data ? [
            'id' => $data['id'],
            'title' => $data['title'],
            'topTitle' => $data['topTitle'],
            'action' => $data['action'],
            'icon' => $data['icon'],
            'controller' => $data['controller'],
            'menu_type' => $data['menu_type'],
            'module' => $data['module'],
            'url_value' => $data['url_value'],
        ] : '');
    }

    /**
     * 更新节点
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function set()
    {
        $post = $this->post;
        $validate = $this->validate($post, 'menu.update');
        if ($validate !== true) {
            return $this->asJson(0, 'error', $validate);
        }

        AdminMenu::quickCreate($post, true);
        return $this->asJson(1, 'success', '更新成功');
    }

    /**
     * 删除节点
     *
     * @param $id // 节点ID
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function del($id)
    {
        $model = new AdminMenu();
        $result = $model->del($id);
        if (!is_string($result)) {
            return $this->asJson(1, 'success', '删除成功');
        }

        return $this->asJson(0, 'error', $result);
    }

    /**
     * 禁用/启用
     *
     * @param $id
     * @param $status
     * @return \think\response\Json
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
        AdminMenu::quickCreate([
            'id' => $id,
            'status' => (int)$status,
        ], true);
        $model = new AdminMenu();
        $model->toggle($id, $status);

        return $this->asJson(1, 'success', '操作成功');
    }
}