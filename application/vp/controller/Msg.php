<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/28
 * Time: 11:44
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\controller;

use app\common\model\AdminMsg;
use app\common\VpController;

/**
 * 后台消息控制器
 *
 * Class Msg
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Msg extends VpController
{

    /**
     * 后台消息列表
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function msgList()
    {
        $get = $this->get;
        $model = new AdminMsg;
        $pagination = $model->getList($get);
        $list = $pagination->toArray();
        $page = $pagination->render();

        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 最新消息(3)
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
    public function newestMsg()
    {
        $model = new AdminMsg;
        $data = $model->getNewestMsg();
        $newMsgCount = $model->getNewMsgCount();

        return $this->asJson(1, 'success', '请求成功', ['data' => $data, 'msgCount' => $newMsgCount]);
    }
}