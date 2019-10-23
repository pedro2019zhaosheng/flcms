<?php

namespace app\pxy\controller;

use app\common\PxyController;
use app\common\model\Order;
use app\common\model\Member;

/**
 * 注单列表
 *
 * Class Bet
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Bet extends PxyController
{
    /**
     * 获取注单列表
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
        // 获取代理商的所有下级用户ID
        $member = new Member;
        $uids = $member->getDownUid(UID);
        // 无下级则虚拟筛选
        $get['member_id'] = !empty($uids) ? $uids : ['none'];
        $order = new Order;
        $pagination = $order->getList($get, 'create_time desc');
        $page = $pagination->render();
        $list = $pagination->toArray();

        return $this->asJson(1, 'success', '获取成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 注单导出Excel
     *
     * @author CleverStone
     * @throws \Exception
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function export()
    {
        $get = $this->get;
        // 获取代理商的所有下级用户ID
        $member = new Member;
        $uids = $member->getDownUid(UID);
        // 无下级则虚拟筛选
        $get['member_id'] = !empty($uids) ? $uids : ['none'];
        $order = new Order();
        $order->exportData($get, 'create_time DESC');
    }

    /**
     * 推单导出Excel
     *
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function exportPush()
    {
        $get = $this->get;
        // 获取代理商的所有下级用户ID
        $member = new Member;
        $uids = $member->getDownUid(UID);
        // 无下级则虚拟筛选
        $get['member_id'] = !empty($uids) ? $uids : ['none'];
        $order = new Order();
        $order->exportPushData($get, 'create_time DESC');
    }

    /**
     * 获取推单列表
     *
     * @return \think\response\Json
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function pushOrder()
    {
        $get = $this->get;
        // 获取代理商的所有下级用户ID
        $member = new Member;
        $uids = $member->getDownUid(UID);
        // 无下级则虚拟筛选
        $get['member_id'] = !empty($uids) ? $uids : ['none'];
        $model = new Order;
        $pagination = $model->getPushOrderList($get);
        $list = $pagination->toArray();
        $page = $pagination->render();

        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 推单审核
     *
     * @param $id // 订单ID
     * @param $state // 状态 1通过  2驳回
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function audit($id, $state)
    {
        $model = new Order;
        $result = $model->audit($id, $state);
        if ($result) {
            return $this->asJson(1, 'success', '审核成功');
        }

        return $this->asJson(0, 'error', '审核失败');
    }

    /**
     * 跟单明细
     *
     * @param $id // 推单ID
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function flowList($id)
    {
        $model = new Order;
        $list = $model->getFlowList($id);

        return $this->asJson(1, 'success', '请求成功', $list);
    }

    /**
     * 注单详细
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function info()
    {
        $get = $this->get;

        // 不可避免用户传入ID负值
        if ((int)$get['id'] <= 0) {
            return $this->asJson(0, 'error', '暂无数据(参数错误)');
        }

        $Order = new Order();
        $data = $Order->getOrderDetail($get['id']);

        return $this->asJson(1, 'success', '获取成功', $data);
    }

    /**
     * 推单详细
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function pushInfo()
    {
        $get = $this->get;

        // 不可避免用户传入ID负值
        if ((int)$get['id'] <= 0) {
            return $this->asJson(0, 'error', '暂无数据(参数错误)');
        }

        $Order = new Order();
        $data = $Order->getOrderDetail($get['id']);

        return $this->asJson(1, 'success', '获取成功', $data);
    }
}