<?php

namespace app\api\controller;

use app\common\RestController;
use app\common\model\FundWithdraw as FundWithdrawModel;
use app\common\model\Member as MemberModel;
use app\common\model\MemberBank as MemberBankModel;
use app\common\model\AdminConfig as AdminConfigModel;
use app\common\model\FundLog as FundLogModel;
use app\common\Helper;
use think\Db;

/**
 * 提现接口控制器
 * Class Pay
 * @package app\api\controller
 */
class Withdraw extends RestController
{
    /**
     * authentication
     * @param array $disableAuthAction
     * @author CleverStone
     */
    protected function init(array $disableAuthAction = [])
    {
        $disableAuthAction = [];
        parent::init($disableAuthAction); // TODO: Change the autogenerated stub
    }

    /**
     * 提现申请
     * @return string
     * @throws \Exception
     * @param // int $member_id 用户id
     * @param // float $account 提现金额
     * @param // int $bank_id 到账银行卡id
     */
    public function applyWithdeaw()
    {
        $data = $this->post;
        $data['member_id'] = UID;
        $data['status'] = FundWithdrawModel::STATUS_AWAIT;
        $validation = $this->validate($data, 'withdraw');
        if ($validation !== true) {
            return $this->asNewJson('withdrawRet', 0, 'error', $validation);
        }
        // 验证是否满足最低提现金额
        $min_amount = AdminConfigModel::config('minimum_amount');
        if (empty($min_amount) && $min_amount !== 0) {
            $min_amount = 100;
        }

        if ($data['account'] < $min_amount) {
            return $this->asNewJson('withdrawRet', 0, 'error', '提现金额不可小于' . $min_amount . '元');
        }

        // 查询是否存在银行卡
        if (!MemberBankModel::where('id', $data['bank_id'])->find()) {
            return $this->asNewJson('withdrawRet', 0, 'error', '请您完善您的银行卡信息');
        };

        Db::startTrans();
        // 查询用户可提现金额(添加排他锁)
        $member = MemberModel::where('id', $data['member_id'])->lock(true)->find();
        if ($member['balance'] < $data['account']) {
            // 回滚事务, 释放锁
            Db::rollback();
            return $this->asNewJson('withdrawRet', 0, 'error', '提现金额不可大于余额');
        }

        // 判断是否允许提现
        if (!$member['is_return_money']){
            Db::rollback();
            return $this->asNewJson('withdrawRet', 0, 'error', '您的提现权限已被冻结');
        }

        // 判断是否是模拟账号
        if (!$member['is_moni']){
            Db::rollback();
            return $this->asNewJson('withdrawRet', 0, 'error', '模拟账号不允许提现');
        }

        // 判断是否实名
        if (!$member['real_status'] && empty($member['id_card'])){
            Db::rollback();
            return $this->asNewJson('withdrawRet', 0, 'error', '请您先实名认证');
        }

        if (!$member['real_status']){
            // 兼容以前没做实名字段标记的数据
            // real_status 设置实名状态为1
            MemberModel::where('id', UID)->setField('real_status', 1);
        }

        // 获取系统设置手续费
        $serviceChange = AdminConfigModel::config('service_charge');
        if (empty($serviceChange)) {
            $serviceChange = 0;
        }

        $data['to_account'] = $data['account'] - $serviceChange;
        $data['create_at'] = date("Y-m-d H:i:s", time());
        $data['update_at'] = date("Y-m-d H:i:s", time());
        $data['order_no'] = Helper::orderNumber();
        $data['identify'] = $member['role']; // 身份
        $data['username'] = $member['username']; // 账号
        // 扣除用户余额，增加冻结资金
        $map['id'] = $member['id'];
        $map['balance'] = bcsub($member['balance'], $data['account'], 2);  // 扣除用户余额
        $map['frozen_capital'] = bcadd($member['frozen_capital'], $data['account'], 2);  // 增加冻结资金
        MemberModel::update($map);
        // 创建提现订单
        if ($curModel = FundWithdrawModel::create($data)) {
            // 添加资金记录
            $param = [
                'member_id' => $data['member_id'],
                'money' => $data['account'],
                'front_money' => $member['balance'],
                'later_money' => bcsub($member['balance'], $data['account'], 2),
                'type' => 2, // 提现
                'username' => $member['username'], // 账号
                'create_time' => date("Y-m-d H:i:s", time()),
                'update_time' => date("Y-m-d H:i:s", time()),
                'identify' => $member['role'],
                'withdraw_id' => $curModel['id'],
            ];
            $param['remark'] = '提现待审核';
            $FundLog = new FundLogModel();
            $FundLog->insertFundLog($param);
            // 写入消息日志并推送
            Helper::logAndPushMsg($param['remark'], $member['chn_name'], $member['username'], 1, 1, $member['photo']);
            Db::commit();
            return $this->asNewJson('withdrawRet', 1, 'success', '提现申请成功');
        } else {
            Db::rollback();
            return $this->asNewJson('withdrawRet', 0, 'error', '系统错误');
        }
    }
}
