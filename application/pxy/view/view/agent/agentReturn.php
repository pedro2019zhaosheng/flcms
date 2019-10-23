<div class="container-fluid" ng-controller="agentReturnCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <button type="button" class="btn btn-sm btn-primary mr-4" data-toggle="modal" data-target="#agent-return-add" ng-click="getaddAgentRebates()">设置代理商返点</button>
                        </div>
                    </form>
                </div>

                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label for="agentPhone" class="col-form-label">代理账号：</label>
                            <input type="text" id="agentPhone" name="agentPhone" placeholder="请输入代理账号" ng-model="agentPhone" class="form-control form-control-sm mx-sm-3">

                            <label for="product" class="col-form-label">彩种：</label>
                            <div class="mx-sm-3" style="width: 150px;">
                                <select class="form-control form-control-sm" id="product" name="product">
                                    <option value="">请选择</option>
                                    <option ng-repeat="item in LotteryData" value="{{item.id}}" ng-bind="item.name"></option>
                                </select>
                            </div>

                            <label for="userstate" class="col-form-label">状态：</label>
                            <div class="mx-sm-3" style="width: 150px;">
                                <select class="form-control form-control-sm" id="userstate" name="userstate">
                                    <option value="">请选择</option>
                                    <option value="0">禁用</option>
                                    <option value="1">启用</option>
                                </select>
                            </div>
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
                                <th>代理账号</th>
                                <th>昵称</th>
                                <th>彩种</th>
                                <th>返佣比例</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="item in agentRetrunList">
                                <td ng-bind="item.username | default : '无'"></td>
                                <td ng-bind="item.chn_name | default : '无'"></td>
                                <td ng-bind="item.name | default : '无'"></td>
                                <td ng-bind="item.ratio | default : '0.00'"></td>
                                <td>
                                    <span ng-if="item.status === 0" class="badge badge-danger">禁用</span>
                                    <span ng-if="item.status === 1" class="badge badge-success">启用</span>
                                </td>
                                <td>
                                    <div class="dropleft">
                                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-fw fa-cogs" aria-label="icon"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <ul class="list-group">
                                                <li class="list-group-item dropdown-item cp text-danger" ng-click="deleteAgent(item.id)">删除</li>
                                                <li ng-if="item.status === 0" ng-click="toggle(item.id, 1)" class="list-group-item dropdown-item cp text-success">启用</li>
                                                <li ng-if="item.status === 1" ng-click="toggle(item.id, 0)" class="list-group-item dropdown-item cp text-warning">禁用</li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="agentRetrunNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div>
        </div>
    </div>
    <!--新增代理商设置返点模态框-->
    {include file="view/agent/addAgent" /}
</div>

