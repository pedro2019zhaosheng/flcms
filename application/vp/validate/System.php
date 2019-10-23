<?php

namespace app\vp\validate;

use app\common\model\AdminService;
use think\Validate;

class System extends Validate
{
    /**
     * 验证规则
     *
     * @var array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    protected $rule = [
        'name|客服名称' => 'require|length:1,15|unique:adminService,name',
        'icon|客服图标' => 'require',
        'num|客服编号' => 'length:1,15|unique:adminService,num',
        'file|二维码' => 'require',
        'status|状态' => 'require',
        'id|客服ID' => 'require|integer',
    ];

    /**
     * 验证场景
     *
     * @var array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    protected $scene = [
        'add' => ['name', 'icon', 'num', 'file', 'status'],
    ];

    /**
     * 自定义`编辑`场景
     *
     * @return System
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function sceneEdit()
    {
        return $this->only(['name', 'num', 'id'])
            ->remove('name', 'unique:adminService,name')
            ->append('name', 'filterName:name')
            ->remove('num', 'unique:adminService,num')
            ->append('num', 'require|filterNum:num');
    }

    /**
     * name验证器
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
    protected function filterName($value, $rule, $data)
    {
        $result = AdminService::quickGetOne(null, [[$rule, '=', $value], ['id', '<>', (int)$data['id']]]);
        if (!empty($result)) {
            return '客服名称已存在';
        }

        return true;
    }

    /**
     * num验证器
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
    protected function filterNum($value, $rule, $data)
    {
        $result = AdminService::quickGetOne(null, [[$rule, '=', $value], ['id', '<>', (int)$data['id']]]);
        if (!empty($result)) {
            return '客服编号已存在';
        }

        return true;
    }
}
