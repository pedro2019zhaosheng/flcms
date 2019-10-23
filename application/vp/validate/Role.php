<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/6
 * Time: 13:43
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\validate;

use app\common\model\AdminRole;
use think\Validate;

/**
 * 角色验证器
 *
 * Class Role
 * @package app\vp\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Role extends Validate
{
    // 规则
    protected $rule = [
        'role|角色' => 'require|length:2,15|unique:adminRole,name',
        'desc|描述' => 'length:2,250',
        'sort|排序' => 'require|integer',
        'roletype|角色类型' => 'require|integer',
        'id|角色ID' => 'require|integer',
        'auth|节点ID' => 'require',
    ];

    // 提示
    protected $message = [];

    // 场景
    protected $scene = [
        'add' => ['role', 'desc', 'sort', 'roletype'],
        'auth' => ['id', 'auth'],
    ];

    // update验证场景定义
    public function sceneUpdate()
    {
        return $this->only(['role', 'desc'])
            ->remove('role', 'unique:adminRole,name')
            ->append('role', 'filterRole:name');
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
    protected function filterRole($value, $rule, $data)
    {
        $result = AdminRole::quickGetOne(null, [[$rule, '=', $value], ['id', '<>', (int)$data['id']]]);
        if (!empty($result)) {
            return '角色已存在';
        }

        return true;
    }
}