<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/22
 * Time: 10:32
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\api\validate;

use think\Validate;
use app\common\model\Member;

/**
 * 登录验证器
 *
 * Class Login
 * @package app\api\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Login extends Validate
{
    // 规则
    protected $rule = [
        'username|用户名' => 'require',
        'password|密码' => 'require|length:6,20',
        'code|验证码' => 'require|length:6',
        'mobile|手机号' => 'require|mobile',
        'nickName|昵称' => 'require|length:1,15|checkName',
        'invCode|邀请码' => 'require',
        'newPassword|新密码'=>'require|length:6,20',
        'affirmPassword|确认密码'=>'require|length:6,20|confirm:newPassword',
    ];

    // 提示语
    protected $message = [
        'username.require' => '请填写用户名',
        'password.require' => '请填写密码',
        'password.length' => '密码的长度6-20位',
        'code.length' => '验证码只能是6位',
        'affirmPassword.length' => '密码的长度6-20位',
        'affirmPassword.confirm' => '两次密码输入不一致',
    ];

    // 场景
    protected $scene = [
        'login' => ['username', 'password'],
    ];
     //发送验证码验证场景
    public function sceneSend()
    {
        return $this->only(['mobile']);
    }
    //注册验证场景
    public function sceneRegister()
    {
        return $this->only(['mobile','code','password','nickName','invCode']);
    }
    //找回密码验证场景
    public function sceneForget()
    {
        return $this->only(['mobile','code','password','affirmPassword']);
    }

    //修改密码验证场景
    public function sceneChangePassword()
    {
        return $this->only(['password','affirmPassword','newPassword']);
    }

    //验证昵称是否重复.
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