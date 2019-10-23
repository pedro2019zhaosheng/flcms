<div class="container-fluid" ng-controller="capitalCorrectionCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline pl-2">
                        <div class="btn-group row">
                            <button type="button" class="btn btn-secondary btn-sm" ng-click="export()">导出Excel</button>
                        </div>
                    </form>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline pl-2">
                        <div class="form-group row">
                            <label for="usersType" class="col-form-label">账号类型:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <select id="usersType" name="usersType"  class="form-control form-control-sm select2">
                                    <option value="">请选择</option>
                                    <option value="2">代理账号</option>
                                    <option value="1">会员账号</option>
                                </select>
                            </div>

                            <div class="agentChoice mr-5" style="display:none;">
                                <label for="agentAccount" class="sr-only"></label>
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
                    <form class="form-inline pl-2">
                        <div class="form-group row">
                            <label for="startTime">校正日期:</label>
                            <input type="text" name="from" placeholder="开始日期" ng-model="startDate" class="form-control form-control-sm mx-sm-3" id="from">

                            <label for="to">至</label>
                            <input type="text" name="to" placeholder="结束日期" ng-model="endDate" class="form-control form-control-sm mx-sm-3" id="to">
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
                                <th>校正金额</th>
                                <th>校正前金额</th>
                                <th>校正后金额</th>
                                <th>校正日期</th>
                                <th class="text-left">备注</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="(index, item) in capitalList"><!--这里是资金校正记录返回数据-->
                                <td ng-bind="(capitalCurrentPage - 1) * capitalPerPage + index + 1"></td>
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
                                <td ng-bind="item.create_time | default : '无'"></td>
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
