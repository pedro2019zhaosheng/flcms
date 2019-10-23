<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/28
 * Time: 10:11
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common;

use think\Controller;

/**
 * 总基类控制器，直接继承think\Controller
 *
 * Class MainController
 * @package app\common
 * @author CleverStone
 * @github https://www.github.com/cleverstone
 * @blog https://cnblogs.com/hellow-world
 */
class MainController extends Controller
{
    /**
     * 请求中post参数
     *
     * @var null
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public $post = null;

    /**
     * 请求中get参数
     *
     * @var null
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public $get = null;

    /**
     * 请求中patch参数
     *
     * @var null
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public $patch = null;

    /**
     * 请求中put参数
     *
     * @var null
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public $put = null;

    /**
     * 请求中delete参数
     *
     * @var null
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public $delete = null;

    /**
     * 请求中所有参数
     *
     * @var null
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public $all = null;

    /**
     * 获取请求参数
     *
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function initialize()
    {
        parent::initialize();
        $this->post = input('post.');
        $this->get = input('get.');
        $this->patch = input('patch.');
        $this->put = input('put.');
        $this->delete = input('delete.');
        $this->all = input();
    }

    /**
     * Json响应格式
     *
     * 状态码code值：
     * -1：重定向，重定向登录页面。
     * 0： 接口正常，业务逻辑执行失败，描述：将返回失败提示。
     * 1： 接口正常，业务逻辑执行成功。
     *
     * @param int $code // -1，0，1
     * @param string $status
     * @param string $msg
     * @param string $data
     * @return \think\response\Json
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     */
    public function asJson($code = 1, $status = 'success', $msg = '请求成功', $data = '')
    {
        return json(compact('code', 'status', 'msg', 'data'));
    }
}