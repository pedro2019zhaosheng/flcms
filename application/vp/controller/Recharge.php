<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4
 * Time: 17:42
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\controller;

use app\common\VpController;
use app\common\model\Member;

/**
 * 代理充值控制器
 *
 * Class Recharge
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Recharge extends VpController
{
    /**
     * 获取要充值的会员列表
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
     * 充值
     *
     * @param $id // 会员ID
     * @return \think\response\Json
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function recharge($id)
    {
        $post = $this->post;
        $amount = (float)$post['amount'];

        if ($amount >= 5000000) {
            return $this->asJson(0, 'error', '您单次充值的金额过大, 请批量充值!');
        }

        $model = new Member;
        $result = $model->memberRecharge($id, $amount);
        if ($result === true) {
            return $this->asJson(1, 'success', '充值成功');
        }

        return $this->asJson(0, 'error', $result);
    }
}