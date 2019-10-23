<?php

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;

/**
 * 提现记录模型
 * Date: 2019/3/10
 * Time: 18:11
 * Author jimadela
 * Github https://github.com/JimAdela
 * Blog https://jimadela.github.io/
 */
class FundWithdraw extends BaseModel
{
    // 提现状态
    const STATUS_AWAIT = 1;  // 审核中
    const STATUS_SUCCESS = 2;  // 提现中
    const STATUS_ERROR = 3;  // 已驳回
    const OUT_SUCCESS = 4; // 提现成功
    const OUT_ERROR = 5; // 提现失败

    /**
     * 条件筛选
     *
     * @param $where
     * @param string $platform
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
            $endWhere[] = ['create_at', 'between time', [$where['startDate'], $where['endDate']]];
        } else {
            if (isset($where['startDate']) && !empty($where['startDate'])) {
                $endWhere[] = ['create_at', '>=', $where['startDate']];
            }

            if (isset($where['endDate']) && !empty($where['endDate'])) {
                $endWhere[] = ['create_at', '<=', $where['endDate']];
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

        return $endWhere;
    }


    /**
     * @desc 获取提现记录列表
     * @author LiBin
     * @param $where
     * @param null $order
     * @return \think\Paginator
     * @throws \Exception
     * @date 2019-04-02
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
                'bank_id', // 银行卡
                'member_id', // 用户 id
                'account', // 提现金额
                'to_account', // 到账金额
                'status', // 充值状态
                'create_at', // 创建时间
                'identify' => 'role', // 用户角色
                'username', // 用户账号
                'remark', // 用户提现备注
            ])
            ->order($order)
            ->paginate($perPage);
        foreach ($paginate as &$v) {
            $memberData = Member::getFieldsByWhere(['id' => $v['member_id']], ['chn_name', 'username']);
            $v['username'] = $memberData['username']; // 用户名
            $v['nick_name'] = $memberData['chn_name']; // 昵称
            // 获取银行卡信息
            $bankData = MemberBank::getFieldsByWhere(['id' => $v['bank_id']], [
                'bank', // 银行名称
                'bank_num', // 银行卡号
                'cardholder', // 持卡人姓名
            ]);

            $v['bank_name'] = '';
            $v['bank_num'] = '';
            $v['cardholder'] = '';
            if (!empty($bankData)) {
                list($v['bank_name'], $v['bank_num'], $v['cardholder']) = array_values($bankData);
            }

        }

        return $paginate;
    }

    /**
     * 总后台Excel数据导出
     *
     * @param $where // 查询条件
     * @param null $order // 排序规则
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
                'bank_id', // 银行卡ID
                'member_id', // 用户 id
                'account', // 提现金额
                'to_account', // 到账金额
                'status', // 充值状态
                'create_at', // 创建时间
                'identify' => 'role', // 用户角色
                'username', // 用户账号
            ])
            ->order($order)
            ->select();

        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        foreach ($data as &$v) {
            // 获取审核状态字符串
            $v['status'] = self::getStatusStrByState($v['status']);
            $v['role'] = ($v['role'] === 1 ? '会员' : '代理商');
            $nickName = Member::getValByWhere(['id' => $v['member_id']], 'chn_name');
            $v['nick_name'] = $nickName; // 昵称
            unset($v['member_id']);
            // 获取银行卡信息
            $bankData = MemberBank::getFieldsByWhere(['id' => $v['bank_id']], [
                'bank', // 银行名称
                'bank_num', // 银行卡号
                'cardholder', // 持卡人姓名
            ]);
            unset($v['bank_id']);
            $v['bank_name'] = '';
            $v['bank_num'] = '';
            $v['cardholder'] = '';
            if (!empty($bankData)) {
                list($v['bank_name'], $v['bank_num'], $v['cardholder']) = array_values($bankData);
            }
        }

        // 输出Excel
        Helper::exportExcel(
            'withdrawExcel',
            [
                '主键ID', '提现金额', '到账金额', '充值状态',
                '提现时间', '用户身份', '用户账号', '昵称',
                '银行卡名称', '银行卡号', '持卡人姓名',
            ],
            $data);
    }

    /**
     * 代理商后台Excel数据导出
     *
     * @param $where // 查询条件
     * @param null $order // 排序规则
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
                'bank_id', // 银行卡ID
                'member_id', // 用户 id
                'account', // 提现金额
                'to_account', // 到账金额
                'status', // 充值状态
                'create_at', // 创建时间
                'identify' => 'role', // 用户角色
                'username', // 用户账号
            ])
            ->order($order)
            ->select();

        $data = [];
        if (!empty($model)) {
            $data = $model->toArray();
        }

        foreach ($data as &$v) {
            // 获取审核状态字符串
            $v['status'] = self::getStatusStrByState($v['status']);
            $v['role'] = ($v['role'] === 1 ? '会员' : '代理商');
            $nickName = Member::getValByWhere(['id' => $v['member_id']], 'chn_name');
            $v['nick_name'] = $nickName; // 昵称
            unset($v['member_id']);
            // 获取银行卡信息
            $bankData = MemberBank::getFieldsByWhere(['id' => $v['bank_id']], [
                'bank', // 银行名称
                'bank_num', // 银行卡号
                'cardholder', // 持卡人姓名
            ]);
            unset($v['bank_id']);
            $v['bank_name'] = '';
            $v['bank_num'] = '';
            $v['cardholder'] = '';
            if (!empty($bankData)) {
                list($v['bank_name'], $v['bank_num'], $v['cardholder']) = array_values($bankData);
            }
        }

        // 输出Excel
        Helper::exportExcel(
            'withdrawExcel',
            [
                '主键ID', '提现金额', '到账金额', '充值状态',
                '提现时间', '用户身份', '用户账号', '昵称',
                '银行卡名称', '银行卡号', '持卡人姓名',
            ],
            $data);
    }

    /**
     * 获取审核状态字符串
     *
     * @param $state // 状态码
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getStatusStrByState($state)
    {
        switch ($state) {
            case 1:
                return '审核中';
            case 2:
                return '审核通过';
            case 3:
                return '已驳回';
            default:
                return '';
        }
    }

    /**
     * @desc 获取代理商下的充值记录列表
     * @auther LiBin
     * @param $where
     * @param null $order
     * @return \think\Paginator
     * @throws \Exception
     * @date 2019-04-02
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
                'bank_id', // 银行卡
                'member_id', // 用户 id
                'account', // 提现金额
                'to_account', // 到账金额
                'status', // 充值状态
                'create_at', // 创建时间
                'identify' => 'role', // 用户角色
                'username', // 用户账号
                'remark', // 用户提现备注
            ])
            ->order($order)
            ->paginate($perPage);
        foreach ($paginate as &$v) {
            $nickName = Member::getValByWhere(['id' => $v['member_id']], 'chn_name');
            $v['nick_name'] = $nickName; // 昵称
            // 获取银行卡信息
            $bankData = MemberBank::getFieldsByWhere(['id' => $v['bank_id']], [
                'bank', // 银行名称
                'bank_num', // 银行卡号
                'cardholder', // 持卡人姓名
            ]);

            $v['bank_name'] = '';
            $v['bank_num'] = '';
            $v['cardholder'] = '';
            if (!empty($bankData)) {
                list($v['bank_name'], $v['bank_num'], $v['cardholder']) = array_values($bankData);
            }

        }

        return $paginate;
    }

    /**
     * 实名信息
     *
     * @desc *
     * @author LiBin
     * @param $memberId // 会员ID
     * @param $fundId // 提现记录主键ID
     * @return array
     * @throws \Exception
     * @date 2019-04-02
     * @api *
     */
    public function getDetail($memberId, $fundId)
    {
        if (empty($memberId)) {
            return [];
        }

        $res = self::alias('f')
            ->leftJoin('member_bank b', 'b.id=f.bank_id')
            ->leftJoin('member m', 'm.id=f.member_id')
            ->field([
                'm.username', // 账号
                'm.id_card', // 身份证号
                'm.real_name', // 真实姓名
                'b.bank', //银行名称
                'b.bank_num', //银行卡号
                'b.cardholder', //持卡人人姓名
            ])
            ->where('m.id', $memberId)
            ->where('f.id', $fundId)
            ->find();
        if (!$res) {
            return [];
        }

        return $res;
    }
}
