<?php

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;

/**
 * 短信记录表
 *
 * Class AdminSmslog
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class AdminSmslog extends BaseModel
{
    /**
     * 短信记录列表
     *
     * @param array $where // 条件
     * @param string $order // 排序
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function smsList($where = [], $order = '')
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        if (empty($order)) {
            $order = 'create_time DESC';
        }

        $where = self::smsWhere($where);

        $page = self::where($where)->order($order)->paginate($perPage);
        return $page;
    }

    /**
     * 公共搜索条件
     *
     * @param $where
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function smsWhere($where)
    {
        $map = [];
        // 手机号
        if (isset($where['phone']) && !empty($where['phone'])) {
            $map[] = ['phone', 'like', '%' . $where['phone'] . '%'];
        }
        // 日期
        if (
            isset($where['start_date'])
            && !empty($where['start_date'])
            && isset($where['end_date'])
            && !empty($where['end_date'])
        ) {
            $map[] = ['create_time', 'between time', [$where['start_date'], $where['start_date']]];
        }

        return $map;
    }

    /**
     * @desc 短信记录的插入
     * @auther LiBin
     * @param $data 插入内容
     * @date 2019-03-22
     */
    public function smsAdd($data)
    {
       return  self::quickCreate($data);
    }

    /**
     * @desc 统计区间的短信数量
     * @auther LiBin
     * @param $where 统计区间的短信数量
     * @date 2019-03-22
     */
    public function smsCount($where)
    {
        return self::where($where)->count('id');
    }

    /**
     * @desc 统计每个电话号的短信数量
     * @auther ken
     * @param $where 统计区间
     * @date 2019-04-30
     */
    public function smsSendCount($where,$phone = '')
    {
        return  self::where($where)->where($phone)->count();
    }

    /**
     * @desc 获取某条短信记录
     * @auther LiBin
     * @param $where
     * @param $order
     * @param $data
     * @date 2019-03-22
     */
    public function smsOne($where,$order,$data)
    {
        return self::where($where)->order($order)->field($data)->find();
    }

    /**
     * @desc 验证手机验证码
     * @auther LiBin
     * @param $mobile //手机号
     * @param $lcode //验证码
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-03-25
     */
    public function checkSms($mobile,$lcode)
    {
        $data = self::where(['phone' => $mobile])->field('create_time,content')->order('create_time DESC')->find();
        if (empty($data)) {
            return [0, 'error', '请输入正确的验证码'];
        }

        $code = $data['content'];
        $expire = strtotime($data['create_time'])+(10*60);//默认10分钟过期
        if (strcmp($lcode, $code)) {
            return [0, 'error', '验证码不正确'];
        }

        if ($expire < time()) {
            return [0, 'error', '验证码已过期'];
        }

        return [1, 'success', '验证通过'];
    }
    /**
     * faker批量插入测试数据
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function faker()
    {
        for ($i = 0; $i < 50; $i++) {
            self::quickCreate([
                'tplid' => 0,
                'tpltype' => rand(0, 1),
                'phone' => rand(0, 10000),
                'r_id' => 0,
                's_id' => 0,
                'content' => '测试一下',
                'orderid' => rand(0, 10),
                'extra' => '测试一下',
                'create_time' => Helper::timeFormat(time(), 's'),
            ]);
        }
    }
}
