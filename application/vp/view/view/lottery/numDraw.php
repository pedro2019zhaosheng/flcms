<div class="container-fluid" ng-controller="numLotteryCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/lottery/exportNumberDarw')): ?>
                        <div class="btn-group row col pl-0">
                            <button type="button" class="btn btn-sm btn-secondary" ng-click="export()">导出Excel</button>
                        </div>
                        <?php endif; ?>
                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/risk')): ?>
                        <div class="btn-group row col justify-content-end">
                            <button type="button" class="btn btn-sm btn-primary" ng-click="riskCtrl()">风险控制</button>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label for="lotteryClassify" class="col-form-label">彩种:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <select class="form-control form-control-sm select2" id="lotteryClassify" name="lotteryClassify">
                                    <option value="">全部</option>
                                    <option value="1">排列三</option>
                                    <option value="2">排列五</option>
                                    <option value="3">澳彩</option>
                                    <option value="4">葡彩</option>
                                    <option value="5">幸运飞艇</option>
                                </select>
                            </div>

                            <label for="openTime" class="col-form-label">开奖时间:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <input type="text" id="openTime" name="openTime" class="form-control form-control-sm" placeholder="开奖时间" autocomplete="off">
                            </div>

                            <label for="numDate" class="col-form-label">期号:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <input type="text" id="numDate" name="numDate" class="form-control form-control-sm" placeholder="请输入期号" autocomplete="off">
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
                                <th>序号</th>
                                <th>彩种</th>
                                <th>期号</th>
                                <th>开奖号码</th>
                                <th>修改时间</th>
                                <th>开奖时间</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="(index, item) in numLotteryList">
                                <td ng-bind="(numLotteryCurrentPage - 1) * numLotteryPerPage + index + 1"></td>
                                <td ng-bind="item.ctype_name | default : '无'"></td>
                                <td ng-bind="item.expect | default : '无'"></td>
                                <td class="text-left">
                                    <span class="badge badge-light define-badge-red" ng-repeat="son in item.open_code track by $index" ng-bind="son"></span>
                                </td>
                                <td ng-bind="item.update_time | default : '无'"></td>
                                <td ng-bind="item.open_time | default : '无'"></td>
                                <td>
                                    <span ng-if="item.status === 0" class="badge badge-secondary">待开奖</span>
                                    <span ng-if="item.status === 1" class="badge badge-success">已开奖</span>
                                </td>
                                <td>
                                    <div class="dropleft">
                                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-fw fa-cogs" aria-label="icon"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <ul class="list-group">
                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/lottery/editSubmit')): ?>
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#num-edit" ng-click="editNum(item.open_code, item.id, item.ctype)">
                                                    编辑开奖号码
                                                </li>
                                                <?php endif; ?>

                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#num-tag" ng-click="editNumBase(item.id, item.expect, item.open_time)">
                                                    编辑期号日期
                                                </li>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/order/numBingo')): ?>
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#num-draw" ng-click="handDraw(item.expect, item.ctype)">
                                                    手动开奖
                                                </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="numLotteryNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div>
        </div>
    </div>
    <!--编辑开奖号码-->
    {include file="view/lottery/numEdit" /}
    <!--编辑期号和日期-->
    {include file="view/lottery/numTag" /}
    <!--手动开奖-->
    {include file="view/lottery/numHand" /}
</div>

