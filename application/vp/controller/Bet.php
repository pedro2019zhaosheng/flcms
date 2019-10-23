<?php

namespace app\vp\controller;

use app\common\VpController;
use app\common\model\Order;

/**
 * 注单列表
 *
 * Class Bet
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Bet extends VpController
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
        $order = new Order();
        $pagination = $order->getList($get, 'create_time DESC');
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
        $model = new Order;
        $pagination = $model->getPushOrderList($this->get);
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

    /**
     * 体彩注单内容编辑
     *
     * @param integer|null $id 注单ID
     * @return \think\response\Json
     * @author CleverStone
     */
    public function update($id = null)
    {
        if ($this->request->isPost()) {
            $data = $this->post;
            $validation = $this->validate($data, [
                'order_id|订单ID' => 'require|integer',
                'match_num|赛事编号' => 'require',
                'bet_item|投注项' => 'require',
            ]);

            if ($validation !== true) {
                return $this->asJson(0, 'error', $validation);
            }

            $model = new Order;
            $result = $model->updateBetContentBody($data);
            if ($result === true) {
                return $this->asJson(1, 'success', '编辑成功!');
            }

            return $this->asJson(0, 'error', $result);
        } else {
            $model = new Order;
            $data = $model->getBetContentBody($id);

            return $this->asJson(1, 'success', '获取成功', $data);
        }
    }

    /**
     * 订单重新开奖
     *
     * @return \think\response\Json
     * @throws \Exception
     * @author CleverStone
     */
    public function redraw()
    {
        set_time_limit(0);
        ini_set('memory_limit', '500M');

        $data = $this->post;
        $validation = $this->validate($data, [
            'match_num|赛事编号' => 'require',
            'order_id|订单ID' => 'require|integer',
            'lottery_code|竞彩代码' => 'require',
        ]);

        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $isClear = Order::getValByWhere(['id' => $data['order_id']], 'is_clear');
        if ($isClear) {
            return $this->asJson(0, 'error', '订单已结算, 无法重新开奖!');
        }

        $model = new Order;
        $result = $model->setDrawByOrderDetailIds($data['lottery_code'], $data['match_num']);
        if ($result === true) {
            return $this->asJson(1, 'success', '开奖成功!');
        }

        return $this->asJson(0, 'error', $result);
    }
}