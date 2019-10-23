<?php

namespace app\pxy\controller;

use app\common\Helper;
use app\common\PxyController;
use app\common\model\Member as MemberModel;
use app\common\model\Lottery;
use app\common\model\MemberRatio;
use think\db;

/**
 * 代理商控制器
 *
 * Class Agent
 * @package app\pxy\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Agent extends PxyController
{
    /**
     * @desc 获取代理商列表
     * @throws \Exception
     * @auther LiBin
     * @date 2019-03-11
     */
    public function index()
    {
        $data = $this->get;
        $data['type'] = 1; // 真实账号
        $data['role'] = 2; // 代理商
        $data['topId'] = UID;
        $member = new MemberModel();
        $filedData = [
            'id',
            'username',
            'chn_name',
            'top_id',
            'photo',
            'balance',
            'frozen_capital',
            'withdraw_deposit',
            'hadsel',
            'frozen',
            'is_return_money',
            'dev_status',
            'create_at',
            'last_login_time',
            'last_login_ip',
            'top_username',
            'recharge',
            'profit',
        ];
        $pagination = $member->getUser($data, $filedData, 'create_at desc');
        $page = $pagination->render();
        $list = $pagination->toArray();
        foreach ($list['data'] as $k => $v) {
            $list['data'][$k]['RecUserNumber'] = $member->getRecUser($v['id']);
        }

        return $this->asJson(1, 'success', '获取成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 导出Excel
     *
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function export()
    {
        $data = $this->get;
        $data['type'] = 1; // 真实账号
        $data['role'] = 2; // 代理商
        $data['path'] = UID;
        $user = new MemberModel();
        $filedData = [
            'id',
            'username', // 账号
            'chn_name', // 昵称
            'top_id', // 上级ID
            'balance', // 余额
            'frozen_capital', // 冻结资金
            'withdraw_deposit', // 总体县
            'hadsel', // 彩金
            'frozen', // 是否冻结
            'is_return_money', // 是否允许提现
            'dev_status', // 是否允许发展下线
            'create_at', // 时间
            'last_login_time', // 上次登录时间
            'last_login_ip', // 上次登录IP
            'top_username', // 上级用户
            'recharge', // 总充值
            'profit', // 总输赢
        ];
        $data = $user->exportMember($data, $filedData, 'create_at desc');
        foreach ($data as &$item) {
            $item['frozen'] = $item['frozen'] === 0 ? '冻结' : '正常'; // 会员状态
            $item['is_return_money'] = $item['is_return_money'] === 0 ? '否' : '是'; // 是否允许提现
            $item['dev_status'] = $item['dev_status'] === 0 ? '否' : '是'; // 是否允许发展下级
        }

        // 导出
        Helper::exportExcel(
            'agentExcel',
            [
                '主键ID', '账号', '昵称', '上级ID', '余额', '冻结资金', '总提现', '彩金', '是否冻结', '是否允许提现',
                '是否允许发展下线', '注册日期', '上次登录时间', '上次登录IP', '我的上级', '总充值', '总输赢', '推荐会员数',
            ],
            $data
        );
    }

    /**
     * 转移代理商
     *
     * @return \think\response\Json
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function transferAgent()
    {
        $data = $this->post;
        $validation = $this->validate($data, 'member.transferMember');
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $model = new MemberModel;
        $result = $model->transformUser('pxy', $data['id'], $data['userName'], $data['passWord'], false);
        if ($result !== true) {
            return $this->asJson(0, 'error', $result);
        }

        return $this->asJson(1, 'success', '转移成功');
    }

    /**
     * @desc 删除会员数据(软删除)
     * @auther LiBin
     * @throws \Exception
     * @date 2019-03-08
     */
    public function deleteAgent()
    {
        $data = $this->get;
        if (!isset($data['id'])) {
            return $this->asJson(0, 'error', '删除失败');
        }

        // 检测会员是否存在下级
        $model = new MemberModel();
        $check = $model->getOneMember(['top_id' => $data['id'], 'is_delete' => 0], 'id');
        if ($check) {
            return $this->asJson(0, 'error', '存在下级会员无法删除');
        }
        $result = $model->deleteMember((int)$data['id']);
        if ($result) {
            return $this->asJson(1, 'success', '删除成功');
        }

        return $this->asJson(0, 'error', '删除失败');
    }

    /**
     * @desc 冻结和解冻
     * @auther LiBin
     * @return \think\response\Json
     * @date 2019-03-08
     */
    public function toggle()
    {
        $get = $this->get;
        if (!isset($get['frozen']) || !isset($get['id'])) {
            return $this->asJson(0, 'error', '操作失败');
        }
        $member = new MemberModel();
        $member->setMember(['id' => (int)$get['id']], ['frozen' => (int)$get['frozen']]);
        return $this->asJson(1, 'success', '操作成功');
    }

    /**
     * @desc 批量冻结解冻
     * @auther LiBin
     * @return \think\response\Json
     * @date 2019-03-08
     */
    public function toggles()
    {
        $data = $this->post;
        if (!isset($data['frozen']) || !isset($data['id'])) {
            return $this->asJson(0, 'error', '操作失败');
        }
        $member = new MemberModel();
        $member->toggle($data['id'], $data['frozen']);
        return $this->asJson(1, 'success', '操作成功');
    }

    /**
     * @desc 获取代理商返佣列表
     * @throws \Exception
     * @auther LiBin
     * @date 2019-03-12
     */
    public function AgentReturnIndex()
    {
        $get = $this->get;
        $member = new MemberModel;
        $aids = $member->getMyDownAgentId(UID);
        // 无下级则使用虚拟查询
        $get['agentId'] = !empty($aids) ? $aids : ['none'];
        $memberRatio = new MemberRatio();
        $pagination = $memberRatio->getAgentRatio($get);
        $page = $pagination->render();
        $list = $pagination->toArray();

        return $this->asJson(1, 'success', '获取成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * @desc 获取彩种ID
     * @auther LiBin
     * @date 2019-03-12
     */
    public function getLotteryId()
    {
        $lottery = new Lottery();
        $data = $lottery->getLottery();

        return $this->asJson(1, 'success', '获取成功', $data);
    }

    /**
     * @desc 添加代理商返点数据
     * @throws \Exception
     * @auther LiBin
     * @date 2019-03-12
     */
    public function getaddAgentRebates()
    {
        // 获取代理商列表
        $member = new MemberModel();
        $where[] = ['role', '=', 2];
        $where[] = ['is_delete', '=', 0];
        $where[] = ['path', 'like', '%,' . UID . ',%'];
        $agentList = $member->getAll($where, 'username,id');
        // 获取彩票数据
        $lottery = new Lottery();
        $lotteryData = $lottery->getLottery();
        $list['agentlist'] = $agentList;
        $list['lottery'] = $lotteryData;

        return $this->asJson(1, 'success', '获取成功', $list);
    }

    /**
     * @desc 获取单个代理商的返佣列表
     * @throws \Exception
     * @auther LiBin
     * @date 2019-03-12
     */
    public function getAgentRebateList($id)
    {
        $memberRatio = new MemberRatio();
        $data = $memberRatio->getLottery((int)$id);
        return $this->asJson(1, 'success', '获取成功', $data);
    }

    /**
     * @desc 代理商设置返佣
     * @throws \Exception
     * @auther LiBin
     * @date 2019-03-06
     */
    public function setAgentrebate()
    {
        $data = $this->post;
        $validateRe = $this->validate($data, 'Agent.setAgentrebate');
        if ($validateRe !== true) {
            return $this->asJson(0, 'error', $validateRe);
        }

        $member = new MemberModel();
        // 判断操作用户是否存在
        $memberId = $member->getOneMember(['id' => $data['id']], 'id,top_id');
        if (empty($memberId)) {
            return $this->asJson(0, 'error', '用户不存在');
        }

        /*if ($memberId['top_id'] != UID) {
            return $this->asJson(0, 'error', '只能设置直属下级代理商返佣');
        }*/

        if (empty($data['Lottery'])) {
            return $this->asJson(0, 'error', '没有彩种, 无法设置.');
        }
        //获取用户的上级返佣
        $topRebate = $member->chekBossAgent($data['id']);
        if (empty($topRebate)) {
            return $this->asJson(0, 'error', '请先设置自身的返佣比例');
        }

        $lottery = new Lottery();//实例化彩种数据模型
        $memberRatio = new MemberRatio();//实例化返佣比例
        foreach ($data['Lottery'] as $k => $v) {
            if (empty($v['value']) && $v['value'] !== '0') {
                return $this->asJson(0, 'error', '佣金不能为空');
            }

            if (!is_numeric($v['value'])) {//检测佣金是否规范
                return $this->asJson(0, 'error', '请输入正确的佣金');
            }

            //检查彩种ID是否存在
            $checkId = $lottery->getOneLottery(['id' => $v['key']], 'id,name');
            if (empty($checkId['id'])) {
                return $this->asJson(0, 'error', '彩种不存在');
            }

            if (!empty($topRebate) && $v['value'] !== '0') {
                if (empty($topRebate[$v['key']])) {
                    return $this->asJson(0, 'error', '请先设置自身的' . $checkId['name'] . '返佣');
                }
                if ($v['value'] > $topRebate[$v['key']]) {
                    return $this->asJson(0, 'error', '返佣比例不能大于上级代理商');
                }
            }
            //组合代理对应彩种返佣比例的数据
            $updata[$k]['member_id'] = $data['id'];
            $updata[$k]['lottery_id'] = $v['key'];
            $updata[$k]['ratio'] = $v['value'];
            $updata[$k]['status'] = $v['state'];
            $updata[$k]['create_at'] = date('Y-m-d H:i:s');
            $updata[$k]['update_at'] = date('Y-m-d H:i:s');
            //组合彩种ID
            $lotteryId[] = $checkId['id'];
        }

        try {
            Db::startTrans();
            // 删除重复的彩种返佣信息
            $memberRatio->deleteMemberBatch(['member_id' => $data['id'], 'lottery_id' => $lotteryId]);
            if ($memberRatio === false) {
                trigger_error('代理商返佣设置失败', E_USER_WARNING);
            }
            // 添加返佣信息
            $upLottery = $memberRatio->saveAllLottery($updata);
            if (empty($upLottery)) {
                trigger_error('代理商返佣设置失败', E_USER_WARNING);
            }

            Db::commit();
            return $this->asJson(1, 'success', '代理商返佣设置成功');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->asJson(0, 'error', $e->getMessage());
        }
    }

    /**
     * @desc 代理商返佣状态设置
     * @auther LiBin
     * @date 2019-03-12
     */
    public function agentRetrunStatus()
    {
        $get = $this->get;
        if (!isset($get['status']) || !isset($get['id'])) {
            return $this->asJson(0, 'error', '操作失败');
        }

        $ratio = new MemberRatio();
        $ratio->setAgentRatioStatus(['id' => (int)$get['id']], ['status' => (int)$get['status'], 'update_at' => date('Y-m-d H:i:s')]);
        return $this->asJson(1, 'success', '操作成功');
    }

    /**
     * @desc 删除代理商返点设置
     * @throws \Exception
     * @auther LiBin
     * @date 2019-03-12
     */
    public function agentRetrunDel()
    {
        $get = $this->get;
        if (!isset($get['id'])) {
            return $this->asJson(0, 'error', '缺失ID');
        }
        $ratio = new MemberRatio();
        $ratio->deleteMemberBatch(['id' => $get['id']]);
        return $this->asJson(1, 'success', '操作成功');
    }
}