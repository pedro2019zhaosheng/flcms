<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/4
 * Time: 13:02
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

return [

    // +----------------------------------------------------------------------
    // | 分页设置
    // +---------------------------------------------------------------------

    // 分页类
    'type'      => '\\app\\common\\page\\Uidriver',
    // 分页变量
    'var_page'  => 'page',
    // 每页数量
    'list_rows' => 10,
    // angular js事件方法
    'js_var' => 'getTpPage'
];