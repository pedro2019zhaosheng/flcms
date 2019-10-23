<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/7
 * Time: 19:44
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\validate;

use think\Validate;

/**
 * 菜单验证器
 *
 * Class Menu
 * @package app\vp\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Menu extends Validate
{
    // 规则
    protected $rule = [
        'pid|父ID' => 'require|integer',
        'id|菜单ID' => 'require|integer',
        'title|菜单名称' => 'require|length:1,15',
        'icon|菜单图标' => '^[\w\s\-]+$',
        'module|模块名' => '^\w+$',
        'controller|控制器名' => '^\w+$',
        'action|方法名' => '^\w+$',
        'menu_type|菜单类型' => 'require|filterModule:menu_type',
        'url_value|实际链接' => '^[\w\-\/\_]+$|length:1,100',
    ];

    // 场景
    protected $scene = [
        'add' => ['pid', 'title', 'icon', 'module', 'controller', 'action', 'menu_type', 'url_value'],
        'update' => ['id', 'title', 'icon', 'module', 'controller', 'action', 'menu_type', 'url_value'],
    ];

    /**
     * 当菜单类型是module时，验证节点详情数据
     *
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    protected function filterModule($value, $rule, $data)
    {
        if (!strcasecmp($value, 'module')) {
            if (
            empty($data['module']
                || empty($data['controller'])
                || empty($data['action']))
            ) {
                return '菜单类型是module时，节点详情必填';
            }

            return true;
        }

        return true;
    }
}