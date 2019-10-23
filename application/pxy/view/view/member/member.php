<div class="container-fluid" ng-controller="memberCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="btn-group row">
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#member-add">新增会员</button>
                            <button type="button" ng-click="batchHandle(1)" class="btn btn-sm btn-success">批量正常</button>
                            <button type="button" ng-click="batchHandle(0)" class="btn btn-sm btn-warning">批量冻结</button>
                            <button type="button" ng-click="exportMember()" class="btn btn-sm btn-secondary">导出Excel</button>
                        </div>
                    </form>
                </div>

                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label>日期:</label>
                            <input type="text" name="from" placeholder="开始日期" autocomplete="off" ng-model="startDate" class="form-control form-control-sm mx-sm-3" id="from">
                            <label for="to">至</label>
                            <input type="text" name="to" placeholder="结束日期" autocomplete="off" ng-model="endDate" class="form-control form-control-sm mx-sm-3" id="to">
                            <label for="username">账号:</label>
                            <input id="username" name="username" type="text" placeholder="请输入会员账号" autocomplete="off" ng-model="username" class="form-control form-control-sm mx-sm-3">
                        </div>

                        <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning mx-3">清空</button>
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
                                <th>昵称</th>
                                <th>头像</th>
                                <th>账号</th>
                                <th>上级</th>
                                <th>总充值</th>
                                <th>总提现</th>
                                <th>总输赢</th>
                                <th>可提现余额</th>
                                <th>彩金</th>
                                <th>冻结资金</th>
                                <th>推荐会员数</th>
                                <th>注册时间</th>
                                <th>最后登录时间</th>
                                <th>最后登录IP</th>
                                <th>状态</th>
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
                                <td ng-bind="member.chn_name"></td>
                                <td><img style="width: 2rem; height: 2rem;" ng-src="{{ member.photo | default : '/static/lib/images/admin.png' }}" alt=""></td>
                                <td ng-bind="member.username"></td>
                                <td ng-bind="member.top_username"></td>
                                <td ng-bind="member.recharge" ng-if="member.recharge==0" class="font-style cp"></td>
                                <td ng-bind="member.recharge" ng-if="member.recharge!=0" ng-click="rechargeList(member.username,1)" class="font-style cp"></td><!--修改ng-if,ng-click参数-->
                                <td ng-bind="member.withdraw_deposit" ng-if="member.withdraw_deposit==0" class="font-style cp"></td>
                                <td ng-bind="member.withdraw_deposit" ng-click="withdrawList(member.username,1)" ng-if="member.withdraw_deposit != 0" class="font-style cp"></td><!--修改ng-if,ng-click参数-->
                                <td ng-bind="member.profit" class="font-style cp" ng-if="member.profit==0"></td>
                                <td ng-bind="member.profit" ng-click="winloseList(member.username,1)" ng-if="member.profit!=0" class="font-style cp"></td><!--修改ng-if,ng-click参数-->
                                <td ng-bind="member.balance"></td>
                                <td ng-bind="member.hadsel"></td>
                                <td ng-bind="member.frozen_capital"></td>
                                <td ng-if="member.RecUserNumber==0" ng-bind="member.RecUserNumber" class="font-style cp"></td>
                                <td ng-click="recommend(member.id)" ng-if="member.RecUserNumber!==0"  ng-bind="member.RecUserNumber" class="font-style cp"></td>
                                <td ng-bind="member.create_at"></td>
                                <td ng-bind="member.last_login_time| default : '无'"></td>
                                <td ng-bind="member.last_login_ip| default : '无'"></td>
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
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#up-agent" ng-click="getUpAgent(member.id)">提升代理</li>
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#transfer-member" ng-click="updataMemberMethod(member.id)">转移会员</li>
<!--                                                <li class="list-group-item dropdown-item cp text-danger"  data-toggle="modal" data-target="#setPassword-member" ng-click="setPasswordMethod(member.id)">修改密码</li>
-->                                                <li ng-if="member.frozen === 0" ng-click="toggle(member.id, 1)" class="list-group-item dropdown-item cp text-warning">解冻</li>
                                                <li ng-if="member.frozen === 1" ng-click="toggle(member.id, 0)" class="list-group-item dropdown-item cp text-success">冻结</li>
                                                <li class="list-group-item dropdown-item cp text-danger" ng-click="deleteMember(member.id)">删除会员</li>
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
</div>
