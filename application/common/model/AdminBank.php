<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/28
 * Time: 18:17
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;

/**
 * 银行卡管理模型
 *
 * Class AdminBank
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class AdminBank extends BaseModel
{
    /**
     * @desc 获取银行卡列表
     * @auther LiBin
     * @param $where
     * @param $data
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-04-24
     */
    public function getBankList($where,$data)
    {
        return self::where($where)->field($data)->select();
    }

    /**
     * @desc 获取单个银行卡
     * @auther LiBin
     * @param $where
     * @param $data
     * @return array|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-04-24
     */
    public function getBankOne($where,$data)
    {
        return self::where($where)->field($data)->find();
    }
}
