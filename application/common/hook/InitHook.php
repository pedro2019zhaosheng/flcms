<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/6
 * Time: 18:09
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

namespace app\common\hook;

use think\Request;

class InitHook
{
    /**
     * 初始化钩子
     *
     * @param Request $request // 当前request对象
     * @param $params // 参数
     * @author CleverStone
     * @github https://www.github.com/cleverstone
     * @blog https://cnblogs.com/hellow-world
     * @api *
     */
    public function run(Request $request, $params)
    {
        // 定义常用常量
        defined("DS") or define("DS", DIRECTORY_SEPARATOR);
        defined("APP_PATH") or define("APP_PATH", dirname(dirname(__DIR__)) . DS);
        $rootPath = dirname(APP_PATH);
        if (!strcmp($rootPath, '/')) {
            defined("ROOT_PATH") or define("ROOT_PATH", "/");
        } else {
            defined("ROOT_PATH") or define("ROOT_PATH", $rootPath . DS);
        }

        defined("PUBLIC_PATH") or define("PUBLIC_PATH", ROOT_PATH . 'public' . DS);
        defined("UPLOAD_PATH") or define("UPLOAD_PATH", PUBLIC_PATH . 'uploads' . DS);
        defined("RUN_TIME") or define("RUN_TIME", ROOT_PATH . 'runtime' . DS);
        defined("EXCEL_PATH") or define("EXCEL_PATH", RUN_TIME . 'excels' . DS);
    }
}