<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/13
 * Time: 15:40
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;

/**
 * 会员关注模型
 *
 * Class Attention
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Attention extends BaseModel
{
    /**
     * 获取我的粉丝列表
     *
     * @param $uid // 会员ID
     * @return array
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getMyFansList($uid)
    {
        $list = self::where('member_attention_id', $uid)
            ->field([
                'id', // ID
                'member_id', // 会员ID
                'create_at', // 关注时间
            ])
            ->select()
            ->toArray();

        return $list;
    }

    /**
     * 获取我的粉丝数量
     *
     * @param $uid // 会员ID
     * @return float|int|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getMyFansCount($uid)
    {
        $count = self::where('member_attention_id', $uid)
            ->count('id');

        return is_string($count) ? 0 : $count;
    }

    /**
     * 我的关注列表
     *
     * @param $uid // 会员ID
     * @return array
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getMyAttentionList($uid)
    {
        $list = self::where('member_id', $uid)
            ->field([
                'id', // ID
                'member_attention_id', // 我关注会员的ID
                'create_at', // 关注时间
            ])
            ->select()
            ->toArray();

        return $list;
    }

    /**
     * 我关注的人数
     *
     * @param $uid // 会员ID
     * @return float|int|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getMyAttentionCount($uid)
    {
        $count = self::where('member_id', $uid)
            ->count('id');

        return is_string($count) ? 0 : $count;
    }

    /**
     * 关注人
     *
     * @param $myUid // 我的会员ID
     * @param $attentionUid // 被关注的会员ID
     * @return true|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function insertTb($myUid, $attentionUid)
    {
        if ($myUid == $attentionUid){
            return '您无法关注自己';
        }

        $attentionId = self::quickCreate([
            'member_id' => $myUid,
            'member_attention_id' => $attentionUid,
            'create_at' => Helper::timeFormat(time(), 's'),
        ]);

        return $attentionId ? true : '关注失败';
    }

    /**
     * 取消关注
     *
     * @param $myUid // 我的会员ID
     * @param $attentionUid // 被关注的会员ID
     * @return int
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function cancelAttention($myUid, $attentionUid)
    {
        $result = self::where('member_id', $myUid)->where('member_attention_id', $attentionUid)->delete();
        return $result;
    }

    /**
     * @desc 获取关注状态
     * @auther LiBin
     * @return mixed
     * @throws \Exception
     * @param $myUid //会员ID
     * @param $attentionUid //被关注人ID
     * @date 2019-04-15
     */
    public function getAttentionType($myUid,$attentionUid)
    {
        $result = self::where('member_id', $myUid)->where('member_attention_id', $attentionUid)->field('id')->find();
        if(!empty($result)){//会员存在
            $result = 1;
        }else{//会员不存在
            $result = 0;
        }
        return $result;
    }
}