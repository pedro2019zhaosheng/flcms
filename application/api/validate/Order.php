<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/29
 * Time: 14:54
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\api\validate;

use think\Validate;

/**
 * 订单验证器
 *
 * Class Order
 * @package app\api\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Order extends Validate
{
    // 验证规则
    protected $rule = [
        'lottery_id|彩种ID' => 'require|integer',
        'beishu|倍数' => 'require|integer',
        'zhu|注数' => 'require|integer|elt:2000',
        'amount|下注金额' => 'require|float|gt:0',
        'bet_type|玩法类型' => 'require|integer',
        'chuan|串关' => 'require',
        'beizhu|备注信息' => 'length:1,255',
        'bet_content|投注项' => 'require',
        'is_yh|是否优化' => 'require|integer',
        'order_content|每注数据' => 'require',
        'order_num|订单号' => 'require',
        'start_amount|起跟金额' => 'require|float',
        'commission_rate|佣金比例' => 'require|float',
        'order_title|订单标题' => 'require|length:0,255',
        'start_time|跟单截止时间' => 'require',
        'pay_type|订单类型' => 'require|in:1,2,3',
        'order_id|订单ID' => 'require|integer',
        'number_count|期号' => 'require',
        'is_follow|是否追号' => 'require|in:0,1',
        'total_zhu|总注数' => 'require|integer',
        'current_number|当前期号' => 'require',
        'follow_detail|追号详情' => 'require',
    ];
    // 错误提示
    protected $message = [
        'pay_type.in' => '无效的订单类型',
        'zhu.elt' => '投注数小于等于2000',
        'amount.gt' => '投注异常,下注金额必须大于0元',
    ];
    // 验证场景
    protected $scene = [
        // 体彩下单
        'insert' => ['lottery_id', 'beishu', 'zhu', 'amount', 'bet_type', 'chuan', 'beizhu', 'bet_content', 'is_yh', 'pay_type', 'order_content'],
        // 体彩推单
        'push' => ['order_num', 'start_amount', 'commission_rate', 'order_title', 'start_time'],
        // 排三,排五,澳彩,葡彩下单
        'nlinsert' => ['amount', 'lottery_id', 'number_count', 'is_follow', 'total_zhu', 'beizhu', 'current_number', 'bet_content', 'follow_detail'],
        // 飞艇下单
        'ftinsert' => ['lottery_id', 'total_zhu', 'amount', 'bet_content', 'current_number'],
    ];

    // // 验证场景
    public function sceneOrderList()
    {
        return $this->only(['lottery_id']);
    }

    // 竞彩跟单验证场景
    public function sceneTailOrder()
    {
        return $this->only(['order_id', 'beishu', 'amount']);
    }

    // 数字彩推单验证场景
    public function sceneNlPushOrder()
    {
        return $this->only(['order_num', 'start_amount', 'commission_rate', 'order_title']);
    }
}