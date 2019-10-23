<?php

namespace app\vp\controller;

use app\common\model\FundCharge;
use app\common\model\FundWithdraw;
use app\common\VpController;
use app\common\model\Member as MemberModel;
use app\common\model\Admin as AdminModel;
use app\common\model\FundLog;
use app\common\model\Lottery;
use app\common\model\MemberRatio;
use app\common\model\MemberBank as MemberBankModel;
use app\common\model\AdminBank;
use app\common\Helper;
use app\common\Config;
use think\db;
use app\common\model\Attach;

/**
 * 会员管理页面
 *
 * Class member
 * @package app\vp\controller
 * @author sumer
 */
class Member extends VpController
{
    /**
     * 会员列表
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function index()
    {
        $data = $this->get;
        $data['type'] = 1;//真实账号
        $data['role'] = 1;//会员
        $member = new MemberModel();
        $fieldData = [
            'id', // 会员ID
            'username', // 会员
            'chn_name', // 昵称
            'top_id', // 上级ID
            'top_username', // 上级会员账号
            'photo', // 会员头像
            'balance', // 余额
            'hadsel', // 彩金
            'frozen_capital', // 冻结资金
            'withdraw_deposit', // 总提现
            'recharge', // 总充值
            'profit', // 总输赢
            'frozen', // 会员状态
        ];
        $pagination = $member->getUser($data, $fieldData, 'id desc');
        $page = $pagination->render();
        $list = $pagination->toArray();

        return $this->asJson(1, 'success', '获取成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 会员数据导出
     *
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function export()
    {
        $data = $this->get;
        $data['type'] = 1; // 真实账号
        $data['role'] = 1; // 会员
        $member = new MemberModel();
        $field = [
            'id', // 会员ID
            'username', // 会员
            'chn_name', // 昵称
            'top_id', // 上级ID
            'balance', // 余额
            'frozen_capital', // 冻结资金
            'withdraw_deposit', // 提现资金
            'hadsel', // 彩金
            'frozen', // 会员状态
            'real_status', // 是否实名
            'is_return_money', // 是否允许提现
            'dev_status', // 是否允许发展下级
            'create_at', // 注册时间
            'last_login_time', // 上次登录时间
            'last_login_ip', // 上次登录IP
            'top_username', // 上级会员账号
            'recharge', // 总充值
            'profit', // 总输赢
        ];
        $result = $member->exportMember($data, $field, 'id desc');
        foreach ($result as &$item) {
            $item['frozen'] = $item['frozen'] === 0 ? '冻结' : '正常'; // 会员状态
            $item['real_status'] = $item['real_status'] === 0 ? '否' : '是'; // 是否实名
            $item['is_return_money'] = $item['is_return_money'] === 0 ? '否' : '是'; // 是否允许提现
            $item['dev_status'] = $item['dev_status'] === 0 ? '否' : '是'; // 是否允许发展下级
        }
        Helper::exportExcel(
            'member',
            [
                '会员ID', '会员账号', '会员昵称', '上级ID', '余额',
                '冻结资金', '总提现', '彩金', '会员状态', '是否实名',
                '是否允许提现', '是否允许发展下级', '注册时间', '上次登录时间',
                '上次登录IP', '上级会员账号', '总充值', '总输赢', '推荐会员数'],
            $result
        );
    }

    /**
     * 会员详情
     *
     * @param $uid // member ID
     * @param $type // 类型 1: 会员  2: 代理商
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function detail($uid, $type = 1)
    {
        $member = MemberModel::quickGetOne($uid);
        $data = $member->toArray();
        if (empty($data)) {
            return $this->asJson(0, 'error', '您查看的会员不存在');
        }

        // 响应控制
        $result = [];
        // 头像
        $result['photo'] = Attach::getPathByAttachId($data['photo']) ?: '/static/lib/images/msgicon.jpg';
        $result['real_name'] = $data['real_name']; // 真实姓名
        $result['id_card'] = $data['id_card']; // 身份证号
        $result['real_status'] = $data['real_status']; // 实名状态
        $result['is_return_money'] = $data['is_return_money']; // 是否允许提现
        $result['dev_status'] = $data['dev_status']; // 是否允许发展下线
        $result['agent_invite_code'] = $data['agent_invite_code']; // 邀请码
        $result['create_at'] = $data['create_at']; // 注册时间
        $result['update_at'] = $data['update_at']; // 更新时间
        $result['last_login_time'] = $data['last_login_time']; // App最后登录时间
        $result['last_login_ip'] = $data['last_login_ip']; // App最后登录IP
        if ($type == 2) {
            $result['backend_last_login_time'] = $data['backend_last_login_time']; // 代理商后台最后登录时间
            $result['backend_last_login_ip'] = $data['backend_last_login_ip']; // 代理商后台最后登录IP
        }

        return $this->asJson(1, 'success', '请求成功', $result);
    }

    /**
     * 会员直属下级列表
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function recoMember()
    {
        $data = $this->get;
        $data['type'] = 1; // 真实账号
        if (empty($data['top_id'])) {
            return $this->asJson(0, 'error', '上级ID参数不能为空');
        }

        $data['topId'] = $data['top_id']; // 会员
        $member = new MemberModel();
        $filedData = [
            'id', // 会员ID
            'username', // 账号
            'chn_name', // 昵称
            'top_id', // 上级ID
            'photo', // 头像
            'balance', // 余额
            'frozen_capital', // 冻结资金
            'withdraw_deposit', // 提现资金
            'hadsel', // 彩金
            'frozen', // 会员状态
            'real_status', // 实名状态
            'is_return_money', // 是否可以提现
            'dev_status', // 是否可以发展下线
            'create_at', // 注册时间
            'last_login_time', // 上次登录时间
            'last_login_ip', // 上次登录IP
            'top_username', // 上级会员账户
            'recharge', // 总充值
            'profit', // 总输赢
            'role', // 角色
        ];

        $pagination = $member->getUser($data, $filedData, 'create_at desc');
        $page = $pagination->render();
        $list = $pagination->toArray();

        return $this->asJson(1, 'success', '获取成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 添加会员
     *
     * @return \think\response\Json
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function add()
    {
        $data = $this->post;
        $validateRe = $this->validate($data, 'Member.add');
        if ($validateRe !== true) {
            return $this->asJson(0, 'error', $validateRe);
        }

        $member = new MemberModel();
        // 查询账号是否重复
        $check = $member->getOneMember(['username' => $data['userName']], 'id');
        if ($check) {
            return $this->asJson(0, 'error', $data['userName'] . '的账号已存在');
        }

        // 组合数据
        $memberData['username'] = $data['userName'];
        $memberData['chn_name'] = $data['nickName'];
        $memberData['password'] = md5($data['passWord']);
        $memberData['role'] = 1;
        $memberData['is_moni'] = 1;
        $memberData['frozen'] = $data['status'];
        $memberData['is_return_money'] = $data['draw'];
        $memberData['dev_status'] = $data['devStatus'];
        $memberData['create_at'] = date('Y-m-d H:i:s');
        $memberData['agent_invite_code'] = $member->generateUserInviteCode();
        $memberData['path'] = '0,'; // 默认是顶级路径

        // 生成二维码
        $path = Helper::qrcode(Config::PictureHost . '?ic=' . $memberData['agent_invite_code']);
//        $path = Helper::qrcode(Helper::getCurrentHost() . '/web/reg?ic=' . $memberData['agent_invite_code']);
        if (empty($path['head'])) {
            return $this->asJson(0, 'error', '会员添加失败');
        }

        $memberData['invite_code_head'] = $path['head'];
        // 上传头像
        if (!empty($data['file'])) {
            $return = Helper::uploadImage('base64', 'members');
            if (!is_array($return)) {
                return $this->asJson(0, 'error', '上传头像失败，错误信息: ' . $return);
            }

            $head = $return['head'];
            $memberData['photo'] = $head;
        }

        try {
            Db::startTrans();
            // 提交保存数据
            $result = $member->addMember($memberData);
            if (empty($result)) {
                trigger_error('会员添加失败', E_USER_WARNING);
            }

            Db::commit();
            return $this->asJson(1, 'success', '会员添加成功');
        } catch (\Exception $e) {
            return $this->asJson(0, 'error', $e->getMessage());
        }
    }

    /**
     * @desc 获取升级代理所需的彩种信息
     * @author LiBin
     * @date 2019-03-08
     */
    public function getUPAgent()
    {
        $lottery = new Lottery();
        $lotteryData = $lottery->getLottery();

        return $this->asJson(1, 'success', '获取成功', $lotteryData);
    }

