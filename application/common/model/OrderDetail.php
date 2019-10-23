<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/21
 * Time: 18:41
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;

/**
 * 订单详情模型
 *
 * Class OrderDetail
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class OrderDetail extends BaseModel
{

    /**
     *  获取赛事所有未结算订单
     *
     * @param $match // 赛事编号
     * @param $code // 竞彩代码
     * @return array // 数据数组
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getMatchDrawOrder($match, $code)
    {
        $lotteryId = Lottery::getIdByCode($code);
        // 获取该赛事的所有订单
        $data = self::alias('od')
            ->leftJoin('order o', 'od.order_id=o.id')
            ->where('od.match_num', $match) // 赛事编号
            ->where('od.lottery_id', $lotteryId) // 彩种ID
            ->where('o.is_clear', 0) // 未结算
            ->field([
                'o.id', // 订单ID
                'o.status', // 订单状态
                'o.order_no', // 订单号
                'o.username', // 会员账号
                'o.pay_status', // 支付状态
                'o.zhu', // 注数
                'o.amount', // 注单金额
                'o.create_time', // 下单时间
            ])
            ->group('o.id') // 分组去重
            ->select()
            ->toArray();

        return $data;
    }

    /**
     * 写入订单详情
     *
     * @param $data // 表单数据
     * @return bool|int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function saveOrderDetail($data)
    {
        $detailId = self::quickCreate($data);
        return $detailId;
    }

    /**
     * @desc 获取订单详情
     * @auther LiBin
     * @param $where
     * @return array|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-04-12
     */
    public function getOrderDetail($where)
    {
        return self::where($where)->select();
    }
}