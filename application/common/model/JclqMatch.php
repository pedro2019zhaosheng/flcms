<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/23
 * Time: 19:56
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;
use app\common\relation\Data;

/**
 * 竞彩篮球奖金指数模型
 *
 * Class JclqMatch
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class JclqMatch extends BaseModel
{
    use Data;

    /**
     * 获取竞彩蓝求奖金指数
     *
     * @param $matchId // 赛事编号
     * @return array|null|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getDetail($matchId)
    {
        $data = self::where('match_num', $matchId)
            ->field([
                'sp_sf', // 胜负奖金指数
                'sp_rfsf', // 让分胜负奖金指数
                'sp_sfc', // 胜负差奖金指数
                'sp_dxf', // 大小分奖金指数
                'sp_sf_var', // 胜负奖金指数变化
                'sp_rfsf_var', // 让分胜负奖金指数变化
                'sp_sfc_var', // 胜负差奖金指数变化
                'sp_dxf_var', // 大小分奖金指数变化
            ])
            ->find();
        $data['sp_sf'] = Helper::jsonDecode($data['sp_sf']);
        $data['sp_rfsf'] = Helper::jsonDecode($data['sp_rfsf']);
        $data['sp_sfc'] = Helper::jsonDecode($data['sp_sfc']);
        $data['sp_dxf'] = Helper::jsonDecode($data['sp_dxf']);
        $data['sp_sf_var'] = $this->odds($data['sp_sf_var']);
        $data['sp_rfsf_var'] = $this->odds($data['sp_rfsf_var']);
        $data['sp_sfc_var'] = $this->odds($data['sp_sfc_var']);
        $data['sp_dxf_var'] = $this->odds($data['sp_dxf_var']);

        return $data;
    }

    /**
     * @desc 处理投注项
     * @auther LiBin
     * @param $bet_content
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @date 2019-04-24
     */
    public function handingBetas($bet_content)
    {
        //处理投注项
        $betContentBody = Helper::jsonDecode($bet_content);//获取跟单订单的投注项
        $gameIndex = [];//最新的赛事奖金指数容器
        foreach ($betContentBody as &$v) {//循环跟单订单的投注项
            $jczqMatchData = self::getDetail($v['mnum']);//获取当前赛事的奖金指数
            foreach($v['muti'] as &$value) {
                $bet = explode('|', $value['bet']);//投注玩法转数组
                $str = '';
                $ptype = 'sp_' . $value['ptype'];//玩法标识
                switch ($ptype) {
                    case "sp_sf"://胜负
                        foreach ($bet as $i => $va) {
                            $str = $str . $jczqMatchData['sp_sf'][$va] . '|';
                            $gameIndex[$v['mnum']][$value['ptype']][$va] = $jczqMatchData['sp_sf'][$va];
                        }
                        $value['i'] = trim($str, '|');//重新赋值奖金指数
                        //$gameIndex[$v['mnum']][$value['ptype']] = trim($str, '|');
                        break;
                    case "sp_rfsf"://让分胜负
                        foreach ($bet as $va) {
                            $str = $str . $jczqMatchData['sp_rfsf'][$va] . '|';
                            $gameIndex[$v['mnum']][$value['ptype']][$va] = $jczqMatchData['sp_rfsf'][$va];
                        }
                        $value['i'] = trim($str, '|');//重新赋值奖金指数
                        //$gameIndex[$v['mnum']][$value['ptype']] = trim($str, '|');
                        break;
                    case "sp_dxf"://大小分
                        foreach ($bet as $va) {
                            $str = $str . $jczqMatchData['sp_dxf'][$va] . '|';
                            $gameIndex[$v['mnum']][$value['ptype']][$va] = $jczqMatchData['sp_dxf'][$va];
                        }
                        $value['i'] = trim($str, '|');//重新赋值奖金指数
                        //$gameIndex[$v['mnum']][$value['ptype']] = trim($str, '|');
                        break;
                    case "sp_zsfc"://主胜分差
                        $zsfcData['1-5'] = $jczqMatchData['sp_sfc']['dat']['home'][0];
                        $zsfcData['6-10'] = $jczqMatchData['sp_sfc']['dat']['home'][1];
                        $zsfcData['11-15'] = $jczqMatchData['sp_sfc']['dat']['home'][2];
                        $zsfcData['16-20'] = $jczqMatchData['sp_sfc']['dat']['home'][3];
                        $zsfcData['21-25'] = $jczqMatchData['sp_sfc']['dat']['home'][4];
                        $zsfcData['26+'] =  $jczqMatchData['sp_sfc']['dat']['home'][5];
                        foreach ($bet as $va) {
                            $str = $str . $zsfcData[$va] . '|';
                            $gameIndex[$v['mnum']][$value['ptype']][$va] = $zsfcData[$va];
                        }
                        $value['i'] = trim($str, '|');//重新赋值奖金指数
                        //$gameIndex[$v['mnum']][$value['ptype']] = trim($str, '|');
                        break;
                    case "sp_ksfc"://客胜分差
                        $ksfcData['1-5'] = $jczqMatchData['sp_sfc']['dat']['away'][0];
                        $ksfcData['6-10'] = $jczqMatchData['sp_sfc']['dat']['away'][1];
                        $ksfcData['11-15'] = $jczqMatchData['sp_sfc']['dat']['away'][2];
                        $ksfcData['16-20'] = $jczqMatchData['sp_sfc']['dat']['away'][3];
                        $ksfcData['21-25'] = $jczqMatchData['sp_sfc']['dat']['away'][4];
                        $ksfcData['26+'] =  $jczqMatchData['sp_sfc']['dat']['away'][5];
                        foreach ($bet as $va) {
                            $str = $str . $ksfcData[$va] . '|';
                            $gameIndex[$v['mnum']][$value['ptype']][$va] = $ksfcData[$va];
                        }
                        $value['i'] = trim($str, '|');//重新赋值奖金指数
                        //$gameIndex[$v['mnum']][$value['ptype']] = trim($str, '|');
                        break;
                }
            }
        }

        $rdata['bet_content'] = $betContentBody;
        $rdata['gameIndex'] = $gameIndex;
        return $rdata;
    }

    /**
     * @desc 获取大小分的预设分数
     * @auther LiBin
     * @param $matchNum
     * @return int
     * @date 2019-04-25
     */
    public static function getDxf($matchNum)
    {
        $dxf = self::where('match_num', $matchNum)->value('sp_dxf');
        $dxf = Helper::jsonDecode($dxf);
        return $dxf['T'] === '' ? 0 : (int)$dxf['T'];
    }
}