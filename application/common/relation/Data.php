<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/16
 * Time: 15:15
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\relation;

use app\common\Config;
use app\common\Helper;
use app\common\model\JcdcBase;
use app\common\model\JczqBase;
use app\common\model\JclqBase;
use app\common\model\JclqMatch;

/**
 * 彩种数据处理(后台)
 *
 * "-1-a": "200.0",负其他
 * "-1-d": "500.0",平其他
 * "-1-h": "80.00" 胜其他
 *
 * Trait Data
 * @package app\common\relation
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
trait Data
{
    /**
     * 竞彩奖金指数变化数据, 按时间降序 (竞彩列表详情展示)
     *
     * json数据包处理，并返回数据包中ut时间戳最大的元素
     * 例如：
     *  json字符串(数据源)
     *   [
     *      {"H":"-1","W":"4.75","D":"3.8","L":"1.46","ut":1552634785000},
     *      {"H":"-1","W":"4.9","D":"3.85","L":"1.44","ut":1552633086000},
     *      {"H":"-1","W":"4.75","D":"3.8","L":"1.46","ut":1552629471000},
     *      {"H":"-1","W":"4.9","D":"3.85","L":"1.44","ut":1552628295000},
     *      {"H":"-1","W":"5.15","D":"3.85","L":"1.42","ut":1552545782000}
     *   ]
     *
     *  排序后数组：
     *  Array (
     *    [0] => Array ( [H] => -1 [W] => 4.75 [D] => 3.8 [L] => 1.46 [ut] => 1552634785000 )
     *    [1] => Array ( [H] => -1 [W] => 4.9 [D] => 3.85 [L] => 1.44 [ut] => 1552633086000 )
     *    [2] => Array ( [H] => -1 [W] => 4.75 [D] => 3.8 [L] => 1.46 [ut] => 1552629471000 )
     *    [3] => Array ( [H] => -1 [W] => 4.9 [D] => 3.85 [L] => 1.44 [ut] => 1552628295000 )
     *    [4] => Array ( [H] => -1 [W] => 5.15 [D] => 3.85 [L] => 1.42 [ut] => 1552545782000 )
     *  )
     *
     * 最终返回：
     *  Array ( [H] => -1 [W] => 4.75 [D] => 3.8 [L] => 1.46 [ut] => 1552634785000 )
     *
     * @param $jsonData // 数据表格json数据包
     * @param boolean $getTop // 是否获取排序后第一个内部数组
     * @return array|boolean
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function odds($jsonData, $getTop = false)
    {
        $data = Helper::jsonDecode($jsonData);

        return $this->utSort($data ?: [], $getTop);
    }

    /**
     * 二维数组以内部数组指定下标元素冒泡降序，并返回排序后第一个的内部数组
     *
     * @param array $data // 二维数组
     * @param boolean $getTop // 是否获取排序后第一个内部数组
     * @param string $sortIndex // 指定的排序下标,变化时间
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function utSort(array $data, $getTop = true, $sortIndex = 'ut')
    {
        $container = [];
        $count = count($data);
        for ($i = 1; $i < $count; $i++) {
            for ($n = 0; $n < $count - $i; $n++) {
                if ($data[$n][$sortIndex] < $data[$n + 1][$sortIndex]) {
                    $container[$n] = $data[$n];
                    $data[$n] = $data[$n + 1];
                    $data[$n + 1] = $container[$n];
                }
            }
        }

        if ($getTop) {
            reset($data);
            return current($data);
        }

        return $data;
    }

    /**
     * 比赛结果转换为竞彩结果
     *
     * `return
     *
     * ['spf' => 'W', 'rqspf' => 'D', 'jqs' => 's1', 'bqc' => 'ww', 'bf' => '0100']
     *
     * @param string $code // 竞彩代码
     * @param array $data // 比赛赛果数据
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function matchResult2JcResult($code, array $data)
    {
        switch ((string)$code) {
            case Config::ZC_CODE: // 足彩
                $halfScore = $data['half_score']; // 半场得分
                $totalScore = $data['normal_score']; // 全场得分(不含加时赛)
                $jcRe = [
                    'spf' => '',
                    'rqspf' => '',
                    'jqs' => '',
                    'bqc' => '',
                    'bf' => '',
                ];
                if (!empty($halfScore)) {
                    $halfArr = explode('-', $halfScore);
                    $hostHalfScore = (int)current($halfArr);
                    $guestHalfScore = (int)end($halfArr);
                    if ($hostHalfScore > $guestHalfScore) {
                        $bqcHalf = 'w'; // 半场主胜
                    } elseif ($hostHalfScore < $guestHalfScore) {
                        $bqcHalf = 'l'; // 半场主负
                    } else {
                        $bqcHalf = 'd'; // 半场平
                    }
                }

                if (!empty($totalScore)) {
                    $totalArr = explode('-', $totalScore);
                    $hostScore = current($totalArr);
                    $guestScore = end($totalArr);
                    // 胜平负
                    if ($hostScore > $guestScore) {
                        $spf = 'W';
                        $bqcWhole = 'w';
                    } elseif ($hostScore < $guestScore) {
                        $spf = 'L';
                        $bqcWhole = 'l';
                    } else {
                        $spf = 'D';
                        $bqcWhole = 'd';
                    }

                    $jcRe['spf'] = $spf;

                    // 让球胜平负
                    $rqs = JczqBase::getRqs($data['match_num']);
                    $rqHostScore = $hostScore + $rqs;
                    if ($rqHostScore > $guestScore) {
                        $rqSpf = 'W';
                    } elseif ($rqHostScore < $guestScore) {
                        $rqSpf = 'L';
                    } else {
                        $rqSpf = 'D';
                    }

                    $jcRe['rqspf'] = $rqSpf;

                    // 总进球数
                    $scoreCount = (int)$hostScore + (int)$guestScore;
                    $scoreSizeStr = 's' . $scoreCount;
                    if ($scoreCount >= 7) {
                        $scoreSizeStr = 's7';
                    }
                    $jcRe['jqs'] = $scoreSizeStr;

                    // 全场比分
                    if (
                        (int)$hostScore === (int)$guestScore
                        && (int)$hostScore > 3
                        && (int)$guestScore > 3
                    ) {
                        $jcRe['bf'] = '-1-d'; // 平其他
                    } elseif (
                        (int)$hostScore > (int)$guestScore
                        && (
                            (int)$hostScore > 5
                            || (int)$guestScore > 2
                        )
                    ) {
                        $jcRe['bf'] = '-1-h'; // 胜其他
                    } elseif (
                        (int)$hostScore < (int)$guestScore
                        && (
                            (int)$hostScore > 2
                            || (int)$guestScore > 5
                        )
                    ) {
                        $jcRe['bf'] = '-1-a'; // 负其他
                    } else {
                        $jcRe['bf'] = '0' . $hostScore . '0' . $guestScore;
                    }
                }

                if (!empty($halfScore) && !empty($totalScore)) {
                    // 半全场胜平负
                    $jcRe['bqc'] = $bqcHalf . $bqcWhole;
                }

                return $jcRe;
            case Config::BJ_CODE: // 北京单场
                $halfScore = $data['half_score']; // 半场得分
                $totalScore = $data['normal_score']; // 全场得分(不含加时赛)
                $jcRe = [
                    'spf' => '',
                    'rqspf' => '',
                    'jqs' => '',
                    'bqc' => '',
                    'bf' => '',
                ];
                if (!empty($halfScore)) {
                    $halfArr = explode('-', $halfScore);
                    $hostHalfScore = (int)current($halfArr);
                    $guestHalfScore = (int)end($halfArr);
                    if ($hostHalfScore > $guestHalfScore) {
                        $bqcHalf = 'w'; // 半场主胜
                    } elseif ($hostHalfScore < $guestHalfScore) {
                        $bqcHalf = 'l'; // 半场主负
                    } else {
                        $bqcHalf = 'd'; // 半场平
                    }
                }

                if (!empty($totalScore)) {
                    $totalArr = explode('-', $totalScore);
                    $hostScore = current($totalArr);
                    $guestScore = end($totalArr);
                    // 胜平负
                    if ($hostScore > $guestScore) {
                        $spf = 'W';
                        $bqcWhole = 'w';
                    } elseif ($hostScore < $guestScore) {
                        $spf = 'L';
                        $bqcWhole = 'l';
                    } else {
                        $spf = 'D';
                        $bqcWhole = 'd';
                    }

                    $jcRe['spf'] = $spf;

                    // 让球胜平负
                    $rqs = JcdcBase::getRqs($data['match_num']);
                    $rqHostScore = $hostScore + $rqs;
                    if ($rqHostScore > $guestScore) {
                        $rqSpf = 'W';
                    } elseif ($rqHostScore < $guestScore) {
                        $rqSpf = 'L';
                    } else {
                        $rqSpf = 'D';
                    }

                    $jcRe['rqspf'] = $rqSpf;

                    // 总进球数
                    $scoreCount = (int)$hostScore + (int)$guestScore;
                    $scoreSizeStr = 's' . $scoreCount;
                    if ($scoreCount >= 7) {
                        $scoreSizeStr = 's7';
                    }
                    $jcRe['jqs'] = $scoreSizeStr;

                    // 全场比分
                    if (
                        (int)$hostScore === (int)$guestScore
                        && (int)$hostScore > 3
                        && (int)$guestScore > 3
                    ) {
                        $jcRe['bf'] = '-1-d'; // 平其他
                    } elseif (
                        (int)$hostScore > (int)$guestScore
                        && (
                            (int)$hostScore > 5
                            || (int)$guestScore > 2
                        )
                    ) {
                        $jcRe['bf'] = '-1-h'; // 胜其他
                    } elseif (
                        (int)$hostScore < (int)$guestScore
                        && (
                            (int)$hostScore > 2
                            || (int)$guestScore > 5
                        )
                    ) {
                        $jcRe['bf'] = '-1-l'; // 负其他
                    } else {
                        $jcRe['bf'] = '0' . $hostScore . '0' . $guestScore;
                    }
                }

                if (!empty($halfScore) && !empty($totalScore)) {
                    // 半全场胜平负
                    $jcRe['bqc'] = $bqcHalf . $bqcWhole;
                }

                return $jcRe;
            case Config::LC_CODE: // 篮彩
                $homeScore = $data['host_score']; // 主队全场得分(不含加时赛)
                $awayScore = $data['guest_score']; // 客队全场得分(不含加时赛)
                if (!empty($homeScore)) {//胜负
                    if ($homeScore > $awayScore) {
                        $sf = 'W'; // 主胜
                    } elseif ($homeScore < $awayScore) {
                        $sf = 'L'; // 主负
                    } else {
                        $sf = 'D'; // 平
                    }
                }

                if (!empty($homeScore)) {//让分胜负
                    $rfs = JclqBase::getRfs($data['match_num']);
                    $rfHostScore = $homeScore + $rfs;
                    if ($rfHostScore > $awayScore) {
                        $rfsf = 'W';//主胜
                    } elseif ($rfHostScore < $awayScore) {
                        $rfsf = 'L'; //主负
                    } else {
                        $rfsf = 'D'; //平
                    }
                }

                if (!empty($homeScore)) {//大小分
                    $ysfs = JclqMatch::getDxf($data['match_num']);//获取大小分的预设分数
                    $countScore = $homeScore + $awayScore;
                    if ($countScore > $ysfs) {
                        $dxf = 'H';//大分
                    } elseif ($countScore < $ysfs) {
                        $dxf = 'L';//小分
                    } else {
                        $dxf = 'D';//平
                    }
                }

                if (!empty($homeScore)) {//主/客 胜分差
                    $scoreGap = $homeScore - $awayScore;
                    $stype = true;//主胜
                    if ($scoreGap < 0) {
                        $stype = false;//客胜
                    }

                    $scoreGap = abs($scoreGap);
                    if ($scoreGap >= 1 && $scoreGap <= 5) {
                        if ($stype) {//主胜
                            $zsfc = '1-5';
                            $ksfc = '';
                        } else {//客胜
                            $ksfc = '1-5';
                            $zsfc = '';
                        }
                    }

                    if ($scoreGap >= 6 && $scoreGap <= 10) {
                        if ($stype) {//主胜
                            $zsfc = '6-10';
                            $ksfc = '';
                        } else {//客胜
                            $ksfc = '6-10';
                            $zsfc = '';
                        }
                    }

                    if ($scoreGap >= 11 && $scoreGap <= 15) {
                        if ($stype) {//主胜
                            $zsfc = '11-15';
                            $ksfc = '';
                        } else {//客胜
                            $ksfc = '11-15';
                            $zsfc = '';
                        }
                    }

                    if ($scoreGap >= 16 && $scoreGap <= 20) {
                        if ($stype) {//主胜
                            $zsfc = '16-20';
                            $ksfc = '';
                        } else {//客胜
                            $ksfc = '16-20';
                            $zsfc = '';
                        }
                    }

                    if ($scoreGap >= 21 && $scoreGap <= 25) {
                        if ($stype) {//主胜
                            $zsfc = '21-25';
                            $ksfc = '';
                        } else {//客胜
                            $ksfc = '21-25';
                            $zsfc = '';
                        }
                    }

                    if ($scoreGap >= 26) {
                        if ($stype) {//主胜
                            $zsfc = '26+';
                            $ksfc = '';
                        } else {//客胜
                            $ksfc = '26+';
                            $zsfc = '';
                        }
                    }
                }

                $jcRe = [
                    'sf' => $sf,
                    'rfsf' => $rfsf,
                    'dxf' => $dxf,
                    'zsfc' => $zsfc,
                    'ksfc' => $ksfc,
                ];
                return $jcRe;
            default:
                return [];
        }
    }

    /**
     * 计算竞彩结果, 并以字符串形式显示(赛事开奖 - 竞彩赛果)
     *
     * `return
     *  [
     *   'reSpf' => '主胜', // 胜平负
     *   'reRqspf' => '平', // 让球胜平负
     *   'reJqs' => '5', // 总进球数
     *   'reBqc' => '胜胜', // 半全场胜平负
     *   'reQcbf' => '1-3', // 全场比分
     *   ]
     *
     * @param string $code // 竞彩代码
     * @param array $data // 比赛赛果数据
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function computedJcResult($code, array $data)
    {
        switch ((string)$code) {
            case Config::ZC_CODE: // 足彩
                $halfScore = $data['half_score'];
                $totalScore = $data['normal_score']; // 不含加时赛
                $jcRe = [
                    'spf' => '-', // 胜平负
                    'rqspf' => '-', // 让球胜平负
                    'jqs' => '-', // 总进球数
                    'bqc' => '-', // 半全场胜平负
                    'bf' => '-', // 全场比分
                ];
                if (!empty($halfScore)) {
                    $halfArr = explode('-', $halfScore);
                    $hostHalfScore = (int)current($halfArr);
                    $guestHalfScore = (int)end($halfArr);
                    if ($hostHalfScore > $guestHalfScore) {
                        $bqcHalf = '胜';
                    } elseif ($hostHalfScore < $guestHalfScore) {
                        $bqcHalf = '负';
                    } else {
                        $bqcHalf = '平';
                    }
                }

                if (!empty($totalScore)) {
                    $totalArr = explode('-', $totalScore);
                    $hostScore = current($totalArr);
                    $guestScore = end($totalArr);
                    // 胜平负
                    if ($hostScore > $guestScore) {
                        $spf = '胜';
                        $bqcWhole = '胜';
                    } elseif ($hostScore < $guestScore) {
                        $spf = '负';
                        $bqcWhole = '负';
                    } else {
                        $spf = '平';
                        $bqcWhole = '平';
                    }

                    $jcRe['spf'] = $spf;

                    // 让球胜平负
                    $rqs = JczqBase::getRqs($data['match_num']);
                    $rqHostScore = $hostScore + $rqs;
                    if ($rqHostScore > $guestScore) {
                        $rqSpf = '让胜';
                    } elseif ($rqHostScore < $guestScore) {
                        $rqSpf = '让负';
                    } else {
                        $rqSpf = '让平';
                    }

                    $jcRe['rqspf'] = $rqSpf;

                    // 总进球数
                    $scoreCount = (int)$hostScore + (int)$guestScore;
                    $jcRe['jqs'] = $scoreCount;

                    // 全场比分
                    $jcRe['bf'] = $totalScore;
                }

                if (!empty($halfScore) && !empty($totalScore)) {
                    // 半全场胜平负
                    $jcRe['bqc'] = $bqcHalf . $bqcWhole;
                }

                return $jcRe;
            case Config::LC_CODE: // 篮彩
                $halfScore = $data['host_score']; //不含加时赛 主队得分
                $totalScore = $data['guest_score']; // 不含加时赛 客队得分
                $jcRe = [
                    'sf' => '-', // 胜负
                    'rfsf' => '-', // 让分胜负
                    'dxf' => '-', // 大小分
                    'zsfc' => '-', // 主胜分差
                    'ksfc' => '-', // 客胜分差
                ];
                if (!empty($halfScore) && !empty($totalScore)) {
                    //胜负
                    if ($halfScore > $totalScore) {
                        $sf = '主胜';
                    } elseif ($halfScore < $totalScore) {
                        $sf = '客胜';
                    } else {
                        $sf = '平';
                    }

                    $jcRe['sf'] = $sf;
                    //让分胜负
                    $rfs = JclqBase::getRfs($data['match_num']);
                    $rfsHalfScore = $halfScore + $rfs;
                    if ($rfsHalfScore > $totalScore) {
                        $rfsf = $rfs.'让分主胜';
                    } elseif ($rfsHalfScore < $totalScore) {
                        $rfsf = $rfs.'让分客胜';
                    } else {
                        $rfsf = $rfs.'让分平';
                    }

                    $jcRe['rfsf'] = $rfsf;
                    //大小分
                    $ysfs = JclqMatch::getDxf($data['match_num']);
                    $zfs = $halfScore + $totalScore;
                    if ($ysfs > $zfs) {
                        $dxf = $zfs.'小分';
                    } elseif ($ysfs < $zfs) {
                        $dxf = $zfs.'大分';
                    } else {
                        $dxf = $zfs.'平';
                    }

                    $jcRe['dxf'] = $dxf;
                    //胜负分差
                    $fx = $halfScore - $totalScore;
                    if ($fx > 0) {//主胜
                        $zsfc = '主胜(' . $fx . ')';
                        $ksfc = '主胜(' . $fx . ')';
                    } elseif ($fx < 0) {//客胜
                        $ksfc = '客胜(' . abs($fx) . ')';
                        $zsfc = '客胜(' . abs($fx) . ')';
                    } else {//平
                        $ksfc = '平';
                        $zsfc = '平';
                    }

                    $jcRe['zsfc'] = $zsfc;
                    $jcRe['ksfc'] = $ksfc;
                }
                return $jcRe;
            case Config::BJ_CODE: // 北京单场
                $halfScore = $data['half_score'];
                $totalScore = $data['normal_score']; // 不含加时赛
                $jcRe = [
                    'spf' => '-', // 胜平负
                    'rqspf' => '-', // 让球胜平负
                    'jqs' => '-', // 总进球数
                    'bqc' => '-', // 半全场胜平负
                    'bf' => '-', // 全场比分
                ];
                if (!empty($halfScore)) {
                    $halfArr = explode('-', $halfScore);
                    $hostHalfScore = (int)current($halfArr);
                    $guestHalfScore = (int)end($halfArr);
                    if ($hostHalfScore > $guestHalfScore) {
                        $bqcHalf = '胜';
                    } elseif ($hostHalfScore < $guestHalfScore) {
                        $bqcHalf = '负';
                    } else {
                        $bqcHalf = '平';
                    }
                }

                if (!empty($totalScore)) {
                    $totalArr = explode('-', $totalScore);
                    $hostScore = current($totalArr);
                    $guestScore = end($totalArr);
                    // 胜平负
                    if ($hostScore > $guestScore) {
                        $spf = '胜';
                        $bqcWhole = '胜';
                    } elseif ($hostScore < $guestScore) {
                        $spf = '负';
                        $bqcWhole = '负';
                    } else {
                        $spf = '平';
                        $bqcWhole = '平';
                    }

                    $jcRe['spf'] = $spf;

                    // 让球胜平负
                    $rqs = JcdcBase::getRqs($data['match_num']);
                    $rqHostScore = $hostScore + $rqs;
                    if ($rqHostScore > $guestScore) {
                        $rqSpf = '让胜';
                    } elseif ($rqHostScore < $guestScore) {
                        $rqSpf = '让负';
                    } else {
                        $rqSpf = '让平';
                    }

                    $jcRe['rqspf'] = $rqSpf;

                    // 总进球数
                    $scoreCount = (int)$hostScore + (int)$guestScore;
                    $jcRe['jqs'] = $scoreCount;

                    // 全场比分
                    $jcRe['bf'] = $totalScore;
                }

                if (!empty($halfScore) && !empty($totalScore)) {
                    // 半全场胜平负
                    $jcRe['bqc'] = $bqcHalf . $bqcWhole;
                }

                return $jcRe;
            default:
                return [];
        }
    }

    /**
     * 出票详情数据组装(注单列表)
     *
     * $data数据JSON格式:
     * {"mnum":"3833810","ptype":"spf","bet":"D|H","i":"3.7|3.5"} // 胜平负投注项
     * {"mnum":"3833729","ptype":"rqspf","bet":"W","i":"2.52","rqs":"-1"} // 让球胜平负投注项
     * {"mnum":"3833729","ptype":"jqs","bet":"s5","i":"2.52"} // 进球数投注项
     * {"mnum":"3833729","ptype":"bqc","bet":"ww","i":"2.52"} // 半全场投注项
     * {"mnum":"3833729","ptype":"bf","bet":"0000","i":"2.52"} // 全场比分投注项
     *
     * `return
     * ['bet_item' => '胜[1.42]/平[1.36]']
     *
     * @param $data
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function jzTouZhuGroup($data)
    {
        $result = [];
        switch (strtolower($data['ptype'])) {
            case 'spf': // 胜平负
                $betArr = explode('|', $data['bet']);
                $iArr = explode('|', $data['i']);
                $newArr = array_combine($betArr, $iArr);
                $betStr = '';
                foreach ($newArr as $bet => $i) {
                    $spfStr = str_replace(['W', 'D', 'L'], ['胜', '平', '负'], strtoupper($bet));
                    $betStr .= $spfStr . '[' . $i . ']' . '/';
                }

                $result['bet_item'] = rtrim($betStr, '/');

                break;
            case 'rqspf': // 让球胜平负
                $betArr = explode('|', $data['bet']);
                $iArr = explode('|', $data['i']);
                $newArr = array_combine($betArr, $iArr);
                $betStr = '';
                foreach ($newArr as $bet => $i) {
                    $rqStr = str_replace(['W', 'D', 'L'], ['让胜', '让平', '让负'], strtoupper($bet));
                    $betStr .= '(' . $data['rqs'] . ')' . $rqStr . '[' . $i . ']' . '/';
                }

                $result['bet_item'] = rtrim($betStr, '/');
                break;
            case 'jqs': // 进球数
                $betArr = explode('|', $data['bet']);
                $iArr = explode('|', $data['i']);
                $newArr = array_combine($betArr, $iArr);
                $betStr = '';
                foreach ($newArr as $bet => $i) {
                    $jqsStr = str_replace(
                        ['s0', 's1', 's2', 's3', 's4', 's5', 's6', 's7'],
                        ['0', '1', '2', '3', '4', '5', '6', '7'],
                        strtolower($bet)
                    );

                    $betStr .= '(' . $jqsStr . ')球[' . $i . ']' . '/';
                }
                $result['bet_item'] = rtrim($betStr, '/');
                break;
            case 'bqc': // 半全场
                $betArr = explode('|', $data['bet']);
                $iArr = explode('|', $data['i']);
                $newArr = array_combine($betArr, $iArr);
                $betStr = '';
                foreach ($newArr as $bet => $i) {
                    $bqcStr = str_replace(
                        ['ww', 'wd', 'wl', 'dw', 'dd', 'dl', 'lw', 'ld', 'll'],
                        ['胜胜', '胜平', '胜负', '平胜', '平平', '平负', '负胜', '负平', '负负'],
                        strtolower($bet)
                    );
                    $betStr .= $bqcStr . '[' . $i . ']' . '/';
                }

                $result['bet_item'] = rtrim($betStr, '/');
                break;
            case 'bf':
                $betArr = explode('|', $data['bet']);
                $iArr = explode('|', $data['i']);
                $newArr = array_combine($betArr, $iArr);
                $betStr = '';
                foreach ($newArr as $bet => $i) {
                    $bfStr = str_replace(
                        [
                            '0000', '0001', '0002', '0003', '0004', '0005', '0100', '0101', '0102', '0103',
                            '0104', '0105', '0200', '0201', '0202', '0203', '0204', '0205', '0300', '0301',
                            '0302', '0303', '0400', '0401', '0402', '0500', '0501', '0502', '-1-a', '-1-d',
                            '-1-h', '-1-l'
                        ],
                        [
                            '0-0', '0-1', '0-2', '0-3', '0-4', '0-5', '1-0', '1-1', '1-2', '1-3',
                            '1-4', '1-5', '2-0', '2-1', '2-2', '2-3', '2-4', '2-5',
                            '3-0', '3-1', '3-2', '3-3', '4-0', '4-1', '4-2', '5-0', '5-1', '5-2',
                            '负其他', '平其他', '胜其他', '负其他'
                        ],
                        $bet
                    );

                    $betStr .= $bfStr . '[' . $i . ']' . '/';
                }


                $result['bet_item'] = rtrim($betStr, '/');
                break;
        }

        return $result;
    }

    /**
     * 出票详情数据组装(注单列表)
     *
     * $data数据JSON格式:
     * {"mnum":"3833810","ptype":"sf","bet":"W|L","i":"3.7|3.5"} // 胜负投注项
     * {"mnum":"3833729","ptype":"rfsf","bet":"W|L","i":"2.52|3.6","rfs":"-1"} // 让分胜负投注项
     * {"mnum":"3833729","ptype":"dxf","bet":"H|L","i":"2.52|2.4"} // 大小分
     * {"mnum":"3833729","ptype":"zsfc","bet":"1-5","i":"2.52"} // 主胜分差
     * {"mnum":"3833729","ptype":"ksfc","bet":"6-10","i":"2.52"} // 客胜分差
     * `return
     * ['bet_item' => '胜[1.42]/平[1.36]']
     *
     * @param $data
     * @return array
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function jlTouZhuGroup($data)
    {
        $result = [];
        switch (strtolower($data['ptype'])) {
            case 'sf': // 胜负
                $betArr = explode('|', $data['bet']);
                $iArr = explode('|', $data['i']);
                $newArr = array_combine($betArr, $iArr);
                $betStr = '';
                foreach ($newArr as $bet => $i) {
                    $spfStr = str_replace(['W', 'L'], ['主胜', '客胜'], strtoupper($bet));
                    $betStr .= $spfStr . '[' . $i . ']' . '/';
                }

                $result['bet_item'] = rtrim($betStr, '/');

                break;
            case 'rfsf': // 让分胜负
                $betArr = explode('|', $data['bet']);
                $iArr = explode('|', $data['i']);
                $newArr = array_combine($betArr, $iArr);
                $betStr = '';
                foreach ($newArr as $bet => $i) {
                    $rqStr = str_replace(['W', 'L'], ['主胜', '客胜'], strtoupper($bet));
                    $betStr .= '(让' . $data['rfs'] . ')' . $rqStr . '[' . $i . ']' . '/';
                }

                $result['bet_item'] = rtrim($betStr, '/');
                break;
            case 'ksfc': // 客胜分差
                $betArr = explode('|', $data['bet']);
                $iArr = explode('|', $data['i']);
                $newArr = array_combine($betArr, $iArr);
                $betStr = '';
                foreach ($newArr as $bet => $i) {
                    $jqsStr = str_replace(
                        ['1-5', '6-10', '11-15', '16-20', '21-25', '26+'],
                        ['客胜(1-5)', '客胜(6-10)', '客胜(11-15)', '客胜(16-20)', '客胜(21-25)', '客胜(26+)'],
                        strtolower($bet)
                    );

                    $betStr .= $jqsStr . '[' . $i . ']' . '/';
                }
                $result['bet_item'] = rtrim($betStr, '/');
                break;
            case 'zsfc': // 主胜分差
                $betArr = explode('|', $data['bet']);
                $iArr = explode('|', $data['i']);
                $newArr = array_combine($betArr, $iArr);
                $betStr = '';
                foreach ($newArr as $bet => $i) {
                    $bqcStr = str_replace(
                        ['1-5', '6-10', '11-15', '16-20', '21-25', '26+'],
                        ['主胜(1-5)', '主胜(6-10)', '主胜(11-15)', '主胜(16-20)', '主胜(21-25)', '主胜(26+)'],
                        strtolower($bet)
                    );
                    $betStr .= $bqcStr . '[' . $i . ']' . '/';
                }

                $result['bet_item'] = rtrim($betStr, '/');
                break;
            case 'dxf': //大小分
                $betArr = explode('|', $data['bet']);
                $iArr = explode('|', $data['i']);
                $newArr = array_combine($betArr, $iArr);
                $betStr = '';
                foreach ($newArr as $bet => $i) {
                    $bfStr = str_replace(
                        [
                            'H', 'L'
                        ],
                        [
                            '大分', '小分'
                        ],
                        $bet
                    );

                    $betStr .= $bfStr . '[' . $i . ']' . '/';
                }


                $result['bet_item'] = rtrim($betStr, '/');
                break;
        }

        return $result;
    }

    /**
     * 处理足球数据订单详情(order_detail), bet字段数据
     * 例如:
     * dealOrderDetailBet(spf, W|2.52:-1)
     *
     * `return
     * ['bet' => 'W', 'i' => '2.52', 'rqs' => '-1']
     *
     * @param $play // 玩法
     * @param $value // 值
     * @return array|false
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function dealOrderDetailBet($play, $value)
    {
        switch (strtolower($play)) {
            case 'spf': // 胜平负
            case 'jqs': // 进球数
            case 'bqc': // 半全场
            case 'bf': // 比分
                list($bet, $i) = explode('|', $value);

                return ['bet' => $bet, 'i' => $i];
            case 'rqspf': // 让球胜平负
                list($bet, $more) = explode('|', $value);
                list($i, $rqs) = explode(':', $more);

                return ['bet' => $bet, 'i' => $i, 'rqs' => $rqs];
            default:

                return false;
        }
    }

    /**
     * 处理篮球数据订单详情(order_detail), bet字段数据
     * 例如:
     * baskOrderDetailBet(rfsf, W|1.64:-5.5)
     *
     * `return
     * ['bet' => 'W', 'i' => '2.52', 'rqs' => '-1']
     *
     * @param $play // 玩法
     * @param $value // 值
     * @return array|false
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function baskOrderDetailBet($play, $value)
    {
        switch (strtolower($play)) {
            case 'sf': // 胜负
            case 'dxf': // 大小分
            case 'ksfc': // 客胜
            case 'zsfc': // 主胜
                list($bet, $i) = explode('|', $value);
                return ['bet' => $bet, 'i' => $i];
            case 'rfsf': // 让分胜负
                list($bet, $more) = explode('|', $value);
                list($i, $rfs) = explode(':', $more);
                return ['bet' => $bet, 'i' => $i, 'rfs' => $rfs];
            default:

                return false;
        }
    }

    /**
     * 数字彩通过玩法代码获取玩法字符串
     *
     * @param $code
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function getPlayStrByCode($code)
    {
        switch ($code) {
            // 排列三
            case 'zhihe':
                return '直选和值';
            case 'zhipu':
                return '直选普通';
            case 'zusanbao':
                return '组三包号';
            case 'zusanhe':
                return '组三和值';
            case 'zuliubao':
                return '组六包号';
            case 'zuliuhe':
                return '组六和值';
            case 'zuliudantuo':
                return '组六胆拖';
            // 排列五
            case 'p5zhipu':
                return '直选普通';
            // 澳彩
            case 'aozhipu':
                return '直选普通';
            case 'aozhihe':
                return '直选和值';
            // 葡彩
            case 'puzhipu':
                return '直选普通';
            // 幸运飞艇
            case 'lm':
                return '两面';
            case 'gh':
                return '冠亚和值';
            case 'pm':
                return '1-10名';
            default:
                return '';
        }
    }

    /**
     * 幸运飞艇type和投注内容字符转换
     *
     * @param $str // 字符标识  如: g, y, gyh, odds, even, big, small
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function convertStr($str)
    {
        switch ($str){
            case 'g': // 冠军
                return '冠军';
            case 'y': // 亚军
                return '亚军';
            case 'gyh': // 冠亚和
                return '冠亚和';
            case 'big': // 大
                return '大';
            case 'small': // 小
                return '小';
            case 'even': // 单
                return '单';
            case 'odds': // 双
                return '双';
            case 'long': // 龙
                return '龙';
            case 'hu': // 虎
                return '虎';
            default:
                return $str;
        }
    }

    /**
     * 体彩玩法标识转汉语字符
     *
     * @param string $ptype 体彩玩法标识
     * @return string
     * @author CleverStone
     */
    public function sportsBetCodeToStr($ptype)
    {
        switch ($ptype){
            case 'spf':
                return '胜平负';
            case 'rqspf':
                return '让球胜平负';
            case 'bf':
                return '比分';
            case 'jqs':
                return '进球数';
            case 'bqc':
                return '半全场';
                // 篮球
            case 'sf':
                return '胜负';
            case 'rfsf':
                return '让分胜负';
            case 'dxf':
                return '大小分';
            case 'zsfc':
                return '主胜分差';
            case 'ksfc':
                return '客胜分差';
            default:
                return '未知玩法';
        }
    }
}