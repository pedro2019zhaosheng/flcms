<div class="container-fluid" ng-controller="agentCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="btn-group row">
                            <button type="button" ng-click="batchHandle(1)" class="btn btn-sm btn-success">批量启用</button>
                            <button type="button" ng-click="batchHandle(0)" class="btn btn-sm btn-warning">批量禁用</button>
                            <button type="button" class="btn btn-secondary btn-sm" ng-click="export()">导出Excel</button>
                        </div>
                    </form>
                </div>

                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label for="phone" class="col-form-label">账号:</label>
                            <input type="text" id="phone" name="phone" placeholder="请输入手机号" ng-model="phone" class="form-control form-control-sm mx-sm-3">

                            <label for="nickname" class="col-form-label">昵称:</label>
                            <input type="text" id="nickname" name="nickname" placeholder="请输入昵称" ng-model="nickname" class="form-control form-control-sm mx-sm-3">

                            <label for="userstate" class="col-form-label">状态:</label>
                            <div class="mx-sm-3 form-control-sm" style="width: 150px;">
                                <select class="form-control form-control-sm select2" id="userstate" name="userstate">
                                    <option value="">请选择</option>
                                    <option value="0">禁用</option>
                                    <option value="1">启用</option>
                                </select>
                            </div>
                        </div>

                        <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning ml-2 mr-1">清空</button>
                        <button type="button" ng-click="searchSubmit()" class="btn btn-sm btn-primary">搜索</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>
                                    <div class="checkbox checkbox-primary">
                                        <input id="checkAll" name="checkAll" class="styled" type="checkbox">
                                        <label for="checkAll" class="position_lable"></label>
                                    </div>
                                </th>
                                <th>昵称</th>
                                <th>账号</th>
                                <th>上级代理</th>
                                <th>总充值</th>
                                <th>总输赢</th>
                                <th>余额</th>
                                <th>彩金</th>
                                <th>推荐会员数</th>
                                <th>注册时间</th>
                                <th>最后登录时间</th>
                                <th>最后登录ip</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="item in agentList" on-finish-render="ngRepeatFinished">
                                <td class="check-child">
                                    <div class="checkbox checkbox-primary">
                                        <input data-value="{{ item.id }}" id="lotCheckbox" class="styled" type="checkbox">
                                        <label for="lotCheckbox" class="position_lable"></label>
                                    </div>
                                </td>
                                <td ng-bind="item.chn_name | default : '无'"></td>
                                <td ng-bind="item.username | default : '无'"></td>
                                <td ng-bind="item.top_username | default : '0'"></td>
                                <td ng-bind="item.recharge" ng-if="item.recharge==0" class="font-style cp"></td>
                                <td ng-bind="item.recharge" ng-click="rechargeList(item.username,1)" ng-if="item.recharge!=0" class="font-style cp"></td><!--修改ng-if,ng-click参数-->
                                <td ng-bind="item.profit" class="font-style cp" ng-if="item.profit==0"></td>
                                <td ng-bind="item.profit" ng-click="winloseList(item.username,1)" ng-if="item.profit!=0" class="font-style cp"></td><!--修改ng-if,ng-click参数-->
                                <td ng-bind="item.balance | default : '0.00'"></td>
                                <td ng-bind="item.hadsel | default : '0.00'"></td>
                                <td ng-if="item.RecUserNumber==0" ng-bind="item.RecUserNumber" class="font-style cp"></td>
                                <td ng-click="recommend(item.id)" ng-if="item.RecUserNumber!==0"  ng-bind="item.RecUserNumber" class="font-style cp"></td>
                                <td ng-bind="item.create_at"></td>
                                <td>{{ item.last_login_time | default : '无'}}</td>
                                <td>{{ item.last_login_ip | default : '无'}}</td>
                                <td>
                                    <span ng-if="item.frozen === 0" class="badge badge-danger ng-scope">禁用</span>
                                    <span ng-if="item.frozen === 1" class="badge badge-success ng-scope">启用</span>
                                </td>
                                <td>
                                    <div class="dropleft">
                                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-fw fa-cogs" aria-label="icon"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <ul class="list-group">
                                                <li class="list-group-item dropdown-item cp"  data-toggle="modal" data-target="#transfer-member" ng-click="updataAgentMethod(item.id)">转移会员</li>
                                                <li class="list-group-item dropdown-item cp text-danger" data-toggle="modal" data-target="#delete-agent" ng-click="deleteAgent(item.id)">删除</li>
                                                <li ng-if="item.frozen === 0" ng-click="toggle(item.id, 1)" class="list-group-item dropdown-item cp text-warning">解冻</li>
                                                <li ng-if="item.frozen === 1" ng-click="toggle(item.id, 0)" class="list-group-item dropdown-item cp text-success">冻结</li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="agentNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div>
        </div>
    </div>
    <!--转移会员模态框-->
    {include file="view/agent/member" /}
    <!--修改会员密码模态框-->
    {include file="view/agent/changeMember" /}
</div>
