<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/24
 * Time: 13:39
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\validate;

use think\Validate;

/**
 * 竞彩篮球验证器
 *
 * Class Jclq
 * @package app\vp\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Jclq extends Validate
{
    // 定义正则规则
    protected $regex = ['score' => '^\-?\d+\.?\d*$'];

    // 规则
    protected $rule = [
        'cutoff_time|手动截止时间' => 'date',
        'rqs|让球数' => 'regex:score',
        'hostScore|主队得分' => 'integer',
        'guestScore|客队得分' => 'integer',
        'match_num|赛事编号' => 'length:1,20'
    ];

    // 提示语
    protected $message = [];

    // 场景
    protected $scene = [
        'edit' => ['cutoff_time', 'rqs', 'hostScore', 'guestScore', 'match_num']
    ];
}