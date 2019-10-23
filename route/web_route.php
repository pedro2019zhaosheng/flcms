<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/16
 * Time: 16:33
 * Author CleverStone
 * Github https://www.github.com/cleverstone
 * Blog https://cnblogs.com/hellow-world
 */

// WEB注册页
Route::get('/web/reg', 'web/web_reg/index');
// 注册提交
Route::post('/web/submit', 'web/web_reg/summit');

return [];