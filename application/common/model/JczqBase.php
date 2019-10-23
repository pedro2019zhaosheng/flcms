<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/12
 * Time: 19:05
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;
use think\Db;

/**
 * 竞彩足球赛事基本信息模型
 *
 * Class Jczq
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class JczqBase extends BaseModel
{

    /**
     * 公共筛选方法
     *
     * @param $param // get参数
     * @return array // where条件数组
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

        // 名称筛选
        if (isset($param['match_name']) && !empty($param['match_name'])) {
            $where[] = ['league_name|host_name|guest_name', 'like', '%' . (string)$param['match_name'] . '%'];
        }

        // 状态筛选，出售中、已停售
        if (
            isset($param['status'])
            && $param['status'] !== ''
            && $param['status'] !== null
        ) {
            $where[] = ['sale_status', '=', (int)$param['status']];
        }

        // 赛事编号查询
        if (isset($param['match_num']) && !empty($param['match_num'])) {
            $where[] = ['match_num', 'like', '%' . (int)$param['match_num'] . '%'];
        }

        return $where;
    }

    /**
     * 获取分页列表
     *
     * @param $param // get参数
     * @param string $order // 排序
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getPage($param, $order = 'sort ASC')
    {
        $perPage = 10;
        if (isset($param['perPage']) && !empty($param['perPage'])) {
            $perPage = (int)$param['perPage'];
        }

        $where = $this->commonFilter($param);
        $pagination = self::where($where)
            ->order($order)
            ->field([
                'id',
                'match_num', // 比赛编号
                'jc_num', // 竞彩编号
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
            ->paginate($perPage);

        foreach ($pagination as $k => $item) {
            $pagination[$k]['start_time'] = Helper::timeFormat($item['start_time'], 's');
        }

        return $pagination;
    }

    /**
     * 导出竞彩足球比赛赛事
     *
     * @param $param
     * @param string $order
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function exportData($param, $order = 'id DESC')
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

    /**
     * @desc 获取竞彩足球的数据比赛详情数据
     * @throws \Exception
     * @return array
     * @auther LiBin
     * @param $where
     * @param $data
     * @param $order // 排序规则
     * @date 2019-03-26
     */
    public function getFootballData($where, $data, $order = 'a.sort ASC')
    {
        return self::alias('a')
            ->leftJoin('jczq_match b', 'a.match_num=b.match_num')
            ->where($where)
            ->field($data)
            ->group('a.match_num')
            ->order($order)
            ->select();
    }
    /**
     * @desc 获取竞彩足球的数据比赛详情数据
     * @throws \Exception
     * @return array
     * @auther LiBin
     * @param $where
     * @param $data
     * @date 2019-03-26
     */
    public function getFootball($where, $data, $order = 'a.jc_date DESC')
    {
        return self::alias('a')
            ->leftJoin('jczq_match b', 'a.match_num = b.match_num')
            ->where($where)
            ->field($data)
            ->order($order)
            ->find();
    }

    /**
     * 设置销售状态
     *
     * @param $where
     * @param $status
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function toggleStatus($where, $status)
    {
        self::where($where)->setField('sale_status', $status);
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

        return $rqs === '' ? 0 : (float)$rqs;
    }

    /**
     * 批量删除
     *
     * @param $where // 删除条件
     * @return bool|string
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function deleteAll($where)
    {
        $find = self::where($where)->where('sale_status', 1)->find();
        if (!empty($find)) {
            return '存在出售中的赛事，禁止删除';
        }

        $data = self::where($where)->select()->toArray();
        // 判断是否已开奖
        $matchNum = array_column($data, 'match_num');
        $result = JczqOpen::where('match_num', 'in', $matchNum)->where('status', 0)->find();
        if (!empty($result)) {
            return '存在未开奖的赛事,禁止删除';
        }

        try {
            Db::startTrans();
            JczqOpen::where('match_num', 'in', $matchNum)->delete();
            JczqMatch::where('match_num', 'in', $matchNum)->delete();
            self::where('match_num', 'in', $matchNum)->delete();
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
    }

    /**
     * 修改竞彩足球基础表
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
     * @desc 获取指定赛事的数据
     * @auther LiBin
     * @throws \Exception
     * @return array
     * @param $where
     * @param $data
     * @date 2019-04-08
     */
    public function getBase($where, $data)
    {
        return self::where($where)->field($data)->select();
    }
}