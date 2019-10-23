<?php

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;
use think\Db;

/**
 * 充值记录模型
 *
 * Class FundCharge
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class FundCharge extends BaseModel
{
    // 快捷调用订单状态
    const STATUS_AWAIT = 1;  // 待充值
    const STATUS_SUCCESS = 2;  // 充值成功
    const STATUS_ERROR = 3;  // 充值失败

    // 快捷调用支付类型
    const TYPE_ALIPAY = 1; // 支付宝
    const TYPE_WEIXIN = 2; // 微信
    const TYPE_WEBBANK = 3; // 网银
    const TYPE_AGENT = 4; // 平台代充值
    const TYPE_QUICK = 5; // 快捷支付

    /**
     * 获取用户的充值统计
     *
     * @param $id
     * @return float
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function rechargeCount($id)
    {
        $data = self::where(['member_id' => $id, 'status' => 2])->sum('to_account');
        return $data;
    }

    /**
     * 条件筛选
     *
     * @param $where // 查询条件
     * @param $platform // 平台  vp:总后台  pxy:代理商后台
     * @return array
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    private function commonFilter($where, $platform = 'vp')
    {
        $endWhere = [];

        // 日期筛选
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

        // 流水号筛选
        if (isset($where['orderNum']) && !empty($where['orderNum'])) {
            $endWhere[] = ['order_no', 'like', '%' . $where['orderNum'] . '%'];
        }

        // 充值方式筛选
        if (isset($where['type']) && $where['type'] !== '') {
            $endWhere[] = ['type', '=', (int)$where['type']];
        }

        // 处理name名称不一致问题
        if (isset($where['name']) && !empty($where['name'])) {
            $where['agentName'] = $where['name'];
        }

        // 总后台筛选
        if (
            isset($where['role']) // 角色 1 会员  2代理商
            && !empty($where['role'])
            && !strcasecmp($platform, 'vp') // 判断当前调用是否来自总后台
        ) {
            // 判断用户账号是否为空
            if (isset($where['agentName']) && !empty($where['agentName'])) {
                if (!isset($where['lower']) || empty($where['lower'])) {
                    // 添加角色筛选
                    $endWhere[] = ['identify', '=', $where['role']];
                    // 只选中角色和填写了代理商账号
                    $endWhere[] = ['username', 'like', '%' . $where['agentName'] . '%'];
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
                $endWhere[] = ['identify', '=', $where['role']];
            }
        }

        // 代理商后台筛选
        if (!strcasecmp($platform, 'pxy')) {
            $member = new Member;
            $memberWhere = [];
            // 获取代理商下面的所有下级
            $memberWhere[] = ['path', 'like', '%,' . UID . ',%'];
            if (isset($where['role']) && !empty($where['role'])) {
                // 判断用户账号是否为空agentName
                if (isset($where['agentName']) && !empty($where['agentName'])) {
                    if (!isset($where['lower']) || empty($where['lower'])) {
                        // 获取角色  1 会员  2代理商
                        $memberWhere[] = ['role', '=', $where['role']];
                        // 只选中角色和填写了代理商账号
                        $memberWhere[] = ['username', 'like', '%' . $where['agentName'] . '%'];
                    } else {
                        $agentWhere[] = ['username', '=', $where['agentName']];
                        $memberId = Member::getValByWhere($agentWhere, 'id');
                        if (!empty($memberId)) {
                            // 获取用户的全部下级
                            if ($where['lower'] == 1) {
                                $myDownLevIds = $member->getDownUid($memberId);
                                if (!empty($myDownLevIds)) {
                                    $memberWhere[] = ['id', 'in', $myDownLevIds];
                                } else {
                                    // 没有下级, 设置不存在的筛选条件, 返回空数据
                                    $memberWhere[] = ['id', '=', 'none'];
                                }
                            }
                            // 获取用户的直属下级
                            if ($where['lower'] == 2) {
                                $topLevWhere[] = ['top_id', '=', $memberId];
                                $myDownLevIds = $member->getUidColumn($topLevWhere);
                                if (!empty($myDownLevIds)) {
                                    $memberWhere[] = ['id', 'in', $myDownLevIds];
                                } else {
                                    // 没有下级, 设置不存在的筛选条件, 返回空数据
                                    $memberWhere[] = ['id', '=', 'none'];
                                }
                            }
                        } else {
                            // 虚拟筛选
                            $memberWhere[] = ['id', '=', 'none'];
                        }
                    }
                } else {
                    // 获取角色  1 会员  2代理商
                    $memberWhere[] = ['role', '=', $where['role']];
                }
            }

            // 获取我要查询的会员ID
            $myNextLevUid = $member->getUidColumn($memberWhere);
            $endWhere[] = ['member_id', 'in', $myNextLevUid];
        }

        return $endWhere;
    }

    /**
     * 总后台 - 获取充值记录列表
     *
     * @param $where // 筛选条件
     * @param null $order
     * @return \think\Paginator
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getList($where, $order = null)
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        $where = $this->commonFilter($where, 'vp');
        $paginate = self::where($where)
            ->field([
                'id', // 主键ID
                'order_no', // 充值订单号
                'member_id', // 用户id
                'account',  // 充值金额
                'to_account', // 到账金额
                'type', // 支付方式
                'status', // 充值状态
                'create_time', // 创建时间
                'identify' => 'role', // 角色
                'username', // 账号
            ])
            ->order($order)
            ->paginate($perPage);

        foreach ($paginate as &$v) {
            $nickName = Member::getValByWhere(['id' => $v['member_id']], 'chn_name');
            $v['nick_name'] = $nickName;
        }

        return $paginate;
    }

    /**
     * 总后台数据导出
     *
     * @param $where // 筛选条件
     * @param null $order // 排序规则
     * @return void
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function exportData($where, $order = null)
    {
        $where = $this->commonFilter($where, 'vp');
        $model = self::where($where)
            ->field([
                'id', // 主键ID
                'order_no', // 充值订单号
                'member_id', // 用户id
                'account',  // 充值金额
                'to_account', // 到账金额
                'type', // 支付方式
                'status', // 充值状态
                'create_time', // 创建时间
                'identify' => 'role', // 角色
                'username', // 账号
            ])
            ->order($order)
            ->select();

        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        foreach ($data as &$v) {
            $nickName = Member::getValByWhere(['id' => $v['member_id']], 'chn_name');
            unset($v['member_id']);
            $v['nick_name'] = $nickName; // 昵称
            $v['type'] = self::getRechargeWayByType($v['type']); // 支付方式
            $v['status'] = self::getRechargeStatusByState($v['status']); // 充值状态
            $v['role'] = ($v['role'] === 1 ? '会员' : '代理商'); // 用户身份
        }

        Helper::exportExcel(
            'rechargeExcel',
            [
                '主键ID', '充值单号', '充值金额',
                '到账金额', '支付方式', '充值状态',
                '充值时间', '用户身份', '用户账号',
                '用户昵称'
            ],
            $data
        );
    }

    /**
     * 代理商后台数据导出
     *
     * @param $where // 筛选条件
     * @param null $order // 排序规则
     * @return void
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function exportDataPxy($where, $order = null)
    {
        $where = $this->commonFilter($where, 'pxy');
        $model = self::where($where)
            ->field([
                'id', // 主键ID
                'order_no', // 充值订单号
                'member_id', // 用户id
                'account',  // 充值金额
                'to_account', // 到账金额
                'type', // 支付方式
                'status', // 充值状态
                'create_time', // 创建时间
                'identify' => 'role', // 角色
                'username', // 账号
            ])
            ->order($order)
            ->select();

        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        foreach ($data as &$v) {
            $nickName = Member::getValByWhere(['id' => $v['member_id']], 'chn_name');
            unset($v['member_id']);
            $v['nick_name'] = $nickName; // 昵称
            $v['type'] = self::getRechargeWayByType($v['type']); // 支付方式
            $v['status'] = self::getRechargeStatusByState($v['status']); // 充值状态
            $v['role'] = ($v['role'] === 1 ? '会员' : '代理商'); // 用户身份
        }

        Helper::exportExcel(
            'rechargeExcel',
            [
                '主键ID', '充值单号', '充值金额',
                '到账金额', '支付方式', '充值状态',
                '充值时间', '用户身份', '用户账号',
                '用户昵称'
            ],
            $data
        );
    }

    /**
     * 获取支付方式(字符串)
     *
     * @param $type // 支付类型
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getRechargeWayByType($type)
    {
        switch ($type) {
            case 1:
                return '支付宝';
            case 2:
                return '微信支付';
            case 3:
                return '网银支付';
            case 4:
                return '平台代充';
            case 5:
                return '快捷支付';
            default:
                return '';
        }
    }

    /**
     * 获取充值状态(字符串)
     *
     * @param $state // 充值状态码
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getRechargeStatusByState($state)
    {
        switch ($state) {
            case 1:
                return '待支付';
            case 2:
                return '充值成功';
            case 3:
                return '充值失败';
            default:
                return '未知';
        }
    }

    /**
     * 代理商后台 - 获取该代理商下的充值记录列表
     *
     * @param $where // 筛选条件
     * @param null $order // 排序
     * @return \think\Paginator
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getAgentList($where, $order = null)
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        $where = $this->commonFilter($where, 'pxy');
        $paginate = self::where($where)
            ->field([
                'id', // 主键ID
                'order_no', // 充值订单号
                'member_id', // 用户id
                'account',  // 充值金额
                'to_account', // 到账金额
                'type', // 支付方式
                'status', // 充值状态
                'create_time', // 创建时间
                'identify' => 'role', // 角色
                'username', // 账号
            ])
            ->order($order)
            ->paginate($perPage);

        foreach ($paginate as &$v) {
            $nickName = Member::getValByWhere(['id' => $v['member_id']], 'chn_name');
            $v['nick_name'] = $nickName;
        }

        return $paginate;
    }

    /**
     * 获取15天入金折线数据
     *
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function get15DaysMoneyLine($where = null)
    {
        $time = mktime(0, 0, 0, date('m'), date('d') - 14, date('Y'));
        $day15 = Helper::timeFormat($time, 's');
        $now = Helper::timeFormat(time(), 's');
        $model = self::where([['create_time', 'between time', [$day15, $now]]])
            ->where('status', 2)// 充值成功
            ->where($where)
            ->field([
                'SUM(account)' => 'number',
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
     * 充值统一下单方法
     *
     * @param $uid // 会员ID
     * @param $money // 充值金额
     * @param $type // 支付方式
     * @return false|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function payOrder($uid, $money, $type)
    {
        // 获取会员
        $memberData = Member::getFieldsByWhere(['id' => $uid], ['username', 'role']);
        // 获取充值赠送配置(单位: 元) string隐式转换为float
        $giveWhere = (float)AdminConfig::conf('recharge_full');
        $giveMoney = (float)AdminConfig::conf('recharge_give');
        // 计算到账金额
        $toMoney = $money;
        if ($money >= $giveWhere) {
            $awardMoney = floor($money / $giveWhere) * $giveMoney;
            $toMoney = $money + $awardMoney;
        }

        try {
            Db::startTrans();
            // 充值下单
            $orderNo = Helper::orderNumber();
            $chargeId = self::quickCreate([
                'order_no' => $orderNo,
                'member_id' => $uid,
                'account' => $money,
                'to_account' => $toMoney,
                'type' => $type,
                'status' => self::STATUS_AWAIT,
                'create_time' => Helper::timeFormat(time(), 's'),
                'update_time' => Helper::timeFormat(time(), 's'),
                'identify' => $memberData['role'],
                'username' => $memberData['username'],
            ]);
            if (!$chargeId) {
                trigger_error('充值下单失败');
            }

            Db::commit();
            return $orderNo;
        } catch (\Exception $e) {
            Db::rollback();
            // 写入系统日志
            Helper::log($memberData['username'], '会员充值', '充值下单失败', $e->getMessage(), 0, 3);
            return false;
        }
    }

    /**
     * 充值成功,业务回调
     * @param $orderNo
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function paySuccess($orderNo)
    {
        // 获取支付订单详情
        $chargeDetail = FundCharge::getFieldsByWhere(['order_no' => $orderNo], ['status', 'to_account', 'member_id']);
        $myStatus = $chargeDetail['status'];
        $toAccount = (float)$chargeDetail['to_account'];
        $uid = $chargeDetail['member_id'];
        if ($myStatus === null) {
            // 商户系统异常,终止回调
            return;
        }

        if ($myStatus !== FundCharge::STATUS_AWAIT) {
            // 已支付,终止回调
            return;
        }

        try {
            Db::startTrans();
            // 获取会员详情(排他锁)
            $memberData = Member::where('id', $uid)
                ->field([
                    'hadsel', // 彩金
                    'username',
                    'role',
                ])
                ->lock(true)
                ->find();
            if (empty($memberData)) {
                trigger_error('会员不存在或mySQL事务lock时间过长');
            }

            // 更改订单支付状态
            self::where('order_no', $orderNo)->setField([
                'status' => 2,
                'update_time' => Helper::timeFormat(time(), 's'),
            ]);
            // 会员增加彩金
            $laterHadsel = bcadd($memberData['hadsel'], $toAccount, 2);
            Member::quickCreate([
                'id' => $uid,
                'hadsel' => $laterHadsel,
                'update_at' => Helper::timeFormat(time(), 's'),
            ], true);

            // 记录资金流水
            $result = FundLog::quickCreate([
                'member_id' => $uid,
                'money' => $toAccount,
                'front_money' => $memberData['hadsel'],
                'later_money' => $laterHadsel,
                'type' => 1,
                'remark' => '充值彩金到账',
                'create_time' => Helper::timeFormat(time(), 's'),
                'update_time' => Helper::timeFormat(time(), 's'),
                'identify' => $memberData['role'],
                'username' => $memberData['username'],
                'charge_id' => self::getValByWhere(['order_no' => $orderNo], 'id'),
            ]);
            if (!$result) {
                trigger_error('记录资金流水失败');
            }

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            // 记录系统日志
            Helper::log('系统', '支付回调', '充值成功,会员资金未到账', $e->getMessage(), 0);
        }
    }
}
