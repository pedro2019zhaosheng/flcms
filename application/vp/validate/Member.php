<?php

namespace app\vp\validate;

use think\Validate;
use app\common\model\Member as MemberModel;

class Member extends Validate
{
    //验证规则
    protected $rule = [
        'userName|账号' => 'require|mobile',
        'nickName|昵称' => 'require|checkName|length:1,15',
        'passWord|密码' => 'require|min:6',
        'id' => 'require',
        'gold|彩金' => 'require',
        'remarks|备注' => 'require',
        'balance|余额' => 'require',
        'Lottery|返佣' => 'require',
        'draw|提现选择' => 'require',
        'status|状态选择' => 'require',
        'devStatus|发展下级' => 'require',
        'lowlevel|发展下级' => 'require',
        'withdraw|提现权限' => 'require',
        'angentPassword|操作密码' => 'require',
        'bank_num|银行卡号' => 'unique:memberBank',
        'member_id|会员id' => 'require',
        'cardholder|持卡人姓名' => 'require',
        'bank|开户行名称' => 'require',
        'isSelf|是否包含自身' => 'require|in:0,1'
    ];
    //提示语
    protected $message = [
    ];
    //验证场景
    protected $scene = [
        'add' => ['userName', 'nickName', 'passWord', 'draw', 'status', 'devStatus'],
        'transferMember' => ['userName', 'passWord', 'id'],
        'reviseGold' => ['id', 'passWord', 'gold', 'remarks'],
        'reviseBalance' => ['id', 'passWord', 'remarks', 'balance'],
        'upAgent' => ['id', 'Lottery', 'passWord', 'lowlevel', 'withdraw'],
        'transferAgent' => ['isSelf', 'userName', 'passWord', 'id'],
    ];

    public function sceneSimulationAdd()
    {
        return $this->only(['userName', 'nickName', 'passWord', 'status'])
            ->remove('userName', 'mobile')
            ->append('userName', 'float'); // 当int类型长度大于11位时, 自动转为float类型.
    }

    public function sceneSetPassword()
    {
        return $this->only(['angentPassword', 'passWord']);
    }

    public function sceneSaveBankCard()
    {
        return $this->only(['bank_num','member_id','bank','cardholder']);
    }

    //验证昵称重复
    protected function checkName($value)
    {
        $member = new MemberModel();
        $id = $member->getOneMember(['chn_name'=>$value],'id');
        if(!empty($id)){
            return '昵称已存在请重新定义';
        }else{
            return true;
        }
    }
}