    /**
     * @desc 会员提升为代理
     * @author LiBin
     * @throws \Exception
     * @date 2019-03-06
     */
    public function upAgent()
    {
        $data = $this->post;
        $validateRe = $this->validate($data, 'Member.upAgent');
        if ($validateRe !== true) {
            return $this->asJson(0, 'error', $validateRe);
        }

        $member = new MemberModel();
        // 判断操作用户是否存在
        $memberData = $member->getOneMember(['id' => $data['id']], ['role', 'path']);
        if (empty($memberData)) {
            return $this->asJson(0, 'error', '用户不存在!');
        }

        // 通过层级路径获取我的直(间)属上级代理返佣设置, 没有代理则为空集合
        $path = $memberData['path'];
        if (empty($path)) {
            return $this->asJson(0, 'error', '系统错误!');
        }

        $topRelationRatio = MemberModel::getRelationTop($path);
        // 通过会员ID获取直属下级(间接下级)代理返点设置, 没有代理则为空集合
        $subordinateRelationRatio = MemberModel::getRelationSubordinate($data['id']);
        // 实例化彩种数据模型
        $lottery = new Lottery();
        $updateData = [];
        $lotteryId = [];
        foreach ($data['Lottery'] as $k => $v) {
            if (empty($v['value']) && $v['value'] !== '0') {
                return $this->asJson(0, 'error', '佣金比例不能为空,不返佣金请设置0!');
            }

            // 检测佣金是否规范
            if (!is_numeric($v['value'])) {
                return $this->asJson(0, 'error', '请输入正确的佣金比例!');
            }

            // 检查彩种ID是否存在
            $checkId = $lottery->getOneLottery(['id' => $v['key']], ['id', 'name']);
            if (empty($checkId)) {
                return $this->asJson(0, 'error', '彩种不存在!');
            }

            // 校验返佣比例是否大于下级小于上级代理
            if (isset($topRelationRatio[$v['key']]) && $topRelationRatio[$v['key']] < $v['value']) {
                return $this->asJson(0, 'error', "请设置彩种{$checkId['name']}返点不大于上级代理返点{$topRelationRatio[$v['key']]}");
            }

            if (isset($subordinateRelationRatio[$v['key']]) && $subordinateRelationRatio[$v['key']] > $v['value']) {
                return $this->asJson(0, 'error', "请设置彩种{$checkId['name']}返点不小于下级代理返点{$subordinateRelationRatio[$v['key']]}");
            }

            // 组合代理对应彩种返佣比例的数据
            $updateData[$k]['member_id'] = $data['id'];
            $updateData[$k]['lottery_id'] = $v['key'];
            $updateData[$k]['ratio'] = $v['value'];
            $updateData[$k]['status'] = 1;//默认启用
            $updateData[$k]['create_at'] = date('Y-m-d H:i:s');
            $updateData[$k]['update_at'] = date('Y-m-d H:i:s');
            // 组合彩种ID
            $lotteryId[] = $checkId['id'];
        }

        // 判断操作密码是否正确
        $password = md5($data['passWord']);
        $admin = new AdminModel();
        $adminData = $admin->getOneAll(['id' => UID, 'password' => $password]);
        if (empty($adminData)) {
            return $this->asJson(0, 'error', '请输入正确的操作密码!');
        }

        // 如果用户是代理商则不可以再提升
        if ($memberData['role'] == 2) {
            return $this->asJson(0, 'error', '代理商无法继续提升!');
        }

        try {
            Db::startTrans();
            // 删除重复的彩种返佣信息
            $memberRatio = new MemberRatio();
            $memberRatio->deleteMemberBatch(['member_id' => $data['id'], 'lottery_id' => $lotteryId]);
            // 修改用户表
            $setMember = $member->setMember(['id' => $data['id']], ['role' => 2, 'dev_status' => $data['lowlevel'], 'is_return_money' => $data['withdraw'], 'update_at' => date('Y-m-d H:i:s')]);
            if (empty($setMember)) {
                trigger_error('提升代理商失败', E_USER_WARNING);
            }

            // 修改资金流水,充值记录,提现记录表
            FundLog::where(['member_id' => $data['id']])->setField('identify', 2);
            FundCharge::where(['member_id' => $data['id']])->setField('identify', 2);
            FundWithdraw::where(['member_id' => $data['id']])->setField('identify', 2);

            // 添加代理商返点设置表
            $upLottery = $memberRatio->saveAllLottery($updateData);
            if (empty($upLottery)) {
                trigger_error('提升代理商失败', E_USER_WARNING);
            }

            Db::commit();
            return $this->asJson(1, 'success', '提升代理商成功');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->asJson(0, 'error', $e->getMessage());
        }
    }

