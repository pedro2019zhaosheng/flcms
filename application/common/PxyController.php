<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/28
 * Time: 10:18
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common;

use think\Session;

/**
 * 代理商管理后台控制器基类
 *
 * Class PxyController
 * @package app\common
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class PxyController extends MainController
{
    /**
     * 代理商后台初始化控制
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function initialize()
    {
        parent::initialize();
        //代理商后台访问控制
        // 允许游客访问的控制器和方法
        $guestConfig = ['login' => 'submit', 'view' => 'login'];
        $guestBothArr = [];
        $currentController = $this->request->controller(true);
        $currentAction = $this->request->action();
        $linkBoth = trim($currentController) . trim($currentAction);
        foreach ($guestConfig as $ctrl => $action) {
            array_push($guestBothArr, trim($ctrl) . trim($action));
        }
        $session = new Session();
        if ($aid = $session->get('agentId')) {
            defined("UID") or define("UID", $aid);
        }

        if (!in_array($linkBoth, $guestBothArr, true) && !$session->get('agentId')) {
            if ($this->request->isAjax()) {
                return $this->asJson(-1, 'error', '请您重新登录');
            } else {
                $this->redirect("/pxy/login");
            }

            exit(0);
        }
    }

    /**
     * 空操作
     *
     * @param $action // 操作名
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function _empty($action)
    {
        $this->redirect('/pxy/error/index');
    }
}