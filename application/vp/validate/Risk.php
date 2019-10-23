<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/20
 * Time: 12:47
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\validate;

use think\Validate;

/**
 * 风控验证器
 *
 * Class Risk
 * @package app\vp\validate
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Risk extends Validate
{
    // 规则
    protected $rule = [
        'number|期号' => 'require',
        'ctype|数字彩种类型' => 'require|in:3,4',
        'openCode|开奖号码' => 'require',
    ];

    // 提示
    protected $message = [];

    // 场景
    protected $scene = [
        'handInsert' => ['number', 'ctype', 'openCode'],
    ];
}