<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/1
 * Time: 13:48
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\validate;

use think\Validate;
use app\common\model\Member;
/**
 * 总后台登录验证器
 *
 * Class Login
 * @package app\vp\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Agent extends Validate
{
    // 规则
    protected $rule = [
        'id|代理商ID'        => 'require|integer',
        'username|手机号'    => 'require|mobile',
        'nickname|用户昵称'  => 'require|checkName',
        'password|密码'      => 'require|min:6',
        'witdraw|权限'       => 'require',
        'status|状态'        => 'require',
        'bank_num|银行卡号' => 'unique:memberBank',
        'member_id|会员id' => 'require',
        'cardholder|持卡人姓名' => 'require',
        'bank|开户行名称' => 'require',
    ];

    // 场景5.0
    protected $scene = [
        'setAgentrebate' => ['id'],
        'addAgent'=>['username','nickname','password','witdraw','status'],
    ];

    // update验证场景定义 5.1
    public function sceneSetAgentrebate()
    {
        return $this->only(['id']);
    }

    public function sceneAddAgent()
    {
        return $this->only(['username','nickname','password','witdraw','status']);
    }

    public function sceneSaveBankCard()
    {
        return $this->only(['bank_num','member_id','bank','cardholder']);
    }

    //验证昵称重复
    protected function checkName($value)
    {
        $member = new Member();
        $id = $member->getOneMember(['chn_name'=>$value],'id');
        if(!empty($id)){
            return '昵称已存在请重新定义';
        }else{
            return true;
        }
    }
}