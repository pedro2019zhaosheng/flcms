<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/19
 * Time: 11:37
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\model;

use app\common\BaseModel;
use app\common\Helper;
use app\common\relation\Data;

/**
 * 竞彩足球赛事模型
 *
 * Class JczqMatch
 * @package app\common\model
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class JczqMatch extends BaseModel
{
    use Data;

    /**
     * 获取竞彩足球奖金指数
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
                'sp_spf', // 胜平负奖金指数
                'sp_rqspf', // 让球胜平负奖金指数
                'sp_bf', // 比分奖金指数
                'sp_jqs', // 进球数奖金指数
                'sp_bqc', // 半场奖金指数
                'sp_spf_var', // 胜平负奖金指数变化
                'sp_rqspf_var', // 让球胜平负奖金指数变化
                'sp_bf_var', // 比分奖金指数变化
                'sp_jqs_var', // 进球数奖金指数变化
                'sp_bqc_var', // 半场奖金指数变化
            ])
            ->find();
        $data['sp_spf'] = Helper::jsonDecode($data['sp_spf']);
        $data['sp_rqspf'] = Helper::jsonDecode($data['sp_rqspf']);
        $data['sp_bf'] = Helper::jsonDecode($data['sp_bf']);
        $data['sp_jqs'] = Helper::jsonDecode($data['sp_jqs']);
        $data['sp_bqc'] = Helper::jsonDecode($data['sp_bqc']);
        $data['sp_spf_var'] = $this->odds($data['sp_spf_var']);
        $data['sp_rqspf_var'] = $this->odds($data['sp_rqspf_var']);
        $data['sp_bf_var'] = $this->odds($data['sp_bf_var']);
        $data['sp_jqs_var'] = $this->odds($data['sp_jqs_var']);
        $data['sp_bqc_var'] = $this->odds($data['sp_bqc_var']);

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
                    case "sp_spf"://胜平负
                        foreach ($bet as $i => $va) {
                            $str = $str . $jczqMatchData['sp_spf'][$va] . '|';
                            $gameIndex[$v['mnum']][$value['ptype']][$va] = $jczqMatchData['sp_spf'][$va];
                        }

                        $value['i'] = trim($str, '|');//重新赋值奖金指数
                        //$gameIndex[$v['mnum']][$value['ptype']] = trim($str, '|');
                        break;
                    case "sp_rqspf"://让球胜平负
                        foreach ($bet as $va) {
                            $str = $str . $jczqMatchData['sp_rqspf'][$va] . '|';
                            $gameIndex[$v['mnum']][$value['ptype']][$va] = $jczqMatchData['sp_rqspf'][$va];
                        }
                        $value['i'] = trim($str, '|');//重新赋值奖金指数
                        //$gameIndex[$v['mnum']][$value['ptype']] = trim($str, '|');
                        break;
                    case "sp_bf"://比分
                        foreach ($bet as $va) {
                            $str = $str . $jczqMatchData['sp_bf']['dat'][$va] . '|';
                            $gameIndex[$v['mnum']][$value['ptype']][$va] = $jczqMatchData['sp_bf']['dat'][$va];
                        }
                        $value['i'] = trim($str, '|');//重新赋值奖金指数
                        //$gameIndex[$v['mnum']][$value['ptype']] = trim($str, '|');
                        break;
                    case "sp_jqs"://进球数
                        foreach ($bet as $va) {
                            $str = $str . $jczqMatchData['sp_jqs']['dat'][$va] . '|';
                            $gameIndex[$v['mnum']][$value['ptype']][$va] = $jczqMatchData['sp_jqs']['dat'][$va];
                        }
                        $value['i'] = trim($str, '|');//重新赋值奖金指数
                        //$gameIndex[$v['mnum']][$value['ptype']] = trim($str, '|');
                        break;
                    case "sp_bqc"://半全场
                        //半全场$jczqMatchData参数存储异常:D L 需要考虑跟单佣金问题
                        foreach ($bet as $va) {
                            $str = $str . $jczqMatchData['sp_bqc']['dat'][$va] . '|';
                            $gameIndex[$v['mnum']][$value['ptype']][$va] = $jczqMatchData['sp_bqc']['dat'][$va];
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
}