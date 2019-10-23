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

use app\common\model\AdminAuth;
use app\common\model\AdminConfig;
use think\Session;

/**
 * 总管理后台控制器基类
 *
 * Class VpController
 * @package app\common
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class VpController extends MainController
{
    /**
     * 总管理后台初始化控制
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
    public function initialize()
    {
        parent::initialize();
        // 总管理后台访问控制
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
        if ($uid = $session->get('adminId')) {
            defined("UID") or define("UID", $uid);
            defined("ROLE") or define("ROLE", $session->get('roleId') ?: '');
        }

        if (!in_array($linkBoth, $guestBothArr, true) && !$session->get('adminId')) {
            if ($this->request->isAjax()) {
                return $this->asJson(-1, 'error', '请您重新登录');
            } else {
                $this->redirect("/vp/login");
            }

            exit(0);
        }

        // 根据角色获取左侧菜单(不是ajax并且不是游客)
        if (!$this->request->isAjax() && !in_array($linkBoth, $guestBothArr, true)) {
            $model = new AdminAuth;
            $menu = $model->getMenu();
            $this->assign('menu', $menu); // 菜单

            $this->assign('webkey', AdminConfig::config('webkey', '竞彩,足篮竞彩,足篮竞彩投注,竞彩总后台')); // 总后台关键字
            $this->assign('describe', AdminConfig::config('describe',  '我们为您提供竞彩足球胜平负、让球胜平负等竞彩足球和篮球代销、竞彩足球和篮球网上投注、定制跟单等服务，方便彩民在线购买竞彩足球。')); // 总后台描述
            $this->assign('webdns', AdminConfig::config('webdns', '')); // 总后台域名
            $this->assign('webname', AdminConfig::config('webname', '足篮彩管理后台')); // 总后台名称
            $this->assign('webImg', AdminConfig::config('webImg', '/static/lib/images/logo.png')); // 总后台logo
        }

        // 权限控制(不是游客)
        if (!in_array($linkBoth, $guestBothArr, true)) {
            $url = $this->request->url();
            $isAuth = AdminAuth::hasAuth($url);
            // 没权限
            if (!$isAuth) {
                if ($this->request->isAjax()) {
                    return $this->asJson(-2, 'error', '您没有访问该接口的权限！');
                } else {
                    $this->redirect("/vp/error/noAuth");
                }

                exit(0);
            }
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
        $this->redirect('/vp/error/index');
    }
}