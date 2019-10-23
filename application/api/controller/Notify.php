<?php

namespace app\api\controller;

use app\common\Helper;
use app\common\model\FundCharge;
use app\common\RestController;

/**
 * 统一异步回调接收器
 * Class Notify
 * @package app\api\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Notify extends RestController
{
    /**
     * 关闭身份认证
     * @var bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    protected $enableAuthentication = false;

    /**
     * 凤凰支付宝支付回调(一)
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function alipay1()
    {
        $notifyData = $this->post;
        // 记录系统日志
        Helper::log('系统', '充值回调', '充值回调日志', var_export($notifyData, true), 2);
        // 数据异常,继续通知回调
        if (empty($notifyData)) {
            exit(0);
        }

        $tradeStatus = isset($notifyData['trade_status']) ? $notifyData['trade_status'] : '';
        // 支付成功
        if (!strcasecmp($tradeStatus, 'TRADE_SUCCESS')) {
            $myOrderNo = $notifyData['out_trade_no'];
            // 订单号异常,继续通知回调
            if (empty($myOrderNo)) {
                exit(0);
            }

            // 支付成功,平台业务处理
            FundCharge::paySuccess($myOrderNo);
            // 支付成功,终止回调
            exit('success');
        } elseif (!strcasecmp($tradeStatus, 'TRADE_CLOSED')) {
            // 用户关闭支付通道,终止回调
            exit('success');
        } else {
            // 未知支付状态,继续通知回调
            exit(0);
        }
    }
}
