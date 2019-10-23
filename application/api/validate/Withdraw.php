<?php

namespace app\api\validate;

use think\Validate;

/**
 * 提现验证器
 * Class Withdraw
 * @package app\api\validate
 */
class Withdraw extends Validate
{
    // 规则
    protected $rule = [
        'member_id|用户id'  => 'require',
        'bank_id|银行卡id'  => 'require',
        'account|提现金额'  => 'require|float',
    ];

    // 提示语
    protected $message = [
        'member_id.require' => '用户id不能为空',
        'bank_id.require'   => '银行卡id不能为空',
        'account.require'   => '提现金额不能为空',
        'account.float'    => '提现金额应为数字',
    ];


}