<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/16
 * Time: 16:43
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\web\validate;

use app\common\model\AdminSmslog;
use think\Validate;

/**
 * web注册页验证器
 *
 * Class Reg
 * @package app\web\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Reg extends Validate
{
    // 验证规则
    protected $rule = [
        'username|手机号' => 'require|mobile|unique:member',
        'nickname|昵称' => 'require|length:1,15|unique:member,chn_name',
        'password|密码' => 'require|length:6,20',
        'verifyCode|验证码' => 'require|verify',
        'inviteCode|邀请码' => 'require',
    ];

    // 提示语
    protected $message = [
        'username.mobile' => '请输入正确的手机号',
        'username.unique' => '手机号已存在',
        'nickname.length' => '请输入1至15个字符的昵称',
        'nickname.unique' => '昵称已存在',
        'password.length' => '请输入6至20个字符的密码',
        'verifyCode.verify' => '请输入正确的验证码',
    ];

    // 验证场景
    protected $scene = [
        'add' => ['username', 'nickname', 'password', 'verifyCode', 'inviteCode'],
    ];

    /**
     * 自定义验证码验证
     *
     * @param $value // 值
     * @param $rule // 规则
     * @param $data // 所有数据
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function verify($value, $rule, $data)
    {
        $smsModel = new AdminSmslog();
        $checkCode = $smsModel->checkSms($data['username'], $value);
        if (is_array($checkCode)) {
            $code = array_shift($checkCode);
            if ($code === 1) {
                return true;
            }

            return '验证码错误';
        }

        return '验证码错误';
    }
}