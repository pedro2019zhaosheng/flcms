<?php

namespace app\common\model;

use app\common\BaseModel;

/**
 * 代理商返点设置
 *
 * Class MemberRatio
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class MemberRatio extends BaseModel
{
    /**
     * 公共筛选
     *
     * @param $param // 筛选条件
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    private function commonFilter($param)
    {
        // 用户名筛选
        $where = [];
        if (isset($param['agentPhone']) && !empty($param['agentPhone'])) {
            $where[] = ['b.username', 'like', '%' . $param['agentPhone'] . '%'];
        }

        // 彩种筛选
        if (isset($param['product']) && !empty($param['product'])) {
            $where[] = ['a.lottery_id', '=', $param['product']];
        }

        // 返佣比例状态筛选
        if (isset($param['status']) && $param['status'] !== '') {
            $where[] = ['a.status', '=', $param['status']];
        }

        // 代理商筛选
        if (isset($param['agentId']) && !empty($param['agentId'])) {
            $where[] = ['a.member_id', 'in', $param['agentId']];
        }

        return $where;
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
     * @desc 获取代理商的返佣数据
     * @auther LiBin
     * @return array
     * @throws \Exception
     * @date 2019-03-08
     */
    public function getLottery($member_id)
    {
        return self::where(['member_id' => $member_id])->field('id,lottery_id,ratio')->select();
    }

    /**
     * @desc 批量删除代理商的返佣数据
     * @throws \Exception
     * @return int
     * @auther LiBin
     * @date 2019-03-11
     */
    public function deleteMemberBatch($where)
    {
        return self::where($where)->delete();
    }

    /**
     * 获取代理商返佣设置列表
     *
     * @param $where // 筛选条件
     * @param $order // 排序规则
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getAgentRatio($where, $order = 'a.id DESC')
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        $where = $this->commonFilter($where);
        $data = Member::where($where)
            ->where('b.is_delete', 0)
            ->where('b.role', 2)
            ->alias('b')
            ->leftJoin('member_ratio a', 'b.id=a.member_id')
            ->leftJoin('lottery c', 'c.id=a.lottery_id')
            ->field([
                'a.id',
                'b.username',
                'b.chn_name',
                'a.member_id',
                'a.lottery_id',
                'a.ratio',
                'a.status',
                'c.name',
            ])
            ->order($order)
            ->paginate($perPage);

        return $data;
    }

    /**
     * @desc 设置代理商的返佣状态
     * @auther LiBin
     * @date 2019-03-12
     */
    public function setAgentRatioStatus($where, $data)
    {
        return self::save($data, $where);
    }
}