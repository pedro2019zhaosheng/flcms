<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/19
 * Time: 11:37
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\Config;
use app\common\Helper;
use app\common\relation\Data;
use think\Db;

/**
 * 竞彩足球开奖模型
 *
 * Class JczqOpen
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class JczqOpen extends BaseModel
{
    use Data;

    // 平台抽取的跟单佣金比 (3%)
    const PLAIN_COMMISSION_RATE = 0.3;

    /**
     * 修改赛果
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
     * 条件筛选
     *
     * @param $params // 查询参数
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function commonFilter($params)
    {
        // 是否开奖筛选
        $where = [];
        if (
            isset($params['status'])
            && $params['status'] !== null
            && $params['status'] !== ''
        ) {
            $where[] = ['status', '=', (int)$params['status']];
        }

        // 竞彩日期筛选
        if (isset($params['date']) && !empty($params['date'])) {
            $where[] = ['jc_date', 'between time', [$params['date'] . ' 00:00:00', $params['date'] . ' 23:59:59']];
        }

        // 赛事编号筛选
        if (isset($params['matchNum']) && !empty($params['matchNum'])) {
            $where[] = ['jb.match_num', 'like', '%' . $params['matchNum'] . '%'];
        }

        // 球队筛选
        if (isset($params['teamName']) && !empty($params['teamName'])) {
            $where[] = ['host_name|guest_name', 'like', '%' . $params['teamName'] . '%'];
        }

        return $where;
    }

    /**
     * 获取赛事赛果列表
     *
     * @param $where
     * @param null $order
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getDrawList($where, $order = null)
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        $where = $this->commonFilter($where);
        if (empty($order)) {
            $order = 'jo.id DESC';
        }

        $page = self::where($where)
            ->alias('jo')
            ->leftJoin('jczq_base jb', 'jo.match_num=jb.match_num')
            ->field([
                'jo.id' => 'openid', // 开奖表ID
                'jo.status', // 开奖状态
                'jo.half_score', // 半场比分
                'jo.normal_score', // 全场比分(不含加时赛)
                'jo.total_score', // 总比分(含加时赛)
                'jo.kick_score', // 点球比分
                'jb.rqs',  // 让球数
                'jb.match_num', // 赛事编号
                'jb.league_name', // 联盟名称
                'jb.host_name', // 主队名称
                'jb.guest_name', // 客队名称
                'jb.jc_date', // 竞彩日期
            ])
            ->order($order)
            ->paginate($perPage);
        foreach ($page as $k => $v) {
            $page[$k]['czName'] = '竞彩足球';
            $page[$k]['code'] = Config::ZC_CODE;
            $page[$k]['jc_date'] = date('Y-m-d', strtotime($v['jc_date']));
        }

        return $page;
    }

    /**
     * 获取开奖的历史数据
     *
     * @param $where
     * @param $limit
     * @throws \Exception
     * @return string|null
     */
    public static function getLooteryResults($where, $limit)
    {
        $openCode = self::where($where)->order('create_at desc')->limit($limit)->select();
        return $openCode ?: null;
    }

    /**
     * 导出竞彩足球赛果
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
    public function exportData($where, $order = 'jo.id DESC')
    {
        $where = $this->commonFilter($where);
        $model = self::where($where)
            ->alias('jo')
            ->leftJoin('jczq_base jb', 'jo.match_num=jb.match_num')
            ->field([
                'jb.match_num', // 赛事编号
                'jb.league_name', // 联盟名称
                'jb.host_name', // 主队名称
                'jb.guest_name', // 客队名称
                'jb.jc_date', // 竞彩日期
                'jo.status', // 开奖状态
                'jo.half_score', // 半场比分
                'jo.normal_score', // 全场比分(不含加时赛)
                'jo.total_score', // 总比分(含加时赛)
                'jo.kick_score', // 点球比分
                'jb.rqs',  // 让球数
            ])
            ->order($order)
            ->select();

        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        foreach ($data as &$v) {
            $v['czName'] = '足彩';
            $v['code'] = Config::ZC_CODE;
            $v['status'] = ($v['status'] === 0 ? '未开奖' : '已开奖');
        }

        // 导出
        Helper::exportExcel(
            'MatchResultExcel',
            [
                '赛事编号', '联盟名称', '主队名称', '客队名称', '竞彩日期', '开奖状态', '半场比分',
                '全场比分(不含加时赛)', '总比分(含加时赛)', '点球比分', '让球数', '彩种名称', '彩种代码',
            ],
            $data
        );
    }

    /**
     * 计算并获取竞彩结果
     *
     * @param $matchNum // 比赛编号
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getJcResult($matchNum)
    {
        $data = $this->getMatchDetail($matchNum);
        return $this->computedJcResult(Config::ZC_CODE, $data);
    }

    /**
     * 获取比赛赛果详情
     *
     * @param $matchNum // 比赛编号
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getMatchDetail($matchNum)
    {
        $model = self::quickGetOne(null, ['match_num' => $matchNum]);
        return $model->toArray();
    }

    /**
     * 是否已经获取足彩竞彩结果
     *
     * @param $matchNum // 比赛编号
     * @return bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function isResult($matchNum)
    {
        $data = self::quickGetOne(null, ['match_num' => $matchNum]);
        if (empty($data)) {
            return false;
        }

        if (!empty($data['half_score']) && !empty($data['normal_score'])) {
            return true;
        }

        return false;
    }

    /**
     * 足彩手动开奖(降低事务层级)
     *
     * @param $matchNum // 比赛编号
     * @throws \Exception
     * @return bool|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function handOpen($matchNum)
    {
        // 获取赛事结果
        $openDetail = $this->getMatchDetail($matchNum);
        if (empty($openDetail) || $openDetail['status'] === 0) {
            // 比赛没结束, 停止开奖
            return '比赛进行中,请稍后重试!';
        }

        // 赛事结果数组
        $openDetailData = [
            'match_num' => $openDetail['match_num'], // 比赛编号
            'half_score' => $openDetail['half_score'], // 半场比分
            'normal_score' => $openDetail['normal_score'], // 90分全场比分
        ];
        //+------------------------------------------------------------------------------
        //| 比赛结果处理为竞彩结果
        //+------------------------------------------------------------------------------
        //| ['spf' => 'W', 'rqspf' => 'D', 'jqs' => 's1', 'bqc' => 'ww', 'bf' => '0100']
        //+------------------------------------------------------------------------------
        $dealData = $this->matchResult2JcResult(Config::ZC_CODE, $openDetailData);
        // 获取彩种ID
        $lotteryId = Lottery::getIdByCode(Config::ZC_CODE);
        // 所有订单详情数据
        $data = OrderDetail::alias('od')
            ->leftJoin('order o', 'od.order_id=o.id')
            ->where('od.match_num', $matchNum)// 赛事编号
            ->where('od.lottery_id', $lotteryId)// 彩种ID
            ->where('o.is_clear', 0)// 未结算
            ->field([
                'od.id', // 订单详情ID
                'od.order_id', // 订单ID
                'od.order_content_id', // 订单内容ID
                'od.play_type', // 玩法
                'od.bet', // 投注内容
                'od.odds', // 奖金指数
                'o.order_no', // 注单号
                'o.member_id', // 会员ID
                'o.amount', // 投注总金额
                'o.status', // 订单状态
                'o.pay_status', // 支付状态
                'o.is_yh', // 是否优化
                'o.beishu', // 倍数
                'o.pay_type', // 购买方式
                'o.follow_order_id', // 推单ID
            ])
            ->select()
            ->toArray();
        if (empty($data)) {
            // 该赛事没有注单数据
            return '暂无开奖订单!';
        }

        // 检查是否有未开票或未支付的订单
        $payStatus = array_column($data, 'pay_status');
        $intersect = array_intersect([-1, 0], $payStatus);
        if (!empty($intersect)) {
            return '该赛事存在未支付或支付中的订单, 请稍后重试!';
        }

        $isComplete = true;
        foreach ($data as $datum) {
            try {
                // 开启事务
                Db::startTrans();
                // 处理订单详情中的投注项数据格式
                $betBody = $this->dealOrderDetailBet($datum['play_type'], $datum['bet']);
                $playType = $datum['play_type'];
                $betVal = $betBody['bet'];
                if (
                    isset($dealData[$playType]) // 投注项存在赛事玩法
                    && !strcasecmp($dealData[$playType], $betVal) // 投注内容和赛事结果相同, 则中奖
                ) {
                    // 处理中奖数据
                    OrderDetail::where('id', $datum['id'])->setField('status', 1);
                } else {
                    // 处理没中奖数据
                    OrderDetail::where('id', $datum['id'])->setField('status', 2);
                }

                // 获取未开奖的数据数量
                $unOpenDrawCount = OrderDetail::where(['order_content_id' => $datum['order_content_id']])
                    ->where('status', 0)
                    ->count();
                // 获取未中奖的数据数量
                $unDrawCount = OrderDetail::where(['order_content_id' => $datum['order_content_id']])
                    ->where('status', 2)
                    ->count();
                // 当前该注中不存在未中奖或未开奖的数据, 则该注为已中奖
                if (!$unOpenDrawCount && !$unDrawCount) {
                    // 获取该注的奖金指数的乘积, 即: 赔率
                    $oddsArr = OrderDetail::getColumnByWhere(['order_content_id' => $datum['order_content_id']], 'odds');
                    $product = array_product($oddsArr);

                    $isYh = $datum['is_yh']; // 是否优化
                    $oneZhuAmount = 2; // 每注支付金额, 默认一注2元
                    $double = (int)$datum['beishu']; // 倍数
                    if ($isYh == 0) {
                        $bonus = round($double * $oneZhuAmount * $product, 2); // 倍数*每注金额*赔率
                    } else {
                        // 获取该注的优化倍数
                        $yhDouble = OrderContent::getValByWhere(['id' => $datum['order_content_id']], 'beishu');
                        $bonus = round($yhDouble * $oneZhuAmount * $product, 2); // 优化倍数*每注金额*赔率
                    }
                    // 设置订单内容中, 该注订单为已中奖, 并写入奖金
                    OrderContent::where('id', $datum['order_content_id'])->setField(['status' => 1, 'bonus' => $bonus]);
                    // 查询订单内容中是否存在未开奖的数据
                    $unDrawContentCount = OrderContent::where('order_id', $datum['order_id'])->where('status', 0)->count();
                    if (!$unDrawContentCount) {
                        // 已中奖
                        $this->handDrawHandler($datum['order_id'], $datum['pay_type'], $datum['follow_order_id']);
                    }
                }
                // 存在未中奖并且不存在未开奖的数据, 则该注未中奖
                if ($unDrawCount && !$unOpenDrawCount) {
                    // 设置订单内容中该注订单为未中奖
                    OrderContent::where('id', $datum['order_content_id'])->setField('status', 2);
                    // 查看订单内容中是否存在未开奖的数据
                    $unDrawData = OrderContent::where('order_id', $datum['order_id'])->where('status', 0)->count();
                    // 不存在未开奖的数据
                    if (!$unDrawData) {
                        // 查看是否存在已中奖的数据
                        $alreadyDrawData = OrderContent::where('order_id', $datum['order_id'])->where('status', 1)->count();
                        if ($alreadyDrawData) {
                            // 已中奖
                            $this->handDrawHandler($datum['order_id'], $datum['pay_type'], $datum['follow_order_id']);
                        } else {
                            // 未中奖
                            // 设置该订单为未中奖状态
                            Order::where('id', $datum['order_id'])->setField([
                                'status' => 3, // 未中奖
                                'pay_out_commission' => 0, // 重置跟单付出
                                'follow_order_commission' => 0, // 重置推单收益
                                'bounty' => 0, // 重置嘉奖
                                'bonus' => 0, // 重置奖金
                                'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
                            ]);
                            // 添加总输赢
                            if ($datum['status'] !== 3){
                                Member::addLoseAndWinning($datum['member_id'], $datum['amount']);
                            }
                        }
                    }
                }
                // 事务提交
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                // 写入系统日志
                Helper::log('系统', '竞彩足球手动开奖', "注单{$datum['order_no']}开奖失败!", $e->getMessage(), 0);
                // 执行失败
                $isComplete = false;
            }
        }

        // 设置该赛事已停售
        JczqBase::where('match_num', $matchNum)->setField('sale_status', 0);
        // 判断是否完成开奖
        if ($isComplete) {
            return true;
        }

        return '手动开奖发生错误,错误详情请查看系统日志!';
    }

    /**
     * 手动开奖中奖处理逻辑
     *
     * @param $orderId // 订单ID
     * @param $payType // 购买方式
     * @param $followOrderId // 推单ID
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    private function handDrawHandler($orderId, $payType, $followOrderId)
    {
        // 获取该订单的原始数据
        $orderOriginData = Order::getFieldsByWhere(['id' => $orderId], ['member_id', 'amount', 'status']);
        $betTotalAmount = $orderOriginData['amount'];
        $memberId = $orderOriginData['member_id'];
        $orderStatus = $orderOriginData['status'];
        // 减少总输赢
        if ($orderStatus === 3){
            // 未中奖转已中奖
            Member::decLoseWinning($memberId, $betTotalAmount);
        }
        // 该订单已中奖, 获取总奖金
        $bonusSum = OrderContent::where('order_id', $orderId)
            ->where('status', 1)
            ->sum('bonus');
        // 获取嘉奖奖金比例
        $bountyAmountCommission = AdminConfig::config('prize_size');
        $bountyAmountCommission /= 100;
        // 跟单
        if ($payType === 2) {
            // 获取推单的佣金比例
            $commissionRate = Order::getValByWhere(['id' => $followOrderId], 'commission_rate');
            $commissionRate = (int)$commissionRate / 100;
            // 获取推单订单应获取的佣金 -- 付出的佣金 = 奖金 * 推单佣金比例
            $followCommissionAmount = round($bonusSum * $commissionRate, 2);
            // 实际推单佣金 = 付出的佣金 - 平台抽取佣金
            $realPushOrderCommissionAmount = $followCommissionAmount - ($followCommissionAmount * self::PLAIN_COMMISSION_RATE);
            $realPushOrderCommissionAmount = round($realPushOrderCommissionAmount, 2);
            // 获取当前跟单订单的实际奖金 -- 到手奖金 = 奖金 - 付出的佣金
            $realBonus = $bonusSum - $followCommissionAmount;
            // 获取当前跟单订单获取的嘉奖彩金 -- 嘉奖奖金 = 奖金 * 嘉奖比例
            $bountyAmount = round($bonusSum * $bountyAmountCommission, 2);
            // 初始化推单订单的跟单佣金
            static $position = [];
            if (empty($position) || !isset($position[$followOrderId])) {
                $position[$followOrderId] = 1;
                Order::where(['id' => $followOrderId])->setField('follow_order_commission', $realPushOrderCommissionAmount);
            } else {
                Order::where(['id' => $followOrderId])->setInc('follow_order_commission', $realPushOrderCommissionAmount);
            }
            // 写入奖金/嘉奖彩金
            Order::where(['id' => $orderId])->setField([
                'bonus' => $realBonus, // 最后获取的奖金
                'bounty' => $bountyAmount, // 嘉奖彩金
                'pay_out_commission' => $followCommissionAmount, // 跟单付出的佣金
                'status' => 4, // 已中奖
                'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
            ]);
        } else {
            // 普通订单
            $bountyAmount = round($bonusSum * $bountyAmountCommission, 2);
            Order::where('id', $orderId)->setField([
                'status' => 4, // 已中奖
                'bonus' => $bonusSum, // 奖金
                'bounty' => $bountyAmount, // 嘉奖彩金
                'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
            ]);
        }
    }

    /**
     * 系统自动开奖中奖处理逻辑
     *
     * @param $orderId // 订单ID
     * @param $payType // 购买方式
     * @param $followOrderId // 推单ID
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    private function autoDrawHandler($orderId, $payType, $followOrderId)
    {
        // 该订单已中奖, 获取总奖金
        $bonusSum = OrderContent::where('order_id', $orderId)
            ->where('status', 1)
            ->sum('bonus');
        // 获取嘉奖奖金比例
        $bountyAmountCommission = AdminConfig::config('prize_size');
        $bountyAmountCommission /= 100;
        // 跟单
        if ($payType === 2) {
            // 获取推单的佣金比例
            $commissionRate = Order::getValByWhere(['id' => $followOrderId], 'commission_rate');
            $commissionRate = (int)$commissionRate / 100;
            // 获取推单订单应获取的佣金 -- 付出的佣金 = 奖金 * 佣金比例
            $followCommissionAmount = round($bonusSum * $commissionRate, 2);
            // 实际推单佣金 = 付出的佣金 - 平台抽取佣金
            $realPushOrderCommissionAmount = $followCommissionAmount - ($followCommissionAmount * self::PLAIN_COMMISSION_RATE);
            $realPushOrderCommissionAmount = round($realPushOrderCommissionAmount, 2);
            // 获取当前跟单订单的实际奖金 -- 到手的奖金 = 奖金 - 付出的佣金
            $realBonus = $bonusSum - $followCommissionAmount;
            // 获取当前跟单订单获取的嘉奖奖金 -- 嘉奖彩金 = 奖金 * 嘉奖比例
            $bountyAmount = round($bonusSum * $bountyAmountCommission, 2);
            // 推单订单写入推单佣金
            Order::where(['id' => $followOrderId])->setInc('follow_order_commission', $realPushOrderCommissionAmount);
            // 写入奖金/嘉奖彩金
            Order::where(['id' => $orderId])->setField([
                'bonus' => $realBonus, // 最后获取的奖金
                'bounty' => $bountyAmount, // 嘉奖彩金
                'pay_out_commission' => $followCommissionAmount, // 跟单付出的佣金
                'status' => 4, // 已中奖
                'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
            ]);
        } else {
            // 普通订单
            $bountyAmount = round($bonusSum * $bountyAmountCommission, 2);
            Order::where('id', $orderId)->setField([
                'status' => 4, // 已中奖
                'bonus' => $bonusSum, // 奖金
                'bounty' => $bountyAmount, // 嘉奖彩金
                'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
            ]);
        }
    }

    /**
     * 足彩系统自动开奖(降低事务层级)
     *
     * @param $matchNum // 赛事编号
     * @author CleverStone
     * @throws \Exception
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function zcAutoDraw($matchNum)
    {
        // 获取赛事结果
        $openDetail = $this->getMatchDetail($matchNum);
        if (
            empty($openDetail)
            || $openDetail['status'] === 0
        ) {
            // 比赛没结束, 停止开奖
            return;
        }
        // 赛事结果数组
        $openDetailData = [
            'match_num' => $openDetail['match_num'], // 比赛编号
            'half_score' => $openDetail['half_score'], // 半场比分
            'normal_score' => $openDetail['normal_score'], // 90分全场比分
        ];
        // 比赛结果处理为竞彩结果
        // 数据格式 : ['spf' => 'W', 'rqspf' => 'D', 'jqs' => 's1', 'bqc' => 'ww', 'bf' => '0100']
        $dealData = $this->matchResult2JcResult(Config::ZC_CODE, $openDetailData);
        // 获取彩种ID
        $lotteryId = Lottery::getIdByCode(Config::ZC_CODE);
        // 先获取该赛事的所有订单详情
        $data = OrderDetail::alias('od')
            ->leftJoin('order o', 'od.order_id=o.id')
            ->where('od.match_num', $matchNum)// 赛事编号
            ->where('od.lottery_id', $lotteryId)// 彩种ID
            ->where('od.status', 0)// 待开奖状态数据
            ->where('o.is_clear', 0)// 未结算
            ->field([
                'od.id', // 订单详情ID
                'od.order_id', // 订单ID
                'od.order_content_id', // 订单内容ID
                'od.play_type', // 玩法
                'od.bet', // 投注内容
                'od.odds', // 奖金指数
                'o.member_id', // 会员ID
                'o.amount', // 投注总金额
                'o.order_no', // 订单号
                'o.status', // 订单状态
                'o.pay_status', // 订单支付状态
                'o.is_yh', // 是否优化
                'o.beishu', // 倍数
                'o.pay_type', // 购买方式
                'o.follow_order_id', // 跟单的订单ID
            ])
            ->select()
            ->toArray();
        foreach ($data as $datum) {
            try {
                $payStatus = $datum['pay_status']; // 支付状态
                $status = $datum['status']; // 订单状态
                // 以下条件不可系统自动开奖, (下列条件出现几率几乎为0)
                if (
                    in_array($payStatus, [-1, 0]) // 未支付 . 支付中
                    || in_array($status, [0, 3, 4]) // 待出票 . 未中奖 . 已开奖
                ) {
                    // 跳过
                    continue;
                }

                // 开启事务
                Db::startTrans();
                // 处理订单详情中的投注项数据格式
                $betBody = $this->dealOrderDetailBet($datum['play_type'], $datum['bet']);
                $playType = $datum['play_type'];
                $betVal = $betBody['bet'];
                if (
                    isset($dealData[$playType]) // 投注项存在赛事玩法
                    && !strcasecmp($dealData[$playType], $betVal) // 投注内容和赛事结果相同, 则中奖
                ) {
                    // 处理中奖数据
                    OrderDetail::where('id', $datum['id'])->setField('status', 1);
                } else {
                    // 处理没中奖数据
                    OrderDetail::where('id', $datum['id'])->setField('status', 2);
                }

                // 获取未开奖的数据数量
                $unOpenDrawCount = OrderDetail::where(['order_content_id' => $datum['order_content_id']])
                    ->where('status', 0)
                    ->count();
                // 获取未中奖的数据数量
                $unDrawCount = OrderDetail::where(['order_content_id' => $datum['order_content_id']])
                    ->where('status', 2)
                    ->count();
                // 当前该注中不存在未中奖或未开奖的数据, 则该注为已中奖
                if (!$unOpenDrawCount && !$unDrawCount) {
                    // 获取该注的奖金指数的乘积, 即: 赔率
                    $oddsArr = OrderDetail::getColumnByWhere(['order_content_id' => $datum['order_content_id']], 'odds');
                    $product = array_product($oddsArr);

                    $isYh = $datum['is_yh']; // 是否优化
                    $oneZhuAmount = 2; // 每注支付金额, 默认一注2元
                    $double = (int)$datum['beishu']; // 倍数
                    if ($isYh == 0) {
                        $bonus = round($double * $oneZhuAmount * $product, 2); // 倍数*每注金额*赔率
                    } else {
                        // 获取该注的优化倍数
                        $yhDouble = OrderContent::getValByWhere(['id' => $datum['order_content_id']], 'beishu');
                        $bonus = round($yhDouble * $oneZhuAmount * $product, 2); // 优化倍数*每注金额*赔率
                    }
                    // 设置订单内容中, 该注订单为已中奖, 并写入奖金
                    OrderContent::where('id', $datum['order_content_id'])->setField(['status' => 1, 'bonus' => $bonus]);
                    // 查询订单内容中是否存在未开奖的数据
                    $unDrawContentCount = OrderContent::where('order_id', $datum['order_id'])->where('status', 0)->count();
                    if (!$unDrawContentCount) {
                        // 已中奖
                        $this->autoDrawHandler($datum['order_id'], $datum['pay_type'], $datum['follow_order_id']);
                    }
                }

                // 存在未中奖并且不存在未开奖的数据, 则该注未中奖
                if ($unDrawCount && !$unOpenDrawCount) {
                    // 设置订单内容中该注订单为未中奖
                    OrderContent::where('id', $datum['order_content_id'])->setField('status', 2);
                    // 查看订单内容中是否存在未开奖的数据
                    $unDrawData = OrderContent::where('order_id', $datum['order_id'])->where('status', 0)->count();
                    // 不存在未开奖的数据
                    if (!$unDrawData) {
                        // 查看是否存在已中奖的数据
                        $alreadyDrawData = OrderContent::where('order_id', $datum['order_id'])->where('status', 1)->count();
                        if ($alreadyDrawData) {
                            // 已中奖
                            $this->autoDrawHandler($datum['order_id'], $datum['pay_type'], $datum['follow_order_id']);
                        } else {
                            // 未中奖
                            // 设置该订单为未中奖状态
                            Order::where('id', $datum['order_id'])->setField([
                                'status' => 3,
                                'open_time' => Helper::timeFormat(time(), 's'), // 开奖时间
                            ]);
                            // 写入总输赢
                            Member::addLoseAndWinning($datum['member_id'], $datum['amount']);
                        }
                    }
                }

                // 事务提交自动释放锁
                Db::commit();
            } catch (\Exception $e) {
                Db::rollback();
                // 写入日志
                Helper::log('系统', '竞彩足球自动开奖', '注单' . $datum['order_no'] . '开奖失败!', $e->getMessage(), 0);
            }
        }
    }
}