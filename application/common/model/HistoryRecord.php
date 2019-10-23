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
use app\common\Helper;

/**
 * 历史记录表
 *
 * Class HistoryRecord
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class HistoryRecord extends BaseModel
{
    /**
     * 添加历史记录
     * @param $data
     * @return bool|int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function insertHistory($data)
    {
        return self::quickCreate($data);
    }

    /**
     * @desc 获取搜索记录
     * @auther LiBin
     * @param $where
     * @param $data
     * @return array|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-04-18
     */
    public function getHistoryInfo($where,$data)
    {
        return self::where($where)->field($data)->order('create_at desc')->select();
    }

    /**
     * @desc 更新历史记录
     * @auther LiBin
     * @param $where
     * @param $data
     * @return int|string
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @date 2019-04-19
     */
    public function setHistoryInfo($where,$data)
    {
        return self::where($where)->update($data);
    }

    /**
     * @desc 删除历史记录
     * @auther LiBin
     * @param $where
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @date 2019-04-19
     */
    public function delHistory($where)
    {
        return self::where($where)->delete();
    }
}
