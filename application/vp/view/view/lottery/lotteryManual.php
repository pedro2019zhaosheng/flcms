<div class="container-fluid" ng-controller="lotteryManualCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <div class="btn-group">
                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/order/sendPrize')): ?>
                        <button type="button" ng-click="batchManual()" class="btn btn-sm btn-danger">批量派奖</button>
                        <?php endif; ?>

                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/order/export')): ?>
                        <button type="button" ng-click="export()" class="btn btn-sm btn-secondary">导出Excel</button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline pl-2">
                       <div class="form-group row">
                           <label for="manualClassify" class="col-form-label">彩种:</label>
                           <div class="mx-sm-3 form-control-sm">
                               <select class="form-control form-control-sm select2" id="manualClassify" name="manualClassify">
                                   <option value="-1">请选择</option>
                                   <option value="{{item.id}}" ng-repeat="item in lotteryList" ng-bind="item.name"></option>
                               </select>
                           </div>

                           <label for="moni" class="col-form-label">注单类型:</label>
                           <div class="mx-sm-3 form-control-sm">
                               <select class="form-control form-control-sm select2" id="moni" name="moni">
                                   <option value="-1">请选择</option>
                                   <option value="0">实单</option>
                                   <option value="1">模拟</option>
                               </select>
                           </div>

                           <label for="manualState" class="col-form-label">结算状态：</label>
                           <div class="mx-sm-3 form-control-sm">
                               <select class="form-control form-control-sm select2" id="manualState" name="manualState">
                                   <option value="-1">请选择</option>
                                   <option value="0">未结算</option>
                                   <option value="1">已结算</option>
                               </select>
                           </div>
                       </div>
                    </form>
                </div>

                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label for="username" class="col-form-label">账号:</label>
                            <input type="text" id="username" name="username" placeholder="请输入账号" class="form-control form-control-sm mx-sm-3" ng-model="username">

                            <label for="betNum" class="col-form-label">注单编号:</label>
                            <input type="text" id="betNum" name="betNum" placeholder="请输入注单编号" class="form-control form-control-sm mx-sm-3" ng-model="betNum">
                        </div>

                        <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning ml-3 mr-1">清空</button>
                        <button type="button" ng-click="searchSubmit()" class="btn btn-sm btn-primary">搜索</button>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="manualList" class="table table-bordered table-hover text-center">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="checkbox checkbox-primary">
                                            <input id="checkAll" name="checkAll" class="styled" type="checkbox">
                                            <label for="checkAll" class="position_lable"></label>
                                        </div>
                                    </th>
                                    <th>彩种</th>
                                    <th>注单号</th>
                                    <th>用户账号</th>
                                    <th>投注金额<small class="text-secondary">(元)</small></th>
                                    <th>中奖金额<small class="text-secondary">(元)</small></th>
                                    <th>嘉奖彩金<small class="text-secondary">(元)</small></th>
                                    <th>投注时间</th>
                                    <th>状态</th>
                                    <th>注单类型</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="item in bingoList" on-finish-render="ngRepeatFinished"><!--此处是注单列表数据-->
                                    <td class="check-child">
                                        <div class="checkbox checkbox-primary">
                                            <input data-iden="{{ item.order_type }}"
                                                   data-value="{{ item.id }}"
                                                   id="lotCheckbox"
                                                   class="styled"
                                                   type="checkbox">
                                            <label for="lotCheckbox" class="position_lable"></label>
                                        </div>
                                    </td>
                                    <td ng-bind="item.lottery | default : '无'"></td>
                                    <td ng-bind="item.order_no | default : '无'"></td>
                                    <td ng-bind="item.member | default : '无'"></td>
                                    <td class="text-danger" ng-bind="item.amount | default : 0 | currency : '￥'"></td>
                                    <td class="text-danger" ng-bind="item.bonus | default : 0 | currency : '￥'"></td>
                                    <td class="text-danger" ng-bind="item.bounty | default : 0 | currency : '￥'"></td>
                                    <td ng-bind="item.create_time | default : '无'"></td>
                                    <td>
                                        <span class="badge badge-secondary" ng-if="item.is_clear === 0">未结算</span>
                                        <span class="badge badge-success" ng-if="item.is_clear === 1">已结算</span>
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
                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/order/sendPrize')): ?>
                                                <ul class="list-group">
                                                    <li ng-click="atOnceAward(item.id, item.order_type)" class="list-group-item dropdown-item cp">立即派奖</li>
                                                </ul>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="bingoNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div>
        </div>
    </div>
</div>

