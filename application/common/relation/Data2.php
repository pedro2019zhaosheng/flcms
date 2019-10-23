<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/26
 * Time: 17:21
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\relation;

use app\common\Helper;

/**
 * 彩种数据处理(API接口)
 *
 * Trait Data
 * @package app\common\relation
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
trait Data2
{
    /**
     * @desc 竞彩足球赔率处理
     * @author LiBin
     * @param $spf //胜平负
     * @param $rqspf //让球胜平负
     * @param $bf //比分胜平负
     * @param $jqs //进球数
     * @param $bqc //半全场
     * @return mixed
     * @date 2019-03-26
     * @updateBy CleverStone
     */
    public function oddSort($spf = [], $rqspf = [], $bf = [], $jqs = [], $bqc = [])
    {
        $one = [];
        $two = [];
        $data = [];
        $dg['spf'] = 0;
        $dg['rqspf'] = 0;
        $dg['bf'] = 0;
        $dg['jqs'] = 0;
        $dg['bqc'] = 0;
        $dg['is_dg'] = 0;
        // 胜平负
        if (!empty($spf)) {
            $one[] = $spf['W'];
            $one[] = $spf['D'];
            $one[] = $spf['L'];
            if (!empty($spf['single'])) { // 判断单关
                $dg['spf'] = 1;
                $dg['is_dg'] = 1;
            }
        } else {
            for ($i = 0; $i < 3; $i++) {
                $one[] = '0';
            }
        }

        // 让球胜平负
        if (!empty($rqspf)) {
            $one[] = $rqspf['W'];
            $one[] = $rqspf['D'];
            $one[] = $rqspf['L'];
            if (!empty($rqspf['single'])) {//判断单关
                $dg['rqspf'] = 1;
                $dg['is_dg'] = 1;
            }
        } else {
            for ($i = 0; $i < 3; $i++) {
                $one[] = '0';
            }
        }

        // 比分
        if (!empty($bf)) {
            $two[] = $bf['dat']['0100'];
            $two[] = $bf['dat']['0200'];
            $two[] = $bf['dat']['0201'];
            $two[] = $bf['dat']['0300'];
            $two[] = $bf['dat']['0301'];
            $two[] = $bf['dat']['0302'];
            $two[] = $bf['dat']['0400'];
            $two[] = $bf['dat']['0401'];
            $two[] = $bf['dat']['0402'];
            $two[] = $bf['dat']['0500'];
            $two[] = $bf['dat']['0501'];
            $two[] = $bf['dat']['0502'];
            $two[] = $bf['dat']['-1-h'];
            $two[] = $bf['dat']['0000'];
            $two[] = $bf['dat']['0101'];
            $two[] = $bf['dat']['0202'];
            $two[] = $bf['dat']['0303'];
            $two[] = $bf['dat']['-1-d'];
            $two[] = $bf['dat']['0001'];
            $two[] = $bf['dat']['0002'];
            $two[] = $bf['dat']['0102'];
            $two[] = $bf['dat']['0003'];
            $two[] = $bf['dat']['0103'];
            $two[] = $bf['dat']['0203'];
            $two[] = $bf['dat']['0004'];
            $two[] = $bf['dat']['0104'];
            $two[] = $bf['dat']['0204'];
            $two[] = $bf['dat']['0005'];
            $two[] = $bf['dat']['0105'];
            $two[] = $bf['dat']['0205'];
            $two[] = $bf['dat']['-1-a'];
            if (!empty($bf['single'])) { // 判断单关
                $dg['bf'] = 1;
                $dg['is_dg'] = 1;
            }
        } else {
            for ($i = 0; $i < 31; $i++) {
                $two[] = '0';
            }
        }

        // 进球
        if (!empty($jqs)) {
            $two[] = $jqs['dat']['s0'];
            $two[] = $jqs['dat']['s1'];
            $two[] = $jqs['dat']['s2'];
            $two[] = $jqs['dat']['s3'];
            $two[] = $jqs['dat']['s4'];
            $two[] = $jqs['dat']['s5'];
            $two[] = $jqs['dat']['s6'];
            $two[] = $jqs['dat']['s7'];
            if (!empty($jqs['single'])) {//判断单关
                $dg['jqs'] = 1;
                $dg['is_dg'] = 1;
            }
        } else {
            for ($i = 0; $i < 8; $i++) {
                $two[] = '0';
            }
        }

        // 半全场
        if (!empty($bqc)) {
            $two[] = $bqc['dat']['ww'];
            $two[] = $bqc['dat']['wd'];
            $two[] = $bqc['dat']['wl'];
            $two[] = $bqc['dat']['dw'];
            $two[] = $bqc['dat']['dd'];
            $two[] = $bqc['dat']['dl'];
            $two[] = $bqc['dat']['lw'];
            $two[] = $bqc['dat']['ld'];
            $two[] = $bqc['dat']['ll'];
            if (!empty($bqc['single'])) {//判断单关
                $dg['bqc'] = 1;
                $dg['is_dg'] = 1;
            }
        } else {
            for ($i = 0; $i < 9; $i++) {
                $two[] = '0';
            }
        }

        $data[] = $one;
        $data[] = $two;
        $data['dg'] = $dg;
        return $data;
    }

    /**
     * @desc 足彩单关处理
     * @author LiBin
     * @param array $data
     * @param $type
     * @return array
     * @date 2019-04-27
     * @updateBy CleverStone
     */
    public function oddSortDg($data, $type)
    {
        if (!$data) {
            $data = [];
        }

        $result = [];
        $i = 0;
        foreach ($data as $v) {
            if (!$v['match_num']) {
                // 赛事编号为空, 则跳过该赛事
                continue;
            }

            // 胜平负
            if ($type == 'spf') {
                // 胜平负
                $sp_spf = Helper::jsonDecode($v['sp_spf']);
                // 让球胜平负
                $sp_rqspf = Helper::jsonDecode($v['sp_rqspf']);
                // 都为空则跳过
                if (!$sp_spf && !$sp_rqspf){
                    continue;
                }
                // 都不是单关则跳过
                if ($sp_spf['single'] == 0 && $sp_rqspf['single'] == 0) {
                    continue;
                }

                $result[$i]['match_num'] = $v['match_num'];
                $result[$i]['league_name'] = $v['league_name'];
                $result[$i]['host_name'] = $v['host_name'];
                $result[$i]['guest_name'] = $v['guest_name'];
                $result[$i]['host_icon'] = $v['host_icon'];
                $result[$i]['guest_icon'] = $v['guest_icon'];
                $result[$i]['rqs'] = $sp_rqspf ? $sp_rqspf['H'] : '0';//让球数
                empty($v['cutoff_time']) ? $result[$i]['cutoff_time'] = $v['sys_cutoff_time'] : $result[$i]['cutoff_time'] = $v['cutoff_time'];
                $result[$i]['cutoff_time'] = date('H:i', strtotime($result[$i]['cutoff_time']));
                $result[$i]['jc_num'] = $v['jc_num'];
                if ($sp_spf) {
                    if ($sp_spf['single'] == 1) {
                        $result[$i]['odds'][] = $sp_spf['W'];
                        $result[$i]['odds'][] = $sp_spf['D'];
                        $result[$i]['odds'][] = $sp_spf['L'];
                    } else {
                        $result[$i]['odds'][] = '0';
                        $result[$i]['odds'][] = '0';
                        $result[$i]['odds'][] = '0';
                    }
                } else {
                    // 赔率未刷
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                }

                if ($sp_rqspf) {
                    if ($sp_rqspf['single'] == 1) {
                        $result[$i]['odds'][] = $sp_rqspf['W'];
                        $result[$i]['odds'][] = $sp_rqspf['D'];
                        $result[$i]['odds'][] = $sp_rqspf['L'];
                    } else {
                        $result[$i]['odds'][] = '0';
                        $result[$i]['odds'][] = '0';
                        $result[$i]['odds'][] = '0';
                    }
                } else {
                    // 赔率未刷
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                }

                $i++;
            }

            // 进球数
            if ($type == 'jqs') {
                $jqs = Helper::jsonDecode($v['sp_jqs']);
                // 赔率为空则跳过
                if (!$jqs){
                    continue;
                }
                // 不是单关跳过
                if ($jqs['single'] == 0) {
                    continue;
                }

                $result[$i]['match_num'] = $v['match_num'];
                $result[$i]['league_name'] = $v['league_name'];
                $result[$i]['host_name'] = $v['host_name'];
                $result[$i]['guest_name'] = $v['guest_name'];
                $result[$i]['host_icon'] = $v['host_icon'];
                $result[$i]['guest_icon'] = $v['guest_icon'];
                empty($v['cutoff_time']) ? $result[$i]['cutoff_time'] = $v['sys_cutoff_time'] : $result[$i]['cutoff_time'] = $v['cutoff_time'];
                $result[$i]['cutoff_time'] = date('H:i', strtotime($result[$i]['cutoff_time']));
                $result[$i]['jc_num'] = $v['jc_num'];
                $result[$i]['odds'][] = $jqs['dat']['s0'];
                $result[$i]['odds'][] = $jqs['dat']['s1'];
                $result[$i]['odds'][] = $jqs['dat']['s2'];
                $result[$i]['odds'][] = $jqs['dat']['s3'];
                $result[$i]['odds'][] = $jqs['dat']['s4'];
                $result[$i]['odds'][] = $jqs['dat']['s5'];
                $result[$i]['odds'][] = $jqs['dat']['s6'];
                $result[$i]['odds'][] = $jqs['dat']['s7'];

                $i++;
            }

            // 比分
            if ($type == 'bf') {
                $bf = Helper::jsonDecode($v['sp_bf']);
                // 赔率为空则跳过
                if (!$bf){
                    continue;
                }
                // 不是单关则跳过
                if ($bf['single'] == 0) {
                    continue;
                }

                $result[$i]['match_num'] = $v['match_num'];
                $result[$i]['league_name'] = $v['league_name'];
                $result[$i]['host_name'] = $v['host_name'];
                $result[$i]['guest_name'] = $v['guest_name'];
                $result[$i]['host_icon'] = $v['host_icon'];
                $result[$i]['guest_icon'] = $v['guest_icon'];
                empty($v['cutoff_time']) ? $result[$i]['cutoff_time'] = $v['sys_cutoff_time'] : $result[$i]['cutoff_time'] = $v['cutoff_time'];
                $result[$i]['cutoff_time'] = date('H:i', strtotime($result[$i]['cutoff_time']));
                $result[$i]['jc_num'] = $v['jc_num'];
                $result[$i]['odds'][] = $bf['dat']['0100'];
                $result[$i]['odds'][] = $bf['dat']['0200'];
                $result[$i]['odds'][] = $bf['dat']['0201'];
                $result[$i]['odds'][] = $bf['dat']['0300'];
                $result[$i]['odds'][] = $bf['dat']['0301'];
                $result[$i]['odds'][] = $bf['dat']['0302'];
                $result[$i]['odds'][] = $bf['dat']['0400'];
                $result[$i]['odds'][] = $bf['dat']['0401'];
                $result[$i]['odds'][] = $bf['dat']['0402'];
                $result[$i]['odds'][] = $bf['dat']['0500'];
                $result[$i]['odds'][] = $bf['dat']['0501'];
                $result[$i]['odds'][] = $bf['dat']['0502'];
                $result[$i]['odds'][] = $bf['dat']['-1-h'];
                $result[$i]['odds'][] = $bf['dat']['0000'];
                $result[$i]['odds'][] = $bf['dat']['0101'];
                $result[$i]['odds'][] = $bf['dat']['0202'];
                $result[$i]['odds'][] = $bf['dat']['0303'];
                $result[$i]['odds'][] = $bf['dat']['-1-d'];
                $result[$i]['odds'][] = $bf['dat']['0001'];
                $result[$i]['odds'][] = $bf['dat']['0002'];
                $result[$i]['odds'][] = $bf['dat']['0102'];
                $result[$i]['odds'][] = $bf['dat']['0003'];
                $result[$i]['odds'][] = $bf['dat']['0103'];
                $result[$i]['odds'][] = $bf['dat']['0203'];
                $result[$i]['odds'][] = $bf['dat']['0004'];
                $result[$i]['odds'][] = $bf['dat']['0104'];
                $result[$i]['odds'][] = $bf['dat']['0204'];
                $result[$i]['odds'][] = $bf['dat']['0005'];
                $result[$i]['odds'][] = $bf['dat']['0105'];
                $result[$i]['odds'][] = $bf['dat']['0205'];
                $result[$i]['odds'][] = $bf['dat']['-1-a'];

                $i++;
            }

            // 半全场
            if ($type == 'bqc') {
                $bqc = Helper::jsonDecode($v['sp_bqc']);
                // 赔率未空则跳过
                if (!$bqc){
                    continue;
                }
                // 不是单关则跳过
                if ($bqc['single'] == 0) {
                    continue;
                }

                $result[$i]['match_num'] = $v['match_num'];
                $result[$i]['league_name'] = $v['league_name'];
                $result[$i]['host_name'] = $v['host_name'];
                $result[$i]['guest_name'] = $v['guest_name'];
                $result[$i]['host_icon'] = $v['host_icon'];
                $result[$i]['guest_icon'] = $v['guest_icon'];
                empty($v['cutoff_time']) ? $result[$i]['cutoff_time'] = $v['sys_cutoff_time'] : $result[$i]['cutoff_time'] = $v['cutoff_time'];
                $result[$i]['cutoff_time'] = date('H:i', strtotime($result[$i]['cutoff_time']));
                $result[$i]['jc_num'] = $v['jc_num'];
                $result[$i]['odds'][] = $bqc['dat']['ww'];
                $result[$i]['odds'][] = $bqc['dat']['wd'];
                $result[$i]['odds'][] = $bqc['dat']['wl'];
                $result[$i]['odds'][] = $bqc['dat']['dw'];
                $result[$i]['odds'][] = $bqc['dat']['dd'];
                $result[$i]['odds'][] = $bqc['dat']['dl'];
                $result[$i]['odds'][] = $bqc['dat']['lw'];
                $result[$i]['odds'][] = $bqc['dat']['ld'];
                $result[$i]['odds'][] = $bqc['dat']['ll'];

                $i++;
            }
        }

        return $result;
    }

    /**
     * @desc 竞彩篮球赔率处理
     * @author LiBin
     * @param $sp_sf //胜负
     * @param $sp_rfsf //让分胜负
     * @param $sp_sfc //胜分差
     * @param $sp_dxf //大小分
     * @return mixed
     * @date 2019-03-26
     */
    public function basketSort($sp_sf = [], $sp_rfsf = [], $sp_sfc = [], $sp_dxf = [])
    {
        $one = [];
        $two = [];
        $data = [];
        // 胜负
        if (!empty($sp_sf)) {
            $one[] = $sp_sf['L'];
            $one[] = $sp_sf['W'];
        } else {
            for ($i = 0; $i < 2; $i++) {
                $one[] = '0';
            }
        }

        // 让分胜负
        // 让分数默认为0
        $data['rfs'] = '0';
        if (!empty($sp_rfsf)) {
            $one[] = $sp_rfsf['L'];
            $one[] = $sp_rfsf['W'];
            $data['rfs'] = $sp_rfsf['H'];
        } else {
            for ($i = 0; $i < 2; $i++) {
                $one[] = '0';
            }
        }

        // 胜分差
        if (!empty($sp_sfc) && !empty($sp_sfc['dat']['away'])) {
            $two[] = $sp_sfc['dat']['away'][0];
            $two[] = $sp_sfc['dat']['away'][1];
            $two[] = $sp_sfc['dat']['away'][2];
            $two[] = $sp_sfc['dat']['away'][3];
            $two[] = $sp_sfc['dat']['away'][4];
            $two[] = $sp_sfc['dat']['away'][5];
        } else {
            for ($i = 0; $i < 6; $i++) {
                $two[] = '0';
            }
        }

        if (!empty($sp_sfc) && !empty($sp_sfc['dat']['home'])) {
            $two[] = $sp_sfc['dat']['home'][0];
            $two[] = $sp_sfc['dat']['home'][1];
            $two[] = $sp_sfc['dat']['home'][2];
            $two[] = $sp_sfc['dat']['home'][3];
            $two[] = $sp_sfc['dat']['home'][4];
            $two[] = $sp_sfc['dat']['home'][5];
        } else {
            for ($i = 0; $i < 6; $i++) {
                $two[] = '0';
            }
        }

        // 大小分
        // 预设总分默认为0
        $data['ys'] = '0';
        if (!empty($sp_dxf)) {
            $data['ys'] = $sp_dxf['T'];
            $two[] = $sp_dxf['H'];
            $two[] = $sp_dxf['L'];
        } else {
            for ($i = 0; $i < 2; $i++) {
                $two[] = '0';
            }
        }

        $data[] = $one;
        $data[] = $two;
        return $data;
    }

    /**
     * @desc 蓝彩单关处理
     * @author LiBin
     * @param array $data
     * @return array
     * @date 2019-04-27
     * @updateBy CleverStone
     */
    public function baskSortDg($data = [])
    {
        $result = [];
        $i = 0;
        foreach ($data as $v) {
            // 胜分差
            $sp_sfc = Helper::jsonDecode($v['sp_sfc']);
            // 赔率为空则跳过
            if (!$sp_sfc){
                continue;
            }
            // 不是单关则跳过
            if (empty($sp_sfc['single'])) {
                continue;
            }
            // 赛事编号为空, 则跳过
            if (!$v['match_num']) {
                continue;
            }

            $result[$i]['match_num'] = $v['match_num'];
            $result[$i]['league_name'] = $v['league_name'];
            $result[$i]['host_name'] = $v['host_name'];
            $result[$i]['guest_name'] = $v['guest_name'];
            $result[$i]['host_icon'] = $v['host_icon'];
            $result[$i]['guest_icon'] = $v['guest_icon'];
            $result[$i]['rfs'] = $v['rfs'];//让分数
            empty($v['cutoff_time']) ? $result[$i]['cutoff_time'] = $v['sys_cutoff_time'] : $result[$i]['cutoff_time'] = $v['cutoff_time'];
            $result[$i]['cutoff_time'] = date('H:i', strtotime($result[$i]['cutoff_time']));
            $result[$i]['jc_num'] = $v['jc_num'];
            // 主胜
            $result[$i]['odds'][] = $sp_sfc['dat']['home'][0];
            $result[$i]['odds'][] = $sp_sfc['dat']['home'][1];
            $result[$i]['odds'][] = $sp_sfc['dat']['home'][2];
            $result[$i]['odds'][] = $sp_sfc['dat']['home'][3];
            $result[$i]['odds'][] = $sp_sfc['dat']['home'][4];
            $result[$i]['odds'][] = $sp_sfc['dat']['home'][5];
            // 客胜
            $result[$i]['odds'][] = $sp_sfc['dat']['away'][0];
            $result[$i]['odds'][] = $sp_sfc['dat']['away'][1];
            $result[$i]['odds'][] = $sp_sfc['dat']['away'][2];
            $result[$i]['odds'][] = $sp_sfc['dat']['away'][3];
            $result[$i]['odds'][] = $sp_sfc['dat']['away'][4];
            $result[$i]['odds'][] = $sp_sfc['dat']['away'][5];

            $i++;
        }

        return $result;
    }

    /**
     * @desc 北单处理
     * @author LiBin
     * @param array $data
     * @param $type
     * @return array
     * @date 2019年5月13日
     * @updateBy CleverStone
     */
    public function oddSortBd($data, $type)
    {
        if (!$data) {
            $data = [];
        }

        $result = [];
        $i = 0;
        foreach ($data as $v) {
            // 赛事编号不存在直接跳过
            if (!$v['match_num']) {
                continue;
            }

            // 胜平负
            if ($type == 'spf') {
                $sp_spf = Helper::jsonDecode($v['sp_spf']);
                $result[$i]['match_num'] = $v['match_num'];
                $result[$i]['league_name'] = $v['league_name'];
                $result[$i]['host_name'] = $v['host_name'];
                $result[$i]['guest_name'] = $v['guest_name'];
                $result[$i]['host_icon'] = $v['host_icon'];
                $result[$i]['guest_icon'] = $v['guest_icon'];
                $result[$i]['rqs'] = $sp_spf ? $sp_spf['H'] : '0';//让球数
                empty($v['cutoff_time']) ? $result[$i]['cutoff_time'] = $v['sys_cutoff_time'] : $result[$i]['cutoff_time'] = $v['cutoff_time'];
                $result[$i]['cutoff_time'] = date('m-d H:i', strtotime($result[$i]['cutoff_time']));
                $result[$i]['jc_num'] = $v['jc_num'];
                // 判断赔率是否存在
                if ($sp_spf) {
                    if ($sp_spf['H'] == 0) {
                        $result[$i]['odds'][] = $sp_spf['W'];
                        $result[$i]['odds'][] = $sp_spf['D'];
                        $result[$i]['odds'][] = $sp_spf['L'];
                        $result[$i]['odds'][] = '0';
                        $result[$i]['odds'][] = '0';
                        $result[$i]['odds'][] = '0';
                    } else {
                        $result[$i]['odds'][] = '0';
                        $result[$i]['odds'][] = '0';
                        $result[$i]['odds'][] = '0';
                        $result[$i]['odds'][] = $sp_spf['W'];
                        $result[$i]['odds'][] = $sp_spf['D'];
                        $result[$i]['odds'][] = $sp_spf['L'];
                    }
                } else {
                    // 不存在赔率
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                }

                $i++;
            }

            // 进球数
            if ($type == 'jqs') {
                $jqs = Helper::jsonDecode($v['sp_jqs']);
                $result[$i]['match_num'] = $v['match_num'];
                $result[$i]['league_name'] = $v['league_name'];
                $result[$i]['host_name'] = $v['host_name'];
                $result[$i]['guest_name'] = $v['guest_name'];
                $result[$i]['host_icon'] = $v['host_icon'];
                $result[$i]['guest_icon'] = $v['guest_icon'];
                empty($v['cutoff_time']) ? $result[$i]['cutoff_time'] = $v['sys_cutoff_time'] : $result[$i]['cutoff_time'] = $v['cutoff_time'];
                $result[$i]['cutoff_time'] = date('m-d H:i', strtotime($result[$i]['cutoff_time']));
                $result[$i]['jc_num'] = $v['jc_num'];
                if ($jqs) {
                    $result[$i]['odds'][] = $jqs['dat']['zero'];
                    $result[$i]['odds'][] = $jqs['dat']['one'];
                    $result[$i]['odds'][] = $jqs['dat']['two'];
                    $result[$i]['odds'][] = $jqs['dat']['three'];
                    $result[$i]['odds'][] = $jqs['dat']['four'];
                    $result[$i]['odds'][] = $jqs['dat']['five'];
                    $result[$i]['odds'][] = $jqs['dat']['six'];
                    $result[$i]['odds'][] = $jqs['dat']['seven'];
                } else {
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                }

                $i++;
            }

            // 比分
            if ($type == 'bf') {
                $bf = Helper::jsonDecode($v['sp_bf']);
                $result[$i]['match_num'] = $v['match_num'];
                $result[$i]['league_name'] = $v['league_name'];
                $result[$i]['host_name'] = $v['host_name'];
                $result[$i]['guest_name'] = $v['guest_name'];
                $result[$i]['host_icon'] = $v['host_icon'];
                $result[$i]['guest_icon'] = $v['guest_icon'];
                empty($v['cutoff_time']) ? $result[$i]['cutoff_time'] = $v['sys_cutoff_time'] : $result[$i]['cutoff_time'] = $v['cutoff_time'];
                $result[$i]['cutoff_time'] = date('m-d H:i', strtotime($result[$i]['cutoff_time']));
                $result[$i]['jc_num'] = $v['jc_num'];
                if ($bf) {
                    $result[$i]['odds'][] = $bf['dat']['0100'];
                    $result[$i]['odds'][] = $bf['dat']['0200'];
                    $result[$i]['odds'][] = $bf['dat']['0201'];
                    $result[$i]['odds'][] = $bf['dat']['0300'];
                    $result[$i]['odds'][] = $bf['dat']['0301'];
                    $result[$i]['odds'][] = $bf['dat']['0302'];
                    $result[$i]['odds'][] = $bf['dat']['0400'];
                    $result[$i]['odds'][] = $bf['dat']['0401'];
                    $result[$i]['odds'][] = $bf['dat']['0402'];
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = $bf['dat']['-1-h'];
                    $result[$i]['odds'][] = $bf['dat']['0000'];
                    $result[$i]['odds'][] = $bf['dat']['0101'];
                    $result[$i]['odds'][] = $bf['dat']['0202'];
                    $result[$i]['odds'][] = $bf['dat']['0303'];
                    $result[$i]['odds'][] = $bf['dat']['-1-d'];
                    $result[$i]['odds'][] = $bf['dat']['0001'];
                    $result[$i]['odds'][] = $bf['dat']['0002'];
                    $result[$i]['odds'][] = $bf['dat']['0102'];
                    $result[$i]['odds'][] = $bf['dat']['0003'];
                    $result[$i]['odds'][] = $bf['dat']['0103'];
                    $result[$i]['odds'][] = $bf['dat']['0203'];
                    $result[$i]['odds'][] = $bf['dat']['0004'];
                    $result[$i]['odds'][] = $bf['dat']['0104'];
                    $result[$i]['odds'][] = $bf['dat']['0204'];
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = $bf['dat']['-1-l'];
                } else {
                    // 赔率未刷新
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                }

                $i++;
            }

            // 半全场
            if ($type == 'bqc') {
                $bqc = Helper::jsonDecode($v['sp_bqc']);
                $result[$i]['match_num'] = $v['match_num'];
                $result[$i]['league_name'] = $v['league_name'];
                $result[$i]['host_name'] = $v['host_name'];
                $result[$i]['guest_name'] = $v['guest_name'];
                $result[$i]['host_icon'] = $v['host_icon'];
                $result[$i]['guest_icon'] = $v['guest_icon'];
                empty($v['cutoff_time']) ? $result[$i]['cutoff_time'] = $v['sys_cutoff_time'] : $result[$i]['cutoff_time'] = $v['cutoff_time'];
                $result[$i]['cutoff_time'] = date('m-d H:i', strtotime($result[$i]['cutoff_time']));
                $result[$i]['jc_num'] = $v['jc_num'];
                if ($bqc) {
                    $result[$i]['odds'][] = $bqc['dat']['ww'];
                    $result[$i]['odds'][] = $bqc['dat']['wd'];
                    $result[$i]['odds'][] = $bqc['dat']['wl'];
                    $result[$i]['odds'][] = $bqc['dat']['dw'];
                    $result[$i]['odds'][] = $bqc['dat']['dd'];
                    $result[$i]['odds'][] = $bqc['dat']['dl'];
                    $result[$i]['odds'][] = $bqc['dat']['lw'];
                    $result[$i]['odds'][] = $bqc['dat']['ld'];
                    $result[$i]['odds'][] = $bqc['dat']['ll'];
                } else {
                    // 赔率未刷新
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                    $result[$i]['odds'][] = '0';
                }

                $i++;
            }
        }

        return $result;
    }
}