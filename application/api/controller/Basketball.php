<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/22
 * Time: 10:28
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\api\controller;

use app\common\model\JclqBase;
use app\common\model\Lottery;
use app\common\relation\Data2;
use app\common\RestController;
use app\common\Config;
use app\common\Helper;

/**
 * 篮彩控制器
 *
 * Class Login
 * @package app\api\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Basketball extends RestController
{
    use Data2;

    /**
     * authentication彩种列表
     *
     * @param array $disableAuthAction
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    protected function init(array $disableAuthAction = [])
    {
        $disableAuthAction = ['basketList', 'searchBasket', 'basketDg'];
        parent::init($disableAuthAction); // TODO: Change the autogenerated stub
    }

    /**
     * @desc 篮彩列表
     * @author LiBin
     * @return \think\response\Json
     * @throws \Exception
     * @date 2019年4月24日
     */
    public function basketList()
    {
        $get = $this->get;
        $model = new JclqBase();
        $findData = [
            'a.match_num',//比赛编号
            'a.league_name',//联赛名称
            'a.host_name',//主队名称
            'a.guest_name',//客队名称
            'a.cutoff_time',//手动截止时间
            'a.sys_cutoff_time',//系统截止时间
            'a.jc_num',//竞彩编号
            'b.sp_sf',//胜平负奖金指数
            'b.sp_rfsf',//让分胜负奖金指数
            'b.sp_sfc',//胜分差奖金指数
            'b.sp_dxf',//大小分奖金指数
        ];
        $where[] = ['sale_status', '=', 1];//竞彩出售中
        if (!empty($get['date'])) {
            if ($get['date'] == 1) {//今天
                $date = date('Y-m-d');
                $where[] = ['jc_date', 'between', [$date . ' 00:00:00', $date . ' 23:59:59']];
            }
            if ($get['date'] == 2) {//明天
                $date = date("Y-m-d", strtotime("+1 day"));
                $where[] = ['jc_date', 'between', [$date . ' 00:00:00', $date . ' 23:59:59']];
            }
        }

        $basketData = $model->getFootballData($where, $findData);
        if (!count($basketData)) {
            return $this->asNewJson('basketListRet', 1, 'success', '获取成功', []);
        }

        foreach ($basketData as $k => $v) {
            if (empty($v['cutoff_time'])) {
                $basketData[$k]['cutoff_time'] = $v['sys_cutoff_time'];
            }

            $basketData[$k]['cutoff_time'] = date('H:i', strtotime($basketData[$k]['cutoff_time']));
            $sp_sf = Helper::jsonDecode($v['sp_sf']);//胜平负奖金指数
            $sp_rfsf = Helper::jsonDecode($v['sp_rfsf']);//让分胜负奖金指数
            $sp_sfc = Helper::jsonDecode($v['sp_sfc']);//胜分差奖金指数
            $sp_dxf = Helper::jsonDecode($v['sp_dxf']);//大小分奖金指数
            $thoBasket = $this->basketSort($sp_sf, $sp_rfsf, $sp_sfc, $sp_dxf);
            $basketData[$k]['rfs'] = $thoBasket['rfs'];
            $basketData[$k]['oddsOne'] = $thoBasket[0];
            $basketData[$k]['oddsTwo'] = $thoBasket[1];
            $basketData[$k]['ys'] = $thoBasket['ys'];

            unset($basketData[$k]['sys_cutoff_time'],
                $basketData[$k]['sp_sf'],
                $basketData[$k]['sp_rfsf'],
                $basketData[$k]['sp_sfc'],
                $basketData[$k]['sp_dxf']
            );
        }

        //获取蓝彩ID
        $lottery = new Lottery();
        $idData = $lottery->getOneLottery(['code' => config::LC_CODE], 'id');
        $funcName = 'basketListRet';
        $code = 1;
        $status = 'success';
        $msg = '获取成功';
        $id = (string)$idData['id'];
        $args = $basketData;

        return json(compact('funcName', 'code', 'status', 'msg', 'id', 'args'));
    }

    /**
     * @desc 赛事检索
     * @auther LiBin
     * @throws \Exception
     * @return \think\response\Json
     * @date 2019年4月27日09:41:08
     */
    public function searchBasket()
    {
        $get = $this->get;
        $model = new JclqBase();
        $findData = [
            'match_num',//比赛编号
        ];
        if (!empty($get['date'])) {
            if ($get['date'] == 1) {//今天
                $date = date('Y-m-d');
                $where[] = ['jc_date', 'between', [$date . ' 00:00:00', $date . ' 23:59:59']];
            }
            if ($get['date'] == 2) {//明天
                $date = date("Y-m-d", strtotime("+1 day"));
                $where[] = ['jc_date', 'between', [$date . ' 00:00:00', $date . ' 23:59:59']];
            }
        } else {
            $where[] = ['sale_status', '=', 1];//竞彩出售中
        }

        $footData = $model->getBase($where, $findData);
        if (!count($footData)) {
            return $this->asNewJson('searchBasketRet', 1, 'success', '获取成功');
        }

        $list = [];
        foreach ($footData as $k => $v) {
            $list[] = $v['match_num'];
        }

        return $this->asNewJson('searchBasketRet', 1, 'success', '获取成功', $list);
    }

    /**
     * @desc 单关列表
     * @auther LiBin
     * @return mixed
     * @throws \Exception
     * @date 2019-03-27
     */
    public function basketDg()
    {
        $model = new JclqBase();
        $findData = [
            'a.match_num',//比赛编号
            'a.league_name',//联赛名称
            'a.host_name',//主队名称
            'a.guest_name',//客队名称
            'a.host_icon', //主队头像
            'a.guest_icon', //客队头像
            'a.cutoff_time',//手动截止时间
            'a.sys_cutoff_time',//系统截止时间
            'a.jc_num',//竞彩编号
            'b.sp_sfc',//胜分差奖金指数
            'a.rqs as rfs',//让分数
        ];
        $where[] = ['jc_date', '<', date('Y-m-d H:i:s')];//暂定竞彩时间小于当前时间
        $where[] = ['sale_status', '=', 1];//竞彩出售中
        $footData = $model->getFootballData($where, $findData);
        //单关的方式 zsfc(主胜) ,ksfc(客胜)
        $rdata = $this->baskSortDg($footData);

        return $this->asNewJson('basketDgRet', 1, 'success', '获取成功', $rdata);
    }
}