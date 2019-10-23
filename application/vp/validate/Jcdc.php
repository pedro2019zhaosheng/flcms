<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/24
 * Time: 13:57
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\validate;

use think\Validate;

/**
 * 北京单场验证器
 *
 * Class Jcdc
 * @package app\vp\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Jcdc extends Validate
{
    // 定义正则规则
    protected $regex = ['score' => '(^\d$)|(^-\d$)'];

    // 规则
    protected $rule = [
        'cutoff_time|手动截止时间' => 'date',
        'rqs|让球数' => 'regex:score',
        'half_score|半场得分' => 'length:3,5',
        'normal_score|全场得分(不含加时)' => 'length:3,5',
        'match_num|赛事编号' => 'length:1,20'
    ];

    // 提示语
    protected $message = [];

    // 场景
    protected $scene = [
        'edit' => ['cutoff_time', 'rqs', 'half_score', 'normal_score', 'match_num']
    ];
}