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
        'nickname|用户昵称'  => 'require',
        'password|密码'      => 'require|min:6',
        'witdraw|权限'       => 'require',
        'status|状态'        => 'require',
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
}