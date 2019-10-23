<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/22
 * Time: 10:04
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;

/**
 * restAPI会员Token
 *
 * Class MemberToken
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class MemberToken extends BaseModel
{
    /**
     * 更新用户token表
     *
     * @param $memberId // 用户ID
     * @param $field // 字段
     * @param $value // 值
     * @return bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function updateRefresh($memberId, $field, $value)
    {
        self::where('member_id', $memberId)->setField($field, $value);
        return true;
    }
}