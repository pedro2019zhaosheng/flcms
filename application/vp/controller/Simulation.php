<?php

namespace app\vp\controller;

use app\common\Helper;
use app\common\VpController;
use app\common\model\Member as MemberModel;
use app\common\model\Admin as AdminModel;
use think\db;

/**
 * 模拟账号
 *
 * Class member
 * @package app\vp\controller
 * @author sumer
 */
class Simulation extends VpController
{
    /**
     * @desc 模拟账号列表
     * @throws \Exception
     * @auther LiBin
     * @date 2019-03-02
     */
    public function index()
    {
        $data = $this->get;
        $data['type'] = 0;//虚拟账号
        $data['role'] = 1;//会员
        $User = new MemberModel();
        $filedData = [
            'id', // ID
            'username', // 用户名
            'chn_name', // 昵称
            'top_id', // 上级ID
            'photo', // 头像
            'balance', // 余额
            'hadsel', // 彩金
            'frozen', // 状态
            'create_at', // 注册时间
            'last_login_time', // 上次登录时间
            'last_login_ip', // 上次登录IP
            'profit', // 总输赢
        ];
        $pagination = $User->getUser($data, $filedData, 'create_at desc');
        $page = $pagination->render();
        $list = $pagination->toArray();

        return $this->asJson(1, 'success', '获取成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * @desc 添加会员
     * @auther LiBin
     * @throws \Exception
     * @date 2019-03-05
     */
    public function add()
    {
        $data = $this->post;
        $validation = $this->validate($data, 'Member.simulationAdd');
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
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
        $memberData['is_moni'] = 0;
        $memberData['frozen'] = $data['status'];
        $memberData['is_return_money'] = 0;
        $memberData['dev_status'] = 0;
        $memberData['create_at'] = date('Y-m-d H:i:s');
        $memberData['agent_invite_code'] = $member->generateUserInviteCode();
        $memberData['path'] = '0,'; // 默认是最顶级会员

        $path = Helper::qrcode(Helper::getCurrentHost() . '/web/reg?ic=' . $memberData['agent_invite_code']);
        if (empty($path['head'])) {
            return $this->asJson(0, 'error', '生成邀请码失败');
        }

        $memberData['invite_code_head'] = $path['head'];
        try {
            Db::startTrans();
            $result = $member->addMember($memberData);
            if (empty($result)) {
                trigger_error('添加失败', E_USER_WARNING);
            }

            Db::commit();
            return $this->asJson(1, 'success', '添加成功');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->asJson(0, 'error', $e->getMessage());
        }
    }

    /**
     * @desc 修改彩金
     * @auther LiBin
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

        // 检测彩金值是否规范
        if (!is_numeric($data['gold'])) {
            return $this->asJson(0, 'error', '请输入正确的彩金值');
        }

        // 判断操作密码是否正确
        $password = md5($data['passWord']);
        $admin = new AdminModel();
        $adminData = $admin->getOneAll(['id' => UID, 'password' => $password]);
        if (empty($adminData)) {
            return $this->asJson(0, 'error', '请输入正确的操作密码');
        }

        // 更新彩金
        $member = new MemberModel();
        $result = $member->setMember(['id' => $data['id']], ['hadsel' => $data['gold']]);
        if (!$result) {
            return $this->asJson(0, 'error', '彩金修改失败');
        }

        return $this->asJson(1, 'success', '彩金修改成功');
    }

    /**
     * @desc 修改余额
     * @auther LiBin
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

        // 检测余额值是否规范
        if (!is_numeric($data['balance'])) {
            return $this->asJson(0, 'error', '请输入正确的金额值');
        }

        // 判断操作密码是否正确
        $password = md5($data['passWord']);
        $admin = new AdminModel();
        $adminData = $admin->getOneAll(['id' => UID, 'password' => $password]);
        if (empty($adminData)) {
            return $this->asJson(0, 'error', '请输入正确的操作密码');
        }

        // 更新余额
        $member = new MemberModel();
        $result = $member->setMember(['id' => $data['id']], ['balance' => $data['balance']]);
        if (!$result) {
            return $this->asJson(0, 'error', '余额修改失败');
        }

        return $this->asJson(1, 'success', '余额修改成功');
    }

    /**
     * @desc 删除会员数据(软删除)
     * @auther LiBin
     * @throws \Exception
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
     * @desc 修改用户密码
     * @auther LiBin
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
     * @desc 更新用户的数据
     * @auther LiBin
     * @date 2019-03-19
     */
    public function updataMember()
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
}