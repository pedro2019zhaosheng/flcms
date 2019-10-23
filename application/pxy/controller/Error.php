<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/11
 * Time: 15:59
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\pxy\controller;

use app\common\PxyController;
use think\Request;

/**
 * 空控制器方法
 *
 * Class Error
 * @package app\vp\controller
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class Error extends PxyController
{
    /**
     * 当前控制器不存在时，执行的方法
     *
     * @param Request $request
     * @return mixed
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function index(Request $request)
    {
        $msg = '您当前访问的链接不存在';
        $options = [
            'title' => '空白提示页',
            'nav' => [
                ['title' => '操作提示', 'active' => true],
            ],
            'msg' => $msg,
        ];

        return $this->fetch('view/error/index', $options);
    }
}