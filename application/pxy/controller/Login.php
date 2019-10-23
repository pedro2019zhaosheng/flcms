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

namespace app\pxy\controller;

use app\common\Helper;
use app\common\PxyController;
use app\common\model\Member;
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
class Login extends PxyController
{
    /**
     * 代理商登录验证
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
        $csrfToken = $session->get('pxy_csrf_token');
        $requestSize = $session->get('pxy_csrf_request_size');
        // 校验
        if (strcmp($aid, $csrfToken)) {
            return $this->asJson(0, 'error', 'CSRF跨站伪造请求拦截');
        }
        // 校验次数
        if ($requestSize >= 10){
            return $this->asJson(403, 'error', '错误次数过多请刷新页面后重试');
        }
        // 校验次数+1
        $session->set('pxy_csrf_request_size', ++$requestSize);
        $data = Member::quickGetOne(null, ['username' => $post['username'], 'password' => md5($post['password'])]);
        if (!empty($data)) {
            if ((int)$data['role'] !== 2) {
                return $this->asJson(0, 'error', '请输入正确的账号密码');
            }

            if ((int)$data['is_delete'] === 1) {
                return $this->asJson(0, 'error', '您的账号已被删除');
            }

            if ((int)$data['frozen'] === 0) {
                return $this->asJson(0, 'error', '您的账号已被冻结');
            }

            $session->set('agentId', $data['id']);
            Member::quickCreate([
                'id' => $data['id'],
                'backend_last_login_time' => Helper::timeFormat(time(), 's'),
                'backend_last_login_ip' => Helper::getClientIP(),
            ], true);

            // 登录成功,删除csrf缓存
            $session->delete(['pxy_csrf_token', 'pxy_csrf_request_size']);
            return $this->asJson(1, 'success', '登录成功');
        }

        return $this->asJson(0, 'error', '用户名或密码错误');
    }
}