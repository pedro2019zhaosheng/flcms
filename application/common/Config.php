<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/20
 * Time: 17:34
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common;

/**
 * 项目配置
 *
 * Class Config
 * @package app\common
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Config
{
    // +----------------------------------------------------------------------
    // | 彩种代码配置
    // +----------------------------------------------------------------------

    // 足彩代码
    const ZC_CODE = 'ZC';
    // 篮彩代码
    const LC_CODE = 'LC';
    // 北京单场代码
    const BJ_CODE = 'BJ';
    // 排列三代码
    const P3_CODE = 'PL3';
    // 排列五代码
    const P5_CODE = 'PL5';
    // 澳彩
    const AO_CODE = 'AC';
    // 葡彩
    const PC_CODE = 'PC';
    //幸运飞艇
    const FT_CODE = 'FT';

    // +----------------------------------------------------------------------
    // | worker推送配置
    // +----------------------------------------------------------------------

    // worker监听推送地址和端口
    const SOCKET_IO_URL = 'http://112.124.201.163:2121/';

    // +----------------------------------------------------------------------
    // | 自动风控配置
    // +----------------------------------------------------------------------

    // 澳彩自动风控配置变量
    const AO_CAI = 'ao_auto_risk';
    // 葡彩自动风控配置变量
    const PU_CAI = 'pu_auto_risk';

    // +----------------------------------------------------------------------
    // | 数字彩截止时间
    // +----------------------------------------------------------------------

    // 排三,排五截止时间(H:i)
    const P3P5_SHUTDOWN_TIME = '20:20';
    // 澳彩,普彩提前截止时间(s)
    const AO_PU_SHUTDOWN_TIME = 10;
    // 幸运飞艇提前截止时间(s)
    const XYFT_SHUTDOWN_TIME = 30;


    //邀请二维码域名
    const PictureHost = 'http://iysdh.com/invite.html';
}