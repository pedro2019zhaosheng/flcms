<?php

namespace app\pxy\validate;

use think\Validate;
use app\common\model\Member as MemberModel;

class Member extends Validate
{
    //验证规则
    protected $rule = [
        'userName|账号' => 'require|mobile',
        'nickName|真实姓名' => 'require|checkName',
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
        'withdraw|提现权限' => 'require'
    ];
    //提示语
    protected $message = [
    ];
    //验证场景
    protected $scene = [
        'add' => ['userName', 'nickName', 'passWord', 'draw', 'status', 'devStatus'],
        'transferMember' => ['userName', 'passWord', 'id'],
        'upAgent' => ['id', 'Lottery', 'passWord', 'lowlevel', 'withdraw'],
    ];

    public function sceneAdd()
    {
        return $this->only(['userName', 'nickName', 'passWord', 'draw', 'status', 'devStatus']);
    }

    public function sceneTransferMember()
    {
        return $this->only(['userName', 'passWord', 'id']);
    }

    public function sceneUpAgent()
    {
        return $this->only(['id', 'passWord', 'Lottery', 'lowlevel', 'withdraw']);
    }

    public function sceneSimulationAdd()
    {
        return $this->only(['userName', 'nickName', 'passWord', 'status']);
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