<div class="container-fluid" ng-controller="capitalFlowCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline pl-2">
                        <div class="btn-group row">
                            <button type="button" class="btn btn-sm btn-secondary" ng-click="export()">导出Excel</button>
                        </div>
                    </form>
                </div>

                <div class="card-body pb-0">
                    <form class="form-inline pl-2">
                        <div class="form-group row">
                            <label for="changeType" class="col-form-label">变动类型:</label>
                            <div class="mx-sm-3">
                                <select class="form-control form-control-sm select2" id="changeType" name="changeType">
                                    <option value="">请选择</option>
                                    <option value="{{ son.id }}" ng-repeat="son in types">{{ son.value }}</option>
                                </select>
                            </div>

                            <label for="startTime" class="col-form-label">日期:</label>
                            <input type="text" name="from" placeholder="开始日期" ng-model="startDate" class="form-control form-control-sm mx-sm-3" id="from">

                            <label for="to">至</label>
                            <input type="text" name="to" placeholder="结束日期" ng-model="endDate" class="form-control form-control-sm mx-sm-3" id="to">

                            <label for="memberAccount" class="col-form-label">账号:</label>
                            <input type="text" id="memberAccount" name="memberAccount" placeholder="请输入账号" class="form-control form-control-sm mx-sm-3">
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
                                <th>账号</th>
                                <th>昵称</th>
                                <th>角色</th>
                                <th>变动金额</th>
                                <th>变动前金额</th>
                                <th>变动后金额</th>
                                <th>变动类型</th>
                                <th>日期</th>
                                <th class="text-left">备注</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="(index, item) in waterList"><!--这里是资金流水记录返回数据-->
                                <td ng-bind="(waterCurrentPage - 1) * waterPerPage + index + 1"></td>
                                <td ng-bind="item.nick_name | default : '无'"></td>
                                <td ng-bind="item.chn_name | default : '无'"></td>
                                <td ng-if="item.role == 1">
                                    <span class="badge badge-primary">会员</span>
                                </td>
                                <td ng-if="item.role == 2">
                                    <span class="badge badge-success">代理商</span>
                                </td>
                                <td ng-bind="item.money | currency : '￥'" class="text-danger"></td>
                                <td ng-bind="item.front_money | currency : '￥'" class="text-danger"></td>
                                <td ng-bind="item.later_money | currency : '￥'" class="text-danger"></td>
                                <td class="text-secondary">
                                    <b ng-if="item.type === 1">充值</b>
                                    <b ng-if="item.type === 2">提现</b>
                                    <b ng-if="item.type === 3">购彩</b>
                                    <b ng-if="item.type === 4">资金冻结</b>
                                    <b ng-if="item.type === 5">奖金</b>
                                    <b ng-if="item.type === 6">系统嘉奖</b>
                                    <b ng-if="item.type === 7">注单返佣</b>
                                    <b ng-if="item.type === 8">充值赠送</b>
                                    <b ng-if="item.type === 9">资金校正</b>
                                    <b ng-if="item.type === 10">跟单返佣</b>
                                </td>
                                <td ng-bind="item.create_time  | default : '无'"></td>
                                <td class="text-left" ng-bind="item.remark  | default : '无'"></td>
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
