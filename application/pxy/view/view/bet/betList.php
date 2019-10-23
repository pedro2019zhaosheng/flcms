<div class="container-fluid" ng-controller="betListCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="btn-group row">
                            <button type="button" class="btn btn-sm btn-secondary" ng-click="export()">导出Excel</button>
                        </div>
                    </form>
                </div>

                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label for="betType" class="col-form-label">彩种:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <select class="form-control form-control-sm select2" id="betType" name="betType">
                                    <option value="-1">请选择</option>
                                    <option value="{{ item.id }}" ng-repeat="item in betsList" ng-bind="item.name"></option>
                                </select>
                            </div>

                            <label for="usersType" class="col-form-label">账号类型:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <select id="usersType" name="usersType"  class="form-control form-control-sm select2">
                                    <option value="">请选择</option>
                                    <option value="2">代理账号</option>
                                    <option value="1">会员账号</option>
                                </select>
                            </div>

                            <label for="orderType" class="col-form-label">订单类型:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <select class="form-control form-control-sm select2" id="orderType" name="orderType">
                                    <option value="-1">请选择</option>
                                    <option value="0">实单</option>
                                    <option value="1">模拟</option>
                                </select>
                            </div>

                            <label for="settlementState" class="col-form-label">支付状态:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <select class="form-control form-control-sm select2" id="settlementState" name="settlementState">
                                    <option value="">请选择</option>
                                    <option value="-1">未支付</option>
                                    <option value="0">支付中</option>
                                    <option value="1">已支付</option>
                                </select>
                            </div>

                            <label for="orderState" class="col-form-label">订单状态：</label>
                            <div class="mx-sm-3 form-control-sm">
                                <select class="form-control form-control-sm select2" id="orderState" name="orderState">
                                    <option value="-1">请选择</option>
                                    <option value="0">待出票</option>
                                    <option value="1">已出票</option>
                                    <option value="2">待开奖</option>
                                    <option value="3">未中奖</option>
                                    <option value="4">已中奖</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label>注单日期:</label>
                            <input type="text" id="from" name="from" autocomplete="off" placeholder="请选择开始日期" class="form-control form-control-sm mx-sm-3">

                            <label for="to">至</label>
                            <input type="text" id="to" name="to" autocomplete="off" placeholder="请选择结束日期" class="form-control form-control-sm mx-sm-3">

                            <label for="betNumber" class="col-form-label">注单单号:</label>
                            <input type="text" id="betNumber" name="betNumber" placeholder="请输入注单单号" class="form-control form-control-sm mx-sm-3" ng-model="orderNo">

                            <label for="memberNumber" class="col-form-label">会员账号:</label>
                            <input type="text" id="memberNumber" name="memberNumber" placeholder="请输入会员账号" class="form-control form-control-sm mx-sm-3" ng-model="username">

                            <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning mr-1">清空</button>
                            <button type="button" ng-click="searchSubmit()" class="btn btn-sm btn-primary">搜索</button>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th rowspan="2" style="vertical-align: middle">彩种</th>
                                <th rowspan="2" style="vertical-align: middle">注单号</th>
                                <th rowspan="2" style="vertical-align: middle">角色</th>
                                <th rowspan="2" style="vertical-align: middle">用户账号</th>
                                <th rowspan="2" style="vertical-align: middle">
                                    投注金额
                                    <small class="text-secondary">(元)</small>
                                </th>
                                <th rowspan="2" style="vertical-align: middle">
                                    中奖金额
                                    <small class="text-secondary">(元)</small>
                                </th>
                                <th rowspan="2" style="vertical-align: middle">
                                    嘉奖彩金
                                    <small class="text-secondary">(元)</small>
                                </th>
                                <th rowspan="2" style="vertical-align: middle">
                                    过关方式(期数)
                                </th>
                                <th rowspan="2" style="vertical-align: middle">支付时间</th>
                                <th colspan="4" style="vertical-align: middle">状态</th>
                                <th rowspan="2" style="vertical-align: middle">操作</th>
                            </tr>
                            <tr>
                                <th>购买方式</th>
                                <th>是否支付</th>
                                <th>订单状态</th>
                                <th>订单类型</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="item in betList"><!--此处是注单列表数据-->
                                <td ng-bind="item.lottery | default : '无'"></td>

                                <td ng-bind="item.order_no | default : '无'"></td>
                                <td ng-if="item.role == 1">
                                    <span class="badge badge-primary">会员</span>
                                </td>
                                <td ng-if="item.role == 2">
                                    <span class="badge badge-success">代理商</span>
                                </td>
                                <td ng-bind="item.username | default : '无'"></td>
                                <td>
                                    <span class="text-danger" ng-bind="item.amount | default : 0 | currency : '￥'"></span>
                                </td>
                                <td>
                                    <span class="text-danger" ng-bind="item.bonus | default : 0 | currency : '￥'"></span>
                                </td>
                                <td>
                                    <span class="text-danger" ng-bind="item.bounty | default : 0 | currency : '￥'"></span>
                                </td>
                                <td>
                                    <span ng-if="item.code === 'JC'" ng-repeat="son in item.chuan" ng-bind="'[' + son + ']'"></span>
                                    <span ng-if="item.code === 'SZC'" ng-bind="item.chuan + '期'"></span>
                                </td>
                                <td ng-bind="item.pay_time | default : '无'"></td>
                                <td>
                                    <span ng-if="item.pay_type === 1" class="badge badge-primary">自购</span>
                                    <span ng-if="item.pay_type === 2" class="badge badge-info">跟单</span>
                                    <span ng-if="item.pay_type === 3" class="badge badge-success">推单</span>
                                </td>
                                <td>
                                    <span ng-if="item.pay_status === -1" class="badge badge-secondary">未支付</span>
                                    <span ng-if="item.pay_status === 0" class="badge badge-secondary">支付中</span>
                                    <span ng-if="item.pay_status === 1" class="badge badge-success">已支付</span>
                                </td>
                                <td>
                                    <span ng-if="item.status === 0" class="badge badge-secondary">
                                        <b>待出票</b>
                                    </span>
                                    <span ng-if="item.status === 1" class="badge badge-info">
                                        <b>已出票</b>
                                    </span>
                                    <span ng-if="item.status === 2" class="badge badge-warning">
                                        <b>待开奖</b>
                                    </span>
                                    <span ng-if="item.status === 3" class="badge badge-secondary">
                                        <b>未中奖</b>
                                    </span>
                                    <span ng-if="item.status === 4" class="badge badge-primary">
                                        <b>已中奖</b>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-primary" ng-if="item.is_moni === 0">实单</span>
                                    <span class="text-secondary" ng-if="item.is_moni === 1">模拟</span>
                                </td>
                                <td>
                                    <div class="dropleft">
                                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-fw fa-cogs" aria-label="icon"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <ul class="list-group">
                                                <li class="list-group-item dropdown-item cp" ng-click="detail(item.id, item.code)">注单详情</li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="betNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div>
        </div>
    </div>
    <!--竞彩详情-->
    {include file="view/bet/betDetail" /}
    <!--数字彩详情-->
    {include file="view/bet/numDetail" /}
</div>
