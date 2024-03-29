<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/22
 * Time: 9:31
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world.
 */

namespace app\api\controller;

use app\common\model\AdminService;
use app\common\model\FundWithdraw;
use app\common\model\HistoryRecord;
use app\common\RestController;
use app\common\model\MemberBank;
use app\common\model\AdminSmslog;
use app\common\model\FundLog;
use app\common\model\Member as MemberModel;
use app\common\model\Order;
use app\common\model\Attention;
use app\common\model\AdminBank;
use app\common\Config;
use app\common\Helper;
use think\Db;
use think\response\Json;

/**
 * 会员接口控制器.
 *
 * Class Member
 *
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Member extends RestController
{
    /**
     * authentication彩种列表.
     *
     * @param array $disableAuthAction
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    protected function init(array $disableAuthAction = [])
    {
        $disableAuthAction = ['hotSearch', 'service'];
        parent::init($disableAuthAction); // TODO: Change the autogenerated stub
    }

    /**
     * @desc 验证会员是否登录
     *
     * @param //_t;
     * @param //_uid;
     * @return Json
     * @author LiBin
     * @date 2019-03-27
     */
    public function checkMember()
    {
        $response = $this->asJson(1, 'success', '用户已登录');
        return $response;
    }

    /**
     * 账户信息 - 银行卡列表.
     *
     * @param //_uid; 用户编号
     * @return Json
     * @author hutao
     * @date 2019-03-29
     */
    public function bankCardList()
    {
        $model = new MemberBank();
        $memberData = $model->getBankCardList(UID);

        return $this->asNewJson('centerRet', 1, 'success', '账户信息 - 银行卡列表', $memberData);
    }

    /**
     * 账户信息 - 设置默认银行卡.
     *
     * @throws \Exception
     * @param //_uid; 用户编号
     * @param //bank_id; 银行卡编号
     * @return Json
     * @author hutao
     * @date 2019-03-29
     */
    public function setBankDefault()
    {
        $param = $this->post;
        if (!isset($param['bank_id']) || empty($param['bank_id'])) {
            return $this->asNewJson('setDefRet', 0, 'error', '缺少参数');
        }

        $model = new MemberBank();
        $result = $model->setBankDefaultById(UID, $param['bank_id']);
        if ($result === true){
            return $this->asNewJson('setDefRet', 1, 'success', '设置成功');
        }

        return $this->asNewJson('setDefRet', 0, 'error', $result);
    }

    /**
     * 账户信息 - 新增银行卡.
     *
     * @param //member_id; 用户编号
     * @param //cardholder; 持卡人姓名
     * @param //bank; 开户行
     * @param //bank_num; 银行卡号
     * @param //mobile; 手机号
     * @param //code; 验证码
     * @throws \Exception
     * @return Json
     * @author hutao
     * @date 2019-03-29
     */
    public function addBankCard()
    {
        $data = $this->post;
        $data['member_id'] = UID;
        $validation = $this->validate($data, 'memberBank.add');
        if ($validation !== true) {
            return $this->asNewJson('addCardRet', 0, 'error', $validation);
        }
        $model = new MemberBank();
        // 验证银行卡号和归属银行
        $falgs = $model->checkCard($data['bank'], $data['bank_num']);
        if ($falgs !== true) {
            return $this->asNewJson('addCardRet', 0, 'error', $falgs);
        }
        //验证短信
        $smslog = new AdminSmslog();
        $smsData = $smslog->checkSms($data['mobile'], $data['code']);
        if ($smsData[0] == 0) {
            return $this->asNewJson('addCardRet', $smsData[0], $smsData[1], $smsData['2']);
        }
        // 验证此银行卡是否已经绑定过
        $card = MemberBank::where(['member_id' => $data['member_id'], 'bank_num' => $data['bank_num']])->value('id');
        if (!empty($card)) {
            return $this->asNewJson('addCardRet', 0, 'error', '此银行卡已经绑定过');
        }

        //获取银行code
        $adminBank = new AdminBank();
        $bankCode = $adminBank->getBankOne(['name' => $data['bank']], 'code');
        $data['bank_code'] = $bankCode['code'];
        $data['create_at'] = Helper::timeFormat(time(), 's');
        $data['update_at'] = Helper::timeFormat(time(), 's');
        $defaultId = MemberBank::getValByWhere(['member_id' => UID, 'default_or_not' => 1], 'id');
        if (!$defaultId) {
            // 不存在默认银行卡, 则设置该银行卡为默认银行卡
            $data['default_or_not'] = 1;
        }

        $memberData = $model->addBankCard($data, false);
        if (empty($memberData)) {
            return $this->asNewJson('addCardRet', 0, 'error', '新增失败');
        }

        return $this->asNewJson('addCardRet', 1, 'success', '新增成功');
    }

    /**
     * 账户信息 - 编辑银行卡.
     *
     * @param //bank_id; 银行卡id编号
     * @param //member_id; 用户编号
     * @param //cardholder; 持卡人姓名
     * @param //bank; 开户行
     * @param //bank_num; 银行卡号
     * @param //mobile; 手机号
     * @param //default_or_not; 是否默认提现卡 状态值 0是不默认 1 默认
     * @throws \Exception
     * @return Json
     * @author hutao
     * @date 2019-03-29
     */
    public function editBankCard()
    {
        $data = $this->post;
        $data['member_id'] = UID;
        $validation = $this->validate($data, 'memberBank.edit');
        if ($validation !== true) {
            return $this->asNewJson('editCardRet', 0, 'error', $validation);
        }

        $model = new MemberBank();

        // 验证银行卡号和归属银行
        $falgs = $model->checkCard($data['bank'], $data['bank_num']);
        if ($falgs !== true) {
            return $this->asNewJson('addCardRet', 0, 'error', $falgs);
        }

        // 验证此银行卡是否已经绑定过
        $card = $model->where(['member_id' => $data['member_id'], 'bank_num' => $data['bank_num']])->value('id');
        if (!empty($card) && $card != $data['bank_id']) {
            return $this->asNewJson('editCardRet', 0, 'error', '此银行卡已经绑定过');
        }

        $data['id'] = $data['bank_id'];
        $memberData = $model->addBankCard($data, true);
        if (empty($memberData)) {
            return $this->asNewJson('editCardRet', 0, 'error', '编辑失败');
        }

        return $this->asNewJson('editCardRet', 1, 'success', '编辑成功');
    }

    /**
     * 账户信息 - 解绑银行卡.
     *
     * @param //bank_id; 银行卡id编号
     * @throws \Exception
     * @return Json
     * @author hutao
     * @date 2019-03-29
     */
    public function delBankCard()
    {
        $data = $this->post;
        $validation = $this->validate($data, 'memberBank.del');
        if ($validation !== true) {
            return $this->asNewJson('delCardRet', 0, 'error', $validation);
        }

        $fundId = FundWithdraw::getValByWhere(['bank_id' => $data['bank_id'], 'member_id' => UID, 'status' => 2], 'id');
        if ($fundId) {
            return $this->asNewJson('delCardRet', 0, 'error', '该卡处于提现中,无法解绑');
        }

        try {
            Db::startTrans();
            // 删除银行卡
            $model = new MemberBank();
            $result = $model->removeCard($data['bank_id']);
            if (!$result) {
                trigger_error('解绑失败');
            }

            // 获取该银行卡处于提现审核中的订单ID
            $withdrawData = FundWithdraw::where([
                'bank_id' => $data['bank_id'],
                'status' => 1,
            ])
                ->field(['account', 'id'])
                ->select()
                ->toArray();
            if (!empty($withdrawData)) {
                $withdrawIds = array_column($withdrawData, 'id');
                $returnAccounts = array_sum(array_column($withdrawData, 'account'));
                // 设置该银行卡审核中的订单为提现失败
                FundWithdraw::where([
                    'bank_id' => $data['bank_id'],
                    'status' => 1,
                ])
                    ->setField('status', 5); // 设置提现失败
                // 修改资金消息
                FundLog::where('withdraw_id', 'in', $withdrawIds)
                    ->where('member_id', UID) // 多加uid作为筛选条件
                    ->setField([
                    'remark' => '银行卡解绑,提现失败',
                ]);
                // 加排他锁
                $memberData = MemberModel::where('id', UID)
                    ->lock(true)
                    ->field([
                        'frozen_capital',
                        'balance',
                        'role',
                        'username',
                    ])
                    ->find();
                $frozenCapital = bcsub($memberData['frozen_capital'], $returnAccounts, 2);
                $surplusBalance = bcadd($memberData['balance'], $returnAccounts, 2);
                if ($frozenCapital < 0){
                    trigger_error('解绑失败, 系统错误');
                }

                // 返还资金
                MemberModel::where('id', UID)
                    ->data([
                        'frozen_capital' => $frozenCapital,
                        'balance' => $surplusBalance,
                    ])
                    ->update();
                // 写入资金变动
                FundLog::quickCreate([
                    'member_id' => UID,
                    'money' => $returnAccounts,
                    'front_money' => $memberData['balance'],
                    'later_money' => $surplusBalance,
                    'type' => 2,
                    'remark' => '银行卡解绑,提现资金返还',
                    'create_time' => Helper::timeFormat(time(), 's'),
                    'update_time' => Helper::timeFormat(time(), 's'),
                    'identify' => $memberData['role'],
                    'username' => $memberData['username'],
                ]);
            }

            Db::commit();
            return $this->asNewJson('delCardRet', 1, 'success', '解绑成功');
        } catch (\Exception $e) {
            Db::rollback();
            return $this->asNewJson('delCardRet', 0, 'error', $e->getMessage());
        }
    }

    /**
     * 账户信息 - 银行卡选择列表.
     * @throws \Exception
     */
    public function bankList()
    {
        //获取银行列表
        $adminBank = new AdminBank();
        $adminBankData = $adminBank->getBankList(['status' => 1], ['name', 'code']);
        $bankArr = [];
        foreach ($adminBankData as $k => $v) {
            $bankArr[] = $v['name'];
        }

        return $this->asNewJson('bankListRet', 1, 'success', '银行卡选择列表', $bankArr);
    }

    /**
     * @desc 获取默认银行卡
     * @author LiBin
     * @date 2019-04-22
     */
    public function getDefaultBank()
    {
        //获取默认银行卡
        $memberBank = new MemberBank();
        $where[] = ['member_id', 'eq', UID];
        $where[] = ['status', 'eq', 0];
        $where[] = ['default_or_not', 'eq', 1];
        $data = $memberBank->getBankOne($where);
        if (empty($data)) {
            return $this->asNewJson('getDefaultBankRet', 0, 'error', '暂未设置默认银行卡');
        }

        unset($data['cardholder']);
        unset($data['status']);
        unset($data['default_or_not']);
        $result[] = (string)$data['id'];
        $result[] = $data['bank'];
        $result[] = $data['bank_num'];

        return $this->asNewJson('getDefaultBankRet', 1, 'success', '获取成功', $result);
    }

    /**
     * @desc 获取会员信息
     * @author LiBin
     * @throws \Exception
     * @return \think\response\Json
     * @date 2019-04-08
     */
    public function getMemberInfo()
    {
        $id = UID;
        if (empty($id)) {
            return $this->asNewJson('getMemberRet', 0, 'error', '缺失用户ID');
        }

        $member = new MemberModel;
        $memberData = $member->getOneMember(['id' => UID], 'balance,hadsel,id_card,chn_name');
        $data[] = $memberData['balance'];//余额
        $data[] = $memberData['hadsel'];//彩金
        if (!empty($memberData['id_card']) && !empty($memberData['chn_name'])) {//实名认证 1已实名 0未实名
            $data[] = '1';
        } else {
            $data[] = '0';
        }

        return $this->asNewJson('getMemberRet', 1, 'success', '获取成功', $data);
    }

    /**
     * @desc   实名认证
     * @author LiBin
     * @return \think\response\Json
     * @throws \Exception
     * @date 2019-04-08
     */
    public function realName()
    {
        $data = $this->post;
        $validation = $this->validate($data, 'member.realName');
        if ($validation !== true) {
            return $this->asNewJson('realNameRet', 0, 'error', $validation);
        }

        // 判断是否已实名
        $memberData = MemberModel::getFieldsByWhere(['id' => UID], ['id_card', 'real_status']);
        if (!empty($memberData['id_card'])){
            if (!$memberData['real_status']){
                // 补写实名状态
                MemberModel::where('id', UID)->setField('real_status', 1);
            }

            return $this->asNewJson('realNameRet', 0, 'error', '您已实名认证,不能重复提交');
        }

        $createData = [
            'id_card' => $data['card'], // 身份证号
            'real_name' => $data['name'], // 真实姓名
            'real_status' => 1, // 是否实名认证 0：否  1：是 (暂时不需要审核, 呵呵哒)
        ];
        $member = new MemberModel();
        $result = $member->setMember(['id' => UID], $createData);
        if ($result === false) {
            return $this->asNewJson('realNameRet', 0, 'error', '提交失败');
        }

        return $this->asNewJson('realNameRet', 1, 'success', '提交成功');
    }

    /**
     * @desc 账单明细
     * @author LiBin
     * @throws \Exception
     * @return \think\response\Json
     * @date 2019-04-15
     * @api *
     */
    public function billingDetails()
    {
        $data = $this->get;
        $fundLog = new FundLog();
        // 需要分类讨论情况 1.代表足球 2.代表篮球 3.代表北京单关
        $fundData = $fundLog->getBillingDetails($data, 20, UID);

        return $this->asNewJson('billingRet', 1, 'success', '获取成功', $fundData);
    }

    /**
     * @desc 推广二维码和返佣列表
     * @author LiBin
     * @return \think\response\Json
     * @throws \Exception
     * @date 2019-04-17
     */
    public function myGenerailze()
    {
        $member = new MemberModel();
        // 获取会员的推广邀请码
        $code = $member->getOneMember(['id' => UID], ['agent_invite_code']);
        // 统计会员的推荐人数
        $number = $member->getRecUser(UID);
        $fundLog = new FundLog();
        // 统计投注佣金
        $countWhere[] = ['member_id', '=', UID];
        $countWhere[] = ['type', '=', 7]; // 投注返佣
        $money = $fundLog->getCountFundLog($countWhere, 'money');
        // 获取返佣记录
        $where[] = ['a.member_id', '=', UID];
        $where[] = ['a.type', '=', '7'];
        $data = ['c.username', 'c.chn_name', 'b.amount', 'a.money'];
        $rebateList = $fundLog->getRelaList($where, $data);
        $rebateList = $rebateList->toArray();
        $result['code_str'] = $code['agent_invite_code'];
        $result['number'] = (string)$number;
        $result['money'] = (string)$money;
        $result['url'] = Config::PictureHost . '?ic=' . $code['agent_invite_code'];
        if (empty($rebateList)) {
            $result['list'] = [];
        } else {
            foreach ($rebateList as $k => $v) {
                empty($v['chn_name']) ? $result['list'][$k]['username'] = (string)$v['username'] : $result['list'][$k]['username'] = $v['chn_name'];
                $result['list'][$k]['amount'] = (string)$v['amount'];
                $result['list'][$k]['money'] = (string)$v['money'];
            }
        }

        return $this->asNewJson('myGenerailzeRet', 1, 'success', '获取成功', $result);
    }

    /**
     * @desc 热门列表
     * @author LiBin
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-04-18
     */
    public function hotSearch()
    {
        $order = new Order();
        //获取热门搜索
        $data = $order->getPopularityOrder('0,5');
        return $this->asNewJson('hotSearchRet', 1, 'success', '获取成功', $data);
    }

    /**
     * @desc 获取历史搜索记录
     * @author LiBin
     * @throws \Exception
     * @return \think\response\Json
     * @date 2019-04-18
     */
    public function historyRecord()
    {
        if (empty(UID)) {
            return $this->asNewJson('historyRecordRet', 1, 'success', '获取成功', []);
        }
        //获取历史数据
        $history = new HistoryRecord();
        $data = $history->getHistoryInfo(['uid' => UID], ['id','content']);
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $data[$k]['id'] = $v['id'];
                $data[$k]['content'] = $v['content'];
            }
        }

        return $this->asNewJson('historyRecordRet', 1, 'success', '获取成功', $data);
    }

    /**
     * @desc 删除历史搜索记录
     * @throws \Exception
     * @return \think\response\Json
     */
    public function delHistory()
    {
        $data = $this->post;
        if (empty($data['id'])) {//是否传递id
            return $this->asNewJson('delHistory', 0, 'error', '参数错误');
        }
        $history = new HistoryRecord();
        $data = $history->delHistory(['id' => $data['id']]);
        if ($data === false) {
            return $this->asNewJson('delHistory', 0, 'error', '删除失败');
        }

        return $this->asNewJson('delHistory', 1, 'success', '删除成功');
    }

    /**
     * @desc  获取搜索的列表值
     * @author LiBin
     * @return \think\response\Json
     * @throws \Exception
     * @date 2019-04-18
     */
    public function checkList()
    {
        $data = $this->post;
        $where = [];
        if (empty($data['name'])) {//是否传递用户昵称
            return $this->asNewJson('checkListRet', 0, 'error', '请输入发起人昵称');
        }
        //记录搜索内容
        $historyRecord = new HistoryRecord();
        $hisoryData = $historyRecord->getHistoryInfo(['uid' => UID], ['content', 'id']);
        $check = true;
        if (!empty($hisoryData)) {
            foreach ($hisoryData as $k => $v) {
                if ($v['content'] == $data['name']) {
                    $check = false;
                }
            }

            if ($check) {
                if (count($hisoryData) >= 4) {//只记录4条历史记录
                    $historyRecord->setHistoryInfo(['id' => $hisoryData[0]['id']], ['content' => $data['name']]);
                } else {
                    $historyRecord->insertHistory(['uid' => UID, 'content' => $data['name'], 'create_at' => date('Y-m-d H:i:s')]);
                }
            }
        }

        $where[] = ['chn_name', 'like', '%' . $data['name'] . '%'];
        $member = new MemberModel();
        $memberID = $member->getMemberDataList($where, 'id as member_attention_id');
        $memberID = $memberID->toArray();
        $result = [];
        if (!empty($memberID)) {
            $order = new Order();
            $result = $order->getMyAttentionData($memberID);
            if (!empty($result)) {
                $attention = new Attention();
                foreach ($result as $k => $v) {
                    $data = $attention->getAttentionType(UID, $v['uid']);
                    $result[$k]['type'] = $data;
                }
            }
        }

        return $this->asNewJson('checkListRet', 1, 'success', '获取成功', $result);
    }

    /**
     * @desc 客服二维码
     * @author LiBin
     * @return \think\response\Json
     * @throws \Exception
     * @date 2019-04-18
     */
    public function service()
    {
        //获取客服二维码
        $service = new AdminService();
        $num = $service->getServiceOne(['status' => 1]);

        return $this->asNewJson('serviceRet', 1, 'success', '获取成功', $num);
    }

    /**
     * @desc 会员修改头像 接收beat64位数据
     * @author LiBin
     * @return \think\response\Json
     * @date 2019-04-19
     */
    public function setPicture()
    {
        //修改头像
        $data = $this->post;
        if (empty($data['file'])) {
            return $this->asNewJson('setPictureRet', 0, 'error', '头像不能为空');
        }

        $return = Helper::uploadImage('base64', 'members');
        if (!is_array($return)) {
            return $this->asNewJson('setPictureRet', 0, 'error', '上传头像失败，错误信息: ' . $return);
        }

        $head = $return['head'];
        $path = Helper::getCurrentHost() . $return['path'];
        $memberData['photo'] = $head;
        // 提交保存数据
        $member = new MemberModel();
        $result = $member->setMember(['id' => UID], $memberData);
        if (empty($result)) {
            return $this->asNewJson('setPictureRet', 0, 'error', '上传头像失败');
        }

        return $this->asNewJson('setPictureRet', 1, 'success', '上传成功', [$path]);
    }

    /**
     * 修改会员资料
     */
    public function setChnName()
    {
        $data = $this->post;
        if (empty($data['chn_name'])) {
            return $this->asNewJson('setPictureRet', 0, 'error', '昵称不能为空');
        }
        $memberData['chn_name'] = $data['chn_name'];
        // 提交保存数据
        $member = new MemberModel();
        $result = $member->setMember(['id' => UID], $memberData);
        if (empty($result)) {
            return $this->asNewJson('setPictureRet', 0, 'error', '修改资料失败');
        }

        return $this->asNewJson('setPictureRet', 1, 'success', '修改成功');
        
    }

    /**
     * @desc 清空搜索历史记录
     * @author LiBin
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     * @date 2019-04-19
     */
    public function clearHistory()
    {
        $history = new HistoryRecord();
        $data = $history->delHistory(['uid' => UID]);
        if ($data === false) {
            return $this->asNewJson('clearHistory', 0, 'error', '清空失败');
        }

        return $this->asNewJson('clearHistory', 1, 'success', '清空成功');
    }
}
