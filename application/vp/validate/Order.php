<?php


namespace app\vp\validate;

use think\Validate;


/**
 * 注单验证器
 *
 * Class Order
 * @package app\vp\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Order extends Validate
{
    // 规则
    protected $rule = [
        'code|竞彩代码' => 'require',
        'matchNum|比赛编号' => 'require'
    ];

    // 提示语
    protected $message = [];

    // 场景
    protected $scene = [
        // 手动开奖
        'handDraw' => ['code', 'matchNum'],
    ];
}