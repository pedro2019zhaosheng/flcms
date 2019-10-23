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
// | Title: 前台接口路由文件
// +----------------------------------------------------------------------

// app登录
Route::post('/api/login', 'api/login/login');

// app退出
Route::post('/api/logout', 'api/logout/index');

// app发送短信
Route::post('/api/send', 'api/sms/send');

//app 注册
Route::post('/api/register', 'api/login/register');

//通过邀请码注册
Route::post('/api/icRegister', 'api/login/icRegister');

//app 找回密码
Route::post('/api/forget', 'api/login/forget');

//app 修改密码
Route::post('/api/changePassword', 'api/login/changePassword');

//app 足彩列表
Route::get('/api/footlist', 'api/football/footList');

//app 足彩检索
Route::get('/api/searchfoot', 'api/football/searchFoot');

//app 单关
Route::get('/api/footDg', 'api/football/footListDG');

//app 验证登录
Route::post('/api/checkmember', 'api/member/checkMember');

//app 银行卡列表
Route::post('/api/cardList', 'api/member/bankCardList');

//app 默认银行卡
Route::post('/api/setDef', 'api/member/setBankDefault');

//app 新增银行卡
Route::post('/api/addCard', 'api/member/addBankCard');

//app 编辑银行卡
Route::post('/api/editCard', 'api/member/editBankCard');

//app 解绑银行卡
Route::post('/api/delCard', 'api/member/delBankCard');

//app 银行卡选择列表
Route::post('/api/bankList', 'api/member/bankList');

//app 获取默认银行卡
Route::get('/api/getDefaultBank','api/member/getDefaultBank');

// 用户提现申请接口
Route::post('/api/applyWithdeaw', 'api/withdraw/applyWithdeaw');

// App足彩下单接口
Route::post('/api/zcOrder', 'api/order/zcOrder');

//App足彩订单详情接口
Route::get('/api/zcOrderDetails/[:orderNum]', 'api/order/poDetail');

//APP用户推单接口
Route::Post('/api/pushOrder', 'api/order/pushOrder');

//APP获取用户的基本信息
Route::get('/api/getMember', 'api/Member/getMemberInfo');

//App实名认证
Route::post('/api/realName', 'api/Member/realName');

//APP订单购彩订单列表
Route::get('/api/orderList', 'api/Order/orderList');

//APP订单个人中奖列表
Route::get('/api/prizeRecord', 'api/Order/prizeRecord');

//APP购彩推单列表
Route::get('/api/pushOrderList', 'api/Order/pushOrderList');

//APP跟单
Route::post('/api/tailOrder', 'api/Order/tailOrder');

//APP我的推单
Route::get('/api/myPush', 'api/Order/myPush');

// APP一周红榜单
Route::get('/api/redList', 'api/compute/redList');

//APP个人账单明细
Route::get('/api/billing',   'api/Member/billingDetails');

// APP红单详情
Route::get('/api/redDetail/[:uid]', 'api/compute/redDetail');

// APP关注
Route::get('/api/attention/[:uid]', 'api/compute/attention');

//APP取关
Route::get('/api/cancelAttention/[:attentionId]','api/compute/cancelAttention');

//APP我的关注列表
Route::get('/api/myAttention','api/compute/myAttention');

//APP连红榜
Route::get('/api/evenRed','api/compute/evenRed');

//APP盈利榜
Route::get('/api/profit','api/compute/profit');

//APP首页
Route::get('/api/index','api/Index/index');

//APP获取马甲包banner
Route::get('/api/getBanner','api/Index/getBanner');

//彩种列表
Route::get('/api/getLottery','api/Index/getLottery');

//APP首页热门赛事
Route::get('/api/hostFoot','api/Index/hostFootData');

//APP首页大神推单
Route::get('/api/goodPush','api/Index/goodPush');

//APP我的推广
Route::get('/api/myGenerailze','api/Member/myGenerailze');

//APP热门搜索列表
Route::get('/api/hotSearch','api/Member/hotSearch');

//APP搜索历史记录列表
Route::get('/api/historyRecord','api/Member/historyRecord');

//APP删除历史记录列表
Route::post('/api/delHistory','api/Member/delHistory');

//APP搜索列表
Route::post('/api/checkList','api/Member/checkList');

//APP清空搜索记录
Route::post('/api/clearHistory','api/Member/clearHistory');

//APP客服二维码
Route::get('/api/service','api/Member/service');

//APP修改图片
Route::post('/api/setPicture','api/Member/setPicture');

//APP编辑资料
Route::post('/api/setChnName','api/Member/setChnName');

//APP版本检测
Route::get('/api/checkVersions','api/Login/checkVersions');

//APP开关
Route::get('/api/switch','api/Login/switch');

//开奖公告
Route::get('/api/openLottery','api/Index/openLottery');

//竞彩足球开奖历史
Route::get('/api/jczqHistoryLottery','api/Index/jczqHistoryLottery');

//竞彩篮球开奖历史
Route::get('/api/jclqHistoryLottery','api/Index/jclqHistoryLottery');

//北京单场开奖历史
Route::get('/api/jcdcHistoryLottery','api/Index/jcdcHistoryLottery');

//--------------篮彩接口----------------#

//篮彩列表
Route::get('/api/basketList','api/Basketball/basketList');

//篮彩单关
Route::get('/api/basketDg','api/Basketball/basketDg');

//篮彩搜索
Route::get('/api/searchBasket','api/Basketball/searchBasket');

//--------------北京单场接口----------------#

//北京单场列表
Route::get('/api/bjSingList','api/BeijingSingle/bjSingList');

//北京单场赛事检索
Route::get('/api/searchbjSing','api/BeijingSingle/searchbjSing');

//--------------数字彩接口----------------#

// 排三,排五获取期号
Route::get('/api/cq', 'api/number/number');

// 排三下单
Route::post('/api/nlOrder','api/Order/nlOrder');

//排三订单详情
Route::get('/api/nlpoDetail/[:orderNum]','api/Order/nlpoDetail');

//排三推单
Route::post('/api/nlPushOrder','api/order/nlPushOrder');

// 追期号码获取
Route::get('/api/futureNum','api/number/futureNumber');

//排三/澳彩历史记录
Route::get('/api/p3HistoryLottery','api/index/p3HistoryLottery');

//排五/葡彩历史记录
Route::get('/api/p5HistoryLottery','api/index/p5HistoryLottery');

//葡彩澳彩下单
Route::post('/api/plOrder','api/order/plOrder');

// 澳彩新闻列表
Route::get('/api/AoNewsList','api/news/AoNewsList');

// 葡彩新闻列表
Route::get('/api/PuNewsList','api/news/PuNewsList');

// 澳彩 葡彩新闻详情
Route::get('/api/getNewsList','api/news/getNewsList');

// 获取葡彩澳彩的开奖时间和期号
Route::get('/api/getNumberIsuue','api/index/getNumberIsuue');

//幸运飞艇下单
Route::post('/api/ftOrder','api/order/ftOrder');

// 幸运飞艇获取当前期和上一期数据
Route::get('/api/xyftBeforeData', 'api/number/xyftBeforeData');

// 幸运飞艇获取历史数据
Route::get('/api/xyftHistoryData', 'api/number/xyftHistoryData');

// 充值下单统一接口
Route::rule('/api/recharge', 'api/pay/pay');

// 场馆列表
Route::get('/api/stadiumList','api/stadium/stadiumList');

// 场馆详情
Route::get('/api/stadiumDetail','api/stadium/stadiumDetail');

// 场馆类型
Route::get('/api/stadiumTypeList','api/stadium/stadiumTypeList');

return [];
