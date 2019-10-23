<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/1
 * Time: 15:16
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\controller;

use app\common\VpController;
use app\common\model\Admin;
use think\Session;

/**
 * 退出控制器
 *
 * Class Logout
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Logout extends VpController
{
    /**
     * 退出
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function index()
    {
        $session = new Session();
        $session->clear();
        Admin::quickCreate([
            'id' => UID,
            'login_status' => 0
        ], true);
        $this->redirect('/vp/login');
    }
}