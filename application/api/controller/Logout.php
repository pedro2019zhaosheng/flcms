<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/22
 * Time: 10:32
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\api\controller;

use app\common\RestController;
use think\Db;

/**
 * 退出控制器
 *
 * Class Logout
 * @package app\api\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Logout extends RestController
{

    /**
     * app安全退出
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function index()
    {
        $uid = self::$uid;
        try {
            Db::startTrans();
            $updateKey = $this->updateKey($uid, true);
            $updateToke = $this->updateToken($uid);
            if (!$updateKey || !$updateToke) {
                trigger_error("退出失败", E_USER_WARNING);
            }

            Db::commit();
            return $this->asNewJson('indexRet',1,'success','已安全退出');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->asNewJson('indexRet',0,'error','退出失败');
        }
    }
}