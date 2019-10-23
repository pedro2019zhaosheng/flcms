<div class="container-fluid" ng-controller="capitalRechargeCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="btn-group row">
                            <button type="button" ng-click="export()" class="btn btn-sm btn-secondary">导出Excel</button>
                        </div>
                    </form>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label for="payWay" class="col-form-label">充值方式:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <select id="payWay" name="payWay" class="form-control form-control-sm">
                                    <option value="">请选择</option>
                                    <option value="1">支付宝</option>
                                    <option value="2">微信</option>
                                    <option value="3">网银支付</option>
                                    <option value="4">代充值</option>
                                    <option value="5">快捷支付</option>
                                </select>
                            </div>

                            <label for="usersType" class="col-form-label">账号类型:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <select id="usersType" name="usersType" class="form-control form-control-sm select2 ">
                                    <option value="">请选择</option>
                                    <option value="2">代理账号</option>
                                    <option value="1">会员账号</option>
                                </select>
                            </div>
                            <div class="agentChoice pull-left mr-5" style="display:none;">
                                <input type="text" id="agentAccount" name="agentAccount" class="form-control form-control-sm" placeholder="请输入代理账户" />

                                <label for="agentUsers" class="sr-only"></label>
                                <select id="agentUsers" name="agentUsers"  class="form-control form-control-sm select2">
                                    <option value="">请选择</option>
                                    <option value="2">直属下级</option>
                                    <option value="1">全部下级</option>
                                </select>
                            </div>
                            <div class="userAccountText mr-5" style="display:none;">
                                <input type="text" id="userAccount" name="userAccount" class="form-control form-control-sm" placeholder="请输入会员账户" />
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label for="startTime">充值时间:</label>
                            <input type="text" name="from" placeholder="开始日期" ng-model="startDate" class="form-control form-control-sm mx-sm-3" id="from">

                            <label for="to">至</label>
                            <input type="text" name="to" placeholder="结束日期" ng-model="endDate" class="form-control form-control-sm mx-sm-3" id="to">

                            <label for="orderNum">订单号:</label>
                            <input type="text" id="orderNum" ng-model="orderNum" class="form-control form-control-sm mx-sm-3" placeholder="请输入订单号" autocomplete="off">
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
                                <th>序号</th>
                                <th>订单号</th>
                                <th>昵称</th>
                                <th>角色</th>
                                <th>账号</th>
                                <th>充值金额</th>
                                <th>到账金额</th>
                                <th>充值时间</th>
                                <th>支付方式</th>
                                <th>充值状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="(index, item) in rechargList">
                                <td ng-bind="(rechargeCurrentPage - 1) * rechargePerPage + index + 1"></td>
                                <td ng-bind="item.order_no | default : '无'"></td>
                                <td ng-bind="item.nick_name | default : '无'"></td>
                                <td>
                                    <span class="badge badge-primary" ng-if="item.role===1">会员</span>
                                    <span class="badge badge-success" ng-if="item.role===2">代理商</span>
                                </td>
                                <td ng-bind="item.username | default : '无'"></td>
                                <td ng-bind="item.account | currency : '￥'" class="text-danger"></td>
                                <td ng-bind="item.to_account | currency : '￥'" class="text-danger"></td>
                                <td ng-bind="item.create_time  | default : '无'"></td>
                                <td>
                                    <span class="badge badge-primary" ng-if="item.type == 1">支付宝</span>
                                    <span class="badge badge-success" ng-if="item.type == 2">微信</span>
                                    <span class="badge badge-warning" ng-if="item.type == 3">网银支付</span>
                                    <span class="badge badge-secondary" ng-if="item.type == 4">代充值</span>
                                    <span class="badge badge-info" ng-if="item.type == 5">快捷支付</span>
                                </td>
                                <td>
                                    <span class="badge badge-warning" ng-if="item.status === 1">待支付</span>
                                    <span class="badge badge-success" ng-if="item.status === 2">成功</span>
                                    <span class="badge badge-secondary" ng-if="item.status === 3">失败</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="capitalNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div><!-- end card-->
        </div>
    </div>
</div>
