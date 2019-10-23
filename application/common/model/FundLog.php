<?php

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;
use app\common\Config;
use think\Db;

/**
 * 资金记录数据模型
 *
 * Class FundLog
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class FundLog extends BaseModel
{
    /**
     * 写入资金记录
     *
     * @param $data // 数据
     * @return bool|int
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function insertFundLog($data)
    {
        return self::quickCreate($data);
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
                                // 虚拟筛选
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
                                // 虚拟筛选
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

        // 投注返佣记录
        if (isset($where['type']) && !empty($where['type'])) {
            if ($where['type'] != 'all') {
                $endWhere[] = ['type', '=', $where['type']];
            }
        }

        // 资金流水
        if (isset($where['status']) && !empty($where['status'])) {
            $endWhere[] = ['type', '=', $where['status']];
        }

        // 用户账号筛选
        if (isset($where['username']) && !empty($where['username'])) {
            $endWhere[] = ['username', 'like', '%' . $where['username'] . '%'];
        }

        return $endWhere;
    }


    /**
     * @desc 获取资金记录列表
     * @author LiBin
     * @param $where
     * @param null $order
     * @return false|\think\Paginator
     * @throws \Exception
     * @date 2019-04-02
     * @api *
     */
    public function getList($where, $order = null)
    {
        // 每页数据条数
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        // 查询字段
        $data = [
            'id', // 主键ID
            'money', // 变动金额
            'member_id', // 用户 id
            'type', // 类型
            'remark',// 备注
            'create_time',// 创建时间
            'order_id',// 订单ID
        ];

        $checkType = false;
        if (isset($where['type']) && !empty($where['type'])) {
            // 资金流水记录和校正记录, 返回变动前后字段
            if ($where['type'] == 'all' || $where['type'] == 9) {
                array_push($data, 'front_money', 'later_money');
            }

            if ($where['type'] == 7) {
                // 投注人
                array_push($data, 'bet_username');
                $checkType = true;
            }
        }

        $where = $this->commonFilter($where, 'vp');
        $paginate = self::where($where)->field($data)->order($order)->paginate($perPage);
        $member = new Member();
        foreach ($paginate as &$v) {
            $memberWhere['id'] = $v['member_id'];
            //获取用户的昵称,账号,上级ID
            $memberData = $member->getOneMember($memberWhere, 'chn_name, role, username, top_id');
            if (empty($memberData)) {
                continue;
            }
            // 释放
            unset($v['member_id']);
            list($v['chn_name'], $v['role'], $v['nick_name'], $v['top_id']) = array_values($memberData->toArray());
            //获取上级昵称
            $chn_name = $member->getOneMember(['id' => $v['top_id']], 'chn_name');
            empty($chn_name) ? $v['top_name'] = '' : $v['top_name'] = $chn_name['chn_name'];
            //判断是否是返佣数据
            if ($checkType) {
                $orderData = Order::getFieldsByWhere(['id' => $v['order_id']], 'order_no,amount');
                $v['order_no'] = '';
                $v['amount'] = '';
                if (!empty($orderData)) {
                    $v['order_no'] = $orderData['order_no'];
                    $v['amount'] = $orderData['amount'];
                }
            }

            // 释放
            unset($v['order_id']);
        }

        return $paginate;
    }

    /**
     * 总后台导出Excel
     *
     * @param $where // 筛选条件
     * @param null $order // 排序规则
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function exportData($where, $order = null)
    {
        // 搜索字段
        $data = [
            'id', // 主键ID
            'money', // 变动金额
            'member_id', // 用户 id
            'type', // 类型
            'remark',// 备注
            'create_time',// 创建时间
            'order_id',// 订单ID
        ];
        // 组装搜索字段
        $checkType = false;
        if (isset($where['type']) && !empty($where['type'])) {
            // 资金流水记录和校正记录, 返回变动前后字段
            if ($where['type'] == 'all' || $where['type'] == 9) {
                array_push($data, 'front_money', 'later_money');
            }

            if ($where['type'] == 7) {
                // 投注人
                array_push($data, 'bet_username');
                $checkType = true;
            }
        }

        // 导出Excel
        if (
            isset($where['type'])
            && (
                !strcasecmp($where['type'], 'all')
                || $where['type'] == 9
            )
        ) {
            $headerName = 'ZiJinBianDongExcel';
            $headerData = ['主键ID', '变动金额', '变动类型', '备注', '日期', '变动前金额', '变动后金额', '昵称', '角色', '账号', '我的上级'];
        } elseif (
            isset($where['type'])
            && $where['type'] == 7
        ) {
            $headerName = 'TouZhuFanYongExcel';
            $headerData = ['主键ID', '返佣金额', '资金类型', '备注', '日期', '投注账号', '昵称', '角色', '收益账号', '我的上级', '订单号', '余额'];
        } else {
            $headerName = 'QiTa--Excel';
            $headerData = ['主键ID', '变动金额', '变动类型', '备注', '日期', '昵称', '角色', '账号', '我的上级'];
        }

        $where = $this->commonFilter($where, 'vp');
        $model = self::where($where)->field($data)->order($order)->select();
        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        $member = new Member();
        foreach ($data as &$v) {
            // 类型
            $v['type'] = self::getStrByType($v['type']);
            $memberWhere['id'] = $v['member_id'];
            // 获取用户的昵称,账号,上级ID
            $memberData = $member->getOneMember($memberWhere, 'chn_name, role, username, top_id');
            // 释放
            unset($v['member_id']);

            list($v['chn_name'], $v['role'], $v['nick_name'], $topId) = array_values($memberData->toArray());
            // 角色
            $v['role'] = ($v['role'] === 1 ? '会员' : '代理商');
            // 获取上级昵称
            $chn_name = $member->getOneMember(['id' => $topId], 'chn_name');
            empty($chn_name) ? $v['top_name'] = '无' : $v['top_name'] = $chn_name['chn_name'];
            // 判断是否是返佣数据
            if ($checkType) {
                $orderData = Order::getFieldsByWhere(['id' => $v['order_id']], 'order_no,amount');
                $v['order_no'] = '无';
                $v['amount'] = '无';
                if (!empty($orderData)) {
                    $v['order_no'] = $orderData['order_no'];
                    $v['amount'] = $orderData['amount'];
                }
            }
            // 释放
            unset($v['order_id']);
        }

        // 导出
        Helper::exportExcel($headerName, $headerData, $data);
    }

    /**
     * 代理商后台导出Excel
     *
     * @param $where // 筛选条件
     * @param null $order // 排序规则
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function exportDataPxy($where, $order = null)
    {
        // 搜索字段
        $data = [
            'id', // 主键ID
            'money', // 变动金额
            'member_id', // 用户 id
            'type', // 类型
            'remark',// 备注
            'create_time',// 创建时间
            'order_id',// 订单ID
        ];
        // 组装搜索字段
        $checkType = false;
        if (isset($where['type']) && !empty($where['type'])) {
            // 资金流水记录和校正记录, 返回变动前后字段
            if ($where['type'] == 'all' || $where['type'] == 9) {
                array_push($data, 'front_money', 'later_money');
            }

            if ($where['type'] == 7) {
                // 投注人
                array_push($data, 'bet_username');
                $checkType = true;
            }
        }

        // 导出Excel
        if (
            isset($where['type'])
            && (
                !strcasecmp($where['type'], 'all')
                || $where['type'] == 9
            )
        ) {
            $headerName = 'ZiJinBianDongExcel';
            $headerData = ['主键ID', '变动金额', '变动类型', '备注', '日期', '变动前金额', '变动后金额', '昵称', '角色', '账号', '我的上级'];
        } elseif (
            isset($where['type'])
            && $where['type'] == 7
        ) {
            $headerName = 'TouZhuFanYongExcel';
            $headerData = ['主键ID', '返佣金额', '资金类型', '备注', '日期', '投注账号', '昵称', '角色', '收益账号', '我的上级', '订单号', '余额'];
        } else {
            $headerName = 'QiTa--Excel';
            $headerData = ['主键ID', '变动金额', '变动类型', '备注', '日期', '昵称', '角色', '账号', '我的上级'];
        }

        $where = $this->commonFilter($where, 'pxy');
        $model = self::where($where)->field($data)->order($order)->select();
        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        $member = new Member();
        foreach ($data as &$v) {
            // 类型
            $v['type'] = self::getStrByType($v['type']);
            $memberWhere['id'] = $v['member_id'];
            // 获取用户的昵称,账号,上级ID
            $memberData = $member->getOneMember($memberWhere, 'chn_name, role, username, top_id');
            // 释放
            unset($v['member_id']);

            list($v['chn_name'], $v['role'], $v['nick_name'], $topId) = array_values($memberData->toArray());
            // 角色
            $v['role'] = ($v['role'] === 1 ? '会员' : '代理商');
            // 获取上级昵称
            $chn_name = $member->getOneMember(['id' => $topId], 'chn_name');
            empty($chn_name) ? $v['top_name'] = '无' : $v['top_name'] = $chn_name['chn_name'];
            // 判断是否是返佣数据
            if ($checkType) {
                $orderData = Order::getFieldsByWhere(['id' => $v['order_id']], 'order_no,amount');
                $v['order_no'] = '无';
                $v['amount'] = '无';
                if (!empty($orderData)) {
                    $v['order_no'] = $orderData['order_no'];
                    $v['amount'] = $orderData['amount'];
                }
            }
            // 释放
            unset($v['order_id']);
        }

        // 导出
        Helper::exportExcel($headerName, $headerData, $data);
    }

    /**
     * 获取类型字符串
     *
     * @param $type // 类型编号
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getStrByType($type)
    {
        switch ($type) {
            case 1:
                return '充值';
            case 2:
                return '提现';
            case 3:
                return '购彩';
            case 4:
                return '资金冻结';
            case 5:
                return '奖金';
            case 6:
                return '加奖';
            case 7:
                return '投注返佣';
            case 8:
                return '充值赠送';
            case 9:
                return '资金校正';
            case 10:
                return '跟单返佣';
            case 11:
                return '代充扣除';
            default:
                return '';
        }
    }

    /**
     * @desc 获取代理商下的充值记录列表
     * @auther LiBin
     * @param $where
     * @param null $order
     * @return bool|\think\Paginator
     * @throws \Exception
     * @date 2019-04-02
     * @api *
     */
    public function getAgentList($where, $order = null)
    {
        // 每页数据条数
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        // 查询字段
        $data = [
            'id', // 主键ID
            'money', // 变动金额
            'member_id', // 用户 id
            'type', // 类型
            'remark',// 备注
            'create_time',// 创建时间
            'order_id',// 订单ID
        ];

        $checkType = false;
        if (isset($where['type']) && !empty($where['type'])) {
            // 资金流水记录和校正记录, 返回变动前后字段
            if ($where['type'] == 'all' || $where['type'] == 9) {
                array_push($data, 'front_money', 'later_money');
            }

            if ($where['type'] == 7) {
                // 投注人
                array_push($data, 'bet_username');
                $checkType = true;
            }
        }

        $where = $this->commonFilter($where, 'pxy');
        $paginate = self::where($where)->field($data)->order($order)->paginate($perPage);
        $member = new Member();
        foreach ($paginate as &$v) {
            $memberWhere['id'] = $v['member_id'];
            //获取用户的昵称,账号,上级ID
            $memberData = $member->getOneMember($memberWhere, 'chn_name, role, username, top_id');
            // 释放
            unset($v['member_id']);
            list($v['chn_name'], $v['role'], $v['nick_name'], $v['top_id']) = array_values($memberData->toArray());
            //获取上级昵称
            $chn_name = $member->getOneMember(['id' => $v['top_id']], 'chn_name');
            empty($chn_name) ? $v['top_name'] = '' : $v['top_name'] = $chn_name['chn_name'];
            //判断是否是返佣数据
            if ($checkType) {
                $orderData = Order::getFieldsByWhere(['id' => $v['order_id']], 'order_no,amount');
                $v['order_no'] = '';
                $v['amount'] = '';
                if (!empty($orderData)) {
                    $v['order_no'] = $orderData['order_no'];
                    $v['amount'] = $orderData['amount'];
                }
            }

            // 释放
            unset($v['order_id']);
        }

        return $paginate;
    }

    /**
     * @desc 注单返佣
     * @author LiBin
     * @param $order_no //订单号
     * @param $belong //默认总后台 1 总后台 2 代理商后台 3 APP
     * @throws \Exception
     * @date 2019-04-04
     */
    public function rebate($order_no, $belong = 1)
    {
        // 查询订单信息
        $order = new Order();
        $orderData = $order->getOrderDetails(['order_no' => $order_no], 'id,member_id,lottery_id,amount,pay_status,is_moni,order_no');
        if (empty($orderData)) { // 不存在的订单直接返回.
            return;
        }

        // 判断订单是否模拟
        if ($orderData['is_moni'] == 1) {
            return;
        }

        // 获取用户的所有上级ID
        $member = new Member();
        $rebMemberData = $member->getOneMember([['id', '=', $orderData['member_id']]], 'path,username');
        $path = rtrim($rebMemberData['path'], ',');//去除两边空格
        $pathArr = explode(',', $path);
        array_shift($pathArr); // 去除数组的第一个元素
        $pathArr = array_reverse($pathArr); // 反转数组
        if (empty($pathArr)) { // 判断没有上级无需返佣
            return;
        }

        // 获取会员返佣比例
        $memberconfig = new AdminConfig();
        $ratio = (float)$memberconfig->config('commission');
        $record = 0;
        $addRatio = [];
        try {
            // 开启事务
            Db::startTrans();
            foreach ($pathArr as $k => $v) {
                // 获取用户数据(加排他锁)
                $memberData = Member::where('id', $v)
                    ->field([
                        'id', // 用户ID
                        'role', // 角色
                        'is_moni', // 是否模拟
                        'hadsel', // 彩金
                        'username', // 用户账号
                        'frozen', // 是否冻结
                    ])
                    ->lock(true)
                    ->find();
                if (!isset($memberData['id'])) {
                    // 用户不存在, 则跳过
                    continue;
                }

                // 判断是否是虚拟 虚拟账号直接终止程序
                if ($memberData['is_moni'] == 0) {
                    Db::commit();
                    return;
                }

                // 判断会员,会员状态是开启的.
                if ($memberData['role'] == 1 && $memberData['frozen'] == 1) {
                    // 直属上级会员返佣
                    if ($k == 0) {
                        if (!empty($ratio)) { // 判断会员是否设置返佣
                            // 计算返佣金额
                            $rebateMoney = $orderData['amount'] * ($ratio * 0.01);
                            // 防止出现负数
                            if ($rebateMoney <= 0) {
                                $rebateMoney = 0;
                            }
                            // 加入到用户彩金里面
                            $hadsel = $memberData['hadsel'] + $rebateMoney;
                            // 写入彩金
                            $member->setMember(['id' => $v], ['hadsel' => $hadsel]);
                        } else {
                            continue;
                        }

                    } else {
                        continue;
                    }
                } else {
                    // 代理商
                    if ($memberData['frozen'] != 1) { // 代理商冻结状态直接跳过不返佣.
                        continue;
                    }

                    // 获取代理商的彩种返佣比例
                    $memberRatio = new MemberRatio();
                    $ratio = $memberRatio->getValByWhere(['member_id' => $v, 'lottery_id' => $orderData['lottery_id'], 'status' => 1], 'ratio');
                    if (empty(floatval($ratio))) { // 未设置返佣比例直接跳出当前循环.
                        continue;
                    }

                    // 计算返佣金额
                    $rebateMoney = ($orderData['amount'] * ($ratio * 0.01)) - $record;
                    // 对返佣金额进行累加方便进行下次返佣的计算.
                    $record += $rebateMoney;
                    // 防止出现负数
                    if ($rebateMoney <= 0) {
                        $rebateMoney = 0;
                    }
                    // 加入到用户彩金里面
                    $hadsel = $memberData['hadsel'] + $rebateMoney;
                    // 更新用户的彩金
                    $member->setMember(['id' => $v], ['hadsel' => $hadsel]);
                }

                // 组装返佣记录数据
                $addRatio[$k]['member_id'] = $v;  // 受益人ID
                $addRatio[$k]['money'] = $rebateMoney; // 变动金额
                $addRatio[$k]['front_money'] = $memberData['hadsel']; // 变动前总彩金
                $addRatio[$k]['later_money'] = $hadsel; // 变动后总彩金
                $addRatio[$k]['remark'] = '注单返佣收益'; // 备注（变动原因）
                $addRatio[$k]['type'] = 7; // 变动类型 1：充值  2：提现  3：购彩  4：资金冻结 5：奖金  6：系统嘉奖  7：投注返佣  8：充值赠送 9:资金校正 10:跟单返佣
                $addRatio[$k]['create_time'] = date('Y-m-d H:i:s'); // 创建时间
                $addRatio[$k]['update_time'] = date('Y-m-d H:i:s'); //更新时间
                $addRatio[$k]['order_id'] = $orderData['id']; // 注单ID
                $addRatio[$k]['identify'] = $memberData['role']; // 受益人角色 1:会员 2:代理商'
                $addRatio[$k]['username'] = $memberData['username']; // 受益人账号
                $addRatio[$k]['bet_username'] = $rebMemberData['username']; // 投注账号
            }

            // 添加资金流水记录
            if (!empty($addRatio)) {
                self::saveAll($addRatio);
            }

            Db::commit();
            return;
        } catch (\Exception $e) {
            Db::rollback();
            Helper::log('系统', '注单返佣', "注单:{$order_no}返佣失败", $e->getMessage(), 0, $belong);
        }
    }

    /**
     * 获取累计充值
     *
     * @param int $type // 变动类型
     * @param null $where // 筛选条件
     * @return float
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getRechargeCount($type, $where = null)
    {
        $count = self::where($where)
            ->where('type', $type)
            ->sum('money');
        return $count;
    }

    /**
     * @desc 账单明细
     * @author LiBin
     * @param $where // 筛选条件  page页码 platform平台 1.足彩 2.篮彩 3.北京单场  starttime 开始时间 endtime结束时间  type 1.一周 2.一月
     * @param $number // 每页数据条数
     * @param null $uid // 会员ID
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-04-15
     */
    public function getBillingDetails($where, $number, $uid = null)
    {
        // 获取页码
        if (isset($where['page']) && !empty($where['page'])) {
            $page = $where['page'];
        } else {
            $page = 1;
        }

        // 计算偏移量
        $limit = ($page - 1) * $number . ',' . $number;
        $whereData = [];
        // 会员筛选
        if (!empty($uid)) {
            $whereData[] = ['a.member_id', '=', $uid];
        }

        // 时间筛选
        if (
            isset($where['starttime'])
            && !empty($where['starttime'])
            && isset($where['endtime'])
            && !empty($where['endtime'])
        ) {
            $whereData[] = ['a.create_time', 'between', [$where['starttime'] . ' 00:00:00', $where['endtime'] . ' 23:59:59']];
        } elseif (isset($where['type']) && !empty($where['type'])) {
            $endtime = date('Y-m-d');
            if ($where['type'] == 1) { // 七天时间
                $starttime = date("Y-m-d", strtotime("-7 days", time()));
            }

            if ($where['type'] == 2) { // 三十天时间
                $starttime = date("Y-m-d H:i:s", strtotime("-1 months", time()));
            }

            $whereData[] = ['a.create_time', 'between', [$starttime . ' 00:00:00', $endtime . ' 23:59:59']];
        }

        // 彩种筛选
        if (isset($where['platform']) && !empty($where['platform'])) {
            // 获取彩种ID
            switch ($where['platform']) {
                case 1: // 竞彩足球
                    $code = Config::ZC_CODE;
                    break;
                case 2: // 篮彩
                    $code = Config::LC_CODE;
                    break;
                case 3: // 北京单场
                    $code = Config::BJ_CODE;
                    break;
                case 4: // 排三
                    $code = Config::P3_CODE;
                    break;
                case 5: // 排五
                    $code = Config::P5_CODE;
                    break;
                case 6: // 澳彩
                    $code = Config::AO_CODE;
                    break;
                case 7: // 葡彩
                    $code = Config::PC_CODE;
                    break;
                case 8: // 幸运飞艇
                    $code = Config::FT_CODE;
                    break;
            }

            $lottery = new Lottery();
            $id = $lottery->getIdByCode($code);
            $whereData[] = ['b.lottery_id', '=', $id];
            $fundLogData = self::alias('a')
                ->leftJoin('order b', 'a.order_id=b.id')
                ->where($whereData)
                ->field(['a.create_time', 'a.remark', 'a.later_money', 'a.money', 'a.front_money'])
                ->order('a.id DESC')
                ->limit($limit)
                ->select();
            // 统计总页数
            $fungLogDataCount = self::alias('a')
                ->leftJoin('order b', 'a.order_id=b.id')
                ->where($whereData)
                ->count('a.id');
            // 总页数
            $pageCount = ceil($fungLogDataCount / $number);
        } else {
            // 不存在平台选项的情况
            $fundLogData = self::alias('a')
                ->where($whereData)
                ->field(['a.create_time', 'a.remark', 'a.later_money', 'a.money', 'a.front_money'])
                ->order('a.id DESC')
                ->limit($limit)
                ->select();
            // 统计总页数
            $fungLogDataCount = self::alias('a')
                ->where($whereData)
                ->count('a.id');
            // 总页数
            $pageCount = ceil($fungLogDataCount / $number);
        }

        // 处理数据
        $returnData = [];
        if (!empty($fundLogData)) {
            foreach ($fundLogData as $k => $v) {
                $place = strpos($v['remark'], $v['money']);
                if (!empty($place)) {
                    $fundLogData[$k]['remark'] = substr($v['remark'], 0, $place);//去除标题上带金额的字符串
                }

                $year = date('Y', strtotime($v['create_time']));
                $month = date('m-d', strtotime($v['create_time']));
                $fundLogData[$k]['year'] = $year;
                $fundLogData[$k]['month'] = $month;
                // 变动金额(带符号)
                $fundLogData[$k]['money'] = (string)floatval(bcsub($v['later_money'], $v['front_money'], 2));
                unset($fundLogData[$k]['front_money']);
                unset($fundLogData[$k]['create_time']);
            }

            $returnData['datas'] = $fundLogData;
            $returnData['pageCount'] = (int)$pageCount;
        }

        return $returnData;
    }

    /**
     * @desc 统计资金记录
     * @auther LiBin
     * @param $where
     * @param $data
     * @return float
     * @date 2019-04-17
     */
    public function getCountFundLog($where, $data)
    {
        return self::where($where)->sum($data);
    }

    /**
     * @desc 获取返佣记录列表
     * @auther LiBin
     * @param $where
     * @param $data
     * @return array|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-04-17
     */
    public function getRelaList($where, $data)
    {
        return self::alias('a')
            ->join('order b', 'a.order_id=b.id')
            ->join('member c', 'c.id=b.member_id')
            ->where($where)
            ->field($data)
            ->order('a.create_time')
            ->limit('0,20')
            ->select();
    }
}
