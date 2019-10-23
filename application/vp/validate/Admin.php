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

/**
 * 总后台登录验证器
 *
 * Class Login
 * @package app\vp\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Admin extends Validate
{
    // 规则
    protected $rule = [
        'id|管理员ID' => 'require|integer',
        'roleId|角色' => 'require|integer',
        'username|用户名' => 'require|length:1,15|unique:admin',
        'pwd|用户密码' => 'require|length:6,18',
        'nickName|昵称' => 'require|length:1,15',
        'phone|手机号' => 'require|mobile|unique:admin',
        'email|邮箱' => 'email',
        'sort|排序' => 'require|integer',
    ];

    // 场景
    protected $scene = [
        'add' => ["roleId", 'username', 'nickName', 'phone', 'pwd'],
        'sort' => ['id', 'sort'],
    ];

    protected $message  =   [
        'pwd.length' => '密码需要包含6-18个英文字符，数字，下划线等'   
    ];

    // update验证场景定义
    public function sceneUpdate()
    {
        return $this->only(['id', 'roleId', 'username', 'pwd','nickName', 'phone'])
            ->remove('phone', 'unique:admin')
            ->remove('pwd', 'require')
            ->append('phone', 'filterMobile:phone')
            ->remove('username', 'unique:admin')
            ->append('username', 'filterUser:username');
    }

    // Modifys验证场景定义
    public function sceneModifys()
    {
        return $this->only(['pwd','nickName','phone', 'email'])
            ->remove('phone', 'unique')
            ->remove('pwd', 'require');
    }

    /**
     * username验证器
     *
     * @param $value // 值
     * @param $rule // 规则
     * @param $data // 数据
     * @return bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function filterUser($value, $rule, $data)
    {
        $result = \app\common\model\Admin::quickGetOne(null, [[$rule, '=', $value], ['id', '<>', (int)$data['id']]]);
        if (!empty($result)) {
            return '用户名已存在';
        }

        return true;
    }

    /**
     * phone验证器
     *
     * @param $value // 值
     * @param $rule // 规则
     * @param $data // 数据
     * @return bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function filterMobile($value, $rule, $data)
    {
        $result = \app\common\model\Admin::quickGetOne(null, [[$rule, '=', $value], ['id', '<>', (int)$data['id']]]);
        if (!empty($result)) {
            return '手机号已存在';
        }

        return true;
    }
}