    /**
     * 转移会员
     *
     * @return \think\response\Json
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function transferMember()
    {
        $data = $this->post;
        $validation = $this->validate($data, 'Member.transferMember');
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $model = new MemberModel;
        $result = $model->transformUser('vp', $data['id'], $data['userName'], $data['passWord']);
        if ($result !== true) {
            return $this->asJson(0, 'error', $result);
        }

        return $this->asJson(1, 'success', '转移成功');
    }

    /**
     * @desc 修改彩金
     * @author LiBin
     * @throws \Exception
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

        Db::startTrans();
        //更新彩金
        $upMemberHadsel = $member->setMember(['id' => $data['id']], ['hadsel' => $data['gold']]);
        if (empty($upMemberHadsel)) {
            Db::rollback();
            return $this->asJson(0, 'error', '彩金修改失败');
        }

        //组合数据
        $funData['member_id'] = $data['id'];
        $funData['money'] = abs(bcsub($memberData['hadsel'], $data['gold'], 2));
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
     * @desc 修改密码
     * @author LiBin
     * @throws \Exception
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
     * @desc 修改余额
     * @author LiBin
     * @throws \Exception
     * @date 2019-03-06
     */
    public function reviseBalance()
    {
        $data = $this->post;
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

        Db::startTrans();
        //更新余额
        $upMemberHadsel = $member->setMember(['id' => $data['id']], ['balance' => $data['balance']]);
        if (empty($upMemberHadsel)) {
            Db::rollback();
            return $this->asJson(0, 'error', '余额修改失败');
        }

        // 组合数据
        $funData['member_id'] = $data['id'];
        $funData['money'] = abs(bcsub($memberData['balance'], $data['balance'], 2));
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
     * @throws \Exception
     * @auther LiBin
     * @date 2019-03-08
     */
    public function deletMember()
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
     * @desc 冻结/解冻
     * @author LiBin
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
     * @author LiBin
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
     * @desc 获取会员详情
     * @throws \Exception
     * @author LiBin
     * @date 2019-03-19
     */
    public function getMemberinfo()
    {
        $data = $this->get;
        if (empty($data['memberid'])) {
            return $this->asJson(0, 'error', '暂无用户信息');
        }
        $where['id'] = $data['memberid'];
        $member = new MemberModel();
        $data = $member->getOneMember($where, [
            'id', // 会员ID
            'username', // 账号
            'chn_name', // 昵称
            'is_return_money', // 是否允许提现
            'dev_status', // 发展下线
            'frozen', // 是否冻结
        ]);

        return $this->asJson(1, 'success', '获取成功', $data);
    }

    /**
     * @desc 更新会员
     * @author LiBin
     * @throws \Exception
     * @date 2019-03-19
     */
    public function updataMember()
    {
        $data = $this->post;
        if (empty($data['memberId'])) {
            return $this->asJson(0, 'error', '用户ID不能为空');
        }

        $where['id'] = $data['memberId'];
        // 验证昵称不能重复
        $updateData = [];
        if (!empty($data['nickName'])) {
            // 昵称限长
            if (mb_strlen($data['nickName'], 'UTF-8') > 15) {
                return $this->asJson(0, 'error', '昵称长度不能大于15个字符');
            }

            $member = new MemberModel();
            $whereMember[] = ['id', 'neq', $data['memberId']];
            $whereMember[] = ['chn_name', 'eq', $data['nickName']];
            $dataMember = $member->getOneMember($whereMember, 'id');
            if (!empty($dataMember)) {
                return $this->asJson(0, 'error', '昵称已存在,请重新定义');
            }

            $updateData['chn_name'] = $data['nickName'];
        }

        if (!empty($data['draw'])) {
            $updateData['is_return_money'] = $data['draw'];
        }

        if (!empty($data['frozen'])) {
            $updateData['frozen'] = $data['frozen'];
        }

        if (!empty($data['status'])) {
            $updateData['dev_status'] = $data['status'];
        }

        $member = new MemberModel();
        $member->setMember($where, $updateData);
        return $this->asJson(1, 'success', '更新成功');
    }

    /**
     * @desc 获取用户绑定银行卡
     * @author LiBin
     * @throws \Exception
     * @return \think\response\Json
     * @date 2019-04-23
     */
    public function getBankCard()
    {
        $data = $this->get;
        if (empty($data['memberid'])) {
            return $this->asJson(0, 'error', '用户ID不能为空');
        }

        $memberBank = new MemberBankModel();
        $list = 'id,bank,bank_num,bank_code,cardholder';
        $bankData = $memberBank->getBankCardListData($data['memberid'], $list);
        //获取银行列表
        $adminBank = new AdminBank();
        $adminBankData = $adminBank->getBankList(['status' => 1], ['name', 'code']);
        $result['bankData'] = $bankData;
        $result['banklist'] = $adminBankData;
        if (empty($bankData)) {
            return $this->asJson(0, 'error', '暂未绑定银行卡');
        }

        return $this->asJson(1, 'success', '获取成功', $result);
    }

    /**
     * @desc 更新用户银行卡数据
     * @author LiBin
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     * @date 2019-04-24
     */
    public function upBankCard()
    {
        $data = $this->post;
        if (empty($data['rdata'])) {
            return $this->asJson(0, 'error', '缺失数据');
        }

        $dataArr = explode('|', trim(trim($data['rdata'], '|'), ','));
        $memberBank = new MemberBankModel();
        $model = new MemberBankModel();
        $list = [];
        Db::startTrans();
        foreach ($dataArr as $k => $v) {
            $bankData = explode(',', trim($v, ','));
            foreach ($bankData as $key => $value) {
                $listData = explode(':', $bankData[$key]);
                $list[$listData[0]] = $listData[1];
            }
            // 验证银行卡号和归属银行
            //获取银行列表
            $adminBank = new AdminBank();
            $adminBankData = $adminBank->getBankOne(['code' => $list['bank_code']], ['name']);
            $falgs = $model->checkCard($adminBankData['name'], $list['bank_num']);
            if ($falgs !== true) {
                return $this->asJson(0, 'error', $falgs);
            }

            $updateResult = $memberBank->setBank($list['id'], $list);
            if ($updateResult === false) {
                Db::rollback();
                return $this->asJson(0, 'error', '银行卡修改失败');
            }
        }

        Db::commit();
        return $this->asJson(1, 'success', '修改成功');
    }

    /**
     * @desc 获取银行卡列表
     * @author ken
     * @throws \Exception
     * @return \think\response\Json
     * @date 2019-04-28
     */
    public function addBankCard()
    {
        // 获取银行列表
        $adminBank = new AdminBank();
        $adminBankData = $adminBank->getBankList(['status' => 1], ['name', 'code']);
        $data['banklist'] = $adminBankData;

        return $this->asJson(1, 'success', '请求成功', $data);
    }

    /**
     * @desc 保存银行卡
     * @author ken
     * @return \think\response\Json
     * @date 2019-04-28
     */
    public function saveBankCard()
    {
        $data = $this->post;
        $validateRe = $this->validate($data, 'Member.saveBankCard');
        if ($validateRe !== true) {
            return $this->asJson(0, 'error', $validateRe);
        }

        $data['create_at'] = Helper::timeFormat(time(), 's');
        $data['update_at'] = Helper::timeFormat(time(), 's');

        // 验证银行卡号对应的开户行
        $model = new MemberBankModel();
        $falgs = $model->checkCard($data['bank'], $data['bank_num']);
        if ($falgs !== true) {
            return $this->asJson(0, 'error', $falgs);
        }
        $memberBank = new MemberBankModel();
        $newBank = $memberBank->addBankCard($data, false);
        if ($newBank === false) {
            return $this->asJson(0, 'error', '银行卡添加失败');
        }

        return $this->asJson(1, 'success', '银行卡添加成功');
    }

    /**
     * @desc 更新所有邀请二维码(用于更换域名后操作)
     * @return string
     * @throws \Exception
     * @date 2019-05-08
     * @updateBy CleverStone
     */
    public function upAllQRcode()
    {
        // 数据量较大, 提高环境运行限制
        set_time_limit(0);
        ini_set('memory_limit', '500M');
        // 关闭缓冲区序列
        ob_end_clean();
        // 开启隐式刷送(需先关闭已有的缓冲区该配置才生效)
        ob_implicit_flush(true);
        // 设置样式
        echo '<style rel="stylesheet">
p{
color: gray;
font-size: 13px;
padding-left: 20px;
margin: 0;
padding-top: 5px;
padding-bottom: 5px;
display: flex;
}
p:hover{
background-color: #eeeeee;
}
p.t{
border-top: 1px solid #eeeeee;
padding-top: 5px;
color: #1bb99a;
font-size: 15px;
}
span{
flex: 1;
display: inline-block;
text-align: center;
}
</style>';
        $beginTime = microtime(true);
        // 获取所有会员
        $data = MemberModel::field([
            'id', // 会员ID
            'username', // 会员账号
            'agent_invite_code', // 邀请码
            'invite_code_head', // 邀请二维码附件ID
        ])->select();
        try {
            // 模板回调
            $tmpFunc = function ($username, $status, $useTime, $type = 1, $msg = '') {
                switch ($type) {
                    case 1: // 每条数据处理成功模板
                        return "<p><span>账号: {$username}</span><span>状态: {$status}</span><span>耗时: {$useTime}秒</span></p>";
                    case 2: // 每条数据处理失败模板
                        return "<p><span>账号: {$username}</span><span>状态: {$status}</span><span>错误信息: {$msg}</span><span>耗时: {$useTime}秒</span></p>";
                    default: // 处理完成模板
                        return "<p class='t'><span>总共更新{$username}条</span><span>{$username}成功 0失败</span><span>总耗时: {$useTime}秒</span></p>";
                }
            };

            // 这里不可以使用事务处理
            // 原因: 程序运行过程中, 随着数据量变大会造成事务锁表, 进而会影响其他业务正常运行
            foreach ($data as $v) {
                $everyStartTime = microtime(true);
                // 生成新的邀请二维码
                $path = Helper::qrcode(Config::PictureHost . '?ic=' . $v['agent_invite_code']);
//                $path = Helper::qrcode(Helper::getCurrentHost() . '/web/reg?ic=' . $v['agent_invite_code']);
                if (is_string($path)) {
                    trigger_error($path);
                }

                // 新生成的二维码和旧二维码一样,则跳过
                if ($path['head'] == $v['invite_code_head']) {
                    continue;
                }
                // 更新为新的二维码附件ID
                MemberModel::quickCreate([
                    'id' => $v['id'], // 主键ID
                    'invite_code_head' => $path['head'], // 新二维码附件ID
                ], true);
                // 获取旧的二维码路径
                $oldQrPath = Attach::getPathByAttachId($v['invite_code_head']);
                $oldQrPath = ltrim($oldQrPath, '/');
                $oldQrPath = realpath(PUBLIC_PATH . $oldQrPath);
                // 删除附件表该条数据
                Attach::del($v['invite_code_head']);
                // 删除旧的二维码图片
                if ($oldQrPath && is_file($oldQrPath)) {
                    unlink($oldQrPath);
                }

                $everyEndTime = microtime(true);
                $everyUseTime = sprintf('%01.2f', $everyEndTime - $everyStartTime);
                echo call_user_func($tmpFunc, $v['username'], '成功', $everyUseTime, 1);
            }

            $endTime = microtime(true);
            $useTime = sprintf('%01.2f', $endTime - $beginTime);
            echo call_user_func($tmpFunc, count($data), '', $useTime, 3);
        } catch (\Exception $e) {
            echo call_user_func($tmpFunc, '未知', '失败', 0, 2, $e->getMessage());
        }

        exit(0);
    }
}
