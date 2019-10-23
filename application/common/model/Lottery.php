<?php

namespace app\common\model;

use app\common\BaseModel;
use think\Exception;

/**
 * 彩种模型
 *
 * Class Lottery
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Lottery extends BaseModel
{
    /**
     * 查询条件
     *
     * @param $param // 查询参数
     * @return array // 搜索条件
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function commonFilter($param)
    {
        $where = [];
        // 根据彩种名称搜索
        if (isset($param['name']) && !empty($param['name'])) {
            $where[] = ['name', 'like', '%' . (string)$param['name'] . '%'];
        }
        // 根据彩种状态搜索
        if (isset($param['status']) && ($param['status'] !== null || $param['status'] !== '')) {
            $where[] = ['status', '=', (int)$param['status']];
        }

        return $where;
    }

    /**
     * 获取彩种列表分页
     *
     * @param null $param // 搜索参数
     * @param null $order // 排序条件
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getLotPage($param = null, $order = null)
    {
        $perPage = 10;
        if (isset($param['perPage']) && !empty($param['perPage'])) {
            $perPage = (int)$param['perPage'];
        }

        $where = $this->commonFilter($param);
        if (!$order) {
            $order = 'create_at DESC';
        }

        $list = self::where($where)
            ->order($order)
            ->paginate($perPage);

        foreach ($list as $k => $item) {
            $list[$k]['img'] = Attach::getPathByAttachId($item['img']);
        }

        return $list;
    }

    /**
     * 批量停售或正常
     *
     * @param $ids // 彩种ID
     * @param $status // 彩种状态
     * @return bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function toggle($ids, $status)
    {
        self::where('id', 'in', $ids)->setField('status', (int)$status);
        return true;
    }

    /**
     * @desc 获取彩种信息数据
     * @auther LiBin
     * @date 2019-03-08
     */
    public function getLottery()
    {
        return self::field('id,name,code')->select();
    }

    /**
     * @desc 获取单条彩种信息
     * @return mixed
     * @throws Exception
     * @auther LiBin
     * @param $where
     * @param $data
     * @date 2019-03-08
     */
    public function getOneLottery($where, $data)
    {
        return self::where($where)->field($data)->find();
    }

    /**
     * @desc 批量添加返佣比例数据
     * @auther LiBin
     * @param $data //添加数据(二维数组)
     * @return int|string
     * @date 2019-03-08
     */
    public function saveAllLottery($data)
    {
        return self::insertAll($data);
    }

    /**
     * 通过彩种获取竞彩代码
     *
     * @param $id // 彩种ID
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getCodeById($id)
    {
        $code = self::getValByWhere(['id' => $id], 'code');
        return $code ?: '';
    }

    /**
     * @desc 通过彩种的code获取彩种ID
     * @auther LiBin
     * @param $code //彩种编码
     * @return string
     * @date 2019-04-15
     */
    public static function getIdByCode($code)
    {
        $id = self::getValByWhere(['code' => $code], 'id');
        return $id ?: '';
    }

    /**
     * 获取彩种列表
     *
     * @param null $param // 搜索参数
     * @param null $order // 排序条件
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @api *
     */
    public function getList($param = null, $order = null)
    {

        $where = $this->commonFilter($param);
        if (!$order) {
            $order = 'create_at DESC';
        }

        $list = self::where($where)->order($order)->field('id,name,code')->select();
        return $list ?: null;
    }
}