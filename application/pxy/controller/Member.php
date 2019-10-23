<?php

namespace app\pxy\controller;

use app\common\Helper;
use app\common\model\Attach;
use app\common\model\FundCharge;
use app\common\model\FundLog;
use app\common\model\FundWithdraw;
use app\common\PxyController;
use app\common\model\Member as MemberModel;
use app\common\model\Lottery;
use app\common\Config;
use app\common\model\MemberRatio;
use think\db;

/**
 * 会员管理页面
 *
 * Class member
 * @package app\vp\controller
 * @author sumer
 */
class Member extends PxyController
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
        $data['type'] = 1; // 真实账号
        $data['role'] = 1; // 会员
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
            'real_status',
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
        $data['path'] = UID;
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
     * @desc 会员直属下级列表
     * @author LiBin
     * @throws \Exception
     * @date 2019-03-02
     */
    public function recoMember()
    {
        $data = $this->get;
        $data['type'] = 1; // 真实账号
        if (empty($data['top_id'])) {
            return $this->asJson(0, 'error', '获取失败');
        }

        $data['topId'] = $data['top_id']; // 会员
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
            'real_status',
            'is_return_money',
            'dev_status',
            'create_at',
            'last_login_time',
            'last_login_ip',
            'top_username',
            'recharge',
            'profit',
            'role',
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
        $validateRe = $this->validate($data, 'member.add');
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
        $memberData['top_id'] = UID;
        $memberData['agent_invite_code'] = $member->generateUserInviteCode();
        $memberData['path'] = '0,' . UID . ',';
        $memberData['top_username'] = MemberModel::getValByWhere(['id' => UID], 'username');
        $path = Helper::qrcode(Config::PictureHost.'/ic=' . $memberData['agent_invite_code']);
//        $path = Helper::qrcode(Helper::getCurrentHost() . '/web/reg?ic=' . $memberData['agent_invite_code']);
        if (empty($path['head'])) {
            return $this->asJson(0, 'error', '会员添加失败');
        }

        $memberData['invite_code_head'] = $path['head'];
        try {
            Db::startTrans();
            $result = $member->addMember($memberData);
            if (empty($result)) {
                trigger_error('会员添加失败', E_USER_WARNING);
            }

            Db::commit();
            return $this->asJson(1, 'success', '会员添加成功');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->asJson(0, 'error', $e->getMessage());
        }
    }

    /**
     * @desc 升级代理商获取彩种
     * @auther LiBin
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
     * @auther LiBin
     * @throws \Exception
     * @date 2019-03-06
     */
    public function upAgent()
    {
        $data = $this->post;
        $validateRe = $this->validate($data, 'member.upAgent');
        if ($validateRe !== true) {
            return $this->asJson(0, 'error', $validateRe);
        }

        $member = new MemberModel();
        // 检验提升的会员是否存在
        $memberInfo = $member->getOneMember(['id' => $data['id']], ['path']);
        if (empty($memberInfo)) {
            return $this->asJson(0, 'error', '会员账号不存在!');
        }

        // 实例化彩种数据模型
        $lottery = new Lottery();
        $memberRatio = new MemberRatio();//实例化返佣比例
        // 校验我的返佣比例
        $myRebateRatio = $memberRatio->getLottery(UID);
        if (!count($myRebateRatio)) {
            return $this->asJson(0, 'error', '请您先设置自己的返佣比例!');
        }

        // 通过层级路径获取会员的直(间)属上级代理返佣设置
        $path = $memberInfo['path'];
        if (empty($path)) {
            return $this->asJson(0, 'error', '系统错误!');
        }

        $topRelationRatio = MemberModel::getRelationTop($path);
        // 通过会员ID获取直属下级(间接下级)代理返点设置
        $subordinateRelationRatio = MemberModel::getRelationSubordinate($data['id']);
        $updateData = [];
        $lotteryId = [];
        foreach ($data['Lottery'] as $k => $v) {
            if (empty($v['value']) && $v['value'] !== '0') {
                return $this->asJson(0, 'error', '佣金比例最低为0,但不可以为空!');
            }

            // 检测佣金是否规范
            if (!is_numeric($v['value'])) {
                return $this->asJson(0, 'error', '请正确填写佣金比例!');
            }

            // 检查彩种ID是否存在
            $checkId = $lottery->getOneLottery(['id' => $v['key']], ['id', 'name']);
            if (empty($checkId['id'])) {
                return $this->asJson(0, 'error', '请求参数存在不存在的彩种!');
            }

            // 校验返佣比例是否大于下级小于上级代理
            if (isset($topRelationRatio[$v['key']]) && $topRelationRatio[$v['key']] < $v['value']) {
                return $this->asJson(0, 'error', "请设置彩种{$checkId['name']}返点不大于上级代理返点{$topRelationRatio[$v['key']]}");
            }

            if (isset($subordinateRelationRatio[$v['key']]) && $subordinateRelationRatio[$v['key']] > $v['value']) {
                return $this->asJson(0, 'error', "请设置彩种{$checkId['name']}返点不小于下级代理返点{$subordinateRelationRatio[$v['key']]}");
            }

            //组合代理对应彩种返佣比例的数据
            $updateData[$k]['member_id'] = $data['id'];
            $updateData[$k]['lottery_id'] = $v['key'];
            $updateData[$k]['ratio'] = $v['value'];
            $updateData[$k]['status'] = 1;//默认启用
            $updateData[$k]['create_at'] = date('Y-m-d H:i:s');
            $updateData[$k]['update_at'] = date('Y-m-d H:i:s');
            //组合彩种ID
            $lotteryId[] = $checkId['id'];
        }
        //判断操作密码是否正确
        $password = md5($data['passWord']);
        $memberData = $member->getOneMember(['id' => UID, 'password' => $password], 'id');
        if (empty($memberData['id'])) {
            return $this->asJson(0, 'error', '请输入正确的操作密码');
        }

        try {
            Db::startTrans();
            // 删除重复的彩种返佣信息
            $memberRatio->deleteMemberBatch(['member_id' => $data['id'], 'lottery_id' => $lotteryId]);
            // 修改用户表
            $setMember = $member->setMember(['id' => $data['id']], [
                'role' => 2,
                'dev_status' => $data['lowlevel'],
                'is_return_money' => $data['withdraw'],
                'update_at' => date('Y-m-d H:i:s')
            ]);
            if (empty($setMember)) {
                trigger_error('提升代理商失败', E_USER_WARNING);
            }

            // 新增返佣设置
            $upLottery = $memberRatio->saveAllLottery($updateData);
            if (empty($upLottery)) {
                trigger_error('提升代理商失败', E_USER_WARNING);
            }

            // 修改资金流水,充值记录,提现记录表
            FundLog::where(['member_id' => $data['id']])->setField('identify', 2);
            FundCharge::where(['member_id' => $data['id']])->setField('identify', 2);
            FundWithdraw::where(['member_id' => $data['id']])->setField('identify', 2);

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
        $validation = $this->validate($data, 'member.transferMember');
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $model = new MemberModel;
        $result = $model->transformUser('pxy', $data['id'], $data['userName'], $data['passWord']);
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
    public function deletMember()
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
     * 代理商后台个人中心
     *
     * @return \think\response\Json
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function detail()
    {
        $detailInfo = MemberModel::quickGetOne(UID);
        if (empty($detailInfo)) {
            return $this->asJson(0, 'error', '代理商不存在');
        }

        unset(
            $detailInfo['role'], // 角色
            $detailInfo['is_moni'], // 是否模拟
            $detailInfo['frozen'], // 是否冻结
            $detailInfo['is_return_money'], // 是否允许提现
            $detailInfo['dev_status'], // 是否允许发展下线
            $detailInfo['real_status'], // 是否实名
            $detailInfo['last_login_time'], // 上次登录时间
            $detailInfo['last_login_ip'], // 上次登录IP
            $detailInfo['is_delete'], // 是否删除
            $detailInfo['delete_time'], // 删除是否
            $detailInfo['top_id'], // 上级ID
            $detailInfo['path'], // 路径
            $detailInfo['password'], // 密码
            $detailInfo['frozen_capital'] // 冻结资金
        );

        // 获取邀请码图片
        $inviteCodeImg = Attach::getPathByAttachId($detailInfo['invite_code_head']);
        if (!empty($inviteCodeImg)) {
            $publicPath = rtrim(PUBLIC_PATH, DS);
            $inviteCodePath = $publicPath . $inviteCodeImg;
            if (realpath($inviteCodePath) === false) {
                $head = Helper::qrcode(Helper::getCurrentHost() . '/web/reg?ic=' . $detailInfo['agent_invite_code']);
                if (is_string($head)) {
                    return $this->asJson(0, 'error', '生成二维码失败');
                }

                // 新的二维码
                $detailInfo['invite_code_head'] = $head['path'];
                MemberModel::quickCreate([
                    'id' => $detailInfo['id'],
                    'invite_code_head' => $head['head'],
                ], true);
            } else {
                $detailInfo['invite_code_head'] = $inviteCodeImg;
            }
        } else {
            $head = Helper::qrcode(Helper::getCurrentHost() . '/web/reg?ic=' . $detailInfo['agent_invite_code']);
            if (is_string($head)) {
                return $this->asJson(0, 'error', '生成二维码失败');
            }

            // 新的二维码
            $detailInfo['invite_code_head'] = $head['path'];
            MemberModel::quickCreate([
                'id' => $detailInfo['id'],
                'invite_code_head' => $head['head'],
            ], true);
        }

        $detailInfo['photo'] = Attach::getPathByAttachId($detailInfo['photo']);
        return $this->asJson(1, 'success', '请求成功', $detailInfo);
    }

    /**
     * 个人中心修改
     *
     * @return \think\response\Json
     * @throws \think\exception\DbException
     * @throws db\exception\DataNotFoundException
     * @throws db\exception\ModelNotFoundException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function modifyMember()
    {
        $post = $this->post;
        $validation = $this->validate($post, [
            'nickName|昵称' => 'require|length:1,20',
            'passWord|密码' => 'length:6,25',
        ]);

        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $data = [];
        $data['chn_name'] = $post['nickName'];
        if (!empty($post['passWord'])) {
            $data['password'] = md5($post['passWord']);
        }

        if (isset($post['file']) && !empty($post['file'])) {
            $result = Helper::uploadImage('base64', 'members');
            if (is_array($result)) {
                $data['photo'] = $result['head'];
            } else {
                return $this->asJson(0, 'error', '上传头像失败');
            }
        }

        $data['id'] = UID;
        MemberModel::quickCreate($data, true);
        return $this->asJson(1, 'success', '修改成功');
    }
}
