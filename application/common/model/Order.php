<?php

namespace app\common\model;

use app\common\BaseModel;
use app\common\Config;
use app\common\Helper;
use app\common\relation\Data;
use think\Db;

/**
 * 注单模型
 *
 * Class Order
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Order extends BaseModel
{
    use Data;

    /**
     * 条件筛选
     *
     * @param $where // 查询条件
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    private function commonFilter($where)
    {
        $endWhere = [];
        // 注单列表, 日期筛选
        if (
            isset($where['endDate'])
            && !empty($where['endDate'])
            && isset($where['startDate'])
            && !empty($where['startDate'])
        ) {
            $endWhere[] = ['create_time', 'between time', [$where['startDate'], $where['endDate']]];
        } else {
            if (isset($where['startDate']) && !empty($where['startDate'])) {
                $endWhere[] = ['create_time', '>=', $where['startDate']];
            }

            if (isset($where['endDate']) && !empty($where['endDate'])) {
                $endWhere[] = ['create_time', '<=', $where['endDate']];
            }
        }

        // 推单列表, 日期筛选
        if (
            isset($where['overDate'])
            && !empty($where['overDate'])
            && isset($where['beginDate'])
            && !empty($where['beginDate'])
        ) {
            $endWhere[] = ['sup_order_time', 'between time', [$where['beginDate'], $where['overDate']]];
        } else {
            if (isset($where['beginDate']) && !empty($where['beginDate'])) {
                $endWhere[] = ['sup_order_time', '>=', $where['beginDate']];
            }

            if (isset($where['overDate']) && !empty($where['overDate'])) {
                $endWhere[] = ['sup_order_time', '<=', $where['overDate']];
            }
        }

        // 彩种筛选
        if (isset($where['lottery_id']) && $where['lottery_id'] != -1) {
            $endWhere[] = ['lottery_id', '=', $where['lottery_id']];
        }

        // 编号筛选
        if (isset($where['order_no']) && !empty($where['order_no'])) {
            $endWhere[] = ['order_no', 'like', '%' . $where['order_no'] . '%'];
        }

        // 用户名筛选
        if (isset($where['username']) && !empty($where['username'])) {
            $id = Member::where('username', 'like', '%' . $where['username'] . '%')->column('id');
            $endWhere[] = ['member_id', 'in', $id];
        }

        // 支付状态 -1:未支付  0：支付中   1：已支付
        if (isset($where['pay_status']) && $where['pay_status'] !== '') {
            $endWhere[] = ['pay_status', '=', $where['pay_status']];
        }

        // 注单状态筛选 -1:未选中  0：待出票  1: 已出票  2：待开奖  3：未中奖  4：已中奖
        if (isset($where['status']) && $where['status'] != -1) {
            $endWhere[] = ['status', '=', $where['status']];
        }

        // 推单, 审核状态筛选
        if (isset($where['authStatus']) && $where['authStatus'] != -1) {
            $endWhere[] = ['sup_order_state', '=', $where['authStatus']];
        }

        // 是否模拟筛选
        if (isset($where['is_moni']) && $where['is_moni'] != -1) {
            $endWhere[] = ['is_moni', '=', $where['is_moni']];
        }

        // 结算状态筛选
        if (isset($where['settle_status']) && $where['settle_status'] != -1) {
            $endWhere[] = ['is_clear', '=', $where['settle_status']];
        }

        // 用户筛选
        if (isset($where['member_id']) && !empty($where['member_id'])) {
            $endWhere[] = ['member_id', 'in', $where['member_id']];
        }

        // 筛选购买方式 1.自购 2.跟单 3.推单
        if (isset($where['pay_type']) && !empty($where['pay_type'])) {
            $endWhere[] = ['pay_type', '=', $where['pay_type']];
        }

        // 账号筛选
        if (isset($where['accountName']) && !empty($where['accountName'])) {
            $endWhere[] = ['username', 'like', '%' . $where['accountName'] . '%'];
        }

        // 处理会员和代理name保持一致
        if (isset($where['name']) && !empty($where['name'])) {
            $where['agentName'] = $where['name'];
        }

        // 角色 代理会员筛选
        if (
            isset($where['role']) // 角色 1 会员  2代理商
            && !empty($where['role'])
        ) {
            // 判断用户账号是否为空
            if (isset($where['agentName']) && !empty($where['agentName'])) {
                if (!isset($where['lower']) || empty($where['lower'])) {
                    // 只选中角色和填写了代理商账号
                    $id = Member::where('username', 'like', '%' . $where['agentName'] . '%')->where('role', $where['role'])->column('id');
                    $endWhere[] = ['member_id', 'in', $id];
                } else {
                    $agentWhere[] = ['username', '=', $where['agentName']];
                    $memberId = Member::getValByWhere($agentWhere, 'id');
                    if (!empty($memberId)) {
                        $member = new Member;
                        // 获取用户的全部下级
                        if ($where['lower'] == 1) {
                            $myDownLevIds = $member->getDownUid($memberId);
                            if (!empty($myDownLevIds)) {
                                $endWhere[] = ['member_id', 'in', $myDownLevIds];
                            } else {
                                // 没有下级, 设置不存在的筛选条件, 返回空数据
                                $endWhere[] = ['member_id', '=', 'none'];
                            }
                        }
                        // 获取用户的直属下级
                        if ($where['lower'] == 2) {
                            $topLevWhere[] = ['top_id', '=', $memberId];
                            $myDownLevIds = $member->getUidColumn($topLevWhere);
                            if (!empty($myDownLevIds)) {
                                $endWhere[] = ['member_id', 'in', $myDownLevIds];
                            } else {
                                // 没有下级, 设置不存在的筛选条件, 返回空数据
                                $endWhere[] = ['member_id', '=', 'none'];
                            }
                        }
                    } else {
                        // 虚拟筛选
                        $endWhere[] = ['member_id', '=', 'none'];
                    }
                }
            } else {
                // 添加角色筛选
                $id = Member::where('role', $where['role'])->column('id');
                $endWhere[] = ['member_id', 'in', $id];
            }
        }

        return $endWhere;
    }

    /**
     * 获取注单列表
     *
     * @param $where // 条件
     * @param string $order // 排序
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getList($where, $order = '')
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        $where = $this->commonFilter($where);
        $paginate = self::where($where)
            ->field([
                'id',
                'member_id',
                'order_no',
                'bet_type',
                'order_title',
                'amount', /* 金额 */
                'bonus', /* 中奖金额(数字彩总奖金) */
                'bounty', /* 嘉奖彩金(数字彩总嘉奖) */
                'status', /* 订单状态 0：待出票 1：已出票 2：待开奖 3：未中奖 4：已中奖 5: 已派奖 6: 未完成 7: 已完成*/
                'pay_status', /* 支付状态 */
                'pay_type', /* 购买方式 1自购 2跟单 */
                'pay_time', /* 支付时间 */
                'lottery_id', // 彩种ID
                'is_moni', // 是否是模拟 0 非  1 是
                'chuan', // 串关信息
                'beishu', // 倍数(期数)
                'order_type', // 订单类型
            ])
            ->order($order)
            ->paginate($perPage);
        foreach ($paginate as &$v) {
            $lotteryInfo = Lottery::quickGetOne($v['lottery_id']);
            $lotteryName = '';
            if (!empty($lotteryInfo)) {
                $lotteryName = $lotteryInfo['name'];
            }

            $v['lottery'] = $lotteryName;
            switch ($v['order_type']) {
                case 1: // 竞技彩订单
                    $v['code'] = 'JC'; // 竞彩
                    // 获取过关方式
                    if ((int)$v['bet_type'] === 2) {
                        $v['chuan'] = Helper::jsonDecode(str_replace('@', '串', $v['chuan']));
                    } else {
                        $v['chuan'] = ['单关'];
                    }

                    break;
                case 2: // 数字彩订单
                    $v['code'] = 'SZC'; // 数字彩
                    $v['chuan'] = $v['beishu']; // 期数

                    break;
                default:
                    ;
            }

            unset(
                $v['lottery_id'],
                $v['beishu']
            );
            $v['username'] = Member::getValueByWhere(['id' => $v['member_id']], 'username');
            $v['role'] = Member::getValueByWhere(['id' => $v['member_id']], 'role');
        }

        return $paginate;
    }

    /**
     * 导出Excel
     *
     * @param $where // 筛选条件
     * @param string $order // 排序规则
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function exportData($where, $order = '')
    {
        $where = $this->commonFilter($where);
        $model = self::where($where)
            ->field([
                'id',
                'member_id',
                'order_no',
                'bet_type',
                'order_title',
                'amount', /* 金额 */
                'bonus', /* 中奖金额 */
                'bounty', /* 嘉奖金额 */
                'status', /* 订单状态 0：待出票 1：已出票 2：待开奖 3：未中奖 4：已中奖  5: 已派奖*/
                'pay_status', /* 支付状态 */
                'pay_type', /* 购买方式 1自购 2跟单 3推单*/
                'pay_time', /* 支付时间 */
                'lottery_id', // 彩种ID
                'is_moni', // 是否是模拟 0 非  1 是
                'chuan', // 串关信息
            ])
            ->order($order)
            ->select();
        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        foreach ($data as &$v) {
            $lotteryInfo = Lottery::quickGetOne($v['lottery_id']);
            $lotteryName = '';
            if (!empty($lotteryInfo)) {
                $lotteryName = $lotteryInfo['name'];
            }

            // 中奖状态
            $v['status'] = self::getOrderStatusByState($v['status']);
            // 支付状态
            $v['pay_status'] = self::getPayStatusByState($v['pay_status']);
            // 购买方式
            $v['pay_type'] = self::getBuyWayByState($v['pay_type']);
            // 是否模拟
            $v['is_moni'] = self::getIsMoniStr($v['is_moni']);
            // 彩种名称
            $v['lottery'] = $lotteryName;
            unset($v['lottery_id']);
            $v['username'] = Member::getValueByWhere(['id' => $v['member_id']], 'username');
            // 释放
            unset($v['member_id']);
            // 获取过关方式
            if ((int)$v['bet_type'] === 2) {
                $v['chuan'] = str_replace('@', '串', $v['chuan']);
            } else {
                $v['chuan'] = Helper::jsonEncode(['单关']);
            }

            // 释放
            unset($v['bet_type']);
        }

        // 导出
        Helper::exportExcel(
            'OrderExcel',
            [
                '主键ID', '订单号', '订单标题', '下注金额', '中奖金额', '嘉奖金额', '订单状态', '支付状态',
                '购买方式', '支付时间', '是否模拟', '过关方式', '彩种名称', '用户账号',
            ],
            $data
        );
    }

    /**
     * 获取是否模拟字符串
     *
     * @param $isMoniState // 状态码
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getIsMoniStr($isMoniState)
    {
        switch ($isMoniState) {
            case 0:
                return '实单';
            case 1:
                return '模拟订单';
            default:
                return '未知';
        }
    }

    /**
     * 获取购买方式
     *
     * @param $state // 状态码
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getBuyWayByState($state)
    {
        switch ($state) {
            case 1:
                return '自购';
            case 2:
                return '跟单';
            case 3:
                return '推单';
            default:
                return '未知';
        }
    }

    /**
     * 获取中奖状态描述
     *
     * @param $state // 状态码
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getOrderStatusByState($state)
    {
        switch ($state) {
            case 0:
                return '待出票';
            case 1:
                return '已出票';
            case 2:
                return '待开奖';
            case 3:
                return '未中奖';
            case 4:
                return '已中奖';
            default:
                return '未知';
        }
    }

    /**
     * 获取订单支付状态字符串
     *
     * @param $state // 状态码
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getPayStatusByState($state)
    {
        switch ($state) {
            case -1:
                return '未支付';
            case 0:
                return '支付中';
            case 1:
                return '已支付';
            default:
                return '未知';
        }
    }

    /**
     * 获取推单审核状态字符串
     *
     * @param $state // 状态码
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getAuditStatusByState($state)
    {
        switch ($state) {
            case 0:
                return '待审核';
            case 1:
                return '已同意';
            case 2:
                return '已驳回';
            default:
                return '未知';
        }
    }

    /**
     * app注单列表筛选
     *
     * @param $get // 查询条件
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    private function orderFilter($get)
    {
        // 定义where容器
        $where = [];
        // 会员筛选
        $where[] = ['member_id', '=', UID];

        // 订单类型筛选 1.自购 2.跟单 3.推单
        if (isset($get['pay_type']) && !empty($get['pay_type'])) {
            $where[] = ['pay_type', '=', $get['pay_type']];
        }

        // 中奖状态筛选
        if (isset($get['state']) && !empty($get['state'])) {
            if ($get['state'] == 1) { // 未中奖
                $where[] = ['status', '=', 3];
            }

            if ($get['state'] == 2) { // 中奖
                $where[] = ['status', '=', 4];
            }

            if ($get['state'] == 3) { // 其他(待出票, 已出票, 待开奖)
                $where[] = ['status', 'between', [0, 2]];
            }

            if ($get['state'] == 4) { // 中奖记录需要
                $where[] = ['status', '=', 4];
            }
        } else {
            // 默认全部订单
            $where[] = ['status', 'between', [0, 4]];
        }

        // 近三天/近一周筛选
        if (isset($get['date']) && !empty($get['date'])) {
            // 近三天
            if ($get['date'] == 1) {
                $starTime = date('Y-m-d', strtotime('-3 day')) . ' 00:00:00';
                $endTime = date('Y-m-d') . ' 23:59:59';
                $where[] = ['create_time', 'between', [$starTime, $endTime]];
            }
            // 近一周
            if ($get['date'] == 2) {
                $starTime = date("Y-m-d H:i:s", strtotime("-1 weeks", strtotime(date('Y-m-d'))));
                $endTime = date('Y-m-d') . ' 23:59:59';
                $where[] = ['create_time', 'between', [$starTime, $endTime]];
            }
        }

        // 时间段筛选
        if (
            isset($get['starttime'])
            && !empty($get['starttime'])
            && isset($get['endtime'])
            && !empty($get['endtime'])
        ) {
            $startTime = $get['starttime'] . ' 00:00:00';
            $endTime = $get['endtime'] . ' 23:59:59';
            $where[] = ['create_time', 'between', [$startTime, $endTime]];
        }

        // 彩种筛选 1.体彩 2.数字彩 默认|0.全部
        if (isset($get['play_type'])) {
            if ($get['play_type'] == 1) {
                // 体彩
                $where[] = ['order_type', '=', 1];
            }

            if ($get['play_type'] == 2) {
                // 数字彩
                $where[] = ['order_type', '=', 2];
            }
        }

        // 彩种筛选 1.体彩 2.数字彩 默认|0.全部
        if (isset($get['lottery_id'])) {
            $where[] = ['lottery_id', '=', $get['lottery_id']];
        }

        return $where;
    }

    /**
     * @desc 获取订单列表
     * @author LiBin
     * @param $get // 查询条件
     * @param $order // 排序规则
     * @param $number // 每页数据条数
     * @return array
     * @throws \Exception
     * @date 2019-04-09
     * @updateBy CleverStone
     */
    public function getOrderList($get, $order, $number = 20)
    {
        // 条件筛选
        $where = $this->orderFilter($get);
        // 分页Sql组装
        if (empty($get['page'])) {
            $limit = '0,' . $number;
        } else {
            $limit = ($get['page'] - 1) * $number . ',' . $number;
        }

        // 获取订单列表
        $paginate = self::where($where)
            ->field([
                'id', // 订单ID
                'beishu', // 数字彩期数/体彩倍数
                'amount', /* 金额 */
                'bonus', /* 中奖金额 */
                'bounty', /* 嘉奖金额 */
                'status', /* 订单状态 0：待出票 1：已出票 2：待开奖 3：未中奖 4：已中奖 */
                'pay_type', /* 购买方式 1 自购 2 跟单 3 推单 */
                'pay_time', /* 支付时间 */
                'sup_order_time', /*推单时间*/
                'lottery_id', // 彩种ID
                'create_time', // 创建时间
                'pay_out_commission', // 跟单付出
                'follow_order_commission', // 推单收益
                'order_no as order_id', // 订单号(注: 数字彩和体彩下单成功后统一返回订单号)
                'order_type', // 订单类型 1: 体彩  2: 数字彩
                'open_time', // 开奖时间
                'is_clear', // 是否结算
            ])
            ->order($order)
            ->limit($limit)
            ->select();
        // 总数据条数
        $paginateCount = self::where($where)->count('id');
        // 彩种模型
        $lottery = new Lottery();
        $list = [];
        foreach ($paginate as $k => $v) {
            // 获取彩种类型(接口自定义)
            $code = $lottery->getCodeById($v['lottery_id']);
            switch ($code) {
                case Config::ZC_CODE: // 足彩
                    $list[$k]['type'] = 1;
                    break;
                case Config::LC_CODE: // 篮彩
                    $list[$k]['type'] = 2;
                    break;
                case Config::BJ_CODE: // 北京单场
                    $list[$k]['type'] = 3;
                    break;
                case Config::P3_CODE: // 排三
                    $list[$k]['type'] = 4;
                    break;
                case Config::P5_CODE: // 排五
                    $list[$k]['type'] = 5;
                    break;
                case Config::PC_CODE: // 葡彩
                    $list[$k]['type'] = 6;
                    break;
                case Config::AO_CODE: // 澳彩
                    $list[$k]['type'] = 7;
                    break;
                case Config::FT_CODE: // 幸运飞艇
                    $list[$k]['type'] = 8;
                    break;
            }

            // 判断订单的支付方式
            switch ($v['pay_type']) {
                case 1: // 自购
                    // 投注金额
                    $list[$k]['amount'] = $v['amount'];
                    // 中奖金额
                    $list[$k]['bonus'] = $v['bonus'];
                    // 嘉奖彩金
                    $list[$k]['bounty'] = $v['bounty'];
                    // 创建时间
                    $list[$k]['time'] = $v['create_time'];
                    // 盈利金额
                    $list[$k]['profite'] = '0';
                    // 未中奖和已中奖状态下计算盈利金额
                    if (in_array($v['status'], [3, 4])) {
                        $list[$k]['profite'] = (string)($v['bonus'] + $v['bounty'] - $v['amount']);
                    }

                    // 期号
                    if ($v['order_type'] == 1) {
                        // 体彩期号为空
                        $list[$k]['number'] = '';
                    } else {
                        $number = '追期';
                        if ($v['beishu'] == 1) {
                            $number = OrderNum::getValByWhere(['order_id' => $v['id']], 'number');
                        }

                        $list[$k]['number'] = $number;
                    }

                    // 订单号
                    $list[$k]['order_id'] = (string)$v['order_id'];
                    break;
                case 2: // 跟单
                case 3: // 推单
                    // 预购成功时间
                    $time = [];
                    $time[] = $v['create_time'];
                    // 出票成功时间
                    $time[] = $v['pay_time'];
                    if (in_array($v['status'], [3, 4])) {
                        // 获取开奖时间
                        $time[] = $v['open_time'];
                        if ($v['pay_type'] == 3) {
                            // 推单盈利计算 奖金+嘉奖+推单收益-投注金额
                            $list[$k]['profite'] = (string)($v['bonus'] + $v['bounty'] + $v['follow_order_commission'] - $v['amount']);
                        } else {
                            // 跟单盈利计算
                            // 计算共计盈亏 奖金+嘉奖-投注金额-跟单付出佣金
                            $list[$k]['profite'] = (string)($v['bonus'] + $v['bounty'] - $v['amount'] - $v['pay_out_commission']);
                        }

                    } else {
                        // 开奖时间默认为空
                        $time[] = '';
                        $list[$k]['profite'] = '0';
                    }
                    // 时间集合
                    $list[$k]['time'] = $time;
                    // 期号
                    if ($v['order_type'] == 1) {
                        // 体彩期号为空
                        $list[$k]['number'] = '';
                    } else {
                        $number = '追期';
                        if ($v['beishu'] == 1) {
                            $number = OrderNum::getValByWhere(['order_id' => $v['id']], 'number');
                        }

                        $list[$k]['number'] = $number;
                    }

                    // 订单号
                    $list[$k]['order_id'] = (string)$v['order_id'];
                    break;
            }

            // 中奖记录列表则添加支付类型和结算标识    购买方式 1：自购 2：跟单 3: 推单  0: 未结算  1: 已结算
            if (isset($get['prizeRecord']) && !empty($get['prizeRecord'])) {
                $list[$k]['pay_type'] = $v['pay_type'];
                // 中奖纪录订单状态被替换成结算状态
                $list[$k]['status'] = $v['is_clear'];
                // 中奖纪录就一个创建时间
                $list[$k]['time'] = $v['create_time'];
                // 兼容中奖记录的推单和跟单
                // 投注金额
                $list[$k]['amount'] = $v['amount'];
                // 中奖金额
                $list[$k]['bonus'] = $v['bonus'];
                // 嘉奖彩金
                $list[$k]['bounty'] = $v['bounty'];
            } else {
                // 订单列表订单状态
                // 订单状态 0：待出票 1：已出票 2：待开奖 3：未中奖 4：已中奖
                $list[$k]['status'] = $v['status'];
            }
        }

        $list['count'] = $paginateCount;
        return $list;
    }

    /**
     * 获取推单列表
     *
     * @param $where // 查询条件
     * @param string $order // 排序
     * @return \think\Paginator
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getPushOrderList($where, $order = 'create_time DESC')
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        $where = $this->commonFilter($where);
        $where[] = ['pay_type', '=', 3]; // 推单
        $pagination = self::where($where)
            ->field([
                'id', // 订单ID
                'order_no', // 订单号
                'member_id', // 会员ID
                'lottery_id', // 彩种ID
                'bet_type', // 玩法类型
                'chuan', // 串关方式
                'beishu', // 倍数
                'create_time', // 创建时间
                'sup_order_state', // 审核状态
                'sup_order_time', // 推单时间
                'status', // 订单状态
                'follows', // 跟单人数
                'start_time', // 截止时间
                'commission_rate', // 佣金比例
                'is_moni', // 是否模拟
                'order_type', // 订单类型
            ])
            ->order($order)
            ->paginate($perPage);

        foreach ($pagination as &$v) {
            // 获取彩种
            $lotteryInfo = Lottery::quickGetOne($v['lottery_id']);
            $lotteryName = '';
            if (!empty($lotteryInfo)) {
                $lotteryName = $lotteryInfo['name'];
            }

            $v['lottery'] = $lotteryName;
            switch ($v['order_type']) {
                case 1: // 竞技彩
                    $v['code'] = 'JC'; // 竞彩
                    // 获取过关方式
                    if ((int)$v['bet_type'] === 2) {
                        $v['chuan'] = Helper::jsonDecode(str_replace('@', '串', $v['chuan']));
                    } else {
                        $v['chuan'] = ['单关'];
                    }

                    break;
                case 2: // 数字彩
                    $v['code'] = 'SZC'; // 数字彩
                    $v['chuan'] = $v['beishu']; // 期数

                    break;
                default:
                    ;
            }

            unset(
                $v['lottery_id'],
                $v['beishu']
            );
            // 获取用户名
            $v['username'] = Member::getValueByWhere(['id' => $v['member_id']], 'username');
            $v['role'] = Member::getValueByWhere(['id' => $v['member_id']], 'role');
        }

        return $pagination;
    }

    /**
     * 导出Excel
     *
     * @param $where // 条件筛选
     * @param string $order // 排序规则
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function exportPushData($where, $order = 'create_time DESC')
    {
        $where = $this->commonFilter($where);
        $where[] = ['pay_type', '=', 3]; // 推单
        $model = self::where($where)
            ->field([
                'id', // 订单ID
                'order_no', // 订单号
                'member_id', // 会员ID
                'lottery_id', // 彩种ID
                'bet_type', // 玩法类型
                'chuan', // 串关方式
                'create_time', // 创建时间
                'sup_order_state', // 审核状态
                'sup_order_time', // 推单时间
                'status', // 订单状态
                'follows', // 跟单人数
                'start_time', // 截止时间
                'commission_rate', // 佣金比例
                'is_moni', // 是否模拟
            ])
            ->order($order)
            ->select();

        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        foreach ($data as &$v) {
            // 审核状态
            $v['sup_order_state'] = self::getAuditStatusByState($v['sup_order_state']);
            // 订单状态
            $v['status'] = self::getOrderStatusByState($v['status']);
            // 是否模拟
            $v['is_moni'] = self::getIsMoniStr($v['is_moni']);
            // 获取彩种
            $lotteryInfo = Lottery::quickGetOne($v['lottery_id']);
            $lotteryName = '';
            if (!empty($lotteryInfo)) {
                $lotteryName = $lotteryInfo['name'];
            }

            $v['lottery'] = $lotteryName;
            unset($v['lottery_id']);
            // 获取用户名
            $v['username'] = Member::getValueByWhere(['id' => $v['member_id']], 'username');
            unset($v['member_id']);
            // 获取过关方式
            if ((int)$v['bet_type'] === 2) {
                $v['chuan'] = str_replace('@', '串', $v['chuan']);
            } else {
                $v['chuan'] = '单关';
            }

            // 释放
            unset($v['bet_type']);
        }

        // 导出
        Helper::exportExcel(
            'PushOrderExcel',
            [
                '订单ID', '订单号', '串关方式', '下单时间', '审核状态', '推单时间', '订单状态', '跟单人数', '跟单截止时间',
                '佣金比例', '是否模拟', '彩种名称', '用户账号',
            ],
            $data
        );
    }

    /**
     * 获取跟单列表
     *
     * @param $id // 推单ID
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getFlowList($id)
    {
        $model = self::where('follow_order_id', $id)// 推单ID
        ->field([
            'order_no', // 订单号
            'member_id', // 会员ID
            'amount', // 金额
            'pay_time', // 支付时间
            'is_moni', // 是否为模拟
            'bounty', // 嘉奖彩金(数字彩总嘉奖)
            'bonus', // 奖金(数字彩总奖金)
        ])
            ->select();

        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
            foreach ($data as &$v) {
                $v['member'] = Member::getValueByWhere(['id' => $v['member_id']], 'username');
                unset($v['member_id']);
            }
        }

        return $data;
    }

    /**
     * 推单审核
     *
     * @param $ids // 订单ID
     * @param $state // 状态 1通过  2驳回
     * @return bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function audit($ids, $state)
    {
        $state = (int)$state;
        $where[] = ['pay_type', '=', 3];
        $where[] = ['id', 'in', $ids];
        if (!in_array($state, [1, 2], true)) {
            return false;
        }

        self::where($where)->setField('sup_order_state', $state);
        return true;
    }

    /**
     * 获取注单详情
     *
     * @param $id // 订单ID
     * @return array
     * @throws \think\exception\DbException
     */
    public function getOrderDetail($id)
    {
        // order详情
        $returnData = [];
        $order = self::where('id', $id)
            ->field([
                'beizhu', // 备注
                'order_no', // 订单号
                'status', // 订单状态
                'amount', // 投注金额
                'bonus', // 中奖金额
                'bounty', // 嘉奖彩金
                'zhu', // 注数
                'beishu', // 倍数
                'pay_time', // 支付时间 格式: 'Y-m-d H:i:s'
                'lottery_id', // 彩种ID
                'bet_content', // 投注项
                'order_title', // 注单标题
                'start_amount', // 起跟金额
                'follow_order_commission', // 跟单佣金
                'bounty', // 彩金
                'is_yh', // 是否优化
            ])
            ->find();
        $orderDetail = [];
        if (!empty($order)) {
            $orderDetail = $order->toArray();
        }
        if (!empty($orderDetail)) {
            $returnData['beizhu'] = $orderDetail['beizhu']; // 备注
            $returnData['order_title'] = $orderDetail['order_title']; // 订单标题
            $returnData['order_no'] = $orderDetail['order_no']; // 订单号
            $returnData['status'] = $orderDetail['status']; // 状态
            $returnData['start_amount'] = $orderDetail['start_amount']; // 起跟金额
            $returnData['follow_order_commission'] = $orderDetail['follow_order_commission']; // 跟单佣金
            $returnData['amount'] = $orderDetail['amount']; // 投注金额
            $returnData['bonus'] = $orderDetail['bonus']; // 奖金(数字彩总奖金)
            $returnData['bounty'] = $orderDetail['bounty']; // 彩金(数字彩总嘉奖)
            $returnData['zhu'] = $orderDetail['zhu']; // 注数
            $returnData['beishu'] = $orderDetail['beishu']; // 倍数(期数)
            $returnData['pay_time'] = $orderDetail['pay_time']; // 支付时间
            // 获取彩种
            $lotteryId = $orderDetail['lottery_id'];
            $code = Lottery::getValByWhere(['id' => $lotteryId], 'code');
            $returnData['lottery_code'] = $code;
            switch ((string)$code) {
                case Config::ZC_CODE: // 竞彩足球
                    $tempBet = [];
                    // 投注项详情数据缓存
                    $betCache = [];
                    // 处理投注项
                    $betContent = Helper::jsonDecode($orderDetail['bet_content']);
                    foreach ($betContent as $item) {
                        $jczqBase = JczqBase::getFieldsByWhere(['match_num' => $item['mnum']], [
                            'jc_num',
                            'start_time',
                            'host_name',
                            'guest_name',
                            'league_name',
                        ]);
                        $jczqBase['start_time'] = date('m-d H:i', $jczqBase['start_time']);
                        $betCache[$item['mnum']] = $jczqBase;
                        // 判断投注项赛事是否已开奖
                        $drawResult = [];
                        $mutiBody = is_array($item['muti']) ? $item['muti'] : Helper::jsonDecode($item['muti']);
                        if (JczqOpen::isResult($item['mnum'])) {
                            $jczqOpen = new JczqOpen;
                            $matchDetail = $jczqOpen->getMatchDetail($item['mnum']);
                            // 获取竞彩结果
                            // 数据格式如下:
                            //       [
                            //        'spf' => '主胜', // 胜平负
                            //        'rqspf' => '平', // 让球胜平负
                            //        'jqs' => '5', // 总进球数
                            //        'bqc' => '胜胜', // 半全场胜平负
                            //        'bf' => '1-3', // 全场比分
                            //       ]
                            $jcResult = $this->computedJcResult(Config::ZC_CODE, $matchDetail);
                            // 投注内容
                            // $drawResult => 数据格式如下:
                            // ['[胜]', ['[胜平]'], ...]
                            $drawResult = array_reduce($mutiBody, function ($result, $item) use ($jcResult) {
                                array_push($result, '[' . $jcResult[$item['ptype']] . ']');
                                return $result;
                            }, []);
                        }

                        $jczqBase['draw_result'] = $drawResult;
                        //$jczqBase['single'] = $item['single']; // 胆, 这个需要前端后期加上去
                        $jczqBase['single'] = 0; // 胆, 这个需要前端后期加上去
                        $jczqBase['match_num'] = $item['mnum'];
                        // $betDataGather => 数据格式如下:
                        // [['bet_item' => '胜[1.42]/平[1.36]'], ['bet_item' => '平胜[1.1]/1-1[1.36]'], ...]
                        $betDataGather = array_reduce($mutiBody, function ($result, $item) {
                            $betItem = $this->jzTouZhuGroup($item);
                            array_push($result, $betItem);
                            return $result;
                        }, []);

                        $jczqBase['bet_body'] = $betDataGather;
                        array_push($tempBet, $jczqBase);
                    }

                    $returnData['bet_content'] = $tempBet;
                    // order_content
                    $orderContent = OrderContent::where('order_id', $id)
                        ->field([
                            'content',
                            'status',
                            'chuan',
                            'bonus',
                            'beishu',
                        ])
                        ->select();
                    $orderContentDetail = [];
                    if (!empty($orderContent)) {
                        $orderContentDetail = $orderContent->toArray();
                    }

                    $tempBetDetail = [];
                    foreach ($orderContentDetail as $item) {
                        $touZhuBody = Helper::jsonDecode($item['content']);
                        $innerBetDetail = [];
                        foreach ($touZhuBody as $sonItem) {
                            $touZhuOne = [];
                            if (isset($betCache[$sonItem['mnum']])) {
                                $touZhuOne = $betCache[$sonItem['mnum']];
                            }

                            $newSonItem = array_merge($touZhuOne, $this->jzTouZhuGroup($sonItem));
                            array_push($innerBetDetail, $newSonItem);
                        }

                        $chuan = str_replace('@', '串', $item['chuan']);
                        // 判断是否奖金优化
                        if ($orderDetail['is_yh'] === 1) {
                            $bei = $item['beishu'];
                        } else {
                            $bei = $orderDetail['beishu'];
                        }

                        array_push($tempBetDetail, [
                            'list' => $innerBetDetail,
                            'extra' => [
                                'chuan' => $chuan,
                                'bei' => $bei,
                                'status' => $item['status'],
                                'bonus' => $item['bonus'],
                            ]
                        ]);
                    }

                    $returnData['bet_detail'] = $tempBetDetail;

                    break;
                case Config::LC_CODE: // 竞彩篮球
                    $tempBet = [];
                    // 投注项详情数据缓存
                    $betCache = [];
                    // 处理投注项
                    $betContent = Helper::jsonDecode($orderDetail['bet_content']);
                    foreach ($betContent as $item) {
                        $jclqBase = JclqBase::getFieldsByWhere(['match_num' => $item['mnum']], [
                            'jc_num',
                            'start_time',
                            'host_name',
                            'guest_name',
                            'league_name',
                        ]);
                        $jclqBase['start_time'] = date('m-d H:i', $jclqBase['start_time']);
                        $betCache[$item['mnum']] = $jclqBase;
                        // 判断投注项赛事是否已开奖
                        $drawResult = [];
                        $mutiBody = is_array($item['muti']) ? $item['muti'] : Helper::jsonDecode($item['muti']);
                        if (JclqOpen::isResult($item['mnum'])) {
                            $jclqOpen = new JclqOpen;
                            $matchDetail = $jclqOpen->getMatchDetail($item['mnum']);
                            // 获取竞彩结果
                            // 数据格式如下:
                            //[
                            //    'sf' => '-', // 胜负
                            //    'rfsf' => '-', // 让分胜负
                            //    'dxf' => '-', // 大小分
                            //    'zsfc'=> '-', // 主胜分差
                            //    'ksfc'=> '-', // 客胜分差
                            //];
                            $jcResult = $this->computedJcResult(Config::LC_CODE, $matchDetail);
                            // 投注内容
                            // $drawResult => 数据格式如下:
                            // ['[胜]', ['[胜平]'], ...]
                            $drawResult = array_reduce($mutiBody, function ($result, $item) use ($jcResult) {
                                array_push($result, '[' . $jcResult[$item['ptype']] . ']');
                                return $result;
                            }, []);
                        }

                        $jclqBase['draw_result'] = $drawResult;
                        //$jclqBase['single'] = $item['single']; // 胆, 这个需要前端后期加上去
                        $jclqBase['single'] = 0; // 胆, 这个需要前端后期加上去
                        $jclqBase['match_num'] = $item['mnum'];
                        // $betDataGather => 数据格式如下:
                        // [['bet_item' => '胜[1.42]/平[1.36]'], ['bet_item' => '平胜[1.1]/1-1[1.36]'], ...]
                        $betDataGather = array_reduce($mutiBody, function ($result, $item) {
                            $betItem = $this->jlTouZhuGroup($item);
                            array_push($result, $betItem);
                            return $result;
                        }, []);
                        $jclqBase['bet_body'] = $betDataGather;
                        array_push($tempBet, $jclqBase);
                    }

                    $returnData['bet_content'] = $tempBet;
                    // order_content
                    $orderContent = OrderContent::where('order_id', $id)
                        ->field([
                            'content',
                            'status',
                            'chuan',
                            'bonus',
                            'beishu',
                        ])
                        ->select();
                    $orderContentDetail = [];
                    if (!empty($orderContent)) {
                        $orderContentDetail = $orderContent->toArray();
                    }

                    $tempBetDetail = [];
                    foreach ($orderContentDetail as $item) {
                        $touZhuBody = Helper::jsonDecode($item['content']);
                        $innerBetDetail = [];
                        foreach ($touZhuBody as $sonItem) {
                            $touZhuOne = [];
                            if (isset($betCache[$sonItem['mnum']])) {
                                $touZhuOne = $betCache[$sonItem['mnum']];
                            }

                            $newSonItem = array_merge($touZhuOne, $this->jlTouZhuGroup($sonItem));
                            array_push($innerBetDetail, $newSonItem);
                        }

                        $chuan = str_replace('@', '串', $item['chuan']);
                        // 判断是否奖金优化
                        if ($orderDetail['is_yh'] === 1) {
                            $bei = $item['beishu'];
                        } else {
                            $bei = $orderDetail['beishu'];
                        }

                        array_push($tempBetDetail, [
                            'list' => $innerBetDetail,
                            'extra' => [
                                'chuan' => $chuan,
                                'bei' => $bei,
                                'status' => $item['status'],
                                'bonus' => $item['bonus'],
                            ]
                        ]);
                    }

                    $returnData['bet_detail'] = $tempBetDetail;

                    break;
                case Config::BJ_CODE:
                    $tempBet = [];
                    // 投注项详情数据缓存
                    $betCache = [];
                    // 处理投注项
                    $betContent = Helper::jsonDecode($orderDetail['bet_content']);
                    foreach ($betContent as $item) {
                        $jcdcBase = JcdcBase::getFieldsByWhere(['match_num' => $item['mnum']], [
                            'jc_num',
                            'start_time',
                            'host_name',
                            'guest_name',
                            'league_name',
                        ]);
                        $jcdcBase['start_time'] = date('m-d H:i', $jcdcBase['start_time']);
                        $betCache[$item['mnum']] = $jcdcBase;
                        // 判断投注项赛事是否已开奖
                        $drawResult = [];
                        $mutiBody = is_array($item['muti']) ? $item['muti'] : Helper::jsonDecode($item['muti']);
                        if (JcdcOpen::isResult($item['mnum'])) {
                            $jcdcOpen = new JcdcOpen;
                            $matchDetail = $jcdcOpen->getMatchDetail($item['mnum']);
                            // 获取竞彩结果
                            // 数据格式如下:
                            //       [
                            //        'spf' => '主胜', // 胜平负
                            //        'rqspf' => '平', // 让球胜平负
                            //        'jqs' => '5', // 总进球数
                            //        'bqc' => '胜胜', // 半全场胜平负
                            //        'bf' => '1-3', // 全场比分
                            //       ]
                            $jcResult = $this->computedJcResult(Config::BJ_CODE, $matchDetail);
                            // 投注内容
                            // $drawResult => 数据格式如下:
                            // ['[胜]', ['[胜平]'], ...]
                            $drawResult = array_reduce($mutiBody, function ($result, $item) use ($jcResult) {
                                array_push($result, '[' . $jcResult[$item['ptype']] . ']');
                                return $result;
                            }, []);
                        }

                        $jcdcBase['draw_result'] = $drawResult;
                        $jcdcBase['single'] = 0; // 胆, 这个需要前端后期加上去
                        $jcdcBase['match_num'] = $item['mnum'];
                        // $betDataGather => 数据格式如下:
                        // [['bet_item' => '胜[1.42]/平[1.36]'], ['bet_item' => '平胜[1.1]/1-1[1.36]'], ...]
                        $betDataGather = array_reduce($mutiBody, function ($result, $item) {
                            $betItem = $this->jzTouZhuGroup($item);
                            array_push($result, $betItem);
                            return $result;
                        }, []);

                        $jcdcBase['bet_body'] = $betDataGather;
                        array_push($tempBet, $jcdcBase);
                    }

                    $returnData['bet_content'] = $tempBet;
                    // order_content
                    $orderContent = OrderContent::where('order_id', $id)
                        ->field([
                            'content',
                            'status',
                            'chuan',
                            'bonus',
                            'beishu',
                        ])
                        ->select();
                    $orderContentDetail = [];
                    if (!empty($orderContent)) {
                        $orderContentDetail = $orderContent->toArray();
                    }

                    $tempBetDetail = [];
                    foreach ($orderContentDetail as $item) {
                        $touZhuBody = Helper::jsonDecode($item['content']);
                        $innerBetDetail = [];
                        foreach ($touZhuBody as $sonItem) {
                            $touZhuOne = [];
                            if (isset($betCache[$sonItem['mnum']])) {
                                $touZhuOne = $betCache[$sonItem['mnum']];
                            }

                            $newSonItem = array_merge($touZhuOne, $this->jzTouZhuGroup($sonItem));
                            array_push($innerBetDetail, $newSonItem);
                        }

                        $chuan = str_replace('@', '串', $item['chuan']);
                        // 判断是否奖金优化
                        if ($orderDetail['is_yh'] === 1) {
                            $bei = $item['beishu'];
                        } else {
                            $bei = $orderDetail['beishu'];
                        }

                        array_push($tempBetDetail, [
                            'list' => $innerBetDetail,
                            'extra' => [
                                'chuan' => $chuan,
                                'bei' => $bei,
                                'status' => $item['status'],
                                'bonus' => $item['bonus'],
                            ]
                        ]);
                    }

                    $returnData['bet_detail'] = $tempBetDetail;

                    break;
                case Config::P3_CODE: // 排列三
                case Config::P5_CODE: // 排列五
                case Config::AO_CODE: // 澳彩
                case Config::PC_CODE: // 葡彩
                    // 获取数字彩类型
                    switch ((string)$code) {
                        case Config::P3_CODE: // 排列三
                            $numType = 1;
                            break;
                        case Config::P5_CODE: // 排五
                            $numType = 2;
                            break;
                        case Config::AO_CODE: // 澳彩
                            $numType = 3;
                            break;
                        case Config::PC_CODE: // 葡彩
                            $numType = 4;
                            break;
                        default:
                            trigger_error('未知的彩种类型');
                    }
                    // 是否追号
                    $returnData['is_yh'] = $orderDetail['is_yh'];
                    $orderNumModel = OrderNum::where('order_id', $id)
                        ->field([
                            'number', // 期号
                            'multiple', // 倍数
                            'amount', // 单期金额
                            'bonus', // 单期奖金
                            'bounty', // 单期嘉奖奖金
                            'status', // 状态
                        ])
                        ->select();
                    $orderNumData = [];
                    if (!empty($orderNumModel)) {
                        $orderNumData = $orderNumModel->toArray();
                    }

                    $plOpen = new PlOpen;
                    $newestNum = $plOpen->getNumber($numType);
                    foreach ($orderNumData as &$item) {
                        $item['is_newest'] = 0;
                        if ($newestNum == $item['number']) {
                            // 标记当前期
                            $item['is_newest'] = 1;
                        }

                        // 获取每期的开奖结果
                        $oneDrawCode = PlOpen::getValByWhere(['expect' => $item['number'], 'ctype' => $numType], 'open_code');
                        $oneDrawCodeArr = [];
                        if (!empty($oneDrawCode)) {
                            $oneDrawCodeArr = explode(',', $oneDrawCode);
                        }

                        $item['open_result'] = $oneDrawCodeArr;
                    }
                    // 追号列表详情
                    $returnData['number_detail'] = $orderNumData;
                    // 处理投注项
                    $betContent = Helper::jsonDecode($orderDetail['bet_content']);
                    $dealContent = array_reduce($betContent, function ($result, $item) {
                        $tempArr = [];
                        $tempArr['play'] = $this->getPlayStrByCode($item['play']); // 玩法
                        $tempArr['zhu'] = $item['zhu']; // 注数
                        $tempArr['amount'] = $item['amount']; // 投注金额
                        $tempArr['bet'] = [];
                        foreach ($item['bet'] as $v) {
                            array_push($tempArr['bet'], explode(',', $v));
                        }

                        array_push($result, $tempArr);
                        return $result;
                    }, []);
                    $returnData['bet_list'] = $dealContent;

                    break;
                case Config::FT_CODE: // 幸运飞艇
                    $returnData['is_yh'] = 0;
                    $orderNumData = OrderNum::where('order_id', $id)
                        ->field([
                            'number', // 期号
                            'multiple', // 倍数
                            'amount', // 单期金额
                            'bonus', // 单期奖金
                            'bounty', // 单期嘉奖奖金
                            'status', // 状态
                        ])
                        ->select()
                        ->toArray();
                    $plOpen = new PlOpen;
                    $newestNum = $plOpen->getNumber(5); // 幸运飞艇
                    foreach ($orderNumData as &$item) {
                        $item['is_newest'] = 0;
                        if ($newestNum == $item['number']) {
                            // 标记当前期
                            $item['is_newest'] = 1;
                        }

                        // 获取每期的开奖结果
                        $oneDrawCode = PlOpen::getValByWhere(['expect' => $item['number'], 'ctype' => 5], 'open_code');
                        $oneDrawCodeArr = [];
                        if (!empty($oneDrawCode)) {
                            $oneDrawCodeArr = explode(',', $oneDrawCode);
                        }

                        $item['open_result'] = $oneDrawCodeArr;
                    }
                    // 追号列表详情
                    $returnData['number_detail'] = $orderNumData;
                    // 处理投注项
                    $betContent = Helper::jsonDecode($orderDetail['bet_content']);
                    $dealContent = array_reduce($betContent, function ($result, $item) {
                        $tempArr = [];
                        $tempArr['play'] = $this->getPlayStrByCode($item['play']); // 玩法
                        $tempArr['bet'] = []; // 投注详情
                        $tempArr['zhu'] = 0; // 注数
                        $tempArr['amount'] = 0; // 投注金额
                        foreach ($item['bet'] as $v) {
                            $type = $this->convertStr($v['type']); // 类型
                            array_walk($v['value'], function ($ele) use (&$tempArr, $type) {
                                $tempArr['zhu']++; // 注数
                                list($b, $m, $i) = explode('|', $ele);
                                $tempArr['amount'] += $m; // 总金额
                                $b = $this->convertStr($b);
                                $betEle = $type . " {$b}" . " {$m}(元)" . " {$i}(赔率)";
                                array_push($tempArr['bet'], $betEle);
                            });
                        }

                        array_push($result, $tempArr);
                        return $result;
                    }, []);
                    $returnData['bet_list'] = $dealContent;

                    break;
                default:

                    return [];
            }
        }

        return $returnData;
    }

    // 下单成功后的订单编号
    public $orderNum = null;

    /**
     * 体彩下单
     *
     * @param $post // 表单数据
     * @return bool|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function insertOrder($post)
    {
        try {
            Db::startTrans();
            // 订单下单数据容器
            $data = [];
            // 下注金额
            $betAmount = round($post['amount'], 2);
            // 获取会员彩金和余额(加排他锁)
            $memberData = Member::where('id', UID)
                ->field([
                    'balance', // 余额
                    'hadsel', // 彩金
                    'role', // 角色
                    'username', // 用户名
                    'chn_name', // 昵称
                    'photo', // 头像|头标
                ])
                ->lock(true)
                ->find()
                ->toArray();
            $hadsel = floatval($memberData['hadsel']);
            $balance = floatval($memberData['balance']);
            // 判断彩金是否大于下注金额
            if ($hadsel >= $betAmount) {
                // 减去下注金额
                $newHadsel = $hadsel - $betAmount;
                // 预支付彩金写入冻结资金
                Member::where('id', UID)->setInc('frozen_capital', $betAmount);
                // 保存支付后的彩金金额
                Member::where('id', UID)->setField([
                    'hadsel' => $newHadsel // 新彩金
                ]);
                // 增加订单彩金字段
                $data['pay_hadsel'] = $betAmount;

            } elseif ($balance + $hadsel >= $betAmount) {  // 判断余额加彩金是否大于等于下注金额
                // 先扣完彩金, 再扣余额, 并计算剩余应支付金额
                $surplusBetAmount = $betAmount - $hadsel;
                // 扣住余额, 获取剩余余额
                $surplusBalance = $balance - $surplusBetAmount;
                // 预支付彩金和余额写入冻结资金
                Member::where('id', UID)->setInc('frozen_capital', $betAmount);
                // 保存支付后的彩金和余额
                Member::where('id', UID)->setField([
                    'hadsel' => 0, // 彩金已扣完, 剩余0,
                    'balance' => $surplusBalance // 保存剩余余额
                ]);

                // 增加订单彩金字段
                $data['pay_hadsel'] = $hadsel;
                // 增加订单余额字段
                $data['pay_balance'] = $surplusBetAmount;
            } else {
                // 提示余额不足
                trigger_error('您的余额不足', E_USER_WARNING);
            }

            if (is_array($post['bet_content'])) { // 投注项
                $matchNum = array_column($post['bet_content'], 'mnum');
                $betContentBody = Helper::jsonEncode($post['bet_content']);
            } else {
                $betContentBody = $post['bet_content'];
                $matchNum = array_column(Helper::jsonDecode($post['bet_content']), 'mnum');
            }

            // 投注项所有赛事
            if (empty($matchNum)) {
                trigger_error('投注项赛事编号不能为空', E_USER_WARNING);
            }

            //获取投注项的赛事类型
            $code = Lottery::getValByWhere(['id' => $post['lottery_id']], 'code');
            // 所有赛事中最小停售时间
            $shutoffSaleTimeGather = [];
            foreach ($matchNum as $mnum) {
                switch ($code) {
                    case Config::ZC_CODE://足彩
                        $shutOffSaleDate = JczqBase::getShutDownTimeByMatchNum($mnum);
                        break;
                    case Config::LC_CODE://篮彩
                        $shutOffSaleDate = JclqBase::getShutDownTimeByMatchNum($mnum);
                        break;
                    case Config::BJ_CODE://北单
                        $shutOffSaleDate = JcdcBase::getShutDownTimeByMatchNum($mnum);
                        break;
                    default:
                        trigger_error('投注项赛事类型不能为空', E_USER_WARNING);

                }
                array_push($shutoffSaleTimeGather, $shutOffSaleDate);
            }

            sort($shutoffSaleTimeGather);
            reset($shutoffSaleTimeGather);
            // 最小停售日期
            $minShutOffStopDate = current($shutoffSaleTimeGather);
            // 当前日期
            $curData = Helper::timeFormat(time(), 's');
            if ($minShutOffStopDate <= $curData) {
                return '投注项存在已停售赛事, 请您选择正常的赛事进行投注';
            }

            $data['username'] = Member::getValByWhere(['id' => UID], 'username');
            $data['pay_status'] = 0; // 支付状态, 支付中
            $data['order_no'] = $this->orderNum = Helper::orderNumber(); // 订单号
            $data['member_id'] = UID; // 会员ID
            $data['lottery_id'] = $post['lottery_id']; // 彩种ID
            $data['beishu'] = $post['beishu']; // 倍数
            $data['zhu'] = $post['zhu']; // 注
            $data['amount'] = $betAmount; // 下注金额
            $data['bet_type'] = $post['bet_type']; // 玩法类型 1:单关  2:过关
            $data['chuan'] = is_array($post['chuan']) ? Helper::jsonEncode($post['chuan']) : $post['chuan']; // 串关信息
            $data['status'] = 0; // 待出票
            $data['beizhu'] = $post['beizhu']; // 备注
            $data['bet_content'] = $betContentBody; // 投注项
            $data['create_time'] = Helper::timeFormat(time(), 's'); // 下单时间
            $data['is_yh'] = $post['is_yh']; // 是否优化
            $isMoni = Member::getValByWhere(['id' => UID], 'is_moni');
            $data['is_moni'] = $isMoni === 0 ? 1 : 0; // 是否是模拟注单
            $data['pay_type'] = isset($post['pay_type']) ? $post['pay_type'] : 1; // 订单类型

            // 写入order表
            $orderId = self::quickCreate($data);
            if (!$orderId) {
                trigger_error('下单失败, 错误代码493', E_USER_ERROR);
            }

            $orderContentData = is_array($post['order_content']) ? $post['order_content'] : Helper::jsonDecode($post['order_content']);
            foreach ($orderContentData as $content) {
                $dataTwo = [];
                $dataTwo['order_id'] = $orderId; // 订单ID
                $dataTwo['lottery_id'] = $post['lottery_id']; // 彩种ID
                $dataTwo['chuan'] = $content['chuan']; // 串关信息
                $dataTwo['content'] = is_array($content['content']) ? Helper::jsonEncode($content['content']) : $content['content']; // 投注内容
                $dataTwo['beishu'] = isset($content['beishu']) ? $content['beishu'] : 0; // 优化倍数
                $dataTwo['create_at'] = Helper::timeFormat(time(), 's'); // 下单时间
                // 写入order_content表
                $orderContent = new OrderContent;
                $orderContentId = $orderContent->saveOrderContent($dataTwo);
                if (!$orderContentId) {
                    trigger_error('下单失败, 错误代码510', E_USER_ERROR);
                }

                $betContent = is_array($content['content']) ? $content['content'] : Helper::jsonDecode($content['content']);
                foreach ($betContent as $detail) {
                    $dataThree = [];
                    $dataThree['order_content_id'] = $orderContentId; // 订单内容ID
                    $dataThree['lottery_id'] = $post['lottery_id']; // 彩种ID
                    $dataThree['match_num'] = $detail['mnum']; // 赛事编号
                    $dataThree['play_type'] = $detail['ptype']; // 玩法
                    $detailBet = $detail['bet'] . '|' . $detail['i'];
                    switch ($code) {
                        case Config::ZC_CODE://足彩
                        case Config::BJ_CODE://北单
                            if (!strcasecmp($detail['ptype'], 'rqspf')) {
                                $detailBet = $detail['bet'] . '|' . $detail['i'] . ':' . $detail['rqs'];
                            }
                            break;
                        case Config::LC_CODE://篮彩
                            if (!strcasecmp($detail['ptype'], 'rfsf')) {
                                $detailBet = $detail['bet'] . '|' . $detail['i'] . ':' . $detail['rfs'];
                            }
                            break;
                        default:
                            trigger_error('下单失败, 订单异常', E_USER_ERROR);
                    }
                    $dataThree['bet'] = $detailBet; // 投注内容 W|1.28:-1
                    $dataThree['create_time'] = Helper::timeFormat(time(), 's'); // 下单时间
                    $dataThree['order_id'] = $orderId; // 订单ID
                    $dataThree['odds'] = $detail['i']; // 赔率

                    // 写入订单详情表
                    $orderDetail = new OrderDetail;
                    $orderDetailId = $orderDetail->saveOrderDetail($dataThree);
                    if (!$orderDetailId) {
                        trigger_error('下单失败, 错误代码503', E_USER_ERROR);
                    }
                }
            }

            // 写入并推送消息
            Helper::logAndPushMsg(
                "用户{$memberData['username']}注单成功, 注单单号: {$this->orderNum}", // 消息内容
                $memberData['chn_name'], // 昵称
                $memberData['username'], // 用户账号
                ($memberData['role'] === 1) ? 3 : 2, // 消息类型
                2, // 内容类型
                $memberData['photo'] ?: 0 // 用户头像或头标
            );

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 体彩推单
     *
     * @param $post
     * @throws \Exception
     * @return true|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function pushOrder($post)
    {
        $orderNum = $post['order_num']; // 订单号
        $data['start_amount'] = $post['start_amount']; // 起跟金额
        $data['commission_rate'] = $post['commission_rate'] * 100; // 佣金比例
        $data['order_title'] = $post['order_title']; // 订单标题
        $data['start_time'] = $post['start_time']; // 跟单截止时间
        if ($data['commission_rate'] > 100) {
            return '佣金比例不能大于百分百';
        }
        // 获取该订单所有赛事和彩种ID
        //$betContent = self::getValByWhere(['order_no' => $orderNum], 'bet_content');
        $orderContent = self::where(['order_no' => $orderNum])->field('bet_content,lottery_id,amount')->find();
        if ($orderContent['amount'] < 500) {
            return '订单金额大于500才能推单';
        }

        $betContentArr = Helper::jsonDecode($orderContent['bet_content']);
        //获取投注项的赛事类型
        $code = Lottery::getValByWhere(['id' => $orderContent['lottery_id']], 'code');
        $matchGather = array_filter(array_unique(array_column($betContentArr, 'mnum')));
        $shutOffTimeGather = [];
        // 获取当前注单投注赛事的最小截止时间
        foreach ($matchGather as $matchNum) {
            switch ($code) {
                case Config::ZC_CODE://足彩
                    $shutOffDate = JczqBase::getShutDownTimeByMatchNum($matchNum);
                    break;
                case Config::LC_CODE://篮彩
                    $shutOffDate = JclqBase::getShutDownTimeByMatchNum($matchNum);
                    break;
                case Config::BJ_CODE://北单
                    $shutOffDate = JcdcBase::getShutDownTimeByMatchNum($matchNum);
                    break;
                default:
                    return '投注项赛事类型不能为空';
            }

            array_push($shutOffTimeGather, $shutOffDate);
        }

        sort($shutOffTimeGather);
        reset($shutOffTimeGather);
        $minShutOffDate = current($shutOffTimeGather);

        // 如果跟单截止时间大于等于赛事截止时间, 则提示错误信息
        if ($data['start_time'] >= $minShutOffDate) {
            return '跟单截止时间不能大于赛事截止时间';
        }

        $data['sup_order_state'] = 0; // 审核状态, 0待审核 1已通过 2已驳回
        $data['pay_type'] = 3; // 推单
        $data['sup_order_time'] = Helper::timeFormat(time(), 's'); // 推单时间

        $rows = self::where('order_no', $orderNum)->update($data); // 设置该订单类型为推单

        if ($rows) {
            // 获取用户详情
            $memberData = Member::getFieldsByWhere(['id' => UID], [
                'role', // 角色
                'username', // 用户名
                'chn_name', // 昵称
                'photo', // 头像|头标
            ]);
            // 写入并推送消息
            Helper::logAndPushMsg(
                "用户{$memberData['username']}发起推单申请, 注单单号: {$orderNum}", // 消息内容
                $memberData['chn_name'], // 昵称
                $memberData['username'], // 用户账号
                ($memberData['role'] === 1) ? 3 : 2, // 消息类型
                2, // 内容类型
                $memberData['photo'] ?: 0 // 用户头像或头标
            );

            return true;
        }

        return '推单失败';
    }

    /**
     * 排三排五推单
     *
     * @param $post // 推单表单数据
     * @return true|string
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function nlpushOrder($post)
    {
        $orderNum = $post['order_num']; // 订单号
        $data['start_amount'] = $post['start_amount']; // 起跟金额
        $data['commission_rate'] = $post['commission_rate'] * 100; // 佣金比例
        $data['order_title'] = $post['order_title']; // 订单标题
        $data['start_time'] = date('Y-m-d') . ' 20:20:00'; // 跟单截止时间
        if ($data['commission_rate'] > 100) {
            return '佣金比例不能大于百分百';
        }

        // 获取该订单所有赛事和彩种ID
        $orderContent = self::where(['order_no' => $orderNum])->field('id,amount')->find();
        if ($orderContent['amount'] < 500) {
            return '订单金额大于500才能推单';
        }

        //获取订单详情
        $orderDetail = OrderNum::where(['order_id' => $orderContent['id']])->order('number ASC')->find();
        $check = PlOpen::checkSaleStatus($orderDetail['number'], 1);
        if (empty($check)) {
            return '该彩期已停售无法推单';
        }

        if ($orderDetail['is_push'] == 1) {
            return '该订单已推过单,无法重复推单';
        }

        $data['sup_order_state'] = 0; // 审核状态, 0待审核 1已通过 2已驳回
        $data['pay_type'] = 3; // 推单
        $data['sup_order_time'] = Helper::timeFormat(time(), 's'); // 推单时间
        try {
            Db::startTrans();
            $rows = self::where('order_no', $orderNum)->update($data); // 设置该订单类型为推单
            //更新订单详情为推单状态
            OrderNum::where(['number' => $orderDetail['number']])->setField(['is_push' => 1]);
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }

        Db::commit();
        if ($rows) {
            // 获取用户详情
            $memberData = Member::getFieldsByWhere(['id' => UID], [
                'role', // 角色
                'username', // 用户名
                'chn_name', // 昵称
                'photo', // 头像|头标
            ]);
            // 写入并推送消息
            Helper::logAndPushMsg(
                "用户{$memberData['username']}发起推单申请, 注单单号: {$orderNum}", // 消息内容
                $memberData['chn_name'], // 昵称
                $memberData['username'], // 用户账号
                ($memberData['role'] === 1) ? 3 : 2, // 消息类型
                2, // 内容类型
                $memberData['photo'] ?: 0 // 用户头像或头标
            );

            return true;
        }

        return '推单失败';
    }

    /**
     * @desc 体彩跟单
     * @author LiBin
     * @param $post
     * @return bool|string
     * @date 2019-04-12
     * @api *
     */
    public function tailOrder($post)
    {
        try {
            Db::startTrans();
            // 前端传输的数据:amount 跟单金额 beishu 跟单倍数 order_id 订单ID zhu 注数
            // 获取跟单的订单信息 需要过滤订单是否是推单订单
            $orderData = self::getOrder(['id' => $post['order_id'], 'pay_type' => 3]);
            if (empty($orderData)) {//判断订单是否存在
                trigger_error('订单不存在', E_USER_WARNING);
            }

            $orderData = $orderData->toArray();
            if (in_array($orderData['status'], [3, 4])) {
                trigger_error('该单已过期');
            }

            //判断订单金额是存在异常
            //计算推单订单的单倍金额
            $oneAmount = $orderData['amount'] / $orderData['beishu'];
            $beishu = $post['amount'] / $oneAmount;//计算出跟单的金额是推单的多少倍.
            if (!is_int($beishu)) {
                trigger_error('金额异常', E_USER_WARNING);
            }

            //判断跟单截止日期
            if ($orderData['start_time'] < date('Y-m-d H:i:s')) {
                trigger_error('跟单已截止', E_USER_WARNING);
            }

            //判断起跟金额
            if ($orderData['start_amount'] > $post['amount']) {
                trigger_error('跟单金额必须大于起跟金额', E_USER_WARNING);
            }

            // 订单跟单数据容器
            $data = [];
            // 跟单金额
            $betAmount = round($post['amount'], 2);
            // 获取会员彩金和余额(加排他锁)
            $memberData = Member::where('id', UID)
                ->field([
                    'is_moni', // 是否模拟
                    'balance', // 余额
                    'hadsel', // 彩金
                    'role', // 角色
                    'username', // 用户名
                    'chn_name', // 昵称
                    'photo', // 头像|头标
                ])
                ->lock(true)
                ->find()
                ->toArray();
            if (empty($memberData['is_moni'])) {//判断模拟账户
                trigger_error('模拟账户不能跟单', E_USER_ERROR);
            }

            $hadsel = floatval($memberData['hadsel']);
            $balance = floatval($memberData['balance']);
            // 判断彩金是否大于下注金额
            if ($hadsel >= $betAmount) {
                // 减去下注金额
                $newHadsel = $hadsel - $betAmount;
                // 预支付彩金写入冻结资金
                Member::where('id', UID)->setInc('frozen_capital', $betAmount);
                // 保存支付后的彩金金额
                Member::where('id', UID)->setField([
                    'hadsel' => $newHadsel // 新彩金
                ]);
                // 增加订单彩金字段
                $data['pay_hadsel'] = $betAmount;
                // 增加订单余额字段
                $data['pay_balance'] = 0;
            } elseif ($balance + $hadsel >= $betAmount) {  // 判断余额加彩金是否大于等于下注金额
                // 先扣完彩金, 再扣余额, 并计算剩余应支付金额
                $surplusBetAmount = $betAmount - $hadsel;
                // 扣住余额, 获取剩余余额
                $surplusBalance = $balance - $surplusBetAmount;
                // 预支付彩金和余额写入冻结资金
                Member::where('id', UID)->setInc('frozen_capital', $betAmount);
                // 保存支付后的彩金和余额
                Member::where('id', UID)->setField([
                    'hadsel' => 0, // 彩金已扣完, 剩余0,
                    'balance' => $surplusBalance // 保存剩余余额
                ]);

                // 增加订单彩金字段
                $data['pay_hadsel'] = $hadsel;
                // 增加订单余额字段
                $data['pay_balance'] = $surplusBetAmount;
            } else {
                // 提示余额不足
                trigger_error('您的余额不足', E_USER_WARNING);
            }

            //获取投注项的赛事类型
            $code = Lottery::getValByWhere(['id' => $orderData['lottery_id']], 'code');
            switch ($code) {
                case Config::ZC_CODE: //足彩
                    //处理投注项
                    $jczqMatch = new JczqMatch();
                    $dataContent = $jczqMatch->handingBetas($orderData['bet_content']);
                    $betContentBody = $dataContent['bet_content'];//跟单订单的投注项
                    $gameIndex = $dataContent['gameIndex'];//处理的最新奖金指数集合.
                    break;
                case Config::LC_CODE: //篮彩
                    //处理投注项
                    $jclqMatch = new JclqMatch();
                    $dataContent = $jclqMatch->handingBetas($orderData['bet_content']);
                    $betContentBody = $dataContent['bet_content'];//跟单订单的投注项
                    $gameIndex = $dataContent['gameIndex'];//处理的最新奖金指数集合.
                    break;
                case Config::BJ_CODE: //北单
                    //处理投注项
                    $jcdcMatch = new JcdcMatch();
                    $dataContent = $jcdcMatch->handingBetas($orderData['bet_content']);
                    $betContentBody = $dataContent['bet_content'];//跟单订单的投注项
                    $gameIndex = $dataContent['gameIndex'];//处理的最新奖金指数集合.
                    break;
                default:
                    trigger_error('跟单投注项异常');
            }

            $orderData['username'] = Member::getValByWhere(['id' => UID], 'username');
            $orderData['order_no'] = $this->orderNum = Helper::orderNumber(); // 订单号
            $orderData['member_id'] = UID; // 会员ID
            $orderData['beishu'] = $beishu; // 倍数
            $orderData['amount'] = $betAmount; // 下注金额
            $orderData['beizhu'] = ''; // 备注
            $orderData['bet_content'] = Helper::jsonEncode($betContentBody); // 投注项
            $orderData['create_time'] = Helper::timeFormat(time(), 's'); // 下单时间
            $orderData['follow_order_id'] = $orderData['id'];//跟单的订单ID
            $orderData['pay_type'] = 2; // 订单类型  1：自购 2：跟单 3: 推单
            $orderData['pay_status'] = 0; // 支付中
            $orderData['status'] = 0; // 待出票
            $orderData['is_moni'] = 0;  // 是否是模拟 0否 1是
            if (!empty($data['pay_hadsel'])) {//彩金支付
                $orderData['pay_hadsel'] = $data['pay_hadsel'];
            } else {
                $orderData['pay_hadsel'] = 0;
            }

            if (!empty($data['pay_balance'])) {//余额支付
                $orderData['pay_balance'] = $data['pay_balance'];
            } else {
                $orderData['pay_balance'] = 0;
            }

            unset(
                $orderData['id'],//订单ID
                $orderData['start_amount'],//起跟金额
                $orderData['commission_rate'],//佣金比例
                $orderData['order_title'],//订单标题
                $orderData['start_time'],// 跟单截止时间
                $orderData['follows'],//跟单人数
                $orderData['pay_time'], // 支付时间
                $orderData['sup_order_state'], // 审核状态
                $orderData['sup_order_time'] // 推单时间
            );

            // 写入order表
            $orderId = self::quickCreate($orderData);
            if (!$orderId) {
                trigger_error('下单失败, 错误代码493', E_USER_ERROR);
            }

            $orderContent = new OrderContent();
            $orderContentData = $orderContent->getOrderContent(['order_id' => $post['order_id']]);//获取跟单的订单内容数据
            $orderContentData = $orderContentData->toArray();
            foreach ($orderContentData as $k => $v) {
                $orderContentData[$k]['order_id'] = $orderId;
                //处理投注内容
                $contentData = Helper::jsonDecode($v['content']);
                foreach ($contentData as $key => $value) {//奖金指数赋值最新
                    $contentData[$key]['i'] = $gameIndex[$value['mnum']][$value['ptype']][$value['bet']];
                }

                $orderContentData[$k]['content'] = Helper::jsonEncode($contentData); //投注内容
                $orderContentData[$k]['beishu'] = $beishu; // 优化倍数
                $orderContentData[$k]['create_at'] = Helper::timeFormat(time(), 's'); // 下单时间
                $id = $orderContentData[$k]['id'];//记录被返佣的订单详情的ID
                unset($orderContentData[$k]['id']);
                // 写入order_content表
                $orderContentId = $orderContent->saveOrderContent($orderContentData[$k]);
                if (!$orderContentId) {
                    trigger_error('下单失败, 错误代码510', E_USER_ERROR);
                }

                $orderDetail = new OrderDetail;
                $orderDetailData = $orderDetail->getOrderDetail(['order_content_id' => $id]);//获取订单详情
                $orderDetailData = $orderDetailData->toArray();
                foreach ($orderDetailData as $key => $detail) {

                    $orderDetailData[$key]['order_content_id'] = $orderContentId; // 订单内容ID
                    $bet = explode('|', $detail['bet']);
                    $rqs = '';
                    if (strstr($bet[1], ':')) {//处理让球数
                        $rqsArr = explode(':', $bet[1]);
                        $rqs = ':' . $rqsArr[1];
                    }

                    $bet[1] = $gameIndex[$detail['match_num']][$detail['play_type']][$bet[0]] . $rqs; //获取最新的奖金指数
                    $orderDetailData[$key]['bet'] = implode('|', $bet);//投注内容
                    $orderDetailData[$key]['create_time'] = Helper::timeFormat(time(), 's'); // 下单时间
                    $orderDetailData[$key]['order_id'] = $orderId; // 订单ID
                    $orderDetailData[$key]['odds'] = $bet[1]; // 赔率
                    unset($orderDetailData[$key]['id']);//去除跟单的订单的数据ID
                    // 写入订单详情表
                    $orderDetailId = $orderDetail->saveOrderDetail($orderDetailData[$key]);
                    if (!$orderDetailId) {
                        trigger_error('下单失败, 错误代码503', E_USER_ERROR);
                    }
                }
            }
            // 写入并推送消息
            Helper::logAndPushMsg(
                "用户{$memberData['username']}跟单成功, 跟单单号: {$this->orderNum}", // 消息内容
                $memberData['chn_name'], // 昵称
                $memberData['username'], // 用户账号
                ($memberData['role'] === 1) ? 3 : 2, // 消息类型
                2, // 内容类型
                $memberData['photo'] ?: 0 // 用户头像或图标
            );

            //增加推单订单的跟单人数
            self::where(['id' => $post['order_id']])->setInc('follows');
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * @desc 数字彩跟单
     * @author LiBin
     * @param $post
     * @return bool|string
     * @date 2019-05-16
     */
    public function nltailOrder($post)
    {
        try {
            // 前端传输的数据:amount 跟单金额 beishu 跟单倍数 order_id 订单ID zhu 注数
            //获取跟单的订单信息 需要过滤订单是否是推单订单
            $orderData = self::getOrder(['id' => $post['order_id'], 'pay_type' => 3]);
            if (empty($orderData)) {//判断订单是否存在
                trigger_error('订单不存在', E_USER_WARNING);
            }

            $orderData = $orderData->toArray();
            if (in_array($orderData['status'], [3, 4])) {
                trigger_error('该订单已过期!');
            }

            //获取订单详情
            $numModel = new OrderNum();
            $numData = $numModel->getOrderNum(['order_id' => $orderData['id'], 'is_push' => 1])->toArray();
            if (empty($numData)) {// 判断订单详情是否存在
                trigger_error('订单异常', E_USER_WARNING);
            }

            //判断订单金额是存在异常
            //计算推单订单的单倍金额
            $oneAmount = $numData['amount'] / $numData['multiple'];
            $beishu = $post['amount'] / (int)$oneAmount;//计算出跟单的金额是推单的多少倍.
            if (!is_int($beishu)) {
                trigger_error('金额异常', E_USER_WARNING);
            }

            //判断跟单截止日期
            if ($orderData['start_time'] < date('Y-m-d H:i:s')) {
                trigger_error('跟单已截止', E_USER_WARNING);
            }

            //判断起跟金额
            if ($orderData['start_amount'] > $post['amount']) {
                trigger_error('跟单金额必须大于起跟金额', E_USER_WARNING);
            }

            Db::startTrans();
            // 订单跟单数据容器
            $data = [];
            // 跟单金额
            $betAmount = round($post['amount'], 2);
            // 获取会员彩金和余额(加排他锁)
            $memberData = Member::where('id', UID)
                ->field([
                    'is_moni', // 是否模拟
                    'balance', // 余额
                    'hadsel', // 彩金
                    'role', // 角色
                    'username', // 用户名
                    'chn_name', // 昵称
                    'photo', // 头像|头标
                ])
                ->lock(true)
                ->find()
                ->toArray();
            if (empty($memberData['is_moni'])) {//判断模拟账户
                trigger_error('模拟账户不能跟单', E_USER_ERROR);
            }

            $hadsel = floatval($memberData['hadsel']);
            $balance = floatval($memberData['balance']);
            // 判断彩金是否大于下注金额
            if ($hadsel >= $betAmount) {
                // 减去下注金额
                $newHadsel = $hadsel - $betAmount;
                // 预支付彩金写入冻结资金
                Member::where('id', UID)->setInc('frozen_capital', $betAmount);
                // 保存支付后的彩金金额
                Member::where('id', UID)->setField([
                    'hadsel' => $newHadsel // 新彩金
                ]);
                // 增加订单彩金字段
                $data['pay_hadsel'] = $betAmount;

            } elseif ($balance + $hadsel >= $betAmount) {  // 判断余额加彩金是否大于等于下注金额
                // 先扣完彩金, 再扣余额, 并计算剩余应支付金额
                $surplusBetAmount = $betAmount - $hadsel;
                // 扣住余额, 获取剩余余额
                $surplusBalance = $balance - $surplusBetAmount;
                // 预支付彩金和余额写入冻结资金
                Member::where('id', UID)->setInc('frozen_capital', $betAmount);
                // 保存支付后的彩金和余额
                Member::where('id', UID)->setField([
                    'hadsel' => 0, // 彩金已扣完, 剩余0,
                    'balance' => $surplusBalance // 保存剩余余额
                ]);

                // 增加订单彩金字段
                $data['pay_hadsel'] = $hadsel;
                // 增加订单余额字段
                $data['pay_balance'] = $surplusBetAmount;
            } else {
                // 提示余额不足
                trigger_error('您的余额不足', E_USER_WARNING);
            }
            // 组装order数据
            $orderData['username'] = Member::getValByWhere(['id' => UID], 'username');//会员账号
            $orderData['order_no'] = $this->orderNum = Helper::orderNumber(); // 订单号
            $orderData['member_id'] = UID; // 会员ID
            $orderData['beishu'] = 1; // 期数
            $orderData['zhu'] = $betAmount / 2; // 注数
            $orderData['amount'] = $betAmount; // 下注金额
            $orderData['beizhu'] = ''; // 备注
            $orderData['create_time'] = Helper::timeFormat(time(), 's'); // 下单时间
            $orderData['follow_order_id'] = $orderData['id'];//跟单的订单ID
            $orderData['pay_type'] = 2; // 订单类型  1：自购 2：跟单 3: 推单
            $orderData['pay_status'] = 0; // 支付中
            $orderData['status'] = 0; // 待出票
            $orderData['is_moni'] = 0;  // 是否是模拟 0否 1是
            $orderData['is_yh'] = 0; // 停止追号
            if (!empty($data['pay_hadsel'])) {//彩金支付
                $orderData['pay_hadsel'] = $data['pay_hadsel'];
            } else {
                $orderData['pay_hadsel'] = 0;
            }

            if (!empty($data['pay_balance'])) {//余额支付
                $orderData['pay_balance'] = $data['pay_balance'];
            } else {
                $orderData['pay_balance'] = 0;
            }

            unset(
                $orderData['id'],//订单ID
                $orderData['start_amount'],//起跟金额
                $orderData['commission_rate'],//佣金比例
                $orderData['order_title'],//订单标题
                $orderData['start_time'],// 跟单截止时间
                $orderData['follows'],//跟单人数
                $orderData['sup_order_state'],//推单审核状态
                $orderData['pay_time'], // 支付时间
                $orderData['sup_order_time'] // 推单时间
            );
            // 写入order表
            $orderId = self::quickCreate($orderData);
            if (!$orderId) {
                trigger_error('下单失败, 错误代码493', E_USER_ERROR);
            }

            // 组装订单明细数据
            $numData['order_id'] = $orderId;// 订单ID
            $numData['multiple'] = $beishu;// 倍数
            $numData['amount'] = $betAmount;// 单期金额
            $numData['bonus'] = 0; // 单期中奖奖金
            $numData['bounty'] = 0; // 单期嘉奖彩金
            $numData['status'] = 1; //待开奖
            $numData['create_time'] = date('Y-m-d H:i:s');// 创建时间
            $numData['update_time'] = date('Y-m-d H:i:s');// 修改时间
            $numData['is_push'] = 0; // 不是
            unset($numData['id']);//去除订单ID
            // 写入order_num表
            $orderNumData = OrderNum::quickCreate($numData);
            if (!$orderNumData) {
                trigger_error('下单失败', E_USER_ERROR);
            }

            // 写入并推送消息
            Helper::logAndPushMsg(
                "用户{$memberData['username']}跟单成功, 跟单单号: {$this->orderNum}", // 消息内容
                $memberData['chn_name'], // 昵称
                $memberData['username'], // 用户账号
                ($memberData['role'] === 1) ? 3 : 2, // 消息类型
                2, // 内容类型
                $memberData['photo'] ?: 0 // 用户头像或图标
            );

            //增加推单订单的跟单人数
            self::where(['id' => $post['order_id']])->setInc('follows');
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 手动派奖, 获取中奖订单分页
     *
     * @param $where // 查询条件
     * @param $order // 排序
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getBingoOrderPage($where, $order = 'create_time DESC')
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        $where = $this->commonFilter($where);
        $pagination = self::where('status', 4)// 已中奖
        ->where($where)
            ->field([
                'id', // 订单ID
                'order_no', // 订单号
                'member_id', // 会员ID
                'amount', // 下注金额
                'bonus', // 奖金
                'bounty', // 嘉奖奖金
                'create_time', // 下单时间
                'is_clear', // 结算状态 0:未结算 1:已结算
                'lottery_id', // 彩种
                'is_moni', // 是否模拟
                'order_type', // 订单类型, 1:体彩订单  2:数字彩订单
            ])
            ->order($order)
            ->paginate($perPage);

        foreach ($pagination as &$item) {
            $name = Lottery::getValByWhere(['id' => $item['lottery_id']], 'name');
            $item['lottery'] = $name;
            $item['member'] = Member::getValueByWhere(['id' => $item['member_id']], 'username');
            unset(
                $item['lottery_id'], // 彩种ID
                $item['member_id'] // 会员ID
            );
        }

        return $pagination;
    }

    /**
     * 导出中奖名单
     *
     * @param $where // 筛选条件
     * @param string $order // 排序规则
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function exportBingoOrder($where, $order = 'create_time DESC')
    {
        $where = $this->commonFilter($where);
        $model = self::where($where)
            ->where('status', 4)// 已中奖
            ->field([
                'id', // 订单ID
                'order_no', // 订单号
                'member_id', // 会员ID
                'amount', // 下注金额
                'bonus', // 奖金
                'bounty', // 嘉奖奖金
                'create_time', // 下单时间
                'is_clear', // 是否结算 0: 未结算  1: 已结算
                'lottery_id', // 彩种
                'is_moni', // 是否模拟
            ])
            ->order($order)
            ->select();
        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        foreach ($data as &$item) {
            // 是否模拟
            $item['is_moni'] = self::getIsMoniStr($item['is_moni']);
            $name = Lottery::getValByWhere(['id' => $item['lottery_id']], 'name');
            // 结算状态
            $item['is_clear'] = ($item['is_clear'] == 0 ? '未结算' : '已结算');
            // 彩种名称
            $item['lottery'] = $name;
            // 会员账号
            $item['member'] = Member::getValueByWhere(['id' => $item['member_id']], 'username');
            unset($item['lottery_id']);
            unset($item['member_id']);
        }

        // 导出
        Helper::exportExcel(
            'ZhongJiangExcel',
            [
                '订单ID', '注单单号', '下注金额', '中奖奖金', '嘉奖奖金', '下单时间', '是否结算', '是否模拟', '彩种名称', '会员账号',
            ],
            $data
        );
    }

    /**
     * 手动开奖,获取赛事名单
     *
     * @param $matchNum // 比赛编号
     * @param $code // 竞彩代码
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getBingoOrderList($matchNum, $code)
    {
        switch ((string)$code) {
            case Config::ZC_CODE: // 足彩
                $isOpen = JczqOpen::isResult($matchNum);
                if (!$isOpen) {
                    // 比赛未结束
                    return [];
                }

                // 获取该赛事所有订单
                $model = new OrderDetail;
                $data = $model->getMatchDrawOrder($matchNum, Config::ZC_CODE);
                if (empty($data)) {
                    return [];
                }

                return $data;
            case Config::BJ_CODE: // 北京单场
                $isOpen = JcdcOpen::isResult($matchNum);
                if (!$isOpen) {
                    // 比赛未结束
                    return [];
                }

                // 获取该赛事所有订单
                $model = new OrderDetail;
                $data = $model->getMatchDrawOrder($matchNum, Config::BJ_CODE);
                if (empty($data)) {
                    return [];
                }

                return $data;
            case Config::LC_CODE: // 篮彩
                $isOpen = JclqOpen::isResult($matchNum);
                if (!$isOpen) {
                    // 比赛未结束
                    return [];
                }

                // 获取该赛事所有订单
                $model = new OrderDetail;
                $data = $model->getMatchDrawOrder($matchNum, Config::LC_CODE);
                if (empty($data)) {
                    return [];
                }

                return $data;
            default:
                return [];
        }
    }

    /**
     * 体彩手动开奖
     *
     * @param $code // 竞彩代码
     * @param $matchNum // 赛事编号
     * @throws \Exception
     * @return true|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function setDrawByOrderDetailIds($code, $matchNum)
    {
        switch ((string)$code) {
            case Config::ZC_CODE: // 足彩
                $model = new JczqOpen;
                return $model->handOpen($matchNum);
            case Config::BJ_CODE: // 北京单场
                $model = new JcdcOpen;
                return $model->handOpen($matchNum);
            case Config::LC_CODE: // 篮彩
                $model = new JclqOpen;
                return $model->handOpen($matchNum);
            default:
                return '手动开奖失败!';
        }
    }

    /**
     * 体彩自动开奖
     *
     * @param $code // 竞彩代码
     * @param $matchNum // 赛事编号
     * @throws \Exception
     * @return void
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function autoDraw($code, $matchNum)
    {
        switch ((string)$code) {
            case Config::ZC_CODE: // 足彩
                $model = new JczqOpen;
                $model->zcAutoDraw($matchNum);
                break;
            case Config::BJ_CODE: // 北京单场
                $model = new JcdcOpen;
                $model->dcAutoDraw($matchNum);
                break;
            case Config::LC_CODE: // 篮彩
                $model = new JclqOpen();
                $model->lcAutoDraw($matchNum);
                break;
            default:
                break;
        }
    }

    /**
     * 后台手动派奖(降低事务层级)
     *
     * @param $param
     *  例如: "1@1,1@2,1@3"
     *  1: 订单类型 1,体彩 2,数字彩
     *  @: 分隔符
     *  1: 订单ID
     * @return true|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function sendPrize($param)
    {
        // 处理请求参数
        $data = explode(',', $param);
        // 请求参数为空
        if (empty($data)) {
            return '请选择订单!';
        }

        // 遍历数据
        $isComplete = true;
        foreach ($data as $item) {
            try {
                // 开启事务
                Db::startTrans();
                // 参数解构
                list($orderType, $id) = explode('@', $item);
                // 获取订单详情
                $orderDetail = self::getFieldsByWhere(['id' => $id], [
                    'order_no', // 注单号
                    'amount', // 投注总金额
                    'bonus', // 奖金(余额)
                    'bounty', // 彩金
                    'follow_order_commission', // 推单佣金收益(余额)
                    'pay_out_commission', // 跟单付出的佣金(余额)
                    'member_id', // 会员ID
                    'status', // 订单状态 0：待出票 1：已出票 2：待开奖 3：未中奖 4：已中奖
                    'is_clear', // 是否结算, 0: 未结算 1: 已结算
                    'pay_type', // 订单类型
                    'exit_account', // 退换资金
                    'is_yh', // 中奖后停止追号(数字彩) 0:停止 1:继续
                ]);
                // 订单不存在
                if (empty($orderDetail)) {
                    trigger_error('派奖失败,该订单不存在!');
                }

                // 订单状态
                $status = $orderDetail['status'];
                if ($status !== 4) {
                    trigger_error('派奖失败,订单未中奖!');
                }

                // 结算状态
                $isClear = $orderDetail['is_clear'];
                if ($isClear === 1) {
                    // 提交事务
                    Db::commit();
                    // 订单已结算, 跳出本次遍历
                    continue;
                }

                // 获取奖金
                $bonus = $orderDetail['bonus'];
                // 获取彩金
                $bounty = $orderDetail['bounty'];
                // 获取推单佣金
                $commission = $orderDetail['follow_order_commission'];
                // 获取会员ID
                $memberId = $orderDetail['member_id'];
                // 计算总奖金 --自购和跟单时 推单奖励$commission = 0.00
                $totalBonus = $bonus + $commission;
                // 获取会员资金和彩金
                $amountDetail = Member::where('id', $memberId)
                    ->lock(true)// 排他锁
                    ->field([
                        'balance', // 余额
                        'hadsel', // 彩金
                        'is_moni', // 0: 模拟 1: 真实
                        'role', // 角色
                        'username', // 账号
                    ])
                    ->find();
                if (empty($amountDetail)) {
                    trigger_error('派奖失败, 会员不存在');
                }

                $member = new Member;
                // 增加资金和彩金
                $writeResult = $member->writeAmount($memberId, $totalBonus, $bounty);
                if (!$writeResult) {
                    trigger_error('派奖失败, 更新账户资金失败');
                }

                // 增加总输赢
                $betTotalAmount = $orderDetail['amount'] - $orderDetail['exit_account'];
                $profit = $betTotalAmount - ($totalBonus + $bounty);
                $profit = abs($profit);
                Member::addLoseAndWinning($memberId, $profit);
                // 如果是真实账号, 则写入资金和余额记录
                if ($amountDetail['is_moni'] === 1) {
                    // 订单类型
                    $payType = (int)$orderDetail['pay_type'];
                    // 需要写入资金记录的数据
                    switch ($payType) {
                        case 1:  // 自购
                            $laterMoney = (float)$amountDetail['balance'] + (float)$totalBonus;
                            break;
                        case 2: // 跟单
                            $laterMoney = (float)$amountDetail['balance'] + (float)$totalBonus + (float)$orderDetail['pay_out_commission'];
                            break;
                        case 3: // 推单
                            $laterMoney = (float)$amountDetail['balance'] + (float)$bonus;
                            break;
                        default: // 自购
                            $laterMoney = (float)$amountDetail['balance'] + (float)$totalBonus;
                    }

                    $data = [
                        [
                            'member_id' => $memberId, // 会员ID
                            'money' => $totalBonus, // 变动余额
                            'front_money' => $amountDetail['balance'], // 变动前余额
                            // 跟单付出佣金在推单和自购时为0.00, 变动后余额
                            'later_money' => $laterMoney,
                            'type' => 5, // 奖金
                            'remark' => '注单奖金派发',
                            'create_time' => Helper::timeFormat(time(), 's'),
                            'update_time' => Helper::timeFormat(time(), 's'),
                            'identify' => $amountDetail['role'],
                            'order_id' => $id,
                            'username' => $amountDetail['username'],
                        ]
                    ];
                    // 兼容幸运飞艇(无嘉奖)
                    if (!empty(floatval($bounty))) {
                        array_push($data, [
                            'member_id' => $memberId, // 会员ID
                            'money' => $bounty, // 变动彩金
                            'front_money' => $amountDetail['hadsel'], // 变动前彩金
                            'later_money' => (float)$amountDetail['hadsel'] + (float)$bounty, // 变动后彩金
                            'type' => 6, // 嘉奖彩金
                            'remark' => '注单嘉奖彩金',
                            'create_time' => Helper::timeFormat(time(), 's'),
                            'update_time' => Helper::timeFormat(time(), 's'),
                            'identify' => $amountDetail['role'],
                            'order_id' => $id,
                            'username' => $amountDetail['username'],
                        ]);
                    }

                    // 跟单
                    if ($payType === 2) {
                        array_push($data, [
                            'member_id' => $memberId, // 会员ID
                            'money' => $orderDetail['pay_out_commission'], // 变动金额
                            'front_money' => (float)$amountDetail['balance'] + (float)$totalBonus + (float)$orderDetail['pay_out_commission'], // 变动前余额
                            'later_money' => (float)$amountDetail['balance'] + (float)$totalBonus, // 变动后余额
                            'type' => 10, // 跟单返佣
                            'remark' => '跟单支付佣金',
                            'create_time' => Helper::timeFormat(time(), 's'),
                            'update_time' => Helper::timeFormat(time(), 's'),
                            'identify' => $amountDetail['role'],
                            'order_id' => $id,
                            'username' => $amountDetail['username'],
                        ]);
                    }

                    // 推单
                    if ($payType === 3) {
                        array_push($data, [
                            'member_id' => $memberId, // 会员ID
                            'money' => $commission, // 变动金额
                            'front_money' => (float)$amountDetail['balance'] + (float)$bonus, // 变动前余额
                            'later_money' => (float)$amountDetail['balance'] + (float)$totalBonus, // 变动后余额
                            'type' => 10, // 跟单返佣
                            'remark' => '推单佣金收益',
                            'create_time' => Helper::timeFormat(time(), 's'),
                            'update_time' => Helper::timeFormat(time(), 's'),
                            'identify' => $amountDetail['role'],
                            'order_id' => $id,
                            'username' => $amountDetail['username'],
                        ]);
                    }

                    // 写入资金流水, 余额记录
                    $fundLog = new FundLog;
                    $logResult = $fundLog->insertAll($data);
                    // 写入失败, 则抛出异常
                    if (!$logResult) {
                        trigger_error('派奖失败,写入资金变动失败');
                    }
                }

                // 更改订单状态
                self::quickCreate([
                    'id' => $id, // 订单ID
                    'is_clear' => 1, // 已结算
                ], true);
                // 数字彩退换彩金
                if ($orderType == 2 && !empty($orderDetail['exit_account'])) {
                    // 退换停止追期后的资金
                    Member::where('id', $memberId)->setInc('hadsel', $orderDetail['exit_account']);
                    // 非模拟并且退还资金非空 则写入资金流水
                    if ($amountDetail['is_moni'] === 1 && !empty(floatval($orderDetail['exit_account']))) {
                        FundLog::quickCreate([
                            'member_id' => $memberId, // 会员ID
                            'money' => $orderDetail['exit_account'], // 变动彩金
                            'front_money' => bcadd($amountDetail['hadsel'], $totalBonus, 2), // 变动前彩金
                            'later_money' => bcadd($amountDetail['hadsel'], $totalBonus, 2) + $orderDetail['exit_account'], // 变动后彩金
                            'type' => 3, // 购彩退换
                            'remark' => '注单彩金退换',
                            'create_time' => Helper::timeFormat(time(), 's'),
                            'update_time' => Helper::timeFormat(time(), 's'),
                            'identify' => $amountDetail['role'],
                            'order_id' => $id,
                            'username' => $amountDetail['username'],
                        ]);
                    }
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                // 未完成派奖
                $isComplete = false;
                // 写入系统日志
                $orderNo = '';
                if (isset($orderDetail) && isset($orderDetail['order_no'])) {
                    $orderNo = $orderDetail['order_no'];
                }

                Helper::log('系统', '手动派奖', "注单{$orderNo}派奖失败!", $e->getMessage(), 0);
            }
        }
        // 判断是否完成派奖
        if ($isComplete) {
            return true;
        }

        return '手动派奖错误, 错误详情请查看系统日志';
    }

    /**
     * 自动派奖逻辑(直接传入订单ID, 二次查询降低耦合)
     * 注: 数字已使用 (体彩暂未使用)
     *
     * @param $orderId // 订单ID
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function autoSendDraw($orderId)
    {
        // 获取订单详情
        $orderDetail = self::getFieldsByWhere(['id' => $orderId], [
            'amount', // 投注总金额
            'bonus', // 奖金(余额)
            'bounty', // 彩金
            'follow_order_commission', // 推单佣金收益(余额)
            'pay_out_commission', // 跟单付出的佣金(余额)
            'member_id', // 会员ID
            'status', // 订单状态 0：待出票 1：已出票 2：待开奖 3：未中奖 4：已中奖
            'is_clear', // 是否结算, 0: 未结算 1: 已结算
            'pay_type', // 购买方式
            'order_type', // 订单类型
            'exit_account', // 退还资金
            'is_yh', // 中奖后停止追号(数字彩) 0:停止 1:继续
        ]);

        // 结算状态
        $isClear = $orderDetail['is_clear'];
        if ($isClear === 1) {
            // 订单已结算, 跳出本次遍历
            return;
        }

        // 获取奖金
        $bonus = $orderDetail['bonus'];
        // 获取彩金
        $bounty = $orderDetail['bounty'];
        // 获取推单佣金
        $commission = $orderDetail['follow_order_commission'];
        // 获取会员ID
        $memberId = $orderDetail['member_id'];
        // 计算总奖金 --自购和跟单时 推单奖励$commission = 0.00
        $totalBonus = $bonus + $commission;
        // 获取会员资金和彩金
        $amountDetail = Member::where('id', $memberId)
            ->lock(true)// 排他锁
            ->field([
                'balance', // 余额
                'hadsel', // 彩金
                'is_moni', // 0: 模拟 1: 真实
                'role', // 角色
                'username', // 账号
            ])
            ->find();
        if (empty($amountDetail)) {
            trigger_error('派奖失败会员不存在,错误码2790');
        }

        $member = new Member;
        // 增加资金和彩金
        $writeResult = $member->writeAmount($memberId, $totalBonus, $bounty);
        if (!$writeResult) {
            trigger_error('派奖失败更新账户资金错误,错误码2797');
        }

        // 写入总输赢
        $totalBetAmount = $orderDetail['amount'] - $orderDetail['exit_account'];
        $profit = $totalBonus + $bounty - $totalBetAmount;
        $profit = abs($profit);
        Member::addLoseAndWinning($memberId, $profit);
        // 如果是真实账号, 则写入资金和余额记录
        if ($amountDetail['is_moni'] === 1) {
            // 订单类型
            $payType = (int)$orderDetail['pay_type'];
            // 需要写入资金记录的数据
            switch ($payType) {
                case 1:  // 自购
                    $laterMoney = (float)$amountDetail['balance'] + (float)$totalBonus;
                    break;
                case 2: // 跟单
                    $laterMoney = (float)$amountDetail['balance'] + (float)$totalBonus + (float)$orderDetail['pay_out_commission'];
                    break;
                case 3: // 推单
                    $laterMoney = (float)$amountDetail['balance'] + (float)$bonus;
                    break;
                default: // 自购
                    $laterMoney = (float)$amountDetail['balance'] + (float)$totalBonus;
            }

            $data = [
                [
                    'member_id' => $memberId, // 会员ID
                    'money' => $totalBonus, // 变动余额
                    'front_money' => $amountDetail['balance'], // 变动前余额
                    // 跟单付出佣金在推单和自购时为0.00, 变动后余额
                    'later_money' => $laterMoney,
                    'type' => 5, // 奖金
                    'remark' => '注单奖金派发',
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                    'identify' => $amountDetail['role'],
                    'order_id' => $orderId,
                    'username' => $amountDetail['username'],
                ]
            ];
            // 兼容幸运飞艇(无嘉奖)
            if (!empty(floatval($bounty))) {
                array_push($data, [
                    'member_id' => $memberId, // 会员ID
                    'money' => $bounty, // 变动彩金
                    'front_money' => $amountDetail['hadsel'], // 变动前彩金
                    'later_money' => (float)$amountDetail['hadsel'] + (float)$bounty, // 变动后彩金
                    'type' => 6, // 嘉奖彩金
                    'remark' => '注单嘉奖彩金',
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                    'identify' => $amountDetail['role'],
                    'order_id' => $orderId,
                    'username' => $amountDetail['username'],
                ]);
            }

            // 跟单
            if ($payType === 2) {
                array_push($data, [
                    'member_id' => $memberId, // 会员ID
                    'money' => $orderDetail['pay_out_commission'], // 变动金额
                    'front_money' => (float)$amountDetail['balance'] + (float)$totalBonus + (float)$orderDetail['pay_out_commission'], // 变动前余额
                    'later_money' => (float)$amountDetail['balance'] + (float)$totalBonus, // 变动后余额
                    'type' => 10, // 跟单返佣
                    'remark' => '跟单支付佣金',
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                    'identify' => $amountDetail['role'],
                    'order_id' => $orderId,
                    'username' => $amountDetail['username'],
                ]);
            }

            // 推单
            if ($payType === 3) {
                array_push($data, [
                    'member_id' => $memberId, // 会员ID
                    'money' => $commission, // 变动金额
                    'front_money' => (float)$amountDetail['balance'] + (float)$bonus, // 变动前余额
                    'later_money' => (float)$amountDetail['balance'] + (float)$totalBonus, // 变动后余额
                    'type' => 10, // 跟单返佣
                    'remark' => '推单佣金收益',
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                    'identify' => $amountDetail['role'],
                    'order_id' => $orderId,
                    'username' => $amountDetail['username'],
                ]);
            }

            // 写入资金流水, 余额记录
            $fundLog = new FundLog;
            $logResult = $fundLog->insertAll($data);
            // 写入失败抛出异常
            if (!$logResult) {
                trigger_error('派奖失败写入资金变动失败,错误码2891');
            }
        }

        // 更改订单状态
        self::quickCreate([
            'id' => $orderId, // 订单ID
            'is_clear' => 1, // 已结算
        ], true);
        // 数字彩退换彩金
        if ($orderDetail['order_type'] == 2 && !empty($orderDetail['exit_account'])) {
            // 退换停止追期后的资金
            Member::where('id', $memberId)->setInc('hadsel', $orderDetail['exit_account']);
            // 非模拟并且退还资金非空 则写入资金流水
            if ($amountDetail['is_moni'] === 1 && !empty(floatval($orderDetail['exit_account']))) {
                FundLog::quickCreate([
                    'member_id' => $memberId, // 会员ID
                    'money' => $orderDetail['exit_account'], // 变动彩金
                    'front_money' => bcadd($amountDetail['hadsel'], $totalBonus, 2), // 变动前彩金
                    'later_money' => bcadd($amountDetail['hadsel'], $totalBonus, 2) + $orderDetail['exit_account'], // 变动后彩金
                    'type' => 3, // 购彩退换
                    'remark' => '注单彩金退换',
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                    'identify' => $amountDetail['role'],
                    'order_id' => $orderId,
                    'username' => $amountDetail['username'],
                ]);
            }
        }
    }

    /**
     *  订单详情
     *
     * @param $where // 查询条件
     * @param $data // 查询字段
     * @return array|null|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getOrderDetails($where, $data)
    {
        return self::where($where)->field($data)->find();
    }

    /**
     * 数字彩订单详情
     *
     * @param $orderNum // 订单号
     * @throws \Exception
     * @author CleverStone
     * @return string|array
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getNumOrderDetail($orderNum)
    {
        // 查询订单详情
        $data = self::getFieldsByWhere(['order_no' => $orderNum], [
            'id', // 订单ID
            'lottery_id', // 彩种ID
            'bet_content', // 投注项
            'status', // 状态, 0：待出票 1：已出票 2：待开奖 3：未中奖 4：已中奖
            'order_no', // 订单号
            'create_time', // 创建时间
            'amount', // 投注金额
            'beishu', // 倍数(体彩) 期数(数字彩)
            'pay_hadsel', // 彩金支付金额
            'pay_balance', // 余额支付金额
            'bonus', // 中奖金额
        ]);
        if (empty($data)) {
            return '该订单不存在';
        }

        // 彩种代码
        $code = Lottery::getValByWhere(['id' => $data['lottery_id']], 'code');
        // 投注详情
        $betContentArr = Helper::jsonDecode($data['bet_content']);
        // 数据容器
        $returnData = [];
        // 投注项容器
        $betTemp = [];
        // 数字彩类型
        $ctype = 0;
        switch ((string)$code) {
            case Config::P3_CODE: // 排3
                $ctype = 1;
                // 赛果拼装
                foreach ($betContentArr as $v) {
                    switch ($v['play']) {
                        case 'zhipu': // 直普
                            $oneArr = [];
                            $twoArr = [];
                            $threeArr = [];
                            foreach ($v['bet'] as $value) {
                                list($a, $b, $c) = explode(',', $value);
                                array_push($oneArr, $a);
                                array_push($twoArr, $b);
                                array_push($threeArr, $c);
                            }

                            $son1 = implode(',', array_unique($oneArr));
                            $son2 = implode(',', array_unique($twoArr));
                            $son3 = implode(',', array_unique($threeArr));
                            $betTemp[] = "直普:[{$v['zhu']}注 {$v['amount']}元] {$son1}|{$son2}|{$son3}";
                            break;
                        case 'zhihe': // 直和
                            $bet = '';
                            foreach ($v['bet'] as $value) {
                                $bet .= $value . '|';
                            }

                            $bet = rtrim($bet, '|');
                            $betTemp[] = "直和:[{$v['zhu']}注 {$v['amount']}元] {$bet}";
                            break;
                        case 'zusanbao': // 组三包
                            $bet = '';
                            foreach ($v['bet'] as $value) {
                                $bet .= $value . '|';
                            }

                            $bet = rtrim($bet, '|');
                            $betTemp[] = "三包:[{$v['zhu']}注 {$v['amount']}元] {$bet}";
                            break;
                        case 'zusanhe': // 组三和
                            $bet = '';
                            foreach ($v['bet'] as $value) {
                                $bet .= $value . '|';
                            }

                            $bet = rtrim($bet, '|');
                            $betTemp[] = "三和:[{$v['zhu']}注 {$v['amount']}元] {$bet}";
                            break;
                        case 'zuliubao': // 组六包
                            $bet = '';
                            foreach ($v['bet'] as $value) {
                                $bet .= $value . '|';
                            }

                            $bet = rtrim($bet, '|');
                            $betTemp[] = "六包:[{$v['zhu']}注 {$v['amount']}元] {$bet}";
                            break;
                        case 'zuliuhe': // 组六和
                            $bet = '';
                            foreach ($v['bet'] as $value) {
                                $bet .= $value . '|';
                            }

                            $bet = rtrim($bet, '|');
                            $betTemp[] = "六和:[{$v['zhu']}注 {$v['amount']}元] {$bet}";
                            break;
                        case 'zuliudantuo': // 组六胆拖
                            $bet = '';
                            foreach ($v['bet'] as $value) {
                                $bet .= $value . '|';
                            }

                            $bet = rtrim($bet, '|');
                            $betTemp[] = "胆拖:[{$v['zhu']}注 {$v['amount']}元] {$bet}";
                            break;
                    }
                }
                break;
            case Config::P5_CODE: // 排五
                $ctype = 2;
                // 赛果拼装
                foreach ($betContentArr as $v) {
                    switch ($v['play']) {
                        case 'p5zhipu': // 排五直普
                            $oneArr = [];
                            $twoArr = [];
                            $threeArr = [];
                            $fourArr = [];
                            $fiveArr = [];
                            foreach ($v['bet'] as $value) {
                                list($a, $b, $c, $d, $e) = explode(',', $value);
                                array_push($oneArr, $a);
                                array_push($twoArr, $b);
                                array_push($threeArr, $c);
                                array_push($fourArr, $d);
                                array_push($fiveArr, $e);
                            }

                            $one = implode(',', array_unique($oneArr));
                            $two = implode(',', array_unique($twoArr));
                            $three = implode(',', array_unique($threeArr));
                            $four = implode(',', array_unique($fourArr));
                            $five = implode(',', array_unique($fiveArr));
                            $bet = "{$one}|{$two}|{$three}|{$four}|{$five}";
                            $betTemp[] = "直普:[{$v['zhu']}注 {$v['amount']}元] {$bet}";
                            break;
                    }
                }
                break;
            case Config::AO_CODE: // 澳彩
                $ctype = 3;
                // 组合赛果
                foreach ($betContentArr as $v) {
                    switch ($v['play']) {
                        case 'aozhipu': // 澳彩直普
                            $oneArr = [];
                            $twoArr = [];
                            $threeArr = [];
                            foreach ($v['bet'] as $value) {
                                list($a, $b, $c) = explode(',', $value);
                                array_push($oneArr, $a);
                                array_push($twoArr, $b);
                                array_push($threeArr, $c);
                            }

                            $son1 = implode(',', array_unique($oneArr));
                            $son2 = implode(',', array_unique($twoArr));
                            $son3 = implode(',', array_unique($threeArr));
                            $betTemp[] = "直普:[{$v['zhu']}注 {$v['amount']}元] {$son1}|{$son2}|{$son3}";
                            break;
                        case 'aozhihe': // 澳彩直和
                            $bet = '';
                            foreach ($v['bet'] as $value) {
                                $bet .= $value . '|';
                            }

                            $bet = rtrim($bet, '|');
                            $betTemp[] = "直和:[{$v['zhu']}注 {$v['amount']}元] {$bet}";
                            break;
                    }
                }
                break;
            case Config::PC_CODE: // 葡彩
                $ctype = 4;
                // 组合赛果
                foreach ($betContentArr as $v) {
                    switch ($v['play']) {
                        case 'puzhipu': // 葡彩直普
                            $oneArr = [];
                            $twoArr = [];
                            $threeArr = [];
                            $fourArr = [];
                            $fiveArr = [];
                            foreach ($v['bet'] as $value) {
                                list($a, $b, $c, $d, $e) = explode(',', $value);
                                array_push($oneArr, $a);
                                array_push($twoArr, $b);
                                array_push($threeArr, $c);
                                array_push($fourArr, $d);
                                array_push($fiveArr, $e);
                            }

                            $one = implode(',', array_unique($oneArr));
                            $two = implode(',', array_unique($twoArr));
                            $three = implode(',', array_unique($threeArr));
                            $four = implode(',', array_unique($fourArr));
                            $five = implode(',', array_unique($fiveArr));
                            $bet = "{$one}|{$two}|{$three}|{$four}|{$five}";
                            $betTemp[] = "直普:[{$v['zhu']}注 {$v['amount']}元] {$bet}";
                            break;
                    }
                }
                break;
            case Config::FT_CODE: // 飞艇
                $ctype = 5;
                // 组合赛果
                foreach ($betContentArr as $v) {
                    $play = $this->getPlayStrByCode($v['play']);
                    foreach ($v['bet'] as $value) {
                        $type = $this->convertStr($value['type']);
                        array_walk($value['value'], function ($item) use (&$betTemp, $type, $play) {
                            list($t, $m, $i) = explode('|', $item);
                            $t = $this->convertStr($t);
                            $betTemp[] = "{$play} {$type}{$t} 金额{$m} 赔率{$i}";
                        });
                    }
                }
                break;
            default:
                trigger_error('该彩种不存在', E_USER_ERROR);
        }
        // 主内容
        $returnData['id'] = $data['id']; // 订单ID
        $returnData['status'] = $data['status']; // 订单状态 0：待出票 1：已出票 2：待开奖 3：未中奖 4：已中奖
        $returnData['order_no'] = $data['order_no']; // 订单编号
        $returnData['deadline'] = ''; // 推单截止时间
        $returnData['creattime'] = $data['create_time']; // 创建时间
        $returnData['amount'] = "{$data['amount']}(共{$data['beishu']}期)";// 投注金额期数倍数
        $returnData['pay_hadsel'] = $data['pay_hadsel']; // 彩金支付
        $returnData['pay_balance'] = $data['pay_balance']; // 余额支付
        $returnData['bonus'] = $data['bonus']; // 奖金
        $returnData['type'] = $ctype; // 数字彩类型, 1: 排列三  2: 排列五  3: 澳彩  4: 葡彩 5: 幸运飞艇
        $returnData['start_amount'] = 0; // 跟单金额
        // 追期详情
        $numData = OrderNum::where('order_id', $data['id'])
            ->field([
                'number', // 期号
                'status', // 单期状态 1：待开奖 2：未中奖 3：已中奖
                'is_push', // 该期是否是推单, 0: 不是  1: 是
                'amount', // 单期投注金额
                'multiple', // 倍数
            ])
            ->order('id ASC')
            ->select()
            ->toArray();
        $returnData['detail'] = [];
        foreach ($numData as $key => $item) {
            // 推单设定
            if ($key == 0) {
                $shutDownTime = PlOpen::getValByWhere(['expect' => $item['number'], 'ctype' => $ctype], 'open_time');
                // 推单截止时间
                if (!empty($shutDownTime)) {
                    $shutDownTime = date('Y-m-d H:i:s', strtotime($shutDownTime . '-10 seconds'));
                    $returnData['deadline'] = $shutDownTime;
                }

                // 起跟金额
                $returnData['start_amount'] = (int)round($item['amount'] / $item['multiple']);
            }

            array_push($returnData['detail'], [
                'number' => $item['number'], // 期号
                'status' => $item['status'], // 单期状态 1：待开奖 2：未中奖 3：已中奖
                'draw_result' => $betTemp, // 投注项
            ]);
        }

        return $returnData;
    }

    /**
     * 获取总投注数(订单数)
     *
     * @param $where // 查询条件
     * @return int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getBetCount($where = null)
    {
        $count = Order::where('pay_status', 1)
            ->where($where)
            ->count('id');
//            ->sum('zhu');
        return $count ?: 0;
    }

    /**
     * 获取15天的订单折线图
     *
     * @param $where // 查询条件
     * @return string
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function get15DaysOrderLine($where = null)
    {
        $time = mktime(0, 0, 0, date('m'), date('d') - 14, date('Y'));
        $day15 = Helper::timeFormat($time, 's');
        $now = Helper::timeFormat(time(), 's');
        $model = self::where([['create_time', 'between time', [$day15, $now]]])
            //->where('is_moni', 0)// 非模拟订单 (此处计算模拟订单)
            ->where('pay_status', 1)// 已支付订单
            ->where($where)
            ->field([
                'COUNT(id)' => 'number',
                'DATE_FORMAT(create_time, "%Y-%m-%d")' => 'date',
            ])
            ->group(['DATE_FORMAT(create_time, "%Y-%m-%d")'])
            ->select();

        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        $dealData = array_column($data, 'number', 'date');
        $range = [];
        $count = [];
        for ($i = 0; strtotime($day15 . '+' . $i . ' days') <= strtotime($now); $i++) {
            $time = strtotime($day15 . '+' . $i . ' days');
            $layerDate = date('Y-m-d', $time);
            $layerDay = date('m-d', $time);
            $range[] = $layerDay;
            $count[] = isset($dealData[$layerDate]) ? $dealData[$layerDate] : 0;
        }

        array_pop($range);
        array_push($range, '今天');
        $resData['x'] = $range;
        $resData['y'] = $count;

        return $resData;
    }

    /**
     * @desc 获取推单的七日命中率和自购金额跟单人气
     * @author CleverStone
     * @param $where // 查询条件
     * @param $data // 查询字段
     * @param $order // 排序规则
     * @return array
     * @throws \Exception
     * @date 2019-04-09
     */
    public function getPushOrder($where, $data, $order = 'create_time desc')
    {
        // 获取推单列表
        $data = self::where($where)->field($data)->order($order)->select()->toArray();
        if (empty($data)) {
            return [];
        }

        // 获取七日时间段
        $starTime = Helper::timeFormat(strtotime('-6 days'), 's');
        $endTime = Helper::timeFormat(time(), 's');
        $list = [];
        foreach ($data as $k => $v) {
            // 会员详情
            $memberData = Member::getFieldsByWhere(['id' => $v['member_id']], ['photo', 'chn_name']);
            // 头像
            $photo = Helper::getCurrentHost() . Attach::getPathByAttachId($memberData['photo']);
            // 获取当前会员7日推单列表
            switch ($v['order_type']) {
                case 1: // 体彩
                    $where = [];
                    $where[] = ['create_time', 'between', [$starTime, $endTime]]; // 检索七日内的推单数据
                    $where[] = ['pay_type', '=', 3]; // 购买类型 1：自购 2：跟单 3: 推单
                    $where[] = ['member_id', '=', $v['member_id']]; // 会员ID
                    $where[] = ['status', 'between', [3, 4]]; // 状态 0：待出票 1：已出票 2：待开奖 3：未中奖 4：已中奖
                    $state7Data = self::where($where)->field('status')->select()->toArray();
                    $list[$k]['issue'] = ''; // 期号
                    break;
                case 2: // 数字彩
                    $where = [];
                    $where[] = ['a.create_time', 'between', [$starTime, $endTime]]; // 检索七日内的推单数据
                    $where[] = ['a.pay_type', '=', 3]; // 购买类型 1：自购 2：跟单 3: 推单
                    $where[] = ['a.member_id', '=', $v['member_id']]; // 会员ID
                    $where[] = ['a.status', 'between', [3, 4]]; // 状态 0：待出票 1：已出票 2：待开奖 3：未中奖 4：已中奖
                    $where[] = ['b.is_push', '=', 1]; // 推单
                    $state7Data = self::alias('a')
                        ->join('order_num b', 'a.id=b.order_id')
                        ->where($where)
                        ->field('b.status')// 数字彩是计算该期中奖状态
                        ->select()
                        ->toArray();
                    // 获取推单的期号
                    $issue = OrderNum::getValByWhere(['order_id' => $v['id'], 'is_push' => 1], 'number');
                    $list[$k]['issue'] = $issue; // 期号
                    break;
            }

            // 计算命中率
            if (!empty($state7Data)) {
                $x = 0;
                foreach ($state7Data as $status) {
                    // 体彩
                    if ($v['order_type'] === 1 && $status['status'] === 4) {
                        $x = $x + 1;
                    }

                    // 数字彩
                    if ($v['order_type'] === 2 && $status['status'] === 3) {
                        $x = $x + 1;
                    }
                }

                $y = count($state7Data);
                // 七日命中率
                $probabillity = number_format($x / $y * 100, 2);
                // 获取跟单人数
                $number = self::where(['follow_order_id' => $v['id']])->count('id');
                $list[$k]['probabillity'] = (string)$probabillity . '%';//计算的七日命中率
                $list[$k]['scort'] = $probabillity; // 命中率排序字段
                $list[$k]['amount'] = $v['amount']; // 自购金额
                $list[$k]['number'] = $number; // 人气数
            } else {
                // 获取跟单人数
                $number = self::where(['follow_order_id' => $v['id']])->count('id');
                $list[$k]['probabillity'] = (string)0; // 七日命中率
                $list[$k]['scort'] = 0; // 命中率排序字段
                $list[$k]['amount'] = $v['amount']; // 自购总金额
                $list[$k]['number'] = $number; // 人气数
            }

            // 根据彩种ID判断彩种类型
            $code = Lottery::getCodeById($v['lottery_id']);
            switch ($code) { // 判断彩票类型 1.足彩 2.篮彩 3.北京单场 4.排三 5.排五
                case Config::ZC_CODE://足彩
                    $list[$k]['type'] = 1;
                    break;
                case Config::LC_CODE://篮彩
                    $list[$k]['type'] = 2;
                    break;
                case Config::BJ_CODE://北京单场
                    $list[$k]['type'] = 3;
                    break;
                case Config::P3_CODE: //排三
                    $list[$k]['type'] = 4;
                    break;
                case Config::P5_CODE: //排五
                    $list[$k]['type'] = 5;
                    break;
            }

            $list[$k]['photo'] = $photo;//头像
            empty($memberData['chn_name']) ? $list[$k]['name'] = '' : $list[$k]['name'] = $memberData['chn_name']; // 昵称
            empty($v['order_title']) ? $list[$k]['title'] = '' : $list[$k]['title'] = $v['order_title']; // 宣言
            empty($v['member_id']) ? $list[$k]['member_id'] = '' : $list[$k]['member_id'] = $v['member_id']; // 用户ID
            $list[$k]['order_id'] = $v['id']; // 订单ID
            $list[$k]['start_time'] = $v['start_time']; // 跟单截止时间
            $list[$k]['chuan'] = preg_replace('/\["|"]/', '', $v['chuan']);// 串
            $list[$k]['start_amount'] = (string)$v['start_amount']; // 起跟金额
            $list[$k]['order_no'] = $v['order_no'];//订单号
        }

        return $list;
    }

    /**
     * 我的推单
     *
     * @param $where // 查询条件
     * @param $data // 查询字段
     * @param int $number // 每页数据条数
     * @param int $page // 当前页
     * @return array|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author LiBin
     * @api *
     */
    public function myPushOrder($where, $data, $number = 20, $page = 1)
    {
        // limit查询SQL
        $limit = ($page - 1) * $number . ',' . $number;
        $data = self::where($where)->field($data)->order('create_time DESC')->limit($limit)->select();
        if (empty($data)) {
            return [];
        }

        // 数据组装
        foreach ($data as &$v) {
            if ($v['order_type'] == 2) {
                // 数字彩
                if ($v['beishu'] === 1) {
                    // 1期
                    $issue = OrderNum::getValByWhere(['order_id' => $v['id']], 'number');
                    $v['issue'] = $issue;
                } else {
                    $v['issue'] = '追期';
                }
            } else {
                // 体彩
                $v['issue'] = '';
            }

            // 获取前台接口自定义彩种类型(该类型为接口自定义)
            $code = Lottery::getCodeById($v['lottery_id']);
            switch ($code) {
                case Config::ZC_CODE: // 足彩
                    $v['type'] = 1;
                    break;
                case Config::LC_CODE: // 篮彩
                    $v['type'] = 2;
                    break;
                case Config::BJ_CODE: // 北京单场
                    $v['type'] = 3;
                    break;
                case Config::P3_CODE: // 排三
                    $v['type'] = 4;
                    break;
                case Config::P5_CODE: // 排五
                    $v['type'] = 5;
                    break;
                case Config::PC_CODE: // 葡彩
                    $v['type'] = 6;
                    break;
                case Config::AO_CODE: // 澳彩
                    $v['type'] = 7;
                    break;
                case Config::FT_CODE: // 飞艇
                    $v['type'] = 8;
                    break;
            }

            // 串
            $v['chuan'] = preg_replace('/\["|"]/', '', $v['chuan']);
            // 获取跟单人数和跟单金额
            $findData = self::getFieldsByWhere(['follow_order_id' => $v['id']], 'COUNT(`id`) AS peopleCount,SUM(`amount`) as followBetMoney');
            if (!empty($findData)) {
                // 跟单人数
                $v['number'] = (int)$findData['peopleCount'];
                // 跟单总金额
                $v['countMoney'] = (int)$findData['followBetMoney'];
            } else {
                $v['number'] = 0;
                $v['countMoney'] = 0;
            }

            unset(
                $v['lottery_id'], // 删除彩种ID
                $v['order_type'], // 删除订单类型
                $v['beishu'] // 删除期数
            );
        }

        return $data;
    }

    /**
     * @desc 体彩的统计数据
     * @auther LiBin
     * @param $get
     * @return float|string
     * @date 2019-04-10
     */
    public function countNumber($get)
    {
        $where[] = ['member_id', '=', UID];
        //订单列表分为三种: 1.下单 2.跟单 3.推单
        if (!empty($get['pay_type'])) {
            $where[] = ['pay_type', '=', $get['pay_type']];
        }

        //判断彩种类型 1.体彩 2.数字彩
        $where[] = ['order_type', '=', 1];

        //中奖,未中奖筛选
        if (!empty($get['state'])) {
            if ($get['state'] == 1) {//未中奖
                $where[] = ['status', '=', 3];
            }

            if ($get['state'] == 2) {//中奖
                $where[] = ['status', '=', 4];
            }

            if ($get['state'] == 3) {//其他
                $where[] = ['status', 'in', '1,2,5'];
            }
        }
        //近三天或近一周
        if (!empty($get['date'])) {
            //近三天
            if ($get['date'] == 1) {
                $startime = date('Y-m-d', strtotime('-3 day')) . ' 00:00:00';
                $endtime = date('Y-m-d') . ' 23:59:59';
                $where[] = ['create_time', 'between', [$startime, $endtime]];
            }
            //近一周
            if ($get['date'] == 2) {
                $startime = date("Y-m-d H:i:s", strtotime("-1weeks", strtotime(date('Y-m-d'))));
                $endtime = date('Y-m-d') . ' 23:59:59';
                $where[] = ['create_time', 'between', [$startime, $endtime]];
            }
        }

        if (!empty($get['starttime']) && !empty($get['endtime'])) {//判断时间段
            $startime = $get['starttime'] . ' 00:00:00';
            $endtime = $get['endtime'] . ' 23:59:59';
            $where[] = ['create_time', 'between', [$startime, $endtime]];
        }

        return self::where($where)->count('id');
    }

    /**
     * @desc 数字彩的统计数据
     * @auther LiBin
     * @param $get
     * @return float|string
     * @date 2019年5月18日
     */
    public function szCountNumber($get)
    {
        $where[] = ['a.member_id', '=', UID];
        //订单列表分为三种: 1.下单 2.跟单 3.推单
        if (!empty($get['pay_type'])) {
            $where[] = ['a.pay_type', '=', $get['pay_type']];
        }

        //判断彩种类型 1.体彩 2.数字彩
        $where[] = ['order_type', '=', 2];
        //中奖,未中奖筛选
        if (!empty($get['state'])) {
            if ($get['state'] == 1) {//未中奖
                $where[] = ['b.status', '=', 2];
            }

            if ($get['state'] == 2) {//中奖
                $where[] = ['b.status', 'between', '3,4'];
            }

            if ($get['state'] == 3) {//其他
                $where[] = ['b.status', 'in', '1'];
            }
        }
        //近三天或近一周
        if (!empty($get['date'])) {
            //近三天
            if ($get['date'] == 1) {
                $startime = date('Y-m-d', strtotime('-3 day')) . ' 00:00:00';
                $endtime = date('Y-m-d') . ' 23:59:59';
                $where[] = ['a.create_time', 'between', [$startime, $endtime]];
            }
            //近一周
            if ($get['date'] == 2) {
                $startime = date("Y-m-d H:i:s", strtotime("-1weeks", strtotime(date('Y-m-d'))));
                $endtime = date('Y-m-d') . ' 23:59:59';
                $where[] = ['a.create_time', 'between', [$startime, $endtime]];
            }
        }

        if (!empty($get['starttime']) && !empty($get['endtime'])) {//判断时间段
            $startime = $get['starttime'] . ' 00:00:00';
            $endtime = $get['endtime'] . ' 23:59:59';
            $where[] = ['a.create_time', 'between', [$startime, $endtime]];
        }
        return self::alias('a')
            ->join('order_num b', 'a.id = b.order_id')
            ->where($where)
            ->count('a.id');
    }

    /**
     * 获取订单数据
     *
     * @param $where // 赛选条件
     * @return array|null|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author LiBin
     * @api *
     */
    public function getOrder($where)
    {
        return self::where($where)->find();
    }

    /**
     * @desc 统计订单数据
     * @auther LiBin
     * @param $where
     * @return float|string
     * @date 2019-04-13
     */
    public function getCountOrder($where)
    {
        return self::where($where)->count('id');
    }

    /**
     * 获取一周红榜单
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getRedList()
    {
        // 6天前日期
        $day6 = strtotime('-6 days');
        $date6 = Helper::timeFormat($day6, 's');
        $now = Helper::timeFormat(time(), 's');
        // 红单榜统计
        $data = self::where('create_time', 'between time', [$date6, $now])
            ->where('status', 4)// 已中奖
            ->field([
                'member_id',
                'COUNT(id)' => 'winPrizeNum', // 中奖单数
            ])
            ->group('member_id')
            ->order('winPrizeNum DESC')
            ->limit(20)
            ->select()
            ->toArray();
        foreach ($data as &$item) {
            $betNum6 = self::where('create_time', 'between time', [$date6, $now])
                ->where('member_id', $item['member_id'])
                ->where('status', 'between', [3, 4])// 未中奖|已中奖
                ->count();
            $memberData = Member::getFieldsByWhere(['id' => $item['member_id']], 'chn_name,photo');
            // 头像
            $item['photo'] = '';
            if ((int)$memberData['photo'] !== 0) {
                $item['photo'] = Helper::getCurrentHost() . Attach::getPathByAttachId($memberData['photo']);
            }

            // 昵称
            $item['chn_name'] = $memberData['chn_name'];
            // 用户ID
            $item['uid'] = $item['member_id'];
            $item['betNum'] = is_string($betNum6) ? 0 : $betNum6;
            unset($item['member_id']);
        }

        return $data;
    }

    /**
     * @desc 获取连红榜
     * @author CleverStone
     * @return array
     * @throws \Exception
     * @date 2019-04-15
     */
    public function getEvenRed()
    {
        // 一月时间段
        $day30 = strtotime('-30 days');
        $date30 = Helper::timeFormat($day30, 's');
        $now = Helper::timeFormat(time(), 's');
        // 获取注单列表
        $data = self::where('create_time', 'between time', [$date30, $now])
            ->field([
                'member_id', // 会员ID
                'status', // 状态 0：待出票 1：已出票 2：待开奖 3：未中奖 4：已中奖
                'sup_order_time', // 推单时间
            ])
            ->order('id ASC')
            ->select()
            ->toArray();
        // 获取每个会员的订单连续状态
        $tempArr = [];
        foreach ($data as $item) {
            $tempArr[$item['member_id']][] = $item['status'];
        }

        // 计算每个会员的连红次数
        // 连红算法
        $concatSize = [];
        foreach ($tempArr as $uid => $statusArr) {
            $i = 0;
            foreach ((array)$statusArr as $status) {
                if ($status === 4) {
                    if (!isset($concatSize[$uid]) || !isset($concatSize[$uid][$i])) {
                        $concatSize[$uid][$i] = 1;
                    } else {
                        $concatSize[$uid][$i]++;
                    }
                } else {
                    $i++;
                    $concatSize[$uid][$i] = 0;
                }
            }
        }

        // 数据组装
        $returnData = [];
        foreach ($concatSize as $uid => $item) {
            rsort($item);
            reset($item);
            $top = current($item);
            // 连赢数大于2
            if ($top >= 2) {
                $memberData = Member::getFieldsByWhere(['id' => $uid], ['chn_name', 'photo']);
                $photo = $memberData['photo'] ? Helper::getCurrentHost() . Attach::getPathByAttachId($memberData['photo']) : ''; // 头像
                $nickName = $memberData['chn_name']; // 昵称
                $winning = $top; // 连赢次数
                array_push($returnData, [
                    'chn_name' => $nickName, // 昵称
                    'photo' => $photo, // 头像
                    'winning' => $winning, // 连赢数
                    'uid' => $uid, // 会员ID
                ]);
            }
        }

        // 根据连赢数降序
        if (!empty($returnData)) {
            $winningGather = array_column($returnData, 'winning');
            array_multisort($winningGather, SORT_DESC, $returnData);
        }

        // 最多20条数据
        return array_slice($returnData, 0, 20);
    }

    /**
     * @desc 盈利榜
     * @author CleverStone
     * @throws \Exception
     * @date 2019-04-15
     */
    public function profitList()
    {
        // 7天的时间段
        $day7 = strtotime('-7 days');
        $date7 = Helper::timeFormat($day7, 's');
        $now = Helper::timeFormat(time(), 's');
        // 获取订单列表
        $data = self::where('create_time', 'between time', [$date7, $now])// 一周内
        ->where('status', 4)// 已中奖
        ->field([
            'member_id', // 会员ID
            'amount', // 下注金额
            'bonus', // 奖金
            'bounty', // 嘉奖
            'follow_order_commission', // 推单佣金
            'exit_account', // 退还金额
        ])
            ->select()
            ->toArray();

        // 计算每个会员回报率
        // 回报率算法
        $tempArr = [];
        foreach ($data as $item) {
            // 总盈利 = 奖金 + 嘉奖 + 推单佣金
            $totalWinMoney = $item['bonus'] + $item['bounty'] + $item['follow_order_commission'];
            // 总下注金额 = 下注金额 - 退还金额
            $totalAmount = $item['amount'] - $item['exit_account'];
            if (!isset($tempArr[$item['member_id']])) {
                $tempArr[$item['member_id']]['betMoney'] = $totalAmount;
                $tempArr[$item['member_id']]['winMoney'] = $totalWinMoney;
            } else {
                $tempArr[$item['member_id']]['betMoney'] += $totalAmount;
                $tempArr[$item['member_id']]['winMoney'] += $totalWinMoney;
            }
        }

        // 组装数据
        $returnData = [];
        foreach ($tempArr as $uid => $value) {
            $memberData = Member::getFieldsByWhere(['id' => $uid], ['chn_name', 'photo']);
            $photo = $memberData['photo'] ? Helper::getCurrentHost() . Attach::getPathByAttachId($memberData['photo']) : ''; // 头像
            $nickName = $memberData['chn_name']; // 昵称
            $tempRate = $value['winMoney'] / $value['betMoney'] * 100;
            $rate = sprintf('%01.2f', $tempRate) . '%';
            array_push($returnData, [
                'rateReturn' => $rate, // 回报率
                'chn_name' => $nickName, // 昵称
                'photo' => $photo, // 头像
                'uid' => $uid, // 会员ID
                'sort' => round($value['winMoney'] / $value['betMoney'] * 100)
            ]);
        }

        // 回报率排序
        if (!empty($returnData)) {
            $tempGather = array_column($returnData, 'sort');
            array_multisort($tempGather, SORT_DESC, $returnData);
        }

        return array_slice($returnData, 0, 20);
    }

    /**
     * 获取红单比和回报率
     *
     * @param $day // 多少天
     * @param $userid // 用户ID
     * @param bool $isWin // 是否是红单数
     * @return array
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getRedRateAndRateReturn($day, $userid, $isWin = false)
    {
        // 获取时间段
        $frontTime = strtotime('-' . $day . ' days');
        $frontDate = Helper::timeFormat($frontTime, 's');
        $now = Helper::timeFormat(time(), 's');
        // 组装查询SQL
        $model = self::where('create_time', 'between time', [$frontDate, $now]); // 时间段
        if ($isWin) {
            $model = $model->where('status', 4); // 已中奖
        } else {
            $model = $model->where('status', 'between', [3, 4]); // 未中奖/已中奖
        }

        // 查询字段
        if (!$isWin) {
            $fields = [
                'SUM(amount)' => 'betAmount', // 总注单金额
                'SUM(bonus)' => 'totalBonus', // 总奖金
                'SUM(bounty)' => 'totalBounty', // 总嘉奖
                'SUM(follow_order_commission)' => 'totalRate', // 总推单佣金
                'SUM(exit_account)' => 'totalExit', // 总退还金额
                'COUNT(id)' => 'count', // 单数
            ];
        } else {
            $fields = [
                'COUNT(id)' => 'count', // 单数
            ];
        }

        $data = $model->where('member_id', $userid)
            ->field($fields)
            ->find()
            ->toArray();
        // 数据组装
        if (!$isWin) {
            // 总奖金 = 奖金 + 佣金 + 嘉奖
            $data['totalBonus'] = $data['totalBonus'] + $data['totalRate'] + $data['totalBounty'];
            // 总投注 = 投注金额 - 退还金额
            $data['betAmount'] = $data['betAmount'] - $data['totalExit'];
            unset(
                $data['totalBounty'],
                $data['totalRate'],
                $data['totalExit']
            );
        }

        return $data;
    }

    /**
     * 红单详情
     *
     * @param $uid // 点击的会员ID
     * @param $userid //会员ID
     * @return array|string
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getRedDetail($uid, $userid)
    {
        // 验证会员是否存在
        $memberData = Member::getFieldsByWhere(['id' => $uid], 'id,photo,chn_name');
        if (empty($memberData)) {
            return '抱歉, 会员账户不存在';
        }

        $data = [];
        // 头像
        $data['photo'] = '';
        if ($memberData['photo'] !== 0) {
            $data['photo'] = Helper::getCurrentHost() . Attach::getPathByAttachId($memberData['photo']);
        }

        // 昵称
        $data['chn_name'] = $memberData['chn_name'];
        $model = new Attention;
        // 我的粉丝数量
        $data['fans_count'] = $model->getMyFansCount($uid);
        // 关注数量
        $data['attention_count'] = $model->getMyAttentionCount($uid);
        // 获取我的近7日的注单中率
        $data7 = self::getRedRateAndRateReturn(6, $uid);
        if (empty($data7)) {
            $day7BetNum = 0;
        } else {
            $day7BetNum = $data7['count'];
        }
        // 注单数
        $data['day7BetNum'] = $day7BetNum;
        // 中奖数
        $data7Win = self::getRedRateAndRateReturn(6, $uid, true);
        if (empty($data7Win)) {
            $day7WinNum = 0;
        } else {
            $day7WinNum = $data7Win['count'];
        }

        $data['day7WinNum'] = $day7WinNum;
        // 7日命中率
        if($day7WinNum == 0 || $day7BetNum == 0){
            $data['day7hit'] = '0%';
        }else{
            $data['day7hit'] = round(($day7WinNum / $day7BetNum) * 100) . '%';
        }
        // 投注总额
        $day7BetAmount = $data7['betAmount'];
        // 奖金总额
        $day7WinAmount = $data7['totalBonus'];
        // 回报率
        if (!empty($day7BetAmount)) {
            $data['day7RateReturn'] = round(($day7WinAmount / $day7BetAmount) * 100) . '%';
        } else {
            $data['day7RateReturn'] = 0 . '%';
        }
        // 获取我的近15日的注单中率
        $data15 = self::getRedRateAndRateReturn(14, $uid);
        if (empty($data15)) {
            $day15BetNum = 0;
        } else {
            $day15BetNum = $data15['count'];
        }
        // 注单数
        $data['day15BetNum'] = $day15BetNum;
        // 中奖数
        $data15Win = self::getRedRateAndRateReturn(14, $uid, true);
        if (empty($data15Win)) {
            $day15WinNum = 0;
        } else {
            $day15WinNum = $data15Win['count'];
        }

        $data['day15WinNum'] = $day15WinNum;
        // 投注总额
        $day15BetAmount = $data15['betAmount'];
        // 奖金总额
        $day15WinAmount = $data15['totalBonus'];
        // 回报率
        if (!empty($day15BetAmount)) {
            $data['day15RateReturn'] = round(($day15WinAmount / $day15BetAmount) * 100) . '%';
        } else {
            $data['day15RateReturn'] = 0 . '%';
        }
        // 获取我的近30日的注单中率
        $data30 = self::getRedRateAndRateReturn(29, $uid);
        if (empty($data30)) {
            $day30BetNum = 0;
        } else {
            $day30BetNum = $data30['count'];
        }
        // 注单数
        $data['day30BetNum'] = $day30BetNum;
        // 中奖数
        $data30Win = self::getRedRateAndRateReturn(29, $uid, true);
        if (empty($data30Win)) {
            $day30WinNum = 0;
        } else {
            $day30WinNum = $data30Win['count'];
        }

        $data['day30WinNum'] = $day30WinNum;
        // 投注总额
        $day30BetAmount = $data30['betAmount'];
        // 奖金总额
        $day30WinAmount = $data30['totalBonus'];
        // 回报率
        if (!empty($day30BetAmount)) {
            $data['day30RateReturn'] = round(($day30WinAmount / $day30BetAmount) * 100) . '%';
        } else {
            $data['day30RateReturn'] = 0 . '%';
        }
        // 获取当前跟单总人数
        $where['pay_type'] = 3;
        $where['sup_order_state'] = 1;
        $where['status'] = 2;
        $followNum = self::where($where)->sum('follows');
        // 获取该用户跟单人数
        $where['member_id'] = $uid;
        $userfollowNum = self::where($where)->sum('follows');
        // 跟单人气
        if($userfollowNum == 0 || $followNum == 0){
            $data['followRatio'] = '0%';
        }else{
            $data['followRatio'] = round(($userfollowNum / $followNum) * 100) . '%';
        }
        
        //获取关注状态
        $attention = new Attention();
        $data['type'] = $attention->getAttentionType($userid, $uid);

        return $data;
    }

    /**
     * 我的关注列表
     *
     * @param $data // 关注会员列表
     * [
     *   [
     *   'id', // 关注表主键
     *   'member_attention_id', // 被关注人ID
     *   'create_at' // 创建时间
     *   ],
     *  ...
     * ]
     * @return array
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getMyAttentionData($data)
    {
        if (empty($data)) {
            return [];
        }

        $ids = array_column($data, 'member_attention_id');
        $result = [];
        foreach ($ids as $k => $uid) {
            $memberData = Member::getFieldsByWhere(['id' => $uid], 'chn_name,photo');
            // 头像
            $result[$k]['photo'] = '';
            if ((int)$memberData['photo'] !== 0) {
                $result[$k]['photo'] = Helper::getCurrentHost() . Attach::getPathByAttachId($memberData['photo']);
            }

            // 昵称
            $result[$k]['chn_name'] = $memberData['chn_name'];
            $result[$k]['uid'] = $uid;
            $data7 = self::getRedRateAndRateReturn(6, $uid);
            $result[$k]['day7BetNum'] = $data7['count'];
            $data7Win = self::getRedRateAndRateReturn(6, $uid, true);
            $result[$k]['day7WinNum'] = $data7Win['count'];
            $data30 = self::getRedRateAndRateReturn(29, $uid);
            // 投注总额
            $day30BetAmount = $data30['betAmount'];
            // 奖金总额
            $day30WinAmount = $data30['totalBonus'];
            // 回报率
            if (!empty($day30BetAmount)) {
                $result[$k]['day30RateReturn'] = round(($day30WinAmount / $day30BetAmount) * 100) . '%';
            } else {
                $result[$k]['day30RateReturn'] = '0' . '%';
            }
        }

        return $result;
    }

    /**
     * @desc 热门搜索列表
     * @auther LiBin
     * @param $limit
     * @return array|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-04-18
     */
    public function getPopularityOrder($limit)
    {
        $where[] = ['sup_order_state', '=', 1];//推单状态
        $where[] = ['pay_type', '=', 3];//订单类型 购买方式1：自购 2：跟单 3: 推单
        //获取热门搜索
        $data = self::where($where)->field(['member_id'])->order('follows desc')->group('member_id')->limit($limit)->select();
        $rdata = [];
        if (!empty($data)) {
            $memberId = '';
            foreach ($data as $va) {
                $memberId .= $va['member_id'] . ',';
            }
            $memberId = trim($memberId, ',');//会员ID
            $member = new Member();
            $memberWhere[] = ['id', 'in', $memberId];
            $rdata = $member->getMemberDataList($memberWhere, ['id', 'chn_name']);
        }

        return $rdata;
    }

    /**
     * 排三排五下单
     *
     * @param $post // POST数据
     * 数据详情:
     * {
     *   amount: 10000, // 投注总金额
     *   lottery_id: 3, // 彩种ID
     *   number_count: 2, // 期数
     *   is_follow: 1, // 是否追号, 0: 否  1: 是
     *   total_zhu: 300, // 总注数
     *   beizhu: 数字彩下单1元, // 备注
     *   current_number: 19002, // 当前彩期
     *   bet_content: [ // 投注项
     *              {
     *                  "play":"zhihe", // 玩法: 'zhipu',"zusanbao","zusanhe","zuliubao","zuliuhe","zuliudantuo",
     *                  "bet":["1", "2", "3", "12"], // 投注详情
     *                  "zhu":10, // 注数
     *                  "amount":125 // 投注金额
     *              },
     *              {...}
     *              ],
     *  follow_detail: [ // 追号详情
     *                  {
     *                   "number":19002, // 期号
     *                   "multiple":2, // 单期倍数
     *                   "amount": 1000, // 单期投注金额
     *                  },
     *                  ...
     *                ]
     * }
     * @return bool|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */

    public function nlOrder($post)
    {
        try {
            Db::startTrans();
            // 检查彩种是否存在
            $lotteryCode = Lottery::getValByWhere(['id' => $post['lottery_id']], 'code');
            if (empty($lotteryCode)) {
                trigger_error('彩种不存在', E_USER_WARNING);
            }

            // 检查彩期是否停售
            $checkResult = true;
            switch ($lotteryCode) {
                case Config::P3_CODE: // 排三
                    $ctype = 1; // 数字彩类型标识
                    $checkResult = PlOpen::checkSaleStatus($post['current_number'], 1);
                    break;
                case Config::P5_CODE: // 排五
                    $ctype = 2; // 数字彩类型标识
                    $checkResult = PlOpen::checkSaleStatus($post['current_number'], 2);
                    break;
                default:
                    trigger_error('彩种下单类型错误', E_USER_WARNING);
            }

            if (!$checkResult) {
                trigger_error('彩种已停售', E_USER_WARNING);
            }

            // 订单下单数据容器
            $data = [];
            // 下注金额
            $betAmount = (float)$post['amount'];
            // 获取会员彩金和余额(排他锁)
            $memberData = Member::where('id', UID)
                ->field([
                    'balance', // 余额
                    'hadsel', // 彩金
                    'role', // 角色
                    'username', // 用户名
                    'chn_name', // 昵称
                    'photo', // 头像|头标
                ])
                ->lock(true)
                ->find()
                ->toArray();
            $hadsel = floatval($memberData['hadsel']);
            $balance = floatval($memberData['balance']);
            // 判断彩金是否大于下注金额
            if ($hadsel >= $betAmount) {
                // 减去下注金额
                $newHadsel = $hadsel - $betAmount;
                // 预支付彩金写入冻结资金
                Member::where('id', UID)->setInc('frozen_capital', $betAmount);
                // 保存支付后的彩金金额
                Member::where('id', UID)->setField([
                    'hadsel' => $newHadsel // 新彩金
                ]);
                // 增加订单彩金字段
                $data['pay_hadsel'] = $betAmount;

            } elseif ($balance + $hadsel >= $betAmount) {  // 判断余额加彩金是否大于等于下注金额
                // 先扣完彩金, 再扣余额, 并计算剩余应支付金额
                $surplusBetAmount = $betAmount - $hadsel;
                // 扣住余额, 获取剩余余额
                $surplusBalance = $balance - $surplusBetAmount;
                // 预支付彩金和余额写入冻结资金
                Member::where('id', UID)->setInc('frozen_capital', $betAmount);
                // 保存支付后的彩金和余额
                Member::where('id', UID)->setField([
                    'hadsel' => 0, // 彩金已扣完, 剩余0,
                    'balance' => $surplusBalance // 保存剩余余额
                ]);

                // 增加订单彩金字段
                $data['pay_hadsel'] = $hadsel;
                // 增加订单余额字段
                $data['pay_balance'] = $surplusBetAmount;
            } else {
                // 提示余额不足
                trigger_error('您的余额不足', E_USER_WARNING);
            }

            // 订单号
            $data['order_no'] = $this->orderNum = Helper::orderNumber();
            $data['member_id'] = UID;
            // 彩种ID
            $data['lottery_id'] = $post['lottery_id'];
            // 期数
            $data['beishu'] = $post['number_count'];
            // 总注数
            $data['zhu'] = $post['total_zhu'];
            // 总投注金额
            $data['amount'] = $post['amount'];
            // 出票状态
            $data['status'] = 0; // 0: 待出票
            // 备注
            $data['beizhu'] = $post['beizhu'];
            // 投注项
            $data['bet_content'] = is_string($post['bet_content']) ? $post['bet_content'] : (Helper::jsonEncode($post['bet_content']));
            // 获取投注项总金额
            $contentArr = Helper::jsonDecode($data['bet_content']);
            if (!$contentArr) {
                trigger_error('投注项格式错误,错误码:4276');
            }

            $contentTotalAmount = array_sum(array_column($contentArr, 'amount'));
            // 下单时间
            $data['create_time'] = Helper::timeFormat(time(), 's');
            // 是否追号 0:停止 1:继续
            $data['is_yh'] = $post['is_follow'];
            $isMoni = Member::getValByWhere(['id' => UID], 'is_moni');
            // 是否模拟 1 是   0 非模拟
            $data['is_moni'] = ($isMoni === 0 ? 1 : 0);
            // 购买方式 1: 自购  2: 跟单  3: 推单
            $data['pay_type'] = 1;
            // 用户账号
            $data['username'] = Member::getValByWhere(['id' => UID], 'username');
            // 订单类型(1: 体彩(默认) 2: 数字彩)
            $data['order_type'] = 2;
            // 存储订单表
            $orderResult = self::quickCreate($data);
            if (!$orderResult) {
                trigger_error('下单失败, 错误码2970', E_USER_WARNING);
            }

            // 追期详情
            $followDetail = is_string($post['follow_detail']) ? Helper::jsonDecode($post['follow_detail']) : $post['follow_detail'];
            // 判断追期是否存在重复
            $followNumbers = array_column($followDetail, 'number');
            $originCount = count($followNumbers);
            $uniqueCount = count(array_unique($followNumbers));
            if ($originCount !== $uniqueCount) {
                trigger_error('追期号不能重复');
            }

            // 下注金额校验
            $computeTotal = array_sum(array_column($followDetail, 'amount'));
            if ($betAmount !== floatval($computeTotal)) {
                trigger_error('投注金额异常,错误码:4117');
            }

            foreach ($followDetail as $item) {
                // 倍数校验
                $computeBeishu = floatval($item['amount']) / $contentTotalAmount;
                if ($computeBeishu != $item['multiple']) {
                    trigger_error('单期倍数和投注金额不符,错误码:4124');
                }

                // 写入追期详情表
                $orderNumResult = OrderNum::quickCreate([
                    'number' => $item['number'], // 期号
                    'order_id' => $orderResult, // 订单ID
                    'multiple' => $item['multiple'], // 单期倍数
                    'amount' => $item['amount'], // 单期投注金额
                    'ctype' => $ctype, // 数字彩类型 1: 排三  2: 排五
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                ]);

                if (!$orderNumResult) {
                    trigger_error('下单失败, 错误码2986', E_USER_WARNING);
                }
            }

            // 写入并推送消息
            Helper::logAndPushMsg(
                "用户{$memberData['username']}注单成功, 注单单号: {$data['order_no']}", // 消息内容
                $memberData['chn_name'], // 昵称
                $memberData['username'], // 用户账号
                ($memberData['role'] === 1) ? 3 : 2, // 消息类型
                2, // 内容类型
                $memberData['photo'] ?: 0 // 用户头像或头标
            );

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 葡彩,澳彩下单
     *
     * @param $post // POST数据
     * 数据详情:
     * {
     *   amount: 10000, // 投注总金额
     *   lottery_id: 3, // 彩种ID
     *   number_count: 2, // 期数
     *   is_follow: 1, // 是否追号, 0: 否  1: 是
     *   total_zhu: 300, // 总注数
     *   beizhu: 数字彩下单1元, // 备注
     *   current_number: 19002, // 当前彩期
     *   bet_content: [ // 投注项
     *              {
     *                  "play":"aozhipu", // 玩法: 'aozhipu', 'aozhihe', 'puzhipu',
     *                  "bet":["1", "2", "3", "12"], // 投注详情
     *                  "zhu":10, // 注数
     *                  "amount":125 // 投注金额
     *              },
     *              {...}
     *              ],
     *  follow_detail: [ // 追号详情
     *                  {
     *                   "number":19002, // 期号
     *                   "multiple":2, // 单期倍数
     *                   "amount": 1000, // 单期投注金额
     *                  },
     *                  ...
     *                ]
     * }
     * @return bool|string
     */

    public function plOrder($post)
    {
        try {
            Db::startTrans();
            // 检查彩种是否存在
            $lotteryCode = Lottery::getValByWhere(['id' => $post['lottery_id']], 'code');
            if (empty($lotteryCode)) {
                trigger_error('彩种不存在', E_USER_WARNING);
            }

            // 检查彩期是否停售
            $checkResult = true;
            switch ($lotteryCode) {
                case Config::AO_CODE: // 澳彩
                    $ctype = 3; // 数字彩类型标识
                    $checkResult = PlOpen::checkSaleStatus($post['current_number'], 3);
                    break;
                case Config::PC_CODE: // 葡彩
                    $ctype = 4; // 数字彩类型标识
                    $checkResult = PlOpen::checkSaleStatus($post['current_number'], 4);
                    break;
                default:
                    trigger_error('彩种下单类型错误', E_USER_WARNING);
            }

            if (!$checkResult) {
                trigger_error('彩种已停售', E_USER_WARNING);
            }

            // 订单下单数据容器
            $data = [];
            // 下注金额
            $betAmount = (float)$post['amount'];
            // 获取会员彩金和余额(排他锁)
            $memberData = Member::where('id', UID)
                ->field([
                    'balance', // 余额
                    'hadsel', // 彩金
                    'role', // 角色
                    'username', // 用户名
                    'chn_name', // 昵称
                    'photo', // 头像|头标
                ])
                ->lock(true)
                ->find()
                ->toArray();
            $hadsel = floatval($memberData['hadsel']);
            $balance = floatval($memberData['balance']);
            // 判断彩金是否大于下注金额
            if ($hadsel >= $betAmount) {
                // 减去下注金额
                $newHadsel = $hadsel - $betAmount;
                // 保存支付后的彩金金额
                Member::where('id', UID)->setField([
                    'hadsel' => $newHadsel // 新彩金
                ]);
                // 增加订单彩金字段
                $data['pay_hadsel'] = $betAmount;
                $data['pay_balance'] = 0;

            } elseif ($balance + $hadsel >= $betAmount) {  // 判断余额加彩金是否大于等于下注金额
                // 先扣完彩金, 再扣余额, 并计算剩余应支付金额
                $surplusBetAmount = $betAmount - $hadsel;
                // 扣住余额, 获取剩余余额
                $surplusBalance = $balance - $surplusBetAmount;
                // 保存支付后的彩金和余额
                Member::where('id', UID)->setField([
                    'hadsel' => 0, // 彩金已扣完, 剩余0,
                    'balance' => $surplusBalance // 保存剩余余额
                ]);

                // 增加订单彩金字段
                $data['pay_hadsel'] = $hadsel;
                // 增加订单余额字段
                $data['pay_balance'] = $surplusBetAmount;
            } else {
                // 提示余额不足
                trigger_error('您的余额不足', E_USER_WARNING);
            }

            // 订单号
            $data['order_no'] = $this->orderNum = Helper::orderNumber();
            $data['member_id'] = UID;
            // 彩种ID
            $data['lottery_id'] = $post['lottery_id'];
            // 期数
            $data['beishu'] = $post['number_count'];
            // 总注数
            $data['zhu'] = $post['total_zhu'];
            // 总投注金额
            $data['amount'] = $post['amount'];
            // 出票状态
            $data['status'] = 2; // 2: 待开奖
            // 备注
            $data['beizhu'] = $post['beizhu'];
            // 投注项
            $data['bet_content'] = is_string($post['bet_content']) ? $post['bet_content'] : (Helper::jsonEncode($post['bet_content']));
            // 获取投注项总金额
            $contentArr = Helper::jsonDecode($data['bet_content']);
            if (!$contentArr) {
                trigger_error('投注项格式错误,错误码:4276');
            }

            $contentTotalAmount = array_sum(array_column($contentArr, 'amount'));
            // 下单时间
            $data['create_time'] = Helper::timeFormat(time(), 's');
            // 是否追号 0:停止 1:继续
            $data['is_yh'] = $post['is_follow'];
            $isMoni = Member::getValByWhere(['id' => UID], 'is_moni');
            // 是否模拟 1 是   0 非模拟
            $data['is_moni'] = ($isMoni === 0 ? 1 : 0);
            // 购买方式 1: 自购  2: 跟单  3: 推单
            $data['pay_type'] = 1;
            // 用户账号
            $data['username'] = Member::getValByWhere(['id' => UID], 'username');
            // 订单类型(1: 体彩(默认) 2: 数字彩)
            $data['order_type'] = 2;
            // 支付状态 -1:未支付 0:支付中 1:已支付
            $data['pay_status'] = 1;
            // 支付时间
            $data['pay_time'] = date('Y-m-d H:i:s');
            // 存储订单表
            $orderResult = self::quickCreate($data);
            if (!$orderResult) {
                trigger_error('下单失败, 错误码2970', E_USER_WARNING);
            }

            // 追期详情
            $followDetail = is_string($post['follow_detail']) ? Helper::jsonDecode($post['follow_detail']) : $post['follow_detail'];
            // 追期号校验去重
            $followNumbers = array_column($followDetail, 'number');
            $originCount = count($followNumbers);
            $uniqueCount = count(array_unique($followNumbers));
            if ($originCount !== $uniqueCount) {
                trigger_error('追期号不能重复');
            }

            // 下注金额校验
            $computeTotal = array_sum(array_column($followDetail, 'amount'));
            if ($betAmount !== floatval($computeTotal)) {
                trigger_error('投注金额异常,错误码:4309');
            }

            foreach ($followDetail as $item) {
                // 倍数校验
                $computeBeishu = floatval($item['amount']) / $contentTotalAmount;
                if ($computeBeishu != $item['multiple']) {
                    trigger_error('单期倍数和投注金额不符,错误码:4323');
                }

                // 写入追期详情表
                $orderNumResult = OrderNum::quickCreate([
                    'number' => $item['number'], // 期号
                    'order_id' => $orderResult, // 订单ID
                    'multiple' => $item['multiple'], // 单期倍数
                    'amount' => $item['amount'], // 单期投注金额
                    'ctype' => $ctype, // 数字彩类型 3: 澳彩  4: 葡彩
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                ]);

                if (!$orderNumResult) {
                    trigger_error('下单失败, 错误码2986', E_USER_WARNING);
                }
            }

            // 写入资金流水
            if ($isMoni === 1) {
                $deductSel = (float)$data['pay_hadsel'];
                $deductMoney = (float)$data['pay_balance'];
                // 写入彩金记录
                if (!empty($deductSel)) {
                    FundLog::quickCreate([
                        'member_id' => UID, // 会员ID
                        'money' => $deductSel, // 变动彩金
                        'front_money' => $hadsel, // 变动前彩金
                        'later_money' => bcsub($hadsel, $deductSel, 2), // 变动后彩金
                        'type' => 3, // 购彩
                        'remark' => '注单出票扣除彩金' . sprintf('%01.2f', $deductSel) . '元',
                        'create_time' => Helper::timeFormat(time(), 's'),
                        'update_time' => Helper::timeFormat(time(), 's'),
                        'order_id' => $orderResult,
                        'identify' => $memberData['role'],
                        'username' => $data['username'],
                    ]);
                }

                // 写入余额记录
                if (!empty($deductMoney)) {
                    FundLog::quickCreate([
                        'member_id' => UID, // 会员ID
                        'money' => $deductMoney, // 变动余额
                        'front_money' => $balance, // 变动前余额
                        'later_money' => bcsub($balance, $deductMoney, 2), // 变动后彩金
                        'type' => 3, // 购彩
                        'remark' => '注单出票扣除余额' . sprintf('%01.2f', $deductMoney) . '元',
                        'create_time' => Helper::timeFormat(time(), 's'),
                        'update_time' => Helper::timeFormat(time(), 's'),
                        'order_id' => $orderResult,
                        'identify' => $memberData['role'],
                        'username' => $data['username'],
                    ]);
                }
            }

            // 写入并推送消息
            Helper::logAndPushMsg(
                "用户{$memberData['username']}注单成功, 注单单号: {$data['order_no']}", // 消息内容
                $memberData['chn_name'], // 昵称
                $memberData['username'], // 用户账号
                ($memberData['role'] === 1) ? 3 : 2, // 消息类型
                2, // 内容类型
                $memberData['photo'] ?: 0 // 用户头像或头标
            );

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 幸运飞艇下单
     *
     * @param $post // POST数据
     * 数据详情:
     * {
     *   lottery_id: 5, // 彩种ID
     *   total_zhu: 300, // 总注数
     *   amount: 10000, // 投注总金额
     *   bet_content: [ // 投注项
     *   {
     *  "play": "lm",
     *  "bet": [
     *  {"type": "g","value": ["odds|1|1.998", "big|2|1.998", "long|3|1.998", "even|4|1.998", "small|5|1.998"]},
     *  {"type": "y","value": ["big|6|1.998", "small|7|1.998"]},
     *  {"type": "3","value": ["small|8|1.998"]},
     *  {"type": "4","value": ["big|9|1.998"]},
     *  {"type": "5","value": ["even|10|1.998", "small|11|1.998"]}
     *  ]
     *  },
     *
     *  {
     *  "play": "gh",
     *  "bet": [
     *  {"type": "gyh","value": ["big|15|2.2", "even|16|2.19", "7|17|14.5", "9|18|10.5", "18|1900|42.5"]}
     *  ]
     *  },
     *
     *  {
     *  "play": "pm",
     *  "bet": [
     *  {"type": "g","value": ["4|12|9.98", "7|13|9.98"]},
     *  {"type": "y","value": ["3|14|9.98"]}
     *  ]
     *  }
     *  ],
     *  current_number: 19002, // 当前彩期
     * }
     * @return bool|string
     * @see
     *  play: lm(两面)
     *  type: g(冠) y(亚) 3-10(排名)
     *  bet: big(大) small(小) even(单) odds(双) long(龙) hu(虎)
     *  ------------------------------------------------------
     *  play: gh(冠和)
     *  type: gyh(冠亚和)
     *  bet: big  small  even(单) odds(双) 3-19(和值)
     *  ------------------------------------------------------
     *  play: pm(排名)
     *  type: g(冠) y(亚) 3-10(排名)
     *  bet: 1-10(飞艇编号)
     */

    public function ftOrder($post)
    {
        try {
            Db::startTrans();
            // 检查彩种是否存在
            $lotteryCode = Lottery::getValByWhere(['id' => $post['lottery_id']], 'code');
            if (empty($lotteryCode)) {
                trigger_error('彩种不存在', E_USER_WARNING);
            }

            //检测彩种是否停售
            $checkResult = true;
            switch ($lotteryCode) {
                case Config::FT_CODE: // 飞艇
                    $checkResult = PlOpen::checkSaleStatus($post['current_number'], 5);
                    break;
                default:
                    trigger_error('彩种下单类型错误', E_USER_WARNING);
            }

            if (!$checkResult) {
                trigger_error('彩种已停售', E_USER_WARNING);
            }
            // 订单下单数据容器
            $data = [];
            // 下注金额
            $betAmount = (float)$post['amount'];
            // 获取会员彩金和余额
            $memberData = Member::where('id', UID)
                ->field([
                    'balance', // 余额
                    'hadsel', // 彩金
                    'role', // 角色
                    'username', // 用户名
                    'chn_name', // 昵称
                    'photo', // 头像|头标
                ])
                ->lock(true)// 加排他锁
                ->find()
                ->toArray();
            $hadsel = floatval($memberData['hadsel']);
            $balance = floatval($memberData['balance']);
            // 判断彩金是否大于下注金额
            if ($hadsel >= $betAmount) {
                // 减去下注金额
                $newHadsel = $hadsel - $betAmount;
                // 保存支付后的彩金金额
                Member::where('id', UID)->setField([
                    'hadsel' => $newHadsel // 新彩金
                ]);
                // 增加订单彩金字段
                $data['pay_hadsel'] = $betAmount;
                $data['pay_balance'] = 0;

            } elseif ($balance + $hadsel >= $betAmount) {  // 判断余额加彩金是否大于等于下注金额
                // 先扣完彩金, 再扣余额, 并计算剩余应支付金额
                $surplusBetAmount = $betAmount - $hadsel;
                // 扣住余额, 获取剩余余额
                $surplusBalance = $balance - $surplusBetAmount;
                // 保存支付后的彩金和余额
                Member::where('id', UID)->setField([
                    'hadsel' => 0, // 彩金已扣完, 剩余0,
                    'balance' => $surplusBalance // 保存剩余余额
                ]);

                // 增加订单彩金字段
                $data['pay_hadsel'] = $hadsel;
                // 增加订单余额字段
                $data['pay_balance'] = $surplusBetAmount;
            } else {
                // 提示余额不足
                trigger_error('您的余额不足', E_USER_WARNING);
            }
            // 订单号
            $data['order_no'] = $this->orderNum = Helper::orderNumber();
            $data['member_id'] = UID;
            // 彩种ID
            $data['lottery_id'] = $post['lottery_id'];
            // 总注数
            $data['zhu'] = $post['total_zhu'];
            // 倍数
            $data['beishu'] = $post['beishu'];
            // 总投注金额
            $data['amount'] = $post['amount'];
            // 出票状态
            $data['status'] = 2; // 2: 待开奖
            // 投注项
            $data['bet_content'] = is_string($post['bet_content']) ? $post['bet_content'] : Helper::jsonEncode($post['bet_content']);
            // 金额校验
            $betChecks = Helper::jsonDecode($data['bet_content']);
            if (!$betChecks) {
                trigger_error('下单失败! 错误码: 4494');
            }

            $betColumns = array_column($betChecks, 'bet');
            $valueArr = array_reduce($betColumns, function ($result, $item) {
                $sonItem = array_column($item, 'value');
                return array_merge($result, $sonItem);
            }, []);
            if (!$valueArr) {
                trigger_error('下单失败! 错误码: 4503');
            }

            $computeTotalAmount = 0;
            array_walk($valueArr, function ($item) use (&$computeTotalAmount) {
                foreach ($item as $part) {
                    list(, $pice,) = explode('|', $part);
                    $computeTotalAmount += $pice;
                }
            });
            if ($betAmount !== floatval($computeTotalAmount)) {
                trigger_error('下单失败, 投注金额异常!');
            }

            // 下单时间
            $data['create_time'] = Helper::timeFormat(time(), 's');

            $isMoni = Member::getValByWhere(['id' => UID], 'is_moni');
            // 是否模拟 1 是   0 非模拟
            $data['is_moni'] = ($isMoni === 0 ? 1 : 0);
            // 支付状态 -1:未支付 0:支付中 1:已支付
            $data['pay_status'] = 1;
            // 支付时间
            $data['pay_time'] = date('Y-m-d H:i:s');
            // 购买方式 1: 自购  2: 跟单  3: 推单
            $data['pay_type'] = 1;
            // 用户账号
            $data['username'] = Member::getValByWhere(['id' => UID], 'username');
            // 订单类型(1: 体彩(默认) 2: 数字彩)
            $data['order_type'] = 2;
            // 存储订单表
            $orderResult = self::quickCreate($data);
            if (!$orderResult) {
                trigger_error('下单失败, 错误码2970', E_USER_WARNING);
            }

            // 写入追期表(该彩种暂无追期)
            $orderNumResult = OrderNum::quickCreate([
                'number' => $post['current_number'], // 期号
                'order_id' => $orderResult, // 订单ID
                'multiple' => 1, // 单期倍数(默认1)
                'amount' => $post['amount'], // 单期投注金额
                'ctype' => 5, // 数字彩类型 3: 澳彩  4: 葡彩, 5: 幸运飞艇
                'create_time' => Helper::timeFormat(time(), 's'),
                'update_time' => Helper::timeFormat(time(), 's'),
            ]);
            if (!$orderNumResult) {
                trigger_error('下单失败, 错误码2986');
            }

            // 写入资金流水
            if ($isMoni === 1) {
                $deductSel = (float)$data['pay_hadsel'];
                $deductMoney = (float)$data['pay_balance'];
                // 写入彩金记录
                if (!empty($deductSel)) {
                    FundLog::quickCreate([
                        'member_id' => UID, // 会员ID
                        'money' => $deductSel, // 变动彩金
                        'front_money' => $hadsel, // 变动前彩金
                        'later_money' => bcsub($hadsel, $deductSel, 2), // 变动后彩金
                        'type' => 3, // 购彩
                        'remark' => '注单出票扣除彩金' . sprintf('%01.2f', $deductSel) . '元',
                        'create_time' => Helper::timeFormat(time(), 's'),
                        'update_time' => Helper::timeFormat(time(), 's'),
                        'order_id' => $orderResult,
                        'identify' => $memberData['role'],
                        'username' => $data['username'],
                    ]);
                }

                // 写入余额记录
                if (!empty($deductMoney)) {
                    FundLog::quickCreate([
                        'member_id' => UID, // 会员ID
                        'money' => $deductMoney, // 变动余额
                        'front_money' => $balance, // 变动前余额
                        'later_money' => bcsub($balance, $deductMoney, 2), // 变动后彩金
                        'type' => 3, // 购彩
                        'remark' => '注单出票扣除余额' . sprintf('%01.2f', $deductMoney) . '元',
                        'create_time' => Helper::timeFormat(time(), 's'),
                        'update_time' => Helper::timeFormat(time(), 's'),
                        'order_id' => $orderResult,
                        'identify' => $memberData['role'],
                        'username' => $data['username'],
                    ]);
                }
            }

            // 写入并推送消息
            Helper::logAndPushMsg(
                "用户{$memberData['username']}注单成功, 注单单号: {$data['order_no']}", // 消息内容
                $memberData['chn_name'], // 昵称
                $memberData['username'], // 用户账号
                ($memberData['role'] === 1) ? 3 : 2, // 消息类型
                2, // 内容类型
                $memberData['photo'] ?: 0 // 用户头像或头标
            );

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 获取体彩注单投注内容
     *
     * @param integer $id 订单ID
     * @return array|mixed
     * @author CleverStone
     */
    public function getBetContentBody($id)
    {
        $orderDetail = self::getFieldsByWhere(['id' => $id], ['bet_content', 'lottery_id']);
        if (empty($orderDetail)) {
            return [];
        }

        $lotteryId = $orderDetail['lottery_id'];
        $code = Lottery::getCodeById($lotteryId);
        $betContent = $orderDetail['bet_content'];
        $betContentArr = Helper::jsonDecode($betContent);
        foreach ($betContentArr as &$item) {
            $item['orderId'] = $id;

            switch ($code) {
                case Config::ZC_CODE: // 竞彩足球
                    $matchBase = JczqBase::getFieldsByWhere(['match_num' => $item['mnum']], ['host_name', 'guest_name']);
                    $item['match_detail'] = "{$matchBase['host_name']} VS {$matchBase['guest_name']}";
                    $item['lottery_code'] = Config::ZC_CODE;
                    break;
                case Config::BJ_CODE: // 北京单场
                    $matchBase = JcdcBase::getFieldsByWhere(['match_num' => $item['mnum']], ['host_name', 'guest_name']);
                    $item['match_detail'] = "{$matchBase['host_name']} VS {$matchBase['guest_name']}";
                    $item['lottery_code'] = Config::BJ_CODE;
                    break;
                case Config::LC_CODE: // 竞彩篮球
                    $matchBase = JclqBase::getFieldsByWhere(['match_num' => $item['mnum']], ['host_name', 'guest_name']);
                    $item['match_detail'] = "{$matchBase['host_name']} VS {$matchBase['guest_name']}";
                    $item['lottery_code'] = Config::LC_CODE;
                    break;
                default:
                    return [];
            }

            foreach ($item['muti'] as &$multiple) {
                $multiple['pstr'] = $this->sportsBetCodeToStr($multiple['ptype']);
            }
        }

        return $betContentArr;
    }

    /**
     * 体彩注单编辑投注内容
     *
     * @param array $data 表单数据
     * @return bool|string
     * @author CleverStone
     */
    public function updateBetContentBody($data)
    {
        $orderId = $data['order_id']; // 订单ID
        $matchNum = $data['match_num']; // 赛事编号
        $betItemArr = $data['bet_item']; // 投注项

        $orderDetail = self::getFieldsByWhere(['id' => $orderId], ['lottery_id', 'bet_content', 'is_clear']);
        if ($orderDetail['is_clear']) {
            return '订单已结算, 则不可以重新开奖!';
        }

        $lotteryId = $orderDetail['lottery_id'];
        $lotteryCode = Lottery::getCodeById($lotteryId);
        $giveCountCode = !strcasecmp($lotteryCode, Config::LC_CODE) ? 'rfs' : 'rqs';
        // 订单投注项
        $betContent = Helper::jsonDecode($orderDetail['bet_content']);
        try {
            Db::startTrans();
            foreach ($betItemArr as $item) {
                list($ptype, $originBetItem, $betItem, $betIndex, $giveCount) = explode('#', $item);
                $ptype = trim($ptype);
                $betItem = implode('|', array_map('trim', explode('|', $betItem)));
                $betIndex = implode('|', array_map('trim', explode('|', $betIndex)));
                $giveCount = trim($giveCount) ?: '0';

                // 获取投注项分隔后的数组
                $originBetItemArr = explode('|', $originBetItem);
                $betBodyArr = explode('|', $betItem);
                $betIndexArr = explode('|', $betIndex);
                if (
                    count($originBetItemArr) !== count($betBodyArr)
                    || count($betBodyArr) !== count($betIndexArr)
                ) {
                    trigger_error('格式错误, 编辑前投注项,赔率和编辑后投注项,赔率数量不同!');
                }

                // 投注项映射
                $betItemMap = array_combine($originBetItemArr, $betBodyArr);
                // 赔率映射
                $betIndexMap = array_combine($originBetItemArr, $betIndexArr);
                // 修改订单表
                foreach ($betContent as &$betValue) {
                    if ($betValue['mnum'] == $matchNum) {
                        foreach ($betValue['muti'] as &$mutiItem) {
                            if (!strcasecmp($mutiItem['ptype'], $ptype)) {
                                $mutiItem['bet'] = $betItem;
                                $mutiItem['i'] = $betIndex;
                                $mutiItem[$giveCountCode] = $giveCount;
                            }
                        }
                    }
                }

                // 修改订单内容表
                $orderContent = OrderContent::where('order_id', $orderId)
                    ->field([
                        'content', // 订单内容表每注内容
                        'id', // 订单内容ID
                    ])
                    ->select();
                foreach ($orderContent as $contentItem) {
                    $contentId = $contentItem['id'];
                    $contentBody = Helper::jsonDecode($contentItem['content']);
                    foreach ($contentBody as &$everyBody) {
                        if (
                            $everyBody['mnum'] == $matchNum
                            && !strcasecmp($everyBody['ptype'], $ptype)
                        ) {
                            $originBody = $everyBody['bet'];
                            $everyBody['bet'] = $betItemMap[$originBody];
                            $everyBody['i'] = $betIndexMap[$originBody];
                            $everyBody[$giveCountCode] = $giveCount;
                        }
                    }

                    // 保存订单内容表
                    OrderContent::where('id', $contentId)->setField([
                        'content' => Helper::jsonEncode($contentBody),
                    ]);
                }

                // 修改订单详情表
                $contentDetail = OrderDetail::where('order_id', $orderId)
                    ->where('match_num', $matchNum)
                    ->field([
                        'id', // 详情ID
                        'play_type', // 玩法
                        'bet', // 投注项
                    ])
                    ->select();
                foreach ($contentDetail as $detailItem) {
                    $detailId = $detailItem['id'];
                    $detailItemPlayType = $detailItem['play_type']; // 投注项
                    $detailItemBet = $detailItem['bet']; // 投注内容
                    // 玩法匹配
                    if (!strcasecmp($ptype, $detailItemPlayType)) {
                        if (strpos($detailItemBet, ':') !== false) {
                            // 存在让球数
                            list($bet, ) = explode('|', $detailItemBet);
                            // 组装新的投注项
                            $newDetailBet = $betItemMap[$bet] . '|' . $betIndexMap[$bet] . ':' . $giveCount;
                            // 更新订单详情表
                            OrderDetail::where('id', $detailId)->setField([
                                'bet' => $newDetailBet,
                                'odds' => $betIndexMap[$bet],
                            ]);
                        } else {
                            // 不存在让球数
                            list($bet, ) = explode('|', $detailItemBet);
                            // 组装新的投注项
                            $newDetailBet = $betItemMap[$bet] . '|' . $betIndexMap[$bet];
                            // 更新订单详情表
                            OrderDetail::where('id', $detailId)->setField([
                                'bet' => $newDetailBet,
                                'odds' => $betIndexMap[$bet],
                            ]);
                        }
                    }
                }
            }

            // 保存订单投注项更改
            self::where('id', $orderId)->setField([
                'bet_content' => Helper::jsonEncode($betContent),
            ]);

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }
}