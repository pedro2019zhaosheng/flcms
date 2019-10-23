<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/1
 * Time: 11:13
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\controller;

use app\common\Helper;
use app\common\VpController;
use app\common\model\Admin;
use think\Session;

/**
 * 总后台登录
 *
 * Class Login
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Login extends VpController
{
    /**
     * 总后台登录验证
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function submit()
    {
        $post = $this->post;
        $validate = $this->validate($post, 'login.login');
        if ($validate !== true) {
            return $this->asJson(0, 'error', $validate);
        }

        // 验证自定义csrf
        $aid = isset($post['_aid']) ? $post['_aid'] : '';
        $session = new Session();
        $csrfToken = $session->get('vp_csrf_token');
        $requestSize = $session->get('vp_csrf_request_size');
        // 校验
        if (strcmp($aid, $csrfToken)) {
            return $this->asJson(0, 'error', 'csrf请求拦截,非法请求');
        }

        // 校验次数
        if ($requestSize >= 10){
            return $this->asJson(403, 'error', '错误次数过多请刷新页面后重试');
        }

        // 校验次数+1
        $session->set('vp_csrf_request_size', ++$requestSize);
        $data = Admin::quickGetOne(null, ['username' => $post['username'], 'password' => md5($post['password'])]);
        if (!empty($data)) {
            if ((int)$data['is_delete'] === 1) {
                return $this->asJson(0, 'error', '该管理员已被删除');
            }

            if ((int)$data['frozen'] === 1) {
                return $this->asJson(0, 'error', '该管理员已被冻结');
            }

            $session->set('adminId', $data['id']);
            $session->set('roleId', $data['role']);
            Admin::quickCreate([
                'id' => $data['id'],
                'login_status' => 1,
                'last_login_time' => Helper::timeFormat(time(), 's'),
                'last_login_ip' => Helper::getClientIP(),
            ], true);

            // 登录成功,删除csrf缓存
            $session->delete(['vp_csrf_token', 'vp_csrf_request_size']);
            return $this->asJson(1, 'success', '登录成功');
        }

        return $this->asJson(0, 'error', '用户名或密码错误');
    }
}