<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/22
 * Time: 9:32
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\api\validate;

use think\Validate;

/**
 * 会员验证器
 *
 * Class Member
 * @package app\api\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Member extends Validate
{
    // 规则
    protected $rule = [
        'card|身份证号' => 'require|idCard',
        'name|真实姓名' => 'require',
    ];

    // 提示语
    protected $message = [
        'card.idCard' => '请输入正确的身份证号',
    ];

    //发送验证码验证场景
    public function sceneRealName()
    {
        return $this->only(['name','card']);
    }
}