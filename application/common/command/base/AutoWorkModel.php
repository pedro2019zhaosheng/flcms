<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/3
 * Time: 11:46
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\command\base;

use app\common\BaseModel;
use app\common\Helper;
use app\common\model\FundLog;
use app\common\model\JcdcBase;
use app\common\model\JclqBase;
use app\common\model\JczqBase;
use app\common\model\Member;
use app\common\model\Order;

/**
 * 即时处理任务公共模型
 *
 * Class AuthWorkModel
 * @package app\common\command\base
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class AutoWorkModel extends BaseModel
{
    // 推单自动审核成功时间间隔
    const AUTO_SUP_TIME_INTERVAL = 180; // 三分钟(180s)

    // 自动出票时间间隔
    const AUTO_DRAW_PILL_TIME_INTERVAL = 60; // 一分钟(60s)

    // 自动刷新待开奖状态时间间隔
    const AUTO_REFRESH_DRAW_TIME_INTERVAL = 60; // 一分钟(60s)

    /**
     * 批量推单审核
     *
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function batchSetOrderSupStatusAtSuccess()
    {
        $timeNode = time() - self::AUTO_SUP_TIME_INTERVAL;
        $dateNode = Helper::timeFormat($timeNode, 's');

        $orderModel = Order::where('pay_type', 3)// 推单
        ->where('sup_order_state', 0)// 推单状态: 待审核
        ->where('sup_order_time', '<= time', $dateNode)// 等待时间大于等于3分钟
        ->field([
            'id', // 订单ID
            'member_id', // 会员ID
            'order_no', // 订单号
        ])
            ->select();

        $data = [];
        if (!empty($orderModel)) {
            $data = $orderModel->toArray();
        }

        foreach ($data as $item) {
            $orderId = $item['id']; // 订单ID
            $uid = $item['member_id']; // 会员ID
            Order::quickCreate([
                'id' => $orderId, // 订单ID
                'sup_order_state' => 1, // 审核状态: 成功
            ], true);

            // 获取会员详情
            $memberData = Member::getFieldsByWhere(['id' => $uid], [
                'role', // 角色
                'username', // 用户名
                'chn_name', // 昵称
            ]);

            // 写入并推送消息
            Helper::logAndPushMsg(
                "推单{$item['order_no']}审核成功", // 消息内容
                $memberData['chn_name'], // 昵称
                $memberData['username'], // 用户账号
                1, // 消息类型: 系统
                2, // 内容类型: 会员注单
                0 // 头像或头标
            );
        }

        return true;
    }

    /**
     * 批量自动出票
     *
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function batchSetOrderDrawBill()
    {
        $timeNode = time() - self::AUTO_DRAW_PILL_TIME_INTERVAL;
        $dateNode = Helper::timeFormat($timeNode, 's');

        // 获取所有未支付待出票订单
        $orderModel = Order::where('pay_status', 0)// 支付中
        ->where('status', 0)// 待出票
        ->where('create_time', '<= time', $dateNode)// 等待时间大于等于3分钟
        ->field([
            'id', // 订单ID
            'member_id', // 会员ID
            'pay_hadsel', // 支付彩金
            'pay_balance', // 支付余额
            'order_no', // 订单号
        ])
            ->select();

        $data = [];
        if (!empty($orderModel)) {
            $data = $orderModel->toArray();
        }

        foreach ($data as $item) {
            $orderId = $item['id'];
            $uid = $item['member_id'];
            Order::quickCreate([
                'id' => $orderId,
                'pay_status' => 1, // 已支付
                'status' => 1, // 已出票
                'pay_time' => Helper::timeFormat(time(), 's'), // 支付时间
            ], true);

            // 扣除会员冻结资金
            $frozenAmount = $item['pay_hadsel'] + $item['pay_balance'];
            Member::where('id', $uid)->setDec('frozen_capital', $frozenAmount);

            // 写入资金流水记录
            // 获取会员彩金,余额,冻结资金
            $memberData = Member::getFieldsByWhere(['id' => $uid], [
                'balance', // 余额
                'hadsel', // 彩金
                'frozen_capital', // 冻结资金
                'role', // 角色
                'username', // 用户名
                'chn_name', // 昵称
                'is_moni', // 0: 模拟     1: 真实
            ]);

            // 如果是实单则写入资金流水记录
            if ((int)$memberData['is_moni'] === 1) {
                if (!empty((float)$item['pay_hadsel'])) {
                    FundLog::quickCreate([
                        'member_id' => $uid, // 会员ID
                        'money' => $item['pay_hadsel'], // 扣除彩金
                        'front_money' => $item['pay_hadsel'] + $memberData['hadsel'], // 变动前彩金
                        'later_money' => $memberData['hadsel'], // 变动后彩金
                        'type' => 3, // 购彩出票
                        'remark' => '注单出票扣除彩金' . $item['pay_hadsel'] . '元',
                        'create_time' => Helper::timeFormat(time(), 's'),
                        'update_time' => Helper::timeFormat(time(), 's'),
                        'order_id' => $orderId, // 注单ID
                        'identify' => $memberData['role'], // 用户角色
                        'username' => $memberData['username'], // 用户账号
                    ]);
                }

                if (!empty((float)$item['pay_balance'])) {
                    FundLog::quickCreate([
                        'member_id' => $uid, // 会员ID
                        'money' => $item['pay_balance'], // 扣除余额
                        'front_money' => $item['pay_balance'] + $memberData['balance'], // 变动前余额
                        'later_money' => $memberData['balance'], // 变动后余额
                        'type' => 3, // 购彩出票
                        'remark' => '注单出票扣除余额' . $item['pay_balance'] . '元',
                        'create_time' => Helper::timeFormat(time(), 's'),
                        'update_time' => Helper::timeFormat(time(), 's'),
                        'order_id' => $orderId, // 注单ID
                        'identify' => $memberData['role'], // 用户角色
                        'username' => $memberData['username'], // 用户账号
                    ]);
                }
            }

            // 写入消息并推送消息
            Helper::logAndPushMsg(
                "注单{$item['order_no']}出票成功", // 消息内容
                $memberData['chn_name'], // 昵称
                $memberData['username'], // 用户账号
                1, // 消息类型: 系统
                2, // 内容类型: 会员注单
                0 // 头像或头标
            );
        }

        return true;
    }

    /**
     * 批量更新已出票状态为待开奖状态
     *
     * @return bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function autoRefreshDrawStatus()
    {
        $timeNode = time() - self::AUTO_REFRESH_DRAW_TIME_INTERVAL;
        $dateNode = Helper::timeFormat($timeNode, 's');
        // 批量设置竞彩状态为待开奖
        Order::where('pay_status', 1)// 已支付
        ->where('status', 1)// 已出票
        ->where('pay_time', '<= time', $dateNode)// 等待时间大于等于3分钟
        ->setField('status', 2);

        return true;
    }

    /**
     * 批量停售到截止时间的足球赛事
     *
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function jzBatchSetMatchAtStopSale()
    {
        // 出售中的赛事
        $model = JczqBase::where('sale_status', 1)
            ->field([
                'id', // 主键ID
                'cutoff_time', // 手动截止时间
                'sys_cutoff_time', // 系统截止时间
            ])
            ->select();

        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        foreach ($data as $item) {
            $sysCutoff = $item['sys_cutoff_time']; // 系统截止时间
            $handCutoff = $item['cutoff_time']; // 手动截止时间

            $cutoffTime = $sysCutoff;
            if (!empty($handCutoff)) {
                $cutoffTime = $handCutoff;
            }

            $curDate = Helper::timeFormat(time(), 's');
            if ($cutoffTime <= $curDate) {
                JczqBase::quickCreate([
                    'id' => $item['id'], // 主键ID
                    'sale_status' => 0, // 已停售
                ], true);
            }
        }

        return true;
    }

    /**
     * 批量停售过期北单赛事
     *
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function bdBatchSetMatchAtStopSale()
    {
        // 出售中的赛事
        $model = JcdcBase::where('sale_status', 1)
            ->field([
                'id', // 主键ID
                'cutoff_time', // 手动截止时间
                'sys_cutoff_time', // 系统截止时间
            ])
            ->select();

        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        foreach ($data as $item) {
            $sysCutoff = $item['sys_cutoff_time']; // 系统截止时间
            $handCutoff = $item['cutoff_time']; // 手动截止时间

            $cutoffTime = $sysCutoff;
            if (!empty($handCutoff)) {
                $cutoffTime = $handCutoff;
            }

            $curDate = Helper::timeFormat(time(), 's');
            if ($cutoffTime <= $curDate) {
                JcdcBase::quickCreate([
                    'id' => $item['id'], // 主键ID
                    'sale_status' => 0, // 已停售
                ], true);
            }
        }

        return true;
    }

    /**
     * 批量停售过期篮球赛事
     *
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function JlBatchSetMatchAtStopSale()
    {
        // 出售中的赛事
        $model = JclqBase::where('sale_status', 1)
            ->field([
                'id', // 主键ID
                'cutoff_time', // 手动截止时间
                'sys_cutoff_time', // 系统截止时间
            ])
            ->select();

        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        foreach ($data as $item) {
            $sysCutoff = $item['sys_cutoff_time']; // 系统截止时间
            $handCutoff = $item['cutoff_time']; // 手动截止时间

            $cutoffTime = $sysCutoff;
            if (!empty($handCutoff)) {
                $cutoffTime = $handCutoff;
            }

            $curDate = Helper::timeFormat(time(), 's');
            if ($cutoffTime <= $curDate) {
                JclqBase::quickCreate([
                    'id' => $item['id'], // 主键ID
                    'sale_status' => 0, // 已停售
                ], true);
            }
        }

        return true;
    }
}