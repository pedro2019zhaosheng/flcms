<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/14
 * Time: 10:56
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;

/**
 * 追期详情模型
 *
 * Class OrderNum
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class OrderNum extends BaseModel
{

    /**
     * 数字彩开奖获取中奖订单
     * @param $param // 请求参数
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getNumBingoList($param)
    {
        $ctype = (integer)$param['ctype']; // 彩种类型
        $expect = (string)$param['expect']; // 期号
        $list = self::alias('on')
            ->leftJoin('order o', 'on.order_id=o.id')
            ->where('on.ctype', $ctype) // 数字彩类型
            ->where('on.number', $expect) // 期号
            ->where('o.is_clear', 0) // 未结算订单
            ->field([
                'o.order_no', // 订单号
                'o.username', // 会员账号
                'o.create_time', // 下注日期
                'o.pay_status', // 支付状态
                'on.number', // 期号
                'on.amount', // 单期下注金额
                'on.multiple', // 单期倍数
                'on.status', // 开奖状态
            ])
            ->select()
            ->toArray();

        return $list;
    }

    /**
     * @desc 获取单条详情
     * @auther LiBin
     * @param $where
     * @return array|null|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-05-16
     */
    public function getOrderNum($where)
    {
        return self::where($where)->find();
    }
}