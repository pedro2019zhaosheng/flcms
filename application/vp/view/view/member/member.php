<div class="container-fluid" ng-controller="memberCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline">
                        <div class="btn-group">
                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/member/add')): ?>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#member-add">添加会员</button>
                            <?php endif; ?>

                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/member/toggles')): ?>
                            <button type="button" ng-click="batchHandle(1)" class="btn btn-sm btn-success">批量正常</button>
                            <button type="button" ng-click="batchHandle(0)" class="btn btn-sm btn-warning">批量冻结</button>
                            <?php endif; ?>

                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/member/export')): ?>
                            <button type="button" ng-click="exportMember()" class="btn btn-sm btn-secondary">导出Excel</button>
                            <?php endif; ?>

                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/member/upAllQRcode')): ?>
                                <button type="button" data-toggle="modal" data-target="#update-qr" class="btn btn-sm btn-warning">更新邀请码</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline">
                        <div class="form-group row pl-2">
                            <label>注册日期:</label>
                            <input type="text" name="from" placeholder="开始日期"  ng-model="startDate" class="form-control form-control-sm mx-sm-3" id="from">

                            <label for="to">至</label>
                            <input type="text" name="to" placeholder="结束日期"  ng-model="endDate" class="form-control form-control-sm mx-sm-3" id="to">

                            <label for="keyword">昵称:</label>
                            <input id="keyword" name="keyword" type="text"  ng-model="keyword" class="form-control form-control-sm mx-sm-3" placeholder="请输入昵称">

                            <label for="username">账号:</label>
                            <input id="username" name="username" type="text"  ng-model="username" class="form-control form-control-sm mx-sm-3" placeholder="请输入账号">
                        </div>

                        <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning mx-1">清空</button>
                        <button type="button" ng-click="searchSubmit()" class="btn btn-sm btn-primary">搜索</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th class="check-parent">
                                    <div class="checkbox checkbox-primary cp">
                                        <input id="checkAll" name="checkAll" class="styled" type="checkbox">
                                        <label for="checkAll" class="position_lable"></label>
                                    </div>
                                </th>
                                <th>头像</th>
                                <th>账号</th>
                                <th>昵称</th>
                                <th>上级</th>
                                <th>总充值</th>
                                <th>总提现</th>
                                <th>总输赢</th>
                                <th>余额</th>
                                <th>彩金</th>
                                <th>冻结资金</th>
                                <th>推荐会员数</th>
                                <th>会员状态</th>
                                <th>操作</th></tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="member in memberList" on-finish-render="ngRepeatFinished">
                                <td class="check-child">
                                    <div class="checkbox checkbox-primary">
                                        <input data-value="{{ member.id }}" id="lotCheckbox" class="styled" type="checkbox">
                                        <label for="lotCheckbox" class="position_lable"></label>
                                    </div>
                                </td>
                                <td><img style="width: 2rem; height: 2rem;" ng-src="{{ member.photo | default : '/static/lib/images/msgicon.jpg' }}" alt=""></td>
                                <td ng-bind="member.username | default : '无'"></td>
                                <td ng-bind="member.chn_name | default : '无'"></td>
                                <td ng-bind="member.top_username | default : '无'"></td>
                                <td ng-if="member.recharge == 0" ng-bind="member.recharge | currency : '￥'" class="text-danger"></td>
                                <td ng-if="member.recharge != 0" ng-click="rechargeList(member.username,1)" class="font-style cp">
                                    <u ng-bind="member.recharge | currency : '￥'"></u>
                                </td>
                                <td ng-if="member.withdraw_deposit == 0" ng-bind="member.withdraw_deposit | currency : '￥'" class="text-danger"></td>
                                <td ng-if="member.withdraw_deposit != 0" ng-click="withdrawList(member.username,1)" class="font-style cp">
                                    <u ng-bind="member.withdraw_deposit | currency : '￥'"></u>
                                </td>
                                <td ng-bind="member.profit | currency : '￥'" class="text-danger"></td>
                                <!--<td ng-if="member.profit != 0" ng-click="winloseList(member.username,1)" class="font-style cp">
                                    <u ng-bind="member.profit | currency : '￥'"></u>
                                </td>-->
                                <td ng-bind="member.balance | currency : '￥'" class="text-danger"></td>
                                <td ng-bind="member.hadsel | currency : '￥'" class="text-danger"></td>
                                <td ng-bind="member.frozen_capital | currency : '￥'" class="text-danger"></td>
                                <td ng-if="member.RecUserNumber == 0" ng-bind="member.RecUserNumber"></td>
                                <td ng-if="member.RecUserNumber !== 0" ng-click="recommend(member.id)" class="font-style cp">
                                    <u ng-bind="member.RecUserNumber"></u>
                                </td>
                                <td>
                                    <span ng-if="member.frozen === 1" class="badge badge-success">正常</span>
                                    <span ng-if="member.frozen === 0" class="badge badge-danger">冻结</span>
                                </td>
                                <td>
                                    <div class="dropleft">
                                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-fw fa-cogs" aria-label="icon"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <ul class="list-group">
                                                <!--此功能不用添加权限管理-->
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#user-detail" ng-click="getUserDetail(member.id)">查看更多</li>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/member/upAgent')): ?>
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#up-agent" ng-click="getUpAgent(member.id)">提升代理</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/member/transferMember')): ?>
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#transfer-member" ng-click="updataMemberMethod(member.id)">转移会员</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/member/reviseGold')): ?>
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#revise-gold" ng-click="updataMemberMethod(member.id)">修改彩金</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/member/reviseBalance')): ?>
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#revise-balance" ng-click="updataMemberMethod(member.id)">修改余额</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/member/setPassword')): ?>
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#setPassword-member" ng-click="setPasswordMethod(member.id)">修改密码</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/member/updataMember')): ?>
                                                    <li class="list-group-item dropdown-item cp" ng-click="getMemberinfo(member.id)">修改会员</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/member/addCard')): ?>
                                                    <li class="list-group-item dropdown-item cp" ng-click="addMembercard(member.id)">添加银行卡</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/member/card')): ?>
                                                    <li class="list-group-item dropdown-item cp" ng-click="getMembercard(member.id)">修改银行卡</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/member/toggle')): ?>
                                                <li ng-if="member.frozen === 0" ng-click="toggle(member.id, 1)" class="list-group-item dropdown-item cp">解冻</li>
                                                <li ng-if="member.frozen === 1" ng-click="toggle(member.id, 0)" class="list-group-item dropdown-item cp">冻结</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/member/deletMember')): ?>
                                                <li class="list-group-item dropdown-item cp" ng-click="deleteMember(member.id)">删除会员</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="memberNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div><!-- end card-->
        </div>
    </div>
    <!--新增会员模态框-->
    {include file="view/member/add" /}
    <!--提升为代理模态框-->
    {include file="view/member/upAgent" /}
    <!--转移会员模态框-->
    {include file="view/member/transferMember" /}
    <!--修改会员密码模态框-->
    {include file="view/member/changeMember" /}
    <!--修改彩金模态框-->
    {include file="view/member/gold" /}
    <!--修改余额模态框-->
    {include file="view/member/balance" /}
    <!--修改基本信息模态框-->
    {include file="view/member/updata" /}
    <!--修改身份证模态框-->
    {include file="view/member/card" /}
    <!--添加银行卡模态框-->
    {include file="view/member/addCard" /}
    <!--更新二维码模态框-->
    {include file="view/member/updateQr" /}
    <!--会员详情-->
    {include file="view/member/memberDetail" /}
</div>
