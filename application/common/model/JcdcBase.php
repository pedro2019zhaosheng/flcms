<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/17
 * Time: 15:54
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;
use think\Db;

/**
 * 北京单场赛事信息模型
 *
 * Class JcdcBase
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class JcdcBase extends BaseModel
{
    /**
     * 公共筛选
     *
     * @param $param // 请求参数
     * @return array // 筛选条件
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function commonFilter($param)
    {
        $where = [];

        // 比赛日期筛选
        if (
            isset($param['startDate'])
            && !empty($param['startDate'])
            && isset($param['endDate'])
            && !empty($param['endDate'])
            && strtotime($param['startDate'])
            && strtotime($param['endDate'])
        ) {
            $where[] = ['match_time', 'between', [strtotime($param['startDate']), strtotime($param['endDate'])]];
        } else {
            if (
                isset($param['startDate'])
                && !empty($param['startDate'])
                && strtotime($param['startDate'])
            ) {
                $where[] = ['match_time', '>=', strtotime($param['startDate'])];
            }

            if (
                isset($param['endDate'])
                && !empty($param['endDate'])
                && strtotime($param['endDate'])
            ) {
                $where[] = ['match_time', '<=', strtotime($param['endDate'])];
            }
        }

        // 联盟名称筛选
        if (isset($param['name']) && !empty($param['name'])) {
            $where[] = ['league_name|host_name|guest_name', 'like', '%' . $param['name'] . '%'];
        }

        // 赛事编号筛选
        if (isset($param['matchNum']) && !empty($param['matchNum'])) {
            $where[] = ['match_num', 'like', '%' . $param['matchNum'] . '%'];
        }

        // 出售状态筛选
        if (isset($param['state']) && $param['state'] !== '') {
            $where[] = ['sale_status', '=', $param['state']];
        }

        return $where;
    }

    /**
     * 北京单场列表页
     *
     * @param $param // 搜索参数
     * @param $order // 排序规则
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getList($param, $order = 'sort ASC')
    {
        $perPage = 10;
        if (isset($param['perPage']) && !empty($param['perPage'])) {
            $perPage = (int)$param['perPage'];
        }

        $where = $this->commonFilter($param);
        $paginate = self::where($where)
            ->field([
                'id', // 主键ID
                'match_num', // 赛事编号
                'league_name', // 联赛名称
                'host_name', // 主队名称
                'guest_name', // 客队名称
                'start_time', // 开赛时间
                'jc_date', // 竞彩日期
                'sys_cutoff_time', // 系统截止时间
                'cutoff_time', // 手动截止时间
                'sale_status', // 出售状态
                'match_status', // 赛事状态
                'rqs', // 让球数
            ])
            ->order($order)
            ->paginate($perPage);

        foreach ($paginate as &$item) {
            $item['start_time'] = Helper::timeFormat($item['start_time'], 's'); // 开赛时间
            $item['jc_date'] = Helper::timeFormat(strtotime($item['jc_date']), 'd'); // 竞彩日期
        }

        return $paginate;
    }

    /**
     * 批量出售停售
     *
     * @param $ids // 主键ID
     * @param $status // 状态码
     * @return bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function toggle($ids, $status)
    {
        if (!in_array($status, [0, 1])) {
            return false;
        }

        self::where('id', 'in', $ids)
            ->setField('sale_status', $status);

        return true;
    }

    /**
     * 删除赛事
     *
     * @param $ids // 主键ID
     * @return bool
     * @throws \think\Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function del($ids)
    {
        $find = self::where('id', 'in', $ids)->where('sale_status', 1)->find();
        if (!empty($find)) {
            return '存在出售中的赛事，禁止删除';
        }

        $data = self::where('id', 'in', $ids)->select()->toArray();
        // 判断是否已开奖
        $matchNum = array_column($data, 'match_num');
        $result = JcdcOpen::where('match_num', 'in', $matchNum)->where('status', 0)->find();
        if (!empty($result)) {
            return '存在未开奖的赛事,禁止删除';
        }

        try {
            Db::startTrans();
            JcdcOpen::where('match_num', 'in', $matchNum)->delete();
            JcdcMatch::where('match_num', 'in', $matchNum)->delete();
            self::where('match_num', 'in', $matchNum)->delete();
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
    }

    /**
     * 修改北京单场基础表
     *
     * @param $where // 条件
     * @param array $data // 数据
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function editData($where, array $data)
    {
        self::where($where)->setField($data);
    }

    /**
     * @desc 获取北京单场的数据比赛详情
     * @throws \Exception
     * @return array
     * @auther LiBin
     * @param $where
     * @param $data
     * @param $order // 排序规则
     * @date 2019年5月11日
     */
    public function getFootballData($where, $data, $order = 'a.sort ASC')
    {
        return self::alias('a')
            ->leftJoin('jcdc_match b', 'a.match_num=b.match_num')
            ->where($where)
            ->field($data)
            ->group('a.match_num')
            ->order($order)
            ->select();
    }
    /**
     * @desc 获取指定赛事的数据
     * @auther LiBin
     * @throws \Exception
     * @return array
     * @param $where
     * @param $data
     * @date 2019年5月13日
     */
    public function getBase($where, $data)
    {
        return self::where($where)->field($data)->select();
    }
    /**
     * 获取赛事截止时间
     *
     * @param $matchNum // 赛事编号
     * @return null|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getShutDownTimeByMatchNum($matchNum)
    {
        // 获取系统截止时间
        $sysDate = self::getValByWhere(['match_num' => $matchNum], 'sys_cutoff_time');
        // 获取手动截止时间
        $handDate = self::getValByWhere(['match_num' => $matchNum], 'cutoff_time');
        if (empty($handDate) && empty($sysDate)) {
            return '';
        }

        if (empty($handDate)) {
            return $sysDate;
        }

        return $handDate;
    }

    /**
     * 获取让球数
     *
     * @param $matchNum // 比赛编号
     * @return int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getRqs($matchNum)
    {
        $rqs = self::where('match_num', $matchNum)->value('rqs');

        return $rqs === '' ? 0 : (int)$rqs;
    }

    /**
     * @desc 导出北京单场比赛赛事
     * @param $param
     * @param string $order
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-05-18
     */
    public function exportData($param, $order = 'match_time ASC')
    {
        $where = $this->commonFilter($param);
        $model = self::where($where)
            ->order($order)
            ->field([
                'id',
                'match_num', // 比赛编号
                'jc_date', // 竞彩日期
                'start_time', // 开赛时间
                'league_name', // 联赛名称
                'host_name', // 主队
                'guest_name', // 客队
                'sys_cutoff_time', // 系统截止时间
                'cutoff_time', // 手动截止时间
                'sale_status', // 销售状态
                'match_status', // 赛事状态
                'rqs', // 让球数
            ])
            ->select();
        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        foreach ($data as &$item) {
            $item['start_time'] = Helper::timeFormat($item['start_time'], 's');
            $item['match_status'] = self::getMatchStatusByState($item['match_status']);
            $item['sale_status'] = ($item['sale_status'] === 0 ? '已停售' : '出售中');
        }

        // 导出
        Helper::exportExcel(
            'JCZuQiuExcel',
            [
                '主键ID', '比赛编号', '竞彩日期', '开赛时间', '联赛名称', '主队', '客队',
                '系统截止时间', '手动截止时间', '销售状态', '赛事状态', '让球数',
            ],
            $data
        );
    }

    /**
     * 获取赛事状态字符串
     *
     * @param $state // 状态码
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getMatchStatusByState($state)
    {
        switch ($state) {
            case 0:
                return '正常';
            case 1:
                return '取消';
            case 2:
                return '延期';
            case 3:
                return '斩腰';
            default:
                return '未知';
        }
    }
}