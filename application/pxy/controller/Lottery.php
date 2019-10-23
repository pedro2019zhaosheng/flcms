<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/11
 * Time: 18:12
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\pxy\controller;

use app\common\PxyController;
use think\response\Json;

/**
 * 代理商彩种控制器
 *
 * Class Lottery
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Lottery extends PxyController
{
    /**
     * 获取所有彩种(['code' => 'name'])
     *
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function all()
    {
        $model = new \app\common\model\Lottery();
        $all = $model->getLottery();
        $data = $all->toArray();
        $endData = [];
        if (!empty($data)) {
            $re = array_column($data, 'name', 'code');
            foreach ($re as $key => $item) {
                array_push($endData, ['code' => $key, 'name' => $item]);
            }
        }

        return $this->asJson(1, 'success', '请求成功', $endData);
    }

    /**
     * 获取所有彩种(['id' => 'name'])
     *
     * @return Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function all2()
    {
        $model = new \app\common\model\Lottery();
        $all = $model->getLottery();
        $data = $all->toArray();
        $endData = [];
        if (!empty($data)) {
            $re = array_column($data, 'name', 'id');
            foreach ($re as $key => $item) {
                array_push($endData, ['id' => $key, 'name' => $item]);
            }
        }

        return $this->asJson(1, 'success', '请求成功', $endData);
    }
}