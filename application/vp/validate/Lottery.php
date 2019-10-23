<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/12
 * Time: 11:11
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\validate;

use think\Validate;

/**
 * 彩种验证器
 *
 * Class Lottery
 * @package app\vp\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Lottery extends Validate
{
    /**
     * 规则
     *
     * @var array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    protected $rule = [
        'status|状态' => 'require|integer',
        'ids|彩种ID' => 'require|length:1,20',
        'name|彩种名称' => 'require|unique:lottery,name|length:1,15',
        'code|彩种代码' => 'require|unique:lottery,code|length:1,255|^[\dA-Za-z_-]+$',
        'status|彩种状态' => 'require|integer',
        'id|彩种ID' => 'require|integer',
        'open_code|开奖号码' => 'require',
        'numId|数字彩开奖表ID' => 'require',
        'next_date|下期开奖日期' => 'date',
        'next_number|下期开奖号码' => 'length:1,20',
        'expect|期号' => 'require',
        'open_date|开奖日期' => 'require|date'
    ];

    /**
     * 场景
     *
     * @var array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    protected $scene = [
        // 新增
        'add' => ['name', 'code', 'status'],
        // 批量/操作
        'toggle' => ['ids', 'status'],
        // 修改数字彩开奖数据
        'editNum' => ['open_code', 'numId', 'next_date', 'next_number'],
        // 修改数字彩基础数据
        'editBase' => ['expect', 'open_date', 'numId'],
    ];

    /**
     * edit验证场景定义
     *
     * @return Lottery
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function sceneEdit()
    {
        return $this->only(['name', 'code', 'id'])
            ->remove('name', 'unique:lottery,name')
            ->remove('code', 'unique:lottery,code')
            ->append('name', 'filterName:name')
            ->append('code', 'filterCode:code');
    }

    /**
     * 修改彩种，验证彩种名称是否唯一
     *
     * @param $value // 值
     * @param $rule // 规则
     * @param $data // 数据
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function filterName($value, $rule, $data)
    {
        $id = (int)$data['id'];
        $result = \app\common\model\Lottery::where('id', '<>', $id)->where($rule, $value)->find();
        if ($result) {
            return '彩种名称重复';
        }

        return true;
    }

    /**
     * 修改彩种，验证彩种代码是否唯一
     *
     * @param $value // 值
     * @param $rule // 规则
     * @param $data // 数据
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function filterCode($value, $rule, $data)
    {
        $id = (int)$data['id'];
        $result = \app\common\model\Lottery::where('id', '<>', $id)->where($rule, $value)->find();
        if ($result) {
            return '彩种代码重复';
        }

        return true;
    }
}