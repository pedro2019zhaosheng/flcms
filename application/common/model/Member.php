<?php

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;
use think\Db;

/**
 * 会员数据模型
 * Class User
 * @package app\common\model
 */
class Member extends BaseModel
{
    /**
     * @desc 条件筛选
     * @auther LiBin
     * @return array
     * @param $where
     * @date 2019-03-05
     */
    private function commonFilter($where)
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

        // 账号筛选
        if (isset($where['username']) && !empty($where['username'])) {
            $endWhere[] = ['username', 'like', '%' . $where['username'] . '%'];
        }

        //昵称筛选
        if (isset($where['chn_name']) && !empty($where['chn_name'])) {
            $endWhere[] = ['chn_name', 'like', '%' . $where['chn_name'] . '%'];
        }

        //状态过滤  0：冻结   1：启用
        if (isset($where['state'])) {
            if (!($where['state'] === '')) {
                $endWhere[] = ['frozen', '=', $where['state']];
            }
        }

        //类型过滤  0：模拟   1：真实
        if (isset($where['type'])) {
            $endWhere[] = ['is_moni', '=', $where['type']];
        }

        //根据上级ID查询下级
        if (isset($where['topId']) && !empty($where['topId'])) {
            $endWhere[] = ['top_id', '=', $where['topId']];
        }

        //获取角色
        if (isset($where['role']) && !empty($where['role'])) {
            $endWhere[] = ['role', '=', $where['role']];
        }

        // 关键词筛选
        if (isset($where['keyword']) && !empty($where['keyword'])) {
            $endWhere[] = ['chn_name', 'like', '%' . $where['keyword'] . '%'];
        }

