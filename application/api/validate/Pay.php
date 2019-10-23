<?php

namespace app\api\validate;

use think\Validate;

/**
 * 充值验证器
 * Class Pay
 * @package app\api\validate
 */
class Pay extends Validate
{
    // 规则
    protected $rule = [
        'account|充值金额'  => 'require|float', // 过滤校验float
        'type|支付方式'     => 'require',
    ];

    // 提示语
    protected $message = [
        'account.require'   => '请填写充值金额',
        'account.float'    => '请正确填写充值金额',
        'type.require'      => '请选择支付方式',
    ];
}