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
 * 订单内容模型
 *
 * Class OrderContent
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class OrderContent extends BaseModel
{
    /**
     * 写入订单内容
     *
     * @param $data // form数据
     * @return integer // 订单内容ID
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function saveOrderContent($data)
    {
        $contentId = self::quickCreate($data);
        return $contentId;
    }

    /**
     * @desc 获取订单内容
     * @auther LiBin
     * @param $where
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-04-11
     */
    public function getOrderContent($where)
    {
        return self::where($where)->select();
    }
}