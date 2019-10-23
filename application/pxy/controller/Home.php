<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/1
 * Time: 16:22
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\pxy\controller;

use app\common\PxyController;
use app\common\model\Member;
use app\common\Helper;
use app\common\model\Order;
use app\common\model\FundLog;
use app\common\model\FundCharge;

/**
 * 首页控制器
 *
 * Class Home
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Home extends PxyController
{
    /**
     * 获取管理员详情
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function index()
    {
        $model = new Member();
        $data = $model->getOneInfo(UID);

        return $this->asJson(1, 'success', '请求成功', $data);
    }

    /**
     * 后台统计(in的性能很不好, 后期代理数量大后, 需要优化)
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function counter()
    {
        $member = new Member;
        // 获取我的所有下级
        $memberWhere[] = ['path', 'like', '%,' . UID . ',%'];
        $ids = $member->getUidColumn($memberWhere);
        // 获取会员数量(除: 已删除会员)
        $memberCount = $member->getMemberCount($memberWhere);
        // 获取今日新增会员
        $currentDay = Helper::mkTime('d');
        $startTime = date('Y-m-d H:i:s', $currentDay['start']);
        $endTime = date('Y-m-d H:i:s', $currentDay['end']);
        $todayMemberCount = $member->getMemberCount([
            ['create_at', 'between time', [$startTime, $endTime]],
            ['path', 'like', '%,' . UID . ',%'],
        ]);
        // 获取总投注(除: 未支付订单)
        $order = new Order;
        $betCount = $order->getBetCount([
            ['member_id', 'in', $ids]
        ]);
        // 获取今日新增投注
        $todayBetCount = $order->getBetCount([
            ['create_time', 'between time', [$startTime, $endTime]],
            ['member_id', 'in', $ids]
        ]);
        // 获取累计充值
        $fundLog = new FundLog;
        $rechargeCount = $fundLog->getRechargeCount(1, [
            ['member_id', 'in', $ids]
        ]);
        // 获取今日充值
        $todayRechargeCount = $fundLog->getRechargeCount(1, [
            ['create_time', 'between time', [$startTime, $endTime]],
            ['member_id', 'in', $ids]
        ]);
        // 获取累计提现
        $withdrawDeposit = $fundLog->getRechargeCount(2, [
            ['member_id', 'in', $ids]
        ]);
        // 获取今日提现
        $todayWithdrawDeposit = $fundLog->getRechargeCount(2, [
            ['create_time', 'between time', [$startTime, $endTime]],
            ['member_id', 'in', $ids]
        ]);
        // 获取客户总余额
        $customTotalBalance = $member->getMemberBalanceCount([
            ['id', 'in', $ids]
        ]);
        // 获取15天的注单走势
        $orderLine = $order->get15DaysOrderLine([
            ['member_id', 'in', $ids]
        ]);
        // 获取15天的入金走势
        $fundCharge = new FundCharge;
        $chargeLine = $fundCharge->get15DaysMoneyLine([
            ['member_id', 'in', $ids]
        ]);

        $data = [
            'memberTotalCount' => $memberCount, // 会员总数
            'memberTodayCount' => $todayMemberCount, // 今日新增会员数
            'betTotalCount' => $betCount, // 投注总数
            'betTodayCount' => $todayBetCount, // 今日新增注数
            'rechargeTotalCount' => $rechargeCount, // 总充值
            'rechargeTodayCount' => $todayRechargeCount, // 今日充值
            'withdrawTotalCount' => $withdrawDeposit, // 总提现
            'withdrawTodayCount' => $todayWithdrawDeposit, // 今日提现
            'customTotalBalance' => $customTotalBalance, // 客户总余额
            'orderLine' => $orderLine, // 订单走势数据
            'chargeLine' => $chargeLine, // 入金走势数据
        ];

        return $this->asJson(1, 'success', '请求成功', $data);
    }
}