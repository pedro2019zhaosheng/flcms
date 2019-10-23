<?php

namespace app\vp\controller;

use app\common\Helper;
use app\common\VpController;
use think\Exception;

/**
 * 管理员列表
 *
 * Class Admin
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Admin extends VpController
{
    /**
     * 管理员列表
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function index()
    {
        $get = $this->get;
        $model = new \app\common\model\Admin();
        $pagination = $model->getList($get);
        $page = $pagination->render();
        $list = $pagination->toArray();

        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 导出管理员
     *
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function export()
    {
        $get = $this->get;
        $model = new \app\common\model\Admin();
        $data = $model->exportAdmin($get);
        if (!empty($data)) {
            Helper::exportExcel(
                'admin',
                [
                    '管理员ID', '昵称', '账号', '手机号', '邮箱', '角色', '最后登录IP', '最后登录时间', '注册IP', '账号状态', '注册时间'
                ],
                $data);
        }

        return;
    }

    /**
     * 冻结和解冻
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function toggle()
    {
        $get = $this->get;
        if (!isset($get['frozen']) || !isset($get['id'])) {
            return $this->asJson(0, 'error', '操作失败');
        }

        if ((int)$get['frozen'] === 1) {
            $adminData = \app\common\model\Admin::quickGetOne((int)$get['id']);
            if (!empty($adminData)) {
                $username = $adminData['username'];
                if (!strcasecmp($username, 'admin')) {
                    return $this->asJson(0, 'error', '系统账号“admin”不能被冻结！');
                }
            }
        }

        \app\common\model\Admin::quickCreate([
            'id' => (int)$get['id'],
            'frozen' => (int)$get['frozen']
        ], true);

        return $this->asJson(1, 'success', '操作成功');
    }

    /**
     * 软删除管理员
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function delete()
    {
        $get = $this->get;
        if (!isset($get['id'])) {
            return $this->asJson(0, 'error', '删除失败');
        }

        $model = new \app\common\model\Admin();
        $result = $model->deleteAdmin((int)$get['id']);
        if (!is_string($result)) {
            if ($result === true) {
                return $this->asJson(1, 'success', '删除成功');
            }
            return $this->asJson(0, 'error', '删除失败');
        }

        return $this->asJson(0, 'error', $result);
    }

    /**
     * 新增管理员
     *
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function add()
    {
        $post = $this->post;
        $validation = $this->validate($post, "admin.add");
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $data = [
            'username' => $post['username'],
            'password' => md5($post['pwd']),
            'nick_name' => $post['nickName'],
            'phone' => $post['phone'],
            'role' => $post['roleId'],
            'signup_ip' => Helper::getClientIP(),
            'sort' => 50,
            'photo' => '',
            'create_at' => Helper::timeFormat(time(), 's'),
            'update_at' => Helper::timeFormat(time(), 's'),
        ];

        if (!empty($post['file'])) {
            $return = Helper::uploadImage('base64', 'admin');
            if (!is_array($return)) {
                return $this->asJson(0, 'error', '上传头像失败，错误信息: ' . $return);
            }

            $head = $return['head'];
            $data['photo'] = $head;
        }

        $model = new \app\common\model\Admin();
        $result = $model->insertAdmin($data);
        if ($result) {
            return $this->asJson(1, 'success', '新增成功');
        }

        return $this->asJson(0, 'error', '新增失败');
    }

    /**
     * 获取管理员个人中心
     *
     * @param $id // 管理员ID
     * @return \think\response\Json
     * @author YanShusheng
     * @api *
     */
    public function adminDetail()
    {
        $model = \app\common\model\Admin::quickGetOne(UID);
        if (empty($model)) {
            return $this->asJson(0, 'error', '管理员不存在');
        }

        // 获取用户角色
        $model->role_name = \app\common\model\AdminRole::getRoleNameById($model->role, 'name');

        // 获取用户头像
        $model->photo = \app\common\model\Attach::getPathByAttachId($model->photo);

        return $this->asJson(1, 'success', '请求成功', $model);
    }

    /**
     * 个人中心修改个人信息
     *
     * @throws Exception
     * @return \think\response\Json
     * @author YanShusheng
     * @api *
     */
    public function adminModify()
    {
        $post = $this->post;

        $validate = $this->validate($post, 'admin.Modifys');
        if ($validate !== true) {
            return $this->asJson(0, 'error', $validate);
        }

        $updateData = [
            'id' => UID,
            'nick_name' => $post['nickName'],
        ];
        if (!empty($post['pwd'])) {
            $updateData['password'] = md5($post['pwd']);
        }

        if (!empty($post['phone'])) {
            $updateData['phone'] = $post['phone'];
        }

        if (!empty($post['email'])) {
            $updateData['email'] = $post['email'];
        }

        // 更新头像
        if (!empty($post['file'])) {
            $return = Helper::uploadImage('base64', 'admin');
            if (!is_array($return)) {
                return $this->asJson(0, 'error', '上传头像失败，错误信息: ' . $return);
            }

            $head = $return['head'];
            $updateData['photo'] = $head;
        }

        \app\common\model\Admin::quickCreate($updateData, true);
        return $this->asJson(1, 'success', '修改成功');
    }

    /**
     * 获取管理员详情
     *
     * @param $id // 管理员ID
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function info($id)
    {
        $model = \app\common\model\Admin::quickGetOne($id);
        if (empty($model)) {
            return $this->asJson(0, 'error', '管理员不存在');
        }

        return $this->asJson(1, 'success', '请求成功', [
            "username" => $model->username,
            'nick_name' => $model->nick_name,
            'phone' => $model->phone,
            'role' => $model->role,
        ]);
    }

    /**
     * 排序
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function sort()
    {
        $post = $this->post;
        $validate = $this->validate($post, 'admin.sort');
        if ($validate !== true) {
            return $this->asJson(0, 'error', $validate);
        }

        \app\common\model\Admin::quickCreate([
            'id' => $post['id'],
            'sort' => $post['sort'],
        ], true);

        return $this->asJson(1, 'success', '排序成功');
    }

    /**
     * 修改管理员基本信息
     *
     * @param $id // 管理员ID
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function modify()
    {
        $post = $this->post;
        $validate = $this->validate($post, 'admin.update');
        if ($validate !== true) {
            return $this->asJson(0, 'error', $validate);
        }

        $adminData = \app\common\model\Admin::quickGetOne($post['id']);
        if (!empty($adminData)) {
            $username = $adminData['username'];
            if (!strcasecmp($username, "admin")) {
                return $this->asJson(0, 'error', '系统账号“admin”不能被修改!');
            }
        }

        \app\common\model\Admin::quickCreate([
            'id' => $post['id'],
            'username' => $post['username'],
            'nick_name' => $post['nickName'],
            'password' => md5($post['pwd']),
            'phone' => $post['phone'],
            'role' => $post['roleId'],
        ], true);

        return $this->asJson(1, 'success', '修改成功');
    }

    /**
     * 批量插入测试数据(faker接口)
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function batch()
    {
        $model = new \app\common\model\Admin();
        $model->faker();
    }
}
