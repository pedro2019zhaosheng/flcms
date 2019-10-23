<?php
/**
 * Created by Visual Studio.
 * User: Administrator
 * Date: 2019/3/30
 * Time: 11:19
 * Author hutao.
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\model\AdminBank;
use think\db;

/**
 * 银行卡模型.
 *
 * Class MemberBank
 *
 * @author hutao
 */
class MemberBank extends BaseModel
{
    /**
     * 获取银行卡列表.
     *
     * @param $uid 用户编号
     *
     * @author hutao
     */
    public function getBankCardList($uid)
    {
        $data = self::where(['member_id' => $uid, 'status' => 0])->field('id,bank,bank_num,default_or_not')->select();

        return sizeof($data) ? $data : [];
    }
    /**
     * 获取银行卡列表.
     *
     * @param $uid 用户编号
     *
     * @author hutao
     */
    public function getBankCardListData($uid,$data)
    {
        $data = self::where(['member_id' => $uid, 'status' => 0])->field($data)->select();
        return sizeof($data) ? $data : [];
    }

    /**
     * 设置默认银行卡.
     *
     * @throws \Exception
     * @return string|true
     * @param $uid     //用户编号
     * @param $bank_id //银行卡id
     * @author hutao
     * @updateBy CleverStone
     */
    public function setBankDefaultById($uid, $bank_id)
    {
        $card = self::where(['member_id' => $uid, 'id' => $bank_id])->field('default_or_not,status')->find();
        if (empty($card)) {
            return '系统错误';
        }

        if ((int) $card['status'] === 1) {
            return '此卡已被禁用,设置无效';
        }

        $default_or_not = (int) $card['default_or_not'] === 1 ? 0 : 1;
        try{
            Db::startTrans();
            // 如果设置状态为默认, 先设置该会员所有银行卡为非默认
            if($default_or_not === 1){
                self::where(['member_id' => $uid])->update(['default_or_not' => 0]);
                // 设置指定银行卡为默认/非默认
                self::where(['member_id' => $uid, 'id' => $bank_id])->update(['default_or_not' => $default_or_not]);
            }

            Db::commit();
            return true;
        }catch (\Exception $e){
            Db::rollback();
            return $e->getMessage();
        }
    }

    /**
     * 新增银行卡
     *
     * @param member_id; 用户编号
     * @param cardholder; 持卡人姓名
     * @param bank; 开户行
     * @param bank_num; 银行卡号
     * @param mobile; 手机号
     * @param code; 验证码
     *
     * 编辑银行卡
     * @param bank_id; 银行卡id编号
     * @param member_id; 用户编号
     * @param cardholder; 持卡人姓名
     * @param bank; 开户行
     * @param bank_num; 银行卡号
     * @param mobile; 手机号
     * @param default_or_not; 是否默认提现卡 状态值 0是不默认 1 默认
     *
     * @author hutao
     * @return boolean|integer
     */
    public function addBankCard($data, $isUpdate)
    {
        return self::quickCreate($data, $isUpdate);
    }

    /**
     * 账户信息 - 解绑银行卡.
     *
     * @param bank_id; 银行卡id编号
     * @param member_id; 用户编号
     *
     * @autuer hutao
     * @date 2019-03-29
     */
    public function removeCard($bank_id)
    {
        return self::where(['id'=>$bank_id])->delete();
    }

    /**
     * 账户信息 - 输入的银行卡号和归属银行是否正确.
     *
     * @param bank; 开户行
     * @param bank_num; 银行卡号
     *
     * @autuer hutao
     * @date 2019-04-01
     */
    public function checkCard($bank, $bank_num)
    {
        $card = file_get_contents("https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?cardNo={$bank_num}&cardBinCheck=true");
        $bankCard = json_decode($card);
        //获取银行列表
        $adminBank = new AdminBank();
        $adminBankData = $adminBank ->getBankList(['status'=>1],['name','code']);
        $bankArr = [];
        foreach($adminBankData as $k=>$v){
            $bankArr[$v['code']] = $v['name'];
        }
        if (isset($bankCard->bank) && isset($bankArr[$bankCard->bank])) {
            $bc = $bankArr[$bankCard->bank];
            if ($bc != $bank) {
                return '输入卡号与所选归属银行不一致';
            }
        } else {
            return '卡号不存在';
        }

        return true;
    }
    /**
     * 获取单条银行卡记录.
     *
     * @param $where
     *
     * @author libin
     */
    public function getBankOne($where)
    {
        $data = self::where($where)->field('id,bank,bank_num,cardholder,status,default_or_not')->find();
        return $data;
    }
    /**
     * 设置默认银行卡.
     *
     * @param $bank_id 银行卡id
     *
     * @author hutao
     */
    public function setBank($bank_id,$data)
    {
        $card = self::where(['id' => $bank_id])->field('default_or_not,status')->find();
        if (empty($card)) {
            return ['setDefRet', 0, 'error', '无效的编号'];
        }
        $rdata = self::where(['id' => $bank_id, 'status' => 0])->update($data);

        return $rdata;
    }
}
