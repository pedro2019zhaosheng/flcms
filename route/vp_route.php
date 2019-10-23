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
// | Title: 总后台接口路由文件
// +----------------------------------------------------------------------

//...
// 总后台登录路由
Route::get('/vp/login', 'vp/view/login');
// 总后台首页路由
Route::get('/vp/', 'vp/view/index');
// 总后台管理员管理
Route::get('/vp/admin', 'vp/view/admin');
// 个人中心
Route::get('/vp/admin/detail', 'vp/view/adminDetail');
// 总后台会员管理
Route::get('/vp/member', 'vp/view/member');
//推荐会员
Route::get('vp/recoMember', 'vp/view/recoMember');
//虚拟账户管理
Route::get('/vp/simulation', 'vp/view/simulation');
//总后台代理商列表
Route::get('/vp/agent', 'vp/view/agent');
//总后台代理商返点设置
Route::get('/vp/agentReturn', 'vp/view/agentReturn');
//总后台--代理返点设置
Route::get('/vp/agent/return', 'vp/view/agentReturn');
//总后台--资金管理--充值记录
Route::get('/vp/capital/recharge', 'vp/view/capitalRecharge');
//总后台--资金管理--提现记录
Route::get('/vp/capital/cash', 'vp/view/capitalCash');
//总后台--资金管理--投注返佣记录
Route::get('/vp/capital/rake_back', 'vp/view/capitalRakeBack');
//总后台--资金管理--资金校正记录
Route::get('/vp/capital/correction', 'vp/view/capitalCorrection');
//总后台--资金管理--资金流水记录
Route::get('/vp/capital/flow', 'vp/view/capitalFlow');
//总后台--注单管理--注单列表
Route::get('/vp/bet/list', 'vp/view/betList');
//总后台--注单管理--注单详情
Route::get('/vp/bet/detail', 'vp/view/betDetail');
//总后台--注单管理--推单列表
Route::get('/vp/bet/extend', 'vp/view/betExtend');
//总后台--注单管理--跟单明细
Route::get('/vp/bet/detailed', 'vp/view/documentaryDetailed');

//总后台--开奖管理--赛事开奖
Route::get('/vp/lottery/match', 'vp/view/lotteryMatch');
//总后台--开奖管理--数字彩开奖
Route::get('/vp/num_draw', 'vp/view/numDraw');
//总后台--开奖管理--手动派奖
Route::get('/vp/lottery/manual', 'vp/view/lotteryManual');

//总后台--彩种管理--彩种列表
Route::get('/vp/types/lottery', 'vp/view/lottery');
//总后台--彩种管理--竞彩足球
Route::get('/vp/types/football', 'vp/view/football');
//总后台--彩种管理--竞彩篮球
Route::get('/vp/types/basketball', 'vp/view/basketball');
//总后台--彩种管理--竞彩篮球
Route::get('/vp/types/basketball/match', 'vp/view/basketballMatch');
//总后台--彩种管理--竞彩篮球
Route::get('/vp/types/basketball/edit', 'vp/view/basketballEdit');
//总后台--彩种管理--北京单场
Route::get('/vp/types/beijing', 'vp/view/beijing');
//总后台--彩种管理--北京单场--赛事查看
Route::get('/vp/types/beijing/match', 'vp/view/beijingMatch');
//总后台--彩种管理--北京单场--赛事编辑
Route::get('/vp/types/beijing/edit', 'vp/view/beijingEdit');

// 角色管理
Route::get('/vp/role', 'vp/view/role');
// 节点管理
Route::get('/vp/node', 'vp/view/node');
//广告管理
Route::get('/vp/adver', 'vp/view/adver');
//广告回收站
Route::get('/vp/adverRecycle', 'vp/view/adverRecycle');
//广告类型
Route::get('/vp/adverType', 'vp/view/adverType');
//新闻管理
Route::get('/vp/news', 'vp/view/news');
//新闻回收站
Route::get('/vp/newsRecycle', 'vp/view/newsRecycle');
//新闻类型
Route::get('/vp/newsType', 'vp/view/newsType');
//场馆管理
Route::get('/vp/stadium', 'vp/view/stadium');
//场馆回收站
Route::get('/vp/stadiumRecycle', 'vp/view/stadiumRecycle');
//场馆类型
Route::get('/vp/stadiumType', 'vp/view/stadiumType');
// 系统设置
Route::get('/vp/site/config', 'vp/view/siteConfig');
// 客服设置
Route::get('/vp/site/service', 'vp/view/siteService');
//短信日志
Route::get('/vp/sms/log', 'vp/view/smslog');
//系统日志
Route::get('/vp/system/log', 'vp/view/systemLog');
//足彩爬取日志
Route::get('/vp/patch', 'vp/view/czLog');
// 消息列表
Route::get('/vp/msg', 'vp/view/msg');
// 代充值
Route::get('/vp/agentRecharge', 'vp/view/agentRecharge');
// 代提现
Route::get('/vp/agentWithdraw', 'vp/view/agentWithdraw');
// 风险控制
Route::get('/vp/risk', 'vp/view/risk');
return [];
