<?php

namespace app\api\controller;

use app\common\Helper;
use app\common\model\FundCharge;
use app\common\RestController;

/**
 * 充值
 * Class Pay
 * @package app\api\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Pay extends RestController
{

    // 凤凰提供的四方支付宝支付
    private static $_ALI_PAY1 = 'http://pay.xinhonsm.com:12530/wappay/pay.php';
    private static $_ALI_PAY2 = 'http://pay.xcrsm.com:12530/wappay/pay.php';
    private static $_ALI_PAY3 = 'http://122.114.232.245/alipay/wappay/pay2.php';

    /**
     * 充值统一入口
     * @inheritdoc
     *            1. 所有支付统一在此接口下单
     *            2. 如若已接支付弃用请标识`弃用`字段
     *            3. 请求类型 GET/POST 安全性(无) 数据提交量(post更大) 接口authentication校验
     * @param // account(充值金额) type(支付方式)
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function pay()
    {
        // 接收参数
        $request = $this->all;
        // 过滤验证
        $validation = $this->validate($request, 'pay');
        if ($validation !== true) {
            return $this->asNewJson('chargePayRet', 0, 'error', $validation);
        }

        // 统一下单
        $orderNo = FundCharge::payOrder(UID, $request['account'], $request['type']);
        if (!$orderNo) {
            return $this->asNewJson('chargePayRet', 0, 'error', '充值下单失败!');
        }

        // 分流派发
        switch ($request['type']) {
            // 支付宝支付(一)
            case FundCharge::TYPE_ALIPAY:
                $uri = ['_t' => self::$originToken, '_uid' => self::$uid, 'orderNo' => $orderNo];
                $query = http_build_query($uri);
                $alipayPreUrl = Helper::getCurrentHost() . '/api/pay/alipay1?' . $query;
                return $this->asNewJson('chargePayRet', 1, 'success', '下单成功', [$alipayPreUrl]);
            default:
                return $this->asNewJson('chargePayRet', 0, 'error', '暂不支持其他支付!');
        }
    }

    /**
     * 支付宝支付(一)
     * (预支付方法没有路由定义, 直接以`/模块/控制器/方法`访问即可)
     * @param $orderNo // 订单号
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function alipay1($orderNo)
    {
        $data = FundCharge::getFieldsByWhere(['order_no' => $orderNo], ['account', 'status', 'member_id']);
        // 校验订单
        if (empty($data)) {
            exit('订单不存在');
        }

        $toMoney = $data['account'];
        $status = $data['status'];
        $uid = $data['member_id'];
        // 校验支付状态
        if ($status !== FundCharge::STATUS_AWAIT) {
            exit('订单已过期');
        }

        // 校验会员
        if ($uid != UID) {
            exit('非法请求');
        }

        // 四方预支付参数
        $orderField = [];
        $orderField['WIDout_trade_no'] = $orderNo;
        $orderField['WIDtotal_amount222'] = $toMoney;
        $orderField['WID_user_id'] = $uid;
        // 预支付提交
        Helper::formSubmit(self::$_ALI_PAY3, $orderField, 'post');
        exit(0);
    }
}

