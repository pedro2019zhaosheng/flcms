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

namespace app\pxy\controller;

use app\common\PxyController;
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
class Logout extends PxyController
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
        $this->redirect('/pxy/login');
    }
}