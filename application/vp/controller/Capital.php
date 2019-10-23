<?php

namespace app\vp\controller;

use app\common\model\FundLog;
use app\common\model\FundWithdraw;
use app\common\model\Member as MemberModel;
use app\common\VpController;
use app\common\Helper;
use think\Db;

/**
 * 资金管理控制器
 *
 * Class Capital
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Capital extends VpController
{

    // 模型服务容器
    private $model;

    /**
     * 初始化
     *
     * @return \think\response\Json|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function initialize()
    {
        parent::initialize();
        if (empty('model')) {
            exit;
        }
        $className = '\app\common\model\\' . input('model');
        $this->model = new $className();
    }

    /**
     * 资金管理列表
     *
     * @return \think\response\Json
     * @author CleverStone
     * @throws \Exception
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function index()
    {
        $get = $this->get;
        $pagination = $this->model->getList($get, 'id DESC');
        $page = $pagination->render();
        $list = $pagination->toArray();
        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 资金管理数据导出
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function export()
    {
        $get = $this->get;
        $this->model->exportData($get, 'id DESC');
    }

    /**
     * 查看实名信息
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function info()
    {
        $get = $this->get;
        $list = $this->model->getDetail($get['memberId'], $get['fundId']);
        return $this->asJson(1, 'success', '请求成功', $list);
    }

    /**
     * 用户提现备注信息
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function verify()
    {
        $id = input('id');
        $remark = FundWithdraw::getValByWhere(['id' => $id], 'remark');
        return $this->asJson(1, 'success', '请求成功', ['remark' => $remark]);
    }

    /**
     * 提交提现审核
     *
     * @return \think\response\Json
     * @author Libin
     * @throws \Exception
     * @updateBy CleverStone
     */
    public function editVerify()
    {
        $post = $this->post;
        // 查询提现信息
        $withdraw = FundWithdraw::get($post['id']);
        if ($withdraw['status'] != FundWithdraw::STATUS_AWAIT) {
            return $this->asJson(0, 'error', '不可重复审核');
        }

        try {
            Db::startTrans();
            // 查询该用户信息(排他锁)
            $member = MemberModel::where(['id' => $withdraw['member_id']])->lock(true)->find();
            // 线下汇款转换状态(线上汇款该代码删除即可)
            // status = 2 提现中
            if ($post['status'] == FundWithdraw::STATUS_SUCCESS) {
                $convertStatus = FundWithdraw::OUT_SUCCESS; // 提现成功
            } else {
                $convertStatus = FundWithdraw::STATUS_ERROR; // 已驳回
            }

            // 线下提现审核成功即提现成功, 更改提现订单状态
            FundWithdraw::where('id', $post['id'])->update(['status' => $convertStatus]);
            // 审核成功
            if ($post['status'] == FundWithdraw::STATUS_SUCCESS) {
                // 线下汇款
                // 修改资金变动记录
                FundLog::where(['withdraw_id' => $post['id']])->setField([
                    'remark' => '提现成功' . $post['remark'],
                    'update_time' => Helper::timeFormat(time(), 's'),
                ]);
                // 更新冻结资金
                $frozenCapital = bcsub($member['frozen_capital'], $withdraw['account'], 2);
                // 做一下负值兼容(此处逻辑应该在mysql的字段中去控制)
                if ($frozenCapital < 0) {
                    $frozenCapital = 0;
                }

                // 增加总提现
                $newestWithdraw = bcadd($member['withdraw_deposit'], $withdraw['account'], 2);
                // 更新用户账户
                MemberModel::where(['id' => $withdraw['member_id']])
                    ->setField([
                        'frozen_capital' => $frozenCapital, // 新冻结资金
                        'withdraw_deposit' => $newestWithdraw, // 新总提现数
                    ]);
            } elseif ($post['status'] == FundWithdraw::STATUS_ERROR) {
                // 已驳回
                // 修改用户表资金信息
                $map['id'] = $withdraw['member_id'];  //用户id
                $map['frozen_capital'] = bcsub($member['frozen_capital'], $withdraw['account'], 2);  //冻结资金  用户当前冻结资金-提现金额
                if ($map['frozen_capital'] < 0) {
                    trigger_error('系统错误');
                }

                $map['balance'] = bcadd($member['balance'], $withdraw['account'], 2);   //用户余额  当前余额+提现金额
                if (MemberModel::update($map)) {
                    // 修改资金变动记录
                    FundLog::where(['withdraw_id' => $post['id']])->setField([
                        'remark' => '提现失败!资金已返还' . $post['remark'],
                        'update_time' => Helper::timeFormat(time(), 's'),
                    ]);
                } else {
                    trigger_error('系统错误');
                }
            } else {
                trigger_error('系统错误');
            }

            Db::commit();
            return $this->asJson(1, 'success', '提现成功');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->asJson(0, 'error', $e->getMessage());
        }
    }

    /**
     * 资金变动类型
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getType()
    {
        $res = array('充值', '提现', '购彩', '资金冻结', '奖金', '系统嘉奖', '注单返佣', '充值赠送', '资金校正', '跟单返佣', '代充扣除');
        $arr = [];
        for ($i = 0; $i < 11; $i++) {
            $arr[$i] = ['id' => $i + 1, 'value' => $res[$i]];
        }

        return $this->asJson(1, 'success', '请求成功', ['list' => $arr]);
    }
}
