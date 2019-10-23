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

namespace app\pxy\validate;

use think\Validate;

/**
 * 代理商登录验证器
 *
 * Class Login
 * @package app\vp\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Login extends Validate
{
    // 规则
    protected $rule = [
        'username|用户名' => 'require',
        'password|密码' => 'require',
    ];

    // 场景
    protected $scene = [
        'login' => ['username', 'password'],
    ];
}