        // 路径筛选
        if (isset($where['path']) && !empty($where['path'])) {
            $endWhere[] = ['path', 'like', '%,' . $where['path'] . ',%'];
        }
        return $endWhere;
    }

    /**
     * 代理充值获取会员列表
     *
     * @param $amountNum // 账号
     * @param $platform // 平台 vp:总后台  pxy:代理商后台
     * @return array|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getMemberRechargeList($amountNum, $platform = 'vp')
    {
        if (!strcasecmp($platform, 'vp')) {
            $where = ['username' => $amountNum];
        } else {
            $where = [
                ['username', '=', $amountNum],
                ['path', 'like', '%,' . UID . ',%'],
            ];
        }

        $data = self::getFieldsByWhere($where, [
            'id', // 会员ID
            'username', // 用户账号
            'chn_name', // 昵称
            'role', // 角色
            'is_moni', // 是否模拟
            'frozen', // 状态
            'is_delete', // 是否删除  1:已删除
            'photo', // 头像
            'balance', // 余额
            'frozen_capital', // 冻结资金
            'hadsel', // 彩金
        ]);

        if (isset($data['is_delete']) && $data['is_delete'] === 1) {
            return '该会员已被删除';
        }

        if (!empty($data)) {
            $data['photo'] = Attach::getPathByAttachId($data['photo']) ?: '/static/lib/images/admin.png';
        }

        return $data;
    }

    /**
     * 会员充值
     *
     * @param $uid // 会员ID
     * @param $amount // 金额
     * @param $platform // 平台
     * @return string|true
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function memberRecharge($uid, $amount, $platform = 'vp')
    {
        try {
            Db::startTrans();
            // 业务执行前, 获取会员信息
            $memberData = self::getFieldsByWhere(['id' => $uid], [
                'role', // 角色
                'username', // 账号
                'hadsel', // 彩金
                'is_moni', // 是否模拟, 0:模拟  1:真实
            ]);
            // 查看会员是否存在
            if (empty($memberData)) {
                trigger_error('会员不存在, 错误代码160', E_USER_WARNING);
            }

            // 更改会员彩金
            $result = self::where('id', $uid)
                ->setInc('hadsel', $amount);
            if (!$result) {
                trigger_error('充值失败, 错误代码167', E_USER_WARNING);
            }

            // 更改会员总充值
            $totalRecharge = self::where('id', $uid)
                ->setInc('recharge', $amount);
            if (!$totalRecharge) {
                trigger_error('充值失败, 错误代码174', E_USER_WARNING);
            }

            // 如果是真实账号, 则添加资金和充值记录
            if ((int)$memberData['is_moni'] === 1) {
                // 增加充值记录
                $chargeId = FundCharge::quickCreate([
                    'order_no' => Helper::orderNumber(), // 订单号
                    'member_id' => $uid,
                    'account' => $amount, // 充值金额
                    'to_account' => $amount, // 到账金额
                    'type' => 4, // 代充值
                    'status' => 2, // 成功
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                    'identify' => $memberData['role'],
                    'username' => $memberData['username'],
                ]);
                if (!$chargeId) {
                    trigger_error('充值失败, 错误代码193', E_USER_WARNING);
                }

                if (!strcasecmp($platform, 'vp')) {
                    $username = Admin::getValByWhere(['id' => UID], 'username');
                    $remark = '总后台用户' . $username . '代充';
                } else {
                    $userData = self::getFieldsByWhere(['id' => UID], [
                        'username', // 代理商账号
                        'balance', // 代理商余额
                        'hadsel', // 代理商彩金
                        'is_moni', // 是否模拟
                        'role', // 角色
                    ]);
                    $remark = '代理商' . $userData['username'] . '代充值';
                    $outAmount = 0; // 扣除余额金额
                    $outHadsel = 0; // 扣除彩金金额
                    if ($userData['balance'] >= $amount) {
                        // 扣除代理商余额
                        $decBalance = self::where('id', UID)->setDec('balance', $amount);
                        $outAmount = $amount;
                        if (!$decBalance) {
                            trigger_error('充值失败, 错误代码209', E_USER_WARNING);
                        }

                    } elseif (bcadd($userData['balance'], $userData['hadsel'], 2) >= $amount) {
                        $userData['balance'] = (float)$userData['balance'];
                        $surplusAmount = $amount;
                        if (!empty($userData['balance'])) {
                            $decAllBal = self::where('id', UID)->setDec('balance', 0);
                            if (!$decAllBal) {
                                trigger_error('充值失败, 错误代码221', E_USER_WARNING);
                            }

                            $outAmount = $userData['balance'];
                            // 剩余充值金额
                            $surplusAmount = bcsub($amount, $userData['balance'], 2);
                        }

                        $decHadsel = self::where('id', UID)->setDec('hadsel', $surplusAmount);
                        $outHadsel = $surplusAmount;
                        if (!$decHadsel) {
                            trigger_error('充值失败, 错误代码230', E_USER_WARNING);
                        }

                    } else {
                        trigger_error('您的余额不足, 请充值后重试', E_USER_WARNING);
                    }

                    // 代理商不是模拟, 记录代理商资金流水记录
                    if ($userData['is_moni'] === 1) {
                        $insertLogData = [];
                        // 记录余额流水记录
                        if ($outAmount !== 0) {
                            array_push($insertLogData, [
                                'member_id' => UID,
                                'money' => $outAmount,
                                'front_money' => $userData['balance'],
                                'later_money' => bcsub($userData['balance'], $outAmount, 2),
                                'type' => 11,
                                'remark' => '代理商代充值余额扣除',
                                'create_time' => Helper::timeFormat(time(), 's'),
                                'update_time' => Helper::timeFormat(time(), 's'),
                                'identify' => $userData['role'],
                                'username' => $userData['username'],
                            ]);
                        }

                        // 记录彩金流水记录
                        if ($outHadsel !== 0) {
                            array_push($insertLogData, [
                                'member_id' => UID,
                                'money' => $outHadsel,
                                'front_money' => $userData['hadsel'],
                                'later_money' => bcsub($userData['hadsel'], $outHadsel, 2),
                                'type' => 11,
                                'remark' => '代理商代充值彩金扣除',
                                'create_time' => Helper::timeFormat(time(), 's'),
                                'update_time' => Helper::timeFormat(time(), 's'),
                                'identify' => $userData['role'],
                                'username' => $userData['username'],
                            ]);
                        }

                        $fundLogModel = new FundLog;
                        $fundLogRe = $fundLogModel->insertAll($insertLogData);
                        if (!$fundLogRe) {
                            trigger_error('充值失败, 错误代码227', E_USER_WARNING);
                        }
                    }
                }

                // 增加会员资金流水
                $fundLogId = FundLog::quickCreate([
                    'member_id' => $uid,
                    'money' => $amount,
                    'front_money' => $memberData['hadsel'],
                    'later_money' => $memberData['hadsel'] + $amount,
                    'type' => 1,
                    'remark' => $remark,
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                    'identify' => $memberData['role'],
                    'username' => $memberData['username'],
                ]);
                if (!$fundLogId) {
                    trigger_error('充值失败, 错误代码246', E_USER_WARNING);
                }
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * APP会员充值,产生的订单记录.
     *
     * @param $uid // 会员ID
     * @param $amount // 金额
     * @param $type //1.支付宝 2.微信 3.银联
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function memberRechargeApp($uid, $amount, $type)
    {
        // 业务执行前, 获取会员信息
        $memberData = self::getFieldsByWhere(['id' => $uid], [
            'role', // 角色
            'username', // 账号
            'hadsel', // 彩金
            'is_moni', // 是否模拟, 0:模拟  1:真实
        ]);

        // 查看会员是否存在
        if (empty($memberData)) {
            return ['code' => 0, 'msg' => '会员不存在'];
        }

        // 如果是真实账号, 则添加资金和充值记录
        if ((int)$memberData['is_moni'] === 1) {
            $addData = [
                'order_no' => Helper::orderNumber(), // 订单号
                'member_id' => $uid,
                'account' => $amount, // 充值金额
                'to_account' => $amount, // 到账金额
                'type' => $type, // 支付方式
                'status' => 1, // 待支付
                'create_time' => Helper::timeFormat(time(), 's'),
                'update_time' => Helper::timeFormat(time(), 's'),
                'identify' => $memberData['role'],
                'username' => $memberData['username'],
            ];
            // 增加充值记录
            $chargeId = FundCharge::quickCreate($addData);

            if (!$chargeId) {
                return ['code' => 0, 'msg' => '充值失败'];
            }

            return ['code' => 1, 'msg' => '获取成功', 'data' => $addData['order_no']];
        } else {
            return ['code' => 0, 'msg' => '模拟账户不能充值'];
        }
    }

    /**
     * 会员列表
     *
     * @param $where // 检索条件
     * @param $data // 获取数据
     * @param $order // 排序
     * @return \think\Paginator
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getUser($where, $data, $order)
    {
        $perPage = 10;
        if (isset($where['perPage']) && !empty($where['perPage'])) {
            $perPage = (int)$where['perPage'];
        }

        $where = $this->commonFilter($where);
        $where[] = ['is_delete', '=', 0];
        $paginate = self::where($where)
            ->field($data)
            ->order($order)
            ->paginate($perPage);
        foreach ($paginate as $k => $v) {
            $paginate[$k]['RecUserNumber'] = self::getRecUser($v['id']);
            $paginate[$k]['photo'] = Attach::getPathByAttachId($v['photo']);
        }

        return $paginate;
    }

    /**
     * 数据导出
     *
     * @param $where // 筛选条件
     * @param $data // 查询数据
     * @param string $order // 排序规则
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function exportMember($where, $data, $order = 'id DESC')
    {
        $where = $this->commonFilter($where);
        $where[] = ['is_delete', '=', 0];
        $model = self::where($where)
            ->field($data)
            ->order($order)
            ->select();

        $result = [];
        if (!empty($model)) {
            $result = $model->toArray();
        }

        foreach ($result as &$v) {
            // 推荐会员数
            $recomCount = self::getRecUser($v['id']);
            $v['recomMemberCount'] = $recomCount;
        }

        return $result;
    }

    /**
     * @desc 获取推荐的会员人数
     * @return mixed
     * @auther LiBin
     * @param $userid //会员ID
     * @date 2019-03-02
     */
    public function getRecUser($userid)
    {
        $number = self::where(['top_id' => $userid])
            ->where('is_delete', 0)
            ->count('id');
        return $number;
    }

    /**
     * 添加会员
     * @auther LiBin
     * @date 2019-03-05
     */
    public function addMember($data)
    {
        return self::quickCreate($data);
    }

    /**
     * @desc 更新会员数据
     * @author LiBin
     * @param $where
     * @param $data
     * @return bool
     * @date 2019-03-07
     */
    public function setMember($where, $data)
    {
        return self::save($data, $where);
    }

    /**
     * 批量冻结或正常
     *
     * @param $ids // 会员ID
     * @param $status // 状态
     * @return bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public function toggle($ids, $status)
    {
        self::where('id', 'in', $ids)->setField('frozen', (int)$status);
        return true;
    }

    /**
     * @desc 查询指定的会员数据
     * @throws \Exception
     * @auther LiBin
     * @date 2019-03-06
     */
    public function getOneMember($where, $data)
    {
        return self::where($where)->field($data)->find();
    }

    /**
     * 通过查询条件获取指定字段的值
     *
     * @param $where
     * @param $column
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getValueByWhere($where, $column)
    {
        $value = self::where($where)->value($column);
        return $value;
    }

    /**
     * 获取会员状态
     *
     * @param $id
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function getMemberStatus($id)
    {
        return self::where('id', $id)->value('frozen');
    }

    /**
     * 删除会员
     *
     * @param $id // 会员ID
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function deleteMember($id)
    {
        return self::quickSoftDel(['is_delete' => 1, 'delete_time' => date('Y-m-d H:i:s')], $id);
    }

    /**
     * 生成会员邀请码
     *
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function generateUserInviteCode()
    {
        $code = strtoupper(Helper::randomStr(6));
        $uid = self::where('agent_invite_code', $code)->value('id');
        if (empty($uid)) {
            return $code;
        }

        return self::generateUserInviteCode();
    }

    /**
     * 向上获取上级代理的返佣数据
     *
     * @param $id // 会员ID
     * @return false|array
     * @throws \Exception
     * @author LiBin
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function chekBossAgent($id)
    {
        // 获取用户上级ID
        $topData = self::where(['id' => $id])->field('top_id')->find();
        // 判断存在上级 最高级的top_id 为0;
        if (empty($topData['top_id'])) {
            return false;
        } else {
            $data = self::where(['id' => $topData['top_id']])->field('id,top_id,role')->find();
            if ($data['role'] != 2) {
                return self::chekBossAgent($data['top_id']);
            } else {
                //获取返佣数据
                $memberRatio = new MemberRatio();
                $ratio = $memberRatio->getLottery($data['id']);
                if (!count($ratio) && !empty($data['top_id'])) {
                    return self::chekBossAgent($data['top_id']);
                }

                if (count($ratio)) {
                    foreach ($ratio as $k => $v) {
                        $rdata[$v['lottery_id']] = $v['ratio'];
                    }
                } else {
                    $rdata = false;
                }
                return $rdata;
            }
        }
    }

    /**
     * 我的所有下级ID
     *
     * @param $id // 用户ID
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getDownUid($id)
    {
        $ids = self::where('path', 'like', '%,' . $id . ',%')
            ->where('is_delete', 0)
            ->column('id');
        return $ids;
    }

    /**
     * 我的所有直属下级代理商ID
     *
     * @param integer $uid 代理商ID
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getMyDownAgentId($uid)
    {
        $ids = self::where('top_id', $uid)
            ->where('role', 2)
            ->where('is_delete', 0)
            ->column('id');
        return $ids;
    }

    /**
     * 转移会员
     *
     * @param $platform // 平台  vp:总后台   pxy: 代理商后台
     * @param $fromUserId // 源会员ID
     * @param $toUsername // 目标会员ID
     * @param $password // 操作密码
     * @param $isTransferSelf // 是否转移自身, 默认 true
     * @throws \Exception
     * @return bool|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function transformUser($platform, $fromUserId, $toUsername, $password, $isTransferSelf = true)
    {
        try {
            // 开启事务
            Db::startTrans();
            // 判断目标会员是否存在
            $toUserInfo = self::getFieldsByWhere(['username' => $toUsername], 'id,role');
            if (!$toUserInfo) {
                trigger_error('账号不存在', E_USER_WARNING);
            }

            // 角色
            $role = $toUserInfo['role'];
            if ($role === 1) {
                trigger_error('转移目标账号不是代理商, 请提升代理商后台重试!');
            }

            // 目标代理商ID
            $toUserId = $toUserInfo['id'];

            // 判断操作密码是否正确
            $password = md5($password);
            if (!strcasecmp($platform, 'vp')) {
                $admin = new Admin();
                $verifyResult = $admin->getOneAll(['id' => UID, 'password' => $password]);
            } else {
                $verifyResult = self::getValByWhere(['id' => UID, 'password' => $password], 'username');
            }

            if (empty($verifyResult)) {
                trigger_error('请输入正确的操作密码', E_USER_WARNING);
            }

            // 获取源会员账号
            $fromUser = self::getValByWhere(['id' => $fromUserId], 'username');
            if (empty($fromUser)) {
                trigger_error('您的账号出现异常', E_USER_WARNING);
            }

            // 如果目标账号是源账号, 则直接返回
            if ($toUsername == $fromUser) {
                return true;
            }

            // 获取目标会员路径
            $path = self::getValByWhere(['id' => $toUserId], 'path');
            $path .= $toUserId;

            // 如果参数是true, 则转移自身
            if ($isTransferSelf) {
                // 先转移自身
                self::where('id', $fromUserId)->setField([
                    'top_id' => $toUserId,
                    'path' => $path . ',',
                    'top_username' => $toUsername,
                ]);
            }

            // 转移我的下级
            $fromUserJunior = self::where('path', 'like', '%,' . $fromUserId . ',%')
                ->field([
                    'id',
                    'path',
                ])
                ->select()
                ->toArray();
            foreach ($fromUserJunior as $item) {
                $pathPart = strstr($item['path'], ',' . $fromUserId . ',');
                if ($pathPart === false) {
                    trigger_error('转移会员失败', E_USER_WARNING);
                }

                if ($isTransferSelf) {
                    $savePath = $path . $pathPart;
                } else {
                    $savePath = $path . ',' . ltrim($pathPart, ',' . $fromUserId . ',');
                }

                self::where('id', $item['id'])->setField([
                    'path' => $savePath, // 路径
                ]);
            }

            if (!$isTransferSelf) {
                // 如果不转移自身, 则更改源会员直属下级数据
                self::where('top_id', $fromUserId)->setField([
                    'top_id' => $toUserId, // 更改直属上级ID
                    'top_username' => $toUsername, // 更改直属上级账号
                ]);
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 获取所有的代理商列表
     *
     * @param $where
     * @param $data
     * @return array|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author LiBin
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getAll($where, $data)
    {
        return self::where($where)->field($data)->select();
    }

    /**
     * 获取代理商详情
     *
     * @param $agentId
     * @return array
     * @author LiBin
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getOneInfo($agentId)
    {
        $data = self::quickGetOne($agentId);
        $result = [];
        $result['nick_name'] = $data['chn_name'] ?: $data['username']; // 昵称
        $result['photo'] = Attach::getPathByAttachId($data['photo']); // 头像
        $result['id'] = $data['id']; // 会员ID
        $result['balance'] = $data['balance']; // 余额
        $result['hadsel'] = $data['hadsel']; // 彩金

        return $result;
    }

    /**
     * 获取会员ID
     *
     * @param $where // 查询条件
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getUidColumn($where)
    {
        return self::where($where)->column('id');
    }

    /**
     * 增加余额和彩金
     *
     * @param $memberId // 会员ID
     * @param $balance // 余额
     * @param $handsel // 彩金
     * @return boolean // 是否成功
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function writeAmount($memberId, $balance, $handsel)
    {
        try {
            Db::startTrans();
            self::where('id', $memberId)
                ->inc(['balance' => $balance, 'hadsel' => $handsel])
                ->update();
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
    }

    /**
     * 获取会员总数
     * @param $where // 查询条件
     * @return float|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getMemberCount($where = null)
    {
        $count = self::where('is_delete', 0)
            ->where('is_moni', 1)
            ->where($where)
            ->count('id');
        return $count ?: 0;
    }

    /**
     * 获取会员总余额总数
     *
     * @param null $where // 筛选条件
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getMemberBalanceCount($where = null)
    {
        $select = self::where('is_delete', 0)// 除去已删除会员
        ->where('is_moni', 1)// 除去模拟会员
        ->where($where)
            ->field([
                'SUM(balance)' => 'use_balance', // 可用余额总和
                'SUM(frozen_capital)' => 'unuse_balance' // 冻结资金总和
            ])
            ->limit(1)
            ->select();

        $data = [];
        if (!empty($select)) {
            $data = $select->toArray();
        }

        $useBalance = array_sum(array_column($data, 'use_balance')) ?: 0;
        $unUseBalance = array_sum(array_column($data, 'unuse_balance')) ?: 0;
        return $useBalance + $unUseBalance;
    }

    /**
     * 邀请码获取会员信息
     *
     * @param $inviteCode
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getUserByInviteCode($inviteCode)
    {
        $model = self::quickGetOne(null, ['agent_invite_code' => $inviteCode]);
        return $model->toArray();
    }

    /**
     * web注册
     *
     * @param $post
     * @return bool|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function webReg($post)
    {
        try {
            $inviteCode = $post['inviteCode'];
            $userInfo = $this->getUserByInviteCode($inviteCode);
            if (empty($userInfo)) {
                trigger_error('邀请码错误', E_USER_WARNING);
            }

            $devStatus = $userInfo['dev_status'];
            if (!$devStatus) {
                trigger_error('邀请人发展下线权限被禁用', E_USER_WARNING);
            }

            $pid = $userInfo['id'];
            $topUser = $userInfo['username'];
            $path = $userInfo['path'] . $pid . ',';
            // 邀请码
            $aic = Member::generateUserInviteCode();
            // 邀请二维码
            $qrRe = Helper::qrcode(Helper::getCurrentHost() . '/web/reg?ic=' . $aic);
            if (is_string($qrRe)) {
                trigger_error($qrRe);
            }

            $aicQr = $qrRe['head'];
            $result = self::quickCreate([
                'username' => $post['username'],
                'chn_name' => $post['nickname'],
                'password' => md5($post['password']),
                'top_id' => $pid,
                'role' => 1,
                'frozen' => 1,
                'is_return_money' => 1,
                'dev_status' => 1,
                'create_at' => Helper::timeFormat(time(), 's'),
                'update_at' => Helper::timeFormat(time(), 's'),
                'agent_invite_code' => $aic, // 二维码
                'top_username' => $topUser, // 上级用户
                'invite_code_head' => $aicQr, // 二维码邀请码
                'path' => $path,
            ]);

            if (!$result) {
                trigger_error('注册失败', E_USER_WARNING);
            }

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 获取邀请二维码
     *
     * @param $uid // 会员ID
     * @param $head // 邀请二维码附件ID
     * @return bool|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getInviteQrCodeByHead($uid, $head)
    {
        try {
            if (
                $head === 0
                || realpath(rtrim(PUBLIC_PATH, DS) . Attach::getPathByAttachId($head)) === false
            ) {
                $inviteCode = self::getValByWhere(['id' => $uid], 'agent_invite_code');
                if (empty($inviteCode)) {
                    // 邀请码不存在, 重新生成
                    $inviteCode = $this->generateUserInviteCode();
                }
                // 生成二维码
                $qrRe = Helper::qrcode(Helper::getCurrentHost() . '/web/reg?ic=' . $inviteCode);
                if (is_string($qrRe)) {
                    trigger_error('系统错误, 错误码:843', E_USER_WARNING);
                }

                $qrHead = $qrRe['head'];
                $result = self::quickCreate([
                    'id' => $uid,
                    'agent_invite_code' => $inviteCode,
                    'invite_code_head' => $qrHead,
                ], true);
                if (empty($result)) {
                    trigger_error('系统错误, 错误码: 853', E_USER_WARNING);
                }

                return Helper::getCurrentHost() . $qrRe['path'];
            } else {
                return Helper::getCurrentHost() . Attach::getPathByAttachId($head);
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @desc 查询会员信息
     * @auther LiBin
     * @param $where
     * @param $data
     * @return array|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-04-18
     */
    public function getMemberDataList($where, $data)
    {
        return self::where($where)->field($data)->select();
    }

    /**
     * 系统代提现(不用验证实名)
     *
     * @param $uid // 会员ID
     * @param $amount // 提现金额
     * @return bool|string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function memberWithdraw($uid, $amount)
    {
        try {
            Db::startTrans();
            $memberInfo = self::getFieldsByWhere(['id' => $uid], [
                'balance', // 余额
                'is_moni', // 是否模拟 0：模拟   1：真实
                'username', // 用户名
                'role', // 身份  1：会员   2：代理商
                'is_return_money', // 是否允许提现 0否 1是
            ]);
            if (empty($memberInfo)) {
                trigger_error('会员不存在!', E_USER_WARNING);
            }

            // 余额
            $balance = $memberInfo['balance'];
            // 是否模拟
            $isMoni = $memberInfo['is_moni'];
            // 是否允许提现
            $isReturnMoney = $memberInfo['is_return_money'];
            if (!$isReturnMoney) {
                trigger_error('该用户不允许提现');
            }

            // 用户名
            $username = $memberInfo['username'];
            if ($balance < $amount) {
                trigger_error('资金余额不足!', E_USER_WARNING);
            }

            if ($isMoni === 0) {
                trigger_error('模拟会员不能提现!', E_USER_WARNING);
            }

            // 更改余额
            $result = self::where('id', $uid)->setDec('balance', $amount);
            if (!$result) {
                trigger_error('提现失败, 错误码1019!', E_USER_WARNING);
            }

            // 添加总提现
            $resultTotal = self::where('id', $uid)->setInc('withdraw_deposit', $amount);
            if (!$resultTotal) {
                trigger_error('提现失败, 错误码1025!', E_USER_WARNING);
            }

            // 写入提现记录
            if ($isMoni === 1) {
                $r1 = FundWithdraw::quickCreate([
                    'member_id' => $uid,
                    'account' => $amount,
                    'to_account' => $amount,
                    'status' => 4,
                    'remark' => '平台代提现',
                    'create_at' => Helper::timeFormat(time(), 's'),
                    'update_at' => Helper::timeFormat(time(), 's'),
                    'identify' => $memberInfo['role'],
                    'username' => $username,
                    'order_no' => Helper::orderNumber(),
                ]);
                if (!$r1) {
                    trigger_error('提现失败, 错误码1043!', E_USER_WARNING);
                }

                // 资金流水记录
                $r2 = FundLog::quickCreate([
                    'member_id' => $uid,
                    'money' => $amount,
                    'front_money' => $balance,
                    'later_money' => bcsub($balance, $amount, 2),
                    'type' => 2, // 提现
                    'remark' => '平台代提现',
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                    'identify' => $memberInfo['role'],
                    'username' => $username,
                ]);

                if (!$r2) {
                    trigger_error('提现失败, 错误码1061!', E_USER_WARNING);
                }
            }
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 增加总输赢
     *
     * @param $uid // 会员ID
     * @param $amount // 金额
     * @throws \think\Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function addLoseAndWinning($uid, $amount)
    {
        self::where('id', $uid)->setInc('profit', (float)$amount);
    }

    /**
     * 减少总输赢
     *
     * @param $uid // 会员ID
     * @param $amount // 金额
     * @return bool
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public static function decLoseWinning($uid, $amount)
    {
        try {
            Db::startTrans();
            $profit = self::where('id', $uid)
                ->lock(true)// 加排他锁
                ->value('profit');
            $surplusProfit = $profit - $amount;
            if ($surplusProfit <= 0) {
                $surplusProfit = 0;
            }

            self::quickCreate([
                'id' => $uid,
                'profit' => $surplusProfit,
            ], true);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
    }

    /**
     * 通过会员层级路径获取会员直属(间接)上级代理商返佣设置
     * (优点: 避免获取全部上级代理做取小算法)
     * (注: 该逻辑成立前提是代理商从上到下返佣比例递减)
     *
     * @param string $path 层级路径
     * @throws \Exception
     * @return array
     * @author CleverStone
     */
    public static function getRelationTop($path)
    {
        $pathArr = explode(',', rtrim($path, ','));
        array_shift($pathArr);
        $topRatioGather = [];
        if (!empty($pathArr)) {
            array_reverse($pathArr);
            foreach ($pathArr as $topId) {
                $topRole = self::getValByWhere(['id' => $topId], 'role');
                if ($topRole === 2) {
                    $topRatio = MemberRatio::where('member_id', $topId)
                        ->field(['lottery_id', 'ratio'])
                        ->select();

                    $topRatioArr = [];
                    if (!empty($topRatio)) {
                        $topRatioArr = $topRatio->toArray();
                    }

                    $topRatioGather = array_column($topRatioArr, 'ratio', 'lottery_id');
                    break;
                }
            }
        }

        return $topRatioGather;
    }

    /**
     * 通过会员ID获取直属下级(间接下级)代理返点设置
     * (注: 获取全部下级代理做取大算法)
     * @param integer $uid 会员ID
     * @throws \Exception
     * @return array
     * @author CleverStone
     */
    public static function getRelationSubordinate($uid)
    {
        $subordinateData = self::alias('m')
            ->leftJoin('member_ratio mr', 'm.id=mr.member_id')
            ->field([
                'mr.lottery_id',
                'mr.ratio',
            ])
            ->where('m.path', 'like', '%,' . $uid . ',%')
            ->where('m.role', 2)
            ->select();

        if (empty($subordinateData)){
            return [];
        }

        $result = [];
        foreach ($subordinateData as $item) {
            $result[$item['lottery_id']][] = $item['ratio'];
        }

        $endResult = [];
        foreach ($result as $lotteryId => $ratioArr) {
            rsort($ratioArr, SORT_NUMERIC);
            $endResult[$lotteryId] = current($ratioArr);
        }

        return $endResult;
    }
}