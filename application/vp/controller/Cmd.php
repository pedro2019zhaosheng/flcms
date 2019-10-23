<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/15
 * Time: 13:57
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\vp\controller;

use app\common\VpController;

/**
 * 执行足彩数据爬取
 *
 * Class Cmd
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Cmd extends VpController
{
    /**
     * 执行爬取足彩数据命令
     *
     * -- 该接口已被crontab服务取代
     * -- 该接口只用于接口测试
     *
     * @return string
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function zc()
    {
        exit(0);
    }

    /**
     * 该接口用于测试
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function test()
    {
        exit(0);
    }
}