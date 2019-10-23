<?php

namespace app\vp\controller;

use app\common\VpController;
use app\common\model\Member as MemberModel;
use app\common\model\Admin as AdminModel;
use app\common\model\FundLog;
use app\common\model\Lottery;
use app\common\model\MemberRatio;
use app\common\model\AdminBank;
use app\common\model\MemberBank as AgentBankModel;
use app\common\Helper;
use think\db;

/**
 * 代理商控制器
 *
 * Class Agent
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Agent extends VpController
{
    /**
     * @desc 获取代理商列表
     * @throws \Exception
     * @author LiBin
     * @date 2019-03-11
     */
    public function index()
    {
        $data = $this->get;
        $data['type'] = 1;//真实账号
        $data['role'] = 2;//代理商
        $user = new MemberModel();
        $filedData = [
            'id',
            'username', // 账号
            'chn_name', // 昵称
            'top_id', // 上级ID
            'photo', // 头像
            'balance', // 余额
            'hadsel', // 彩金
            'frozen_capital', // 冻结资金
            'withdraw_deposit', // 总提现
            'recharge', // 总充值
            'profit', // 总输赢
            'frozen', // 是否冻结
            'top_username', // 上级用户
        ];
        $pagination = $user->getUser($data, $filedData, 'create_at desc');
        $page = $pagination->render();
        $list = $pagination->toArray();
        foreach ($list['data'] as $k => $v) {
            $list['data'][$k]['RecUserNumber'] = $user->getRecUser($v['id']);
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
     * @desc 新增代理商
     * @author LiBin
     * @throws \Exception
     * @date 2019-03-12
     */
    public function addAgent()
    {
        $data = $this->post;
        $validateRe = $this->validate($data, 'Agent.addAgent');
        if ($validateRe !== true) {
            return $this->asJson(0, 'error', $validateRe);
        }

        $member = new MemberModel();
        // 判断操作用户是否存在
        $memberId = $member->getOneMember(['username' => $data['username']], 'id');
        if ($memberId) {
            return $this->asJson(0, 'error', '账号不能重复');
        }

        if (empty($data['Lottery'])) {
            return $this->asJson(0, 'error', '请输入返佣比例');
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

            // 检查彩种ID是否存在
            $checkId = $lottery->getOneLottery(['id' => $v['key']], 'id');
            if (empty($checkId)) {
                return $this->asJson(0, 'error', '彩种不存在');
            }
            // 组合代理对应彩种返佣比例的数据
            $updateData[$k]['lottery_id'] = $v['key'];
            $updateData[$k]['ratio'] = $v['value'];
            $updateData[$k]['status'] = $v['status'];
            $updateData[$k]['create_at'] = date('Y-m-d H:i:s');
            $updateData[$k]['update_at'] = date('Y-m-d H:i:s');
        }

        // 组合用户数据
        $userData['username'] = $data['username'];
        $userData['chn_name'] = $data['nickname'];
        $userData['password'] = md5($data['password']);
        $userData['top_id'] = 0;
        $userData['photo'] = 0;
        $userData['role'] = 2;
        $userData['frozen'] = $data['status'];
        $userData['is_return_money'] = $data['witdraw'];
        $userData['dev_status'] = $data['lower'];
        $userData['create_at'] = date('Y-m-d H:i:s');
        $userData['agent_invite_code'] = $member->generateUserInviteCode();
        $userData['path'] = '0,';
        $path = Helper::qrcode(Helper::getCurrentHost() . '/web/reg?ic=' . $userData['agent_invite_code']);
        if (empty($path['head'])) {
            return $this->asJson(0, 'error', '会员添加失败');
        }

        $userData['invite_code_head'] = $path['head'];
        try {
            Db::startTrans();
            $result = $member->addMember($userData);
            if (empty($result)) {
                trigger_error('添加代理商失败', E_USER_WARNING);
            }

            foreach ($updateData as $k => $v) {
                $updateData[$k]['member_id'] = $result;
            }

            // 添加返佣信息
            $upLottery = $memberRatio->saveAllLottery($updateData);
            if (empty($upLottery)) {
                trigger_error('添加代理商失败', E_USER_WARNING);
            }

            Db::commit();
            return $this->asJson(1, 'success', '添加代理商添加成功');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->asJson(0, 'error', $e->getMessage());
        }
    }

    /**
     * @desc 代理商转移会员
     * @auther LiBin
     * @throws \Exception
     * @date 2019-03-11
     */
    public function transferAgent()
    {
        $data = $this->post;
        $validation = $this->validate($data, 'Member.transferAgent');
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $isSelf = (int)$data['isSelf'] === 0 ? false : true;
        $model = new MemberModel;
        $result = $model->transformUser('vp', $data['id'], $data['userName'], $data['passWord'], $isSelf);
        if ($result !== true) {
            return $this->asJson(0, 'error', $result);
        }

        return $this->asJson(1, 'success', '转移成功');
    }

    /**
     * @desc 修改彩金
     * @auther LiBin
     * @date 2019-03-06
     */
    public function reviseGold()
    {
        $data = $this->post;
        $validateRe = $this->validate($data, 'Member.reviseGold');
        if ($validateRe !== true) {
            return $this->asJson(0, 'error', $validateRe);
        }

        if (!is_numeric($data['gold'])) {//检测彩金值是否规范
            return $this->asJson(0, 'error', '请输入正确的彩金值');
        }

        //判断操作密码是否正确
        $password = md5($data['passWord']);
        $admin = new AdminModel();
        $adminData = $admin->getOneAll(['id' => UID, 'password' => $password]);
        if (empty($adminData)) {
            return $this->asJson(0, 'error', '请输入正确的操作密码');
        }

        //获取用户彩金信息
        $member = new MemberModel();
        $memberData = $member->getOneMember(['id' => $data['id']], 'hadsel,role,username');
        if (empty($memberData)) {
            return $this->asJson(0, 'error', '用户不存在');
        }

        //计算用户彩金
        //$later_money = $memberData['hadsel']+($data['gold']);
        Db::startTrans();
        //更新彩金
        $upMemberHadsel = $member->setMember(['id' => $data['id']], ['hadsel' => $data['gold']]);
        if (empty($upMemberHadsel)) {
            Db::rollback();
            return $this->asJson(0, 'error', '彩金修改失败');
        }

        //组合数据
        $funData['member_id'] = $data['id'];
        $funData['money'] = $data['gold'];
        $funData['front_money'] = $memberData['hadsel'];
        $funData['later_money'] = $data['gold'];
        $funData['type'] = 9;
        $funData['remark'] = $data['remarks'];
        $funData['create_time'] = date('Y-m-d H:i:s');
        $funData['update_time'] = date('Y-m-d H:i:s');
        $funData['identify'] = $memberData['role'];
        $funData['username'] = $memberData['username'];
        //添加资金记录表信息
        $fundlog = new FundLog();
        $upFund = $fundlog->insertFundLog($funData);
        if (empty($upFund)) {
            Db::rollback();
            return $this->asJson(0, 'error', '彩金修改失败');
        }

        Db::commit();
        return $this->asJson(1, 'success', '彩金修改成功');
    }

    /**
     * @desc 修改余额
     * @auther LiBin
     * @date 2019-03-06
     */
    public function reviseBalance()
    {
        $data = $this->post;
        //dump($data);//die();
        $validateRe = $this->validate($data, 'Member.reviseBalance');
        if ($validateRe !== true) {
            return $this->asJson(0, 'error', $validateRe);
        }

        if (!is_numeric($data['balance'])) {//检测余额值是否规范
            return $this->asJson(0, 'error', '请输入正确的金额值');
        }

        //判断操作密码是否正确
        $password = md5($data['passWord']);
        $admin = new AdminModel();
        $adminData = $admin->getOneAll(['id' => UID, 'password' => $password]);
        if (empty($adminData)) {
            return $this->asJson(0, 'error', '请输入正确的操作密码');
        }

        //获取用户余额信息
        $member = new MemberModel();
        $memberData = $member->getOneMember(['id' => $data['id']], 'balance,role,username');
        if (empty($memberData)) {
            return $this->asJson(0, 'error', '用户不存在');
        }

        //计算用户余额
        //$later_balance = $memberData['balance']+($data['balance']);
        Db::startTrans();
        //更新余额
        $upMemberHadsel = $member->setMember(['id' => $data['id']], ['balance' => $data['balance']]);
        if (empty($upMemberHadsel)) {
            Db::rollback();
            return $this->asJson(0, 'error', '余额修改失败');
        }

        //组合数据
        $funData['member_id'] = $data['id'];
        $funData['money'] = $data['balance'];
        $funData['front_money'] = $memberData['balance'];
        $funData['later_money'] = $data['balance'];
        $funData['type'] = 9;
        $funData['remark'] = $data['remarks'];
        $funData['create_time'] = date('Y-m-d H:i:s');
        $funData['update_time'] = date('Y-m-d H:i:s');
        $funData['identify'] = $memberData['role'];
        $funData['username'] = $memberData['username'];
        //添加资金记录表信息
        $fundlog = new FundLog();
        $upFund = $fundlog->insertFundLog($funData);
        if (empty($upFund)) {
            Db::rollback();
            return $this->asJson(0, 'error', '余额修改失败');
        }

        Db::commit();
        return $this->asJson(1, 'success', '余额修改成功');
    }

    /**
     * @desc 删除会员数据(软删除)
     * @auther LiBin
     * @date 2019-03-08
     */
    public function deleteAgent()
    {
        $data = $this->get;
        if (!isset($data['id'])) {
            return $this->asJson(0, 'error', '删除失败');
        }
        //检测会员上级是否存在下级
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
     * 获取代理商返佣设置列表
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function AgentReturnIndex()
    {
        $memberRatio = new MemberRatio();
        $redata = $memberRatio->getAgentRatio($this->get);
        $page = $redata->render();
        $list = $redata->toArray();

        return $this->asJson(1, 'success', '获取成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * @desc 获取代理商基本信息
     * @auther LiBin
     * @date 2019-03-21
     */
    public function getAgentInfo()
    {
        $data = $this->get;
        //return $this->asJson(0, 'error', '暂无用户信息');
        if (empty($data['agentid'])) {
            return $this->asJson(0, 'error', '暂无用户信息');
        }
        $where['id'] = $data['agentid'];
        $member = new MemberModel();
        $data = $member->getOneMember($where, 'id,username,chn_name,is_return_money,dev_status,frozen');
        return $this->asJson(1, 'success', '获取成功', $data);
    }

    /**
     * @desc 修改代理商的基本信息
     * @auther LiBin
     * @date 2019-03-21
     */
    public function updataAgent()
    {
        $data = $this->post;
        if (empty($data['memberId'])) {
            return $this->asJson(0, 'error', '用户ID不能为空');
        }
        $where['id'] = $data['memberId'];

        if(!empty($data['nickName'])) {//验证昵称不能重复
            $member = new MemberModel();
            $whereMember[] = ['id','neq',$data['memberId']];
            $whereMember[] = ['chn_name','eq',$data['nickName']];
            $dataMember = $member->getOneMember($whereMember,'id');
            if(!empty($dataMember)){
                return $this->asJson(0,'error','昵称已存在,请重新定义');
            }
            $updata['chn_name'] = $data['nickName'];
        }
        empty($data['draw'])?'':$updata['is_return_money'] = $data['draw'];
        empty($data['frozen'])?'':$updata['frozen'] = $data['frozen'];
        empty($data['status'])?'':$updata['dev_status'] = $data['status'];
        $member = new MemberModel();
        $data = $member->setMember($where, $updata);
        if ($data === false) {
            return $this->asJson(0, 'error', '修改失败');
        }
        return $this->asJson(1, 'success', '修改成功');
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
     * @auther LiBin
     * @throws \Exception
     * @date 2019-03-12
     */
    public function getaddAgentRebates()
    {
        //获取代理商列表
        $admin = new MemberModel();
        $where['role'] = 2;
        $where['frozen'] = 1;
        $where['is_delete'] = 0;
        $agentlist = $admin->getAll($where, 'username,id');
        //获取彩票数据
        $lottery = new Lottery();
        $lotteryData = $lottery->getLottery();
        $list['agentlist'] = $agentlist;
        $list['lottery'] = $lotteryData;
        return $this->asJson(1, 'success', '获取成功', $list);
    }

    /**
     * @desc 获取单个代理商的返佣列表
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
        //判断操作用户是否存在
        $memberId = $member->getOneMember(['id' => $data['id']], 'id');
        if (empty($memberId)) {
            return $this->asJson(0, 'error', '用户不存在');
        }
        if (empty($data['Lottery'])) {
            return $this->asJson(0, 'error', '没有彩种,无法设置.');
        }
        //获取用户的上级返佣
        $topRebate = $member->chekBossAgent($data['id']);
        $lottery = new Lottery();//实例化彩种数据模型
        $memberRatio = new MemberRatio();//实例化返佣比例
        foreach ($data['Lottery'] as $k => $v) {
            if (empty($v['value']) && $v['value'] !== '0') {
                return $this->asJson(0, 'error', '佣金不能为空');
            }

            if (!is_numeric($v['value'])) {//检测佣金是否规范
                return $this->asJson(0, 'error', '请输入正确的佣金');
            }

            /*if($v['value']>=10){
                return $this->asJson(0,'error','返佣比例不能大于等于百分之十');
            }*/
            if (!empty($topRebate)) {
                if ($v['value'] > $topRebate[$v['key']]) {
                    return $this->asJson(0, 'error', '返佣比例不能大于上级代理商');
                }
            }
            //检查彩种ID是否存在
            $checkId = $lottery->getOneLottery(['id' => $v['key']], 'id');
            if (empty($checkId)) {
                return $this->asJson(0, 'error', '彩种不存在');
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

        Db::startTrans();
        //删除重复的彩种返佣信息
        $memberRatio->deleteMemberBatch(['member_id' => $data['id'], 'lottery_id' => $lotteryId]);
        if ($memberRatio === false) {
            Db::rollback();
            return $this->asJson(0, 'error', '代理商返佣设置失败');
        }
        //添加返佣信息
        $upLottery = $memberRatio->saveAllLottery($updata);
        if (empty($upLottery)) {
            Db::rollback();
            return $this->asJson(0, 'error', '代理商返佣设置失败');
        }
        Db::commit();
        return $this->asJson(1, 'success', '代理商返佣设置成功');
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

    /**
     * @desc 修改用户密码
     * @auther LiBin
     * @return \think\response\Json
     * @date 2019-04-03
     */
    public function setPassword()
    {
        $data = $this->post;
        $validateRe = $this->validate($data, 'Member.setPassword');
        if ($validateRe !== true) {
            return $this->asJson(0, 'error', $validateRe);
        }

        //判断操作密码是否正确
        $angentPassword = md5($data['angentPassword']);
        $admin = new AdminModel();
        $adminData = $admin->getOneAll(['id' => UID, 'password' => $angentPassword]);
        if (empty($adminData)) {
            return $this->asJson(0, 'error', '请输入正确的操作密码');
        }

        //修改用户密码
        $member = new MemberModel();
        $setMember = $member->setMember(['id' => $data['memberId']], ['password' => md5($data['passWord'])]);
        if ($setMember === false) {
            return $this->asJson(0, 'error', '修改失败');
        }

        return $this->asJson(1, 'success', '密码修改成功');
    }

    /**
     * @desc 代理商暂未绑定银行卡
     * @auther rk
     * @return \think\response\Json
     * @date 2019-04-29
     */
    public function getBankCard()
    {
        $data = $this->get;
        if(empty($data['agentid'])){
            return $this->asJson(0, 'error', '代理商ID不能为空');
        }

        $agentBank = new AgentBankModel();
        $list = 'id,bank,bank_num,bank_code,cardholder';
        $bankData = $agentBank->getBankCardListData($data['agentid'],$list);
        //获取银行列表
        $adminBank = new AdminBank();
        $adminBankData = $adminBank ->getBankList(['status'=>1],['name','code']);
        $rdata['bankData'] = $bankData;
        $rdata['banklist'] = $adminBankData;
        if(empty($bankData)){
            return $this->asJson(0,'error','暂未绑定银行卡');
        }
        return $this->asJson(1,'success','获取成功',$rdata);
    }

    /**
     * @desc 更新代理商银行卡数据
     * @auther rk
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     * @date 2019-04-29
     */
    public function upBankCard()
    {
        $data = $this->post;
        if(empty($data['rdata'])){
            return $this->asJson(0,'error','缺失数据');
        }

        $rdata = explode('|',trim(trim($data['rdata'],'|'),','));
        $memberBank = new AgentBankModel();
        $model = new AgentBankModel();
        $list = [];
        Db::startTrans();
        foreach($rdata as $k=>$v){
            $bankData =  explode(',',trim($v,','));
            foreach ($bankData as $key=>$value){
                $listData = explode(':',$bankData[$key]);
                $list[$listData[0]] = $listData[1];
            }
            // 验证银行卡号和归属银行
            //获取银行列表
            $adminBank = new AdminBank();
            $adminBankData = $adminBank ->getBankOne(['code'=>$list['bank_code']],['name']);
            $falgs = $model->checkCard($adminBankData['name'], $list['bank_num']);
            if ($falgs !== true) {
                return $this->asJson(0, 'error', $falgs);
            }

            $updata = $memberBank->setBank($list['id'],$list);
            if($updata === false){
                Db::rollback();
                return $this->asJson(0,'error','银行卡修改失败');
            }
        }

        Db::commit();
        return $this->asJson(1,'success','修改成功');
    }

    /**
     * @desc 添加银行卡
     * @auther ken
     * @return \think\response\Json
     * @date 2019-04-29
     */
    public function addBankCard()
    {
        $data = $this->get;
        if(empty($data['agentid'])){
            return $this->asJson(0, 'error', '代理ID不能为空');
        }

        //获取银行列表
        $adminBank = new AdminBank();
        $adminBankData = $adminBank ->getBankList(['status'=>1],['name','code']);
        $rdata['banklist'] = $adminBankData;
        return $this->asJson(1,'success','添加银行卡成功',$rdata);
    }

    /**
     * @desc 保存银行卡
     * @auther ken
     * @return \think\response\Json
     * @date 2019-04-29
     */
    public function saveBankCard()
    {
        $data = $this->post;
        $validateRe = $this->validate($data, 'Agent.saveBankCard');
        if ($validateRe !== true) {
            return $this->asJson(0, 'error', $validateRe);
        }

        $data['create_at'] = Helper::timeFormat(time(), 's') ;
        $data['update_at'] = Helper::timeFormat(time(), 's') ;

        // 验证银行卡号对应的开户行
        $model = new AgentBankModel();
        $falgs = $model->checkCard($data['bank'], $data['bank_num']);
        if ($falgs !== true) {
            return $this->asJson(0, 'error', $falgs);
        }
        $memberBank = new AgentBankModel();
        $newBank = $memberBank->addBankCard($data,false);
        if ($newBank === false){
            return $this->asJson(0,'error','银行卡添加失败');
        }

        return $this->asJson(1,'success','银行卡添加成功');
    }
}