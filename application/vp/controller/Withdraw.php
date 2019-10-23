<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/23
 * Time: 12:01
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\controller;

use app\common\VpController;
use app\common\model\Member;

/**
 * 代提现
 *
 * Class Withdraw
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Withdraw extends VpController
{
    /**
     * 代提现会员详情
     *
     * @param $amountNum // 会员账号
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function member($amountNum)
    {
        $model = new Member;
        $data = $model->getMemberRechargeList($amountNum);

        return $this->asJson(1, 'success', '请求成功', $data);
    }

    /**
     * 提现
     *
     * @param $id // 会员ID
     * @return \think\response\Json
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function withdraw($id)
    {
        $post = $this->post;
        $amount = (float)$post['amount'];

        if ($amount >= 5000000) {
            return $this->asJson(0, 'error', '您单次提现的金额过大, 请批量提现!');
        }

        $model = new Member;
        $result = $model->memberWithdraw($id, $amount);
        if ($result === true) {
            return $this->asJson(1, 'success', '提现成功');
        }

        return $this->asJson(0, 'error', $result);
    }
}