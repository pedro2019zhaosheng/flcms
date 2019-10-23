<div class="container-fluid" ng-controller="betExtendCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <div class="btn-group">
                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/bet/audit')): ?>
                        <button type="button" ng-click="batchAudit(1)" class="btn btn-sm btn-success">审核通过</button>
                        <button type="button" ng-click="batchAudit(2)" class="btn btn-sm btn-warning">审核驳回</button>
                        <?php endif; ?>

                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/bet/exportPush')): ?>
                        <button type="button" ng-click="export()" class="btn btn-sm btn-secondary">导出Excel</button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline pl-2">
                        <div class="form-group row">
                            <label for="lotteryType" class="col-form-label">&nbsp;彩种:</label>
                            <div class="mx-sm-3">
                                <select class="form-control form-control-sm select2" id="lotteryType" name="lotteryType">
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

                            <label for="orderType" class="col-form-label">&nbsp;订单类型:</label>
                            <div class="mx-sm-3">
                                <select class="form-control form-control-sm select2" id="orderType" name="orderType">
                                    <option value="-1">请选择</option>
                                    <option value="0">实单</option>
                                    <option value="1">模拟</option>
                                </select>
                            </div>

                            <label for="orderState" class="col-form-label">&nbsp;订单状态:</label>
                            <div class="mx-sm-3">
                                <select class="form-control form-control-sm select2" id="orderState" name="orderState">
                                    <option value="-1">请选择</option>
                                    <option value="0">待出票</option>
                                    <option value="1">已出票</option>
                                    <option value="2">待开奖</option>
                                    <option value="3">未中奖</option>
                                    <option value="4">已中奖</option>
                                </select>
                            </div>

                            <label for="supStatus" class="col-form-label">&nbsp;审核状态:</label>
                            <div class="mx-sm-3">
                                <select class="form-control form-control-sm select2" id="supStatus" name="supStatus">
                                    <option value="-1">请选择</option>
                                    <option value="0">待审核</option>
                                    <option value="1">已通过</option>
                                    <option value="2">已驳回</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline pl-2">
                        <div class="form-group row">
                            <label>推单日期:</label>
                            <input type="text" id="from" name="from" autocomplete="off" placeholder="请选择开始日期" class="form-control form-control-sm mx-sm-3">
                            <label for="to">至</label>
                            <input type="text" id="to" name="to" autocomplete="off" placeholder="请选择结束日期" class="form-control form-control-sm mx-sm-3">

                            <label for="betNumber" class="col-form-label">注单单号:</label>
                            <input type="text" id="betNumber" name="betNumber" placeholder="请输入注单单号" class="form-control form-control-sm mx-sm-3" ng-model="orderNo">

                            <label for="memberNumber" class="col-form-label">会员账号:</label>
                            <input type="text" id="memberNumber" name="memberNumber" placeholder="请输入会员账号" class="form-control form-control-sm mx-sm-3" ng-model="username">
                        </div>
                        <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning ml-2 mr-1">清空</button>
                        <button type="button" ng-click="searchSubmit()" class="btn btn-sm btn-primary">搜索</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="pushOrderList" class="table table-bordered table-hover text-center">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="vertical-align: middle">
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkAll" name="checkAll" class="styled" type="checkbox">
                                            <label for="checkAll" class="position_lable"></label>
                                        </div>
                                    </th>
                                    <th rowspan="2" style="vertical-align: middle">彩种</th>
                                    <th rowspan="2" style="vertical-align: middle">角色</th>
                                    <th rowspan="2" style="vertical-align: middle">注单号</th>
                                    <th rowspan="2" style="vertical-align: middle">用户账号</th>
                                    <th rowspan="2" style="vertical-align: middle">
                                        佣金比例
                                        <small class="text-secondary">(%)</small>
                                    </th>
                                    <th rowspan="2" style="vertical-align: middle">过关方式(期数)</th>
                                    <th rowspan="2" style="vertical-align: middle">跟单人数</th>
                                    <th rowspan="2" style="vertical-align: middle">推单时间</th>
                                    <th rowspan="2" style="vertical-align: middle">截止时间</th>
                                    <th colspan="3" style="vertical-align: middle">状态</th>
                                    <th rowspan="2" style="vertical-align: middle">操作</th>
                                </tr>
                                <tr>
                                    <th>订单状态</th>
                                    <th>审核状态</th>
                                    <th>订单类型</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="item in betExtendList" on-finish-render="ngRepeatFinished"><!--此处是注单列表数据-->
                                    <td class="check-child">
                                        <div class="checkbox checkbox-primary">
                                            <input data-value="{{ item.id }}" id="lotCheckbox" class="styled" type="checkbox">
                                            <label for="lotCheckbox" class="position_lable"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <span ng-bind="item.lottery | default : '无'"></span>
                                    </td>
                                    <td ng-if="item.role == 1">
                                        <span class="badge badge-primary">会员</span>
                                    </td>
                                    <td ng-if="item.role == 2">
                                        <span class="badge badge-success">代理商</span>
                                    </td>
                                    <td ng-bind="item.order_no | default : '无'"></td><!--推单号-->
                                    <td ng-bind="item.username | default : '无'"></td> <!--发单人-->
                                    <td ng-bind="item.commission_rate | default : '0'"></td><!--佣金比例-->
                                    <td>
                                        <span ng-if="item.code === 'JC'" ng-repeat="son in item.chuan" ng-bind="'[' + son + ']'"></span>
                                        <span ng-if="item.code === 'SZC'" ng-bind="item.chuan + '期'"></span>
                                    </td><!--过关方式-->
                                    <td>
                                        <b class="text-primary" ng-bind="item.follows | default : '0'"></b>
                                    </td><!--跟单人数-->
                                    <td ng-bind="item.sup_order_time | default : '无'"></td><!--推单时间-->
                                    <td ng-bind="item.start_time | default : '无'"></td><!--跟单截止时间-->
                                    <td>
                                        <span ng-if="item.status ===0" class="badge badge-warning ng-scope">待出票</span>
                                        <span ng-if="item.status ===1" class="badge badge-info ng-scope">已出票</span>
                                        <span ng-if="item.status ===2" class="badge badge-warning ng-scope">待开奖</span>
                                        <span ng-if="item.status ===3" class="badge badge-secondary ng-scope">未中奖</span>
                                        <span ng-if="item.status ===4" class="badge badge-primary ng-scope">已中奖</span>
                                    </td>
                                    <td>
                                        <span ng-if="item.sup_order_state ===0" class="badge badge-info ng-scope">待审核</span>
                                        <span ng-if="item.sup_order_state ===1" class="badge badge-success ng-scope">已通过</span>
                                        <span ng-if="item.sup_order_state ===2" class="badge badge-secondary ng-scope">已驳回</span>
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
                                            <div class="dropdown-menu" role="menu" aria-labelledby="dropdownMenuButton">
                                                <ul class="list-group">
                                                    <?php if(\app\common\model\AdminAuth::hasAuth('/vp/bet/pushInfo')): ?>
                                                    <li class="list-group-item dropdown-item cp text-secondary" ng-click="pushDetail(item.id, item.code)">
                                                        推单详情
                                                    </li>
                                                    <?php endif; ?>

                                                    <?php if(\app\common\model\AdminAuth::hasAuth('/vp/bet/flowList')): ?>
                                                    <li data-toggle="modal" data-target="#documentary-detailed" class="list-group-item dropdown-item cp text-secondary" ng-click="detail(item.id)">
                                                        跟单明细
                                                    </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="betExtendNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div>
        </div>
    </div>
    <!--跟单明细模态框-->
    {include file="view/bet/documentaryDetailed" /}
    <!--竞彩推单详情-->
    {include file="view/bet/pushDetail" /}
    <!--数字彩推单详情-->
    {include file="view/bet/numPushDetail" /}
</div>

