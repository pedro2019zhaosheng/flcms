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

/**
 * 登录验证器
 *
 * Class Login
 * @package app\api\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Sms extends Validate
{
    // 规则
    protected $rule = [
        'mobile|用户名' => 'require|regex:/^1[34578]{1}\d{9}$/u',
        'code|手机号' => 'require|integer|length:6',
    ];

    // 提示语
    protected $message = [
        'mobile.require' => '请填写手机号',
        'mobile.regex' => '手机号格式不正确',
        'code.integer' => '短信验证码不能为空',
        'code.length'=> '短信验证码必须是六位',
    ];
    // update验证场景定义 5.1
    //发送手机短信验证码
    public function sceneSend()
    {
        return $this->only(['mobile']);
    }

    //验证短信验证码
    public function sceneVerify()
    {
        return $this->only(['mobile','code']);
    }


}