<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// | Title: 代理商后台接口路由文件
// +----------------------------------------------------------------------

//...
// 代理商后台登录路由
Route::get('/pxy/login', 'pxy/view/login');
// 代理商后台首页路由
Route::get('/', 'pxy/view/index');
// 代理商后台会员列表
Route::get('/pxy/member','pxy/view/member');
// 代理商后台推荐会员
Route::get('pxy/recoMember','pxy/view/recoMember');
// 代理商后台代理商列表
Route::get('/pxy/agent','pxy/view/agent');
// 代理商后台代理商返点设置
Route::get('/pxy/agentReturn','pxy/view/agentReturn');
// 代理商后台--代理商资金管理--充值记录
Route::get('/pxy/capital/recharge', 'pxy/view/capitalRecharge');
// 代理商后台--代理商资金管理--提现记录
Route::get('/pxy/capital/cash', 'pxy/view/capitalCash');
// 代理商后台--代理商资金管理--投注返佣记录
Route::get('/pxy/capital/rake_back', 'pxy/view/capitalRakeBack');
// 代理商后台--代理商资金管理--资金校正记录
Route::get('/pxy/capital/correction', 'pxy/view/capitalCorrection');
// 代理商后台--代理商资金管理--资金流水记录
Route::get('/pxy/capital/flow', 'pxy/view/capitalFlow');
// 代理商后台--代理商注单管理--注单列表
Route::get('/pxy/bet/list', 'pxy/view/betList');
// 代理商后台--代理商注单管理--注单详情
Route::get('/pxy/bet/detail', 'pxy/view/betDetail');
// 代理商后台--代理商注单管理--推单列表
Route::get('/pxy/bet/extend', 'pxy/view/betExtend');
// 代理商后台--代理商注单管理--跟单明细
Route::get('/pxy/bet/detailed', 'pxy/view/documentaryDetailed');
// 代理商后台--个人中心
Route::get('/pxy/personal', 'pxy/view/personal');
// 代充值
Route::get('/pxy/agentRecharge', 'pxy/view/agentRecharge');
return [];
