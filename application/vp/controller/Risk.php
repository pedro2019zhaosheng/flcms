<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/18
 * Time: 14:14
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\controller;

use app\common\Config;
use app\common\model\AdminConfig;
use app\common\model\PreDraw;
use app\common\VpController;
use think\response\Json;

/**
 * 风险控制
 *
 * Class Risk
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Risk extends VpController
{
    /**
     * 获取数字彩自动风控配置列表
     *
     * @return Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function riskList()
    {
        $list = [];
        // 澳彩
        $aoCaiVal = AdminConfig::conf(Config::AO_CAI, null, 0);
        array_push($list, ['name' => '澳彩', 'value' => $aoCaiVal, 'var' => Config::AO_CAI]);
        // 葡彩
        $puCaiVal = AdminConfig::conf(Config::PU_CAI, null, 0);
        array_push($list, ['name' => '葡彩', 'value' => $puCaiVal, 'var' => Config::PU_CAI]);

        return $this->asJson(1, 'success', '请求成功', $list);
    }

    /**
     * 自动风控配置
     *
     * @param $var // 变量
     * @param $value // 值
     * @return Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function inConfig($var, $value)
    {
        switch ($var) {
            case Config::AO_CAI: // 澳彩
                $mark = '澳彩自动风控配置标识';
                break;
            case Config::PU_CAI: // 普彩
                $mark = '葡彩自动风控配置标识';
                break;
            default:
                $mark = '';
        }

        $result = AdminConfig::conf($var, $value, $mark);
        if ($result) {
            return $this->asJson(1, 'success', '配置成功');
        }

        return $this->asJson(0, 'error', '配置失败');
    }

    /**
     * 手动风控列表
     *
     * @return Json
     * @throws \Exception
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function handList()
    {
        $model = new PreDraw;
        $list = $model->handList();

        return $this->asJson(1, 'success', '请求成功', $list);
    }

    /**
     * 写入预设开奖结果
     *
     * @return Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function setCode()
    {
        $post = $this->post;
        $validation = $this->validate($post, 'risk.handInsert');
        if ($validation !== true) {
            return $this->asJson(0, 'error', $validation);
        }

        $model = new PreDraw;
        $result = $model->setPreCode($post['number'], $post['ctype'], $post['openCode']);
        if ($result === true) {
            return $this->asJson(1, 'success', '操作成功');
        }

        return $this->asJson(0, 'error', $result);
    }

    /**
     * 获取数字彩预设开奖号码列表
     *
     * @throws \Exception
     * @return Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function preList()
    {
        $model = new PreDraw;
        $pagination = $model->getPreList($this->get);
        $list = $pagination->toArray();
        $page = $pagination->render();

        return $this->asJson(1, 'success', '请求成功', ['list' => $list, 'page' => $page]);
    }
}