<?php

namespace app\api\validate;

use think\Validate;

/**
 * 银行卡验证器.
 *
 * Class MemberBank
 *
 * @author hutao
 */
class MemberBank extends Validate
{
    // 规则
    protected $rule = [
        'bank_id' => 'require|integer',
        'member_id' => 'require|integer',
        'cardholder' => 'require',
        'bank' => 'require',
        'bank_num|银行卡号' => 'require|length:16,19',
        'mobile|手机号' => 'require|mobile',
        'code|验证码' => 'require|length:6',
        'default_or_not' => 'require',
    ];

    // 提示语
    protected $message = [
        'bank_id.require' => '缺少银行卡id编号',
        'bank_id.integer' => '银行卡id编号必须是整数',
        'member_id.require' => '缺少用户编号',
        'member_id.integer' => '用户编号必须是整数',
        'cardholder.require' => '请填写持卡人姓名',
        'bank.require' => '请填写开户行',
        'bank_num.require' => '请填写银行卡号',
        'bank_num.length' => '银行卡号位数不对',
        'default_or_not.require' => '请选择是否默认提现卡',
    ];

    // 新增场景
    protected $scene = [
        'add' => ['member_id', 'cardholder', 'bank', 'bank_num', 'mobile', 'code'],
    ];

    // 发送验证码验证场景
    public function sceneSend()
    {
        return $this->only(['mobile']);
    }

    // 修改场景
    public function sceneEdit()
    {
        return $this->only(['bank_id', 'member_id', 'cardholder', 'bank', 'bank_num', 'mobile', 'default_or_not']);
    }

    // 解绑场景
    public function sceneDel()
    {
        return $this->only(['bank_id']);
    }
}
