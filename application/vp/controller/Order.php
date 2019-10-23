<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/21
 * Time: 15:45
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\controller;

use app\common\model\OrderNum;
use app\common\model\PlOpen;
use app\common\VpController;
use app\common\model\Order as OrderModel;
use think\response\Json;

/**
 * 订单控制器
 *
 * Class Order
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Order extends VpController
{
    /**
     * 体彩手动开奖, 获取赛事名单
     *
     * @param $matchNum // 比赛编号
     * @param $code // 彩种代码
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function bingo($matchNum, $code)
    {
        $list = OrderModel::getBingoOrderList($matchNum, $code);
        return $this->asJson(1, 'success', '请求成功', $list);
    }

    /**
     * 体彩手动开奖
     *
     * @return Json
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function handBingo()
    {
        set_time_limit(0);
        ini_set('memory_limit', '500M');

        $post = $this->post;
        $validation = $this->validate($post, 'order.handDraw');
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $model = new OrderModel;
        $result = $model->setDrawByOrderDetailIds($post['code'], $post['matchNum']);

        if ($result === true) {
            return $this->asJson(1, 'success', '开奖成功');
        }

        return $this->asJson(0, 'error', $result);
    }

    /**
     * 体彩导出中奖订单
     *
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function export()
    {
        $get = $this->get;
        $model = new OrderModel();
        $model->exportBingoOrder($get);
    }

    /**
     * 手动派奖列表
     *
     * @return Json
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function bingoPage()
    {
        $get = $this->get;
        $model = new OrderModel();
        $pagination = $model->getBingoOrderPage($get);
        $list = $pagination->toArray();
        $page = $pagination->render();

        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }

    /**
     * 总后台派发奖金
     *
     * @param //$data // 派奖数据 例如: "1@1,1@2,1@3"
     * @return Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function sendPrize()
    {
        $post = $this->post;
        if (!isset($post['data'])) {
            return $this->asJson(0, 'error', '请选择您要派奖的订单!');
        }

        $model = new OrderModel;
        $result = $model->sendPrize($post['data']);

        if ($result === true) {
            return $this->asJson(1, 'success', '派奖成功');
        }

        return $this->asJson(0, 'error', $result);
    }

    /**
     * 数字彩获取该期所有订单
     *
     * @return Json
     * @author CleverStone
     * @throws \Exception
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function numBingo()
    {
        $model = new OrderNum;
        $list = $model->getNumBingoList($this->get);

        return $this->asJson(1, 'success', '请求成功', $list);
    }

    /**
     * 数字彩手动开奖
     *
     * @return Json
     * @author CleverStone
     * @throws \Exception
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function numDraw()
    {
        $model = new PlOpen;
        $result = $model->handOpenDraw($this->get);
        if ($result === true) {
            return $this->asJson(1, 'success', '开奖成功');
        }

        return $this->asJson(0, 'error', $result);
    }
}