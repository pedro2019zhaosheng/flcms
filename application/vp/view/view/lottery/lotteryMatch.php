<div class="container-fluid" ng-controller="lotteryMatchCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="btn-group row">
                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/lottery/exportDraw')): ?>
                            <button type="button" class="btn btn-sm btn-secondary" ng-click="export()">导出Excel</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label for="lotteryClassify" class="col-form-label">彩种:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <select class="form-control form-control-sm select2" id="lotteryClassify" name="lotteryClassify">
                                    <option value="{{ item.code }}" ng-repeat="item in lotteryList" ng-bind="item.name"></option>
                                </select>
                            </div>

                            <label for="lotteryState" class="col-form-label">开奖状态:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <select class="form-control form-control-sm select2" id="lotteryState" name="lotteryState">
                                    <option value="">请选择</option>
                                    <option value="1">已开奖</option>
                                    <option value="0">待开奖</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label for="jcDate" class="col-form-label">竞彩日期:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <input type="text" id="jcDate" name="jcDate" class="form-control form-control-sm" placeholder="请选择竞彩日期" autocomplete="off">
                            </div>

                            <label for="mchNum" class="col-form-label">赛事编号:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <input type="text" id="mchNum" name="mchNum" class="form-control form-control-sm" placeholder="请输入赛事编号" autocomplete="off">
                            </div>

                            <label for="teamName" class="col-form-label">球队名称:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <input type="text" id="teamName" name="teamName" class="form-control form-control-sm" placeholder="请输入球队名称" autocomplete="off">
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
                                    <th>彩种</th>
                                    <th>竞彩日期</th>
                                    <th>赛事编号</th>
                                    <th>联赛名称</th>
                                    <th>主队名称</th>
                                    <th>客队名称</th>
                                    <th>让球数</th>
                                    <th ng-if="lotteryZcCode">半场比分</th>
                                    <th ng-if="lotteryZcCode">全场比分</th>
                                    <th ng-if="lotteryZcCode">总比分</th>
                                    <th ng-if="lotteryZcCode">点球比分</th>
                                    <th ng-if="lotteryLcCode">主队全场比分</th>
                                    <th ng-if="lotteryLcCode">客队全场比分</th>
                                    <th>开奖状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="item in drawList">
                                <td ng-bind="item.czName | default : '无'"></td>
                                <td ng-bind="item.jc_date | default : '无'"></td>
                                <td ng-bind="item.match_num | default : '无'"></td>
                                <td ng-bind="item.league_name | default : '无'"></td>
                                <td ng-bind="item.host_name | default : '无'"></td>
                                <td ng-bind="item.guest_name | default : '无'"></td>
                                <td>
                                    <span ng-if="item.rqs == 0" class="text-primary fb" ng-bind="item.rqs| default : '0'"></span>
                                    <span ng-if="item.rqs < 0" class="text-secondary fb" ng-bind="item.rqs| default : '0'"></span>
                                    <span ng-if="item.rqs > 0" class="text-success fb" ng-bind="item.rqs| default : '0'"></span>
                                </td>
                                <td ng-if="lotteryZcCode">
                                    <span class="text-danger fb">{{ item.half_score.split('-')[0] }}</span>
                                    <span class="text-secondary">-</span>
                                    <span class="text-success fb">{{ item.half_score.split('-')[1] }}</span>
                                </td>
                                <td ng-if="lotteryZcCode">
                                    <span class="text-danger fb">{{ item.normal_score.split('-')[0] }}</span>
                                    <span class="text-secondary">-</span>
                                    <span class="text-success fb">{{ item.normal_score.split('-')[1] }}</span>
                                </td>
                                <td ng-if="lotteryZcCode">
                                    <span class="text-danger fb">{{ item.total_score.split('-')[0] }}</span>
                                    <span class="text-secondary">-</span>
                                    <span class="text-success fb">{{ item.total_score.split('-')[1] }}</span>
                                </td>
                                <td ng-if="lotteryZcCode">
                                    <span class="text-danger fb">{{ item.kick_score.split('-')[0] }}</span>
                                    <span class="text-secondary">-</span>
                                    <span class="text-success fb">{{ item.kick_score.split('-')[1] }}</span>
                                </td>
                                <td ng-if="lotteryLcCode">
                                    <span class="text-danger">{{ item.host_score}}</span>
                                </td>
                                <td ng-if="lotteryLcCode">
                                    <span class="text-danger">{{ item.guest_score}}</span>
                                </td>
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
                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/lottery/jcResult')): ?>
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#draw-detail" ng-click="lookDetail(item)">
                                                    <a href="#" target="_self">竞彩赛果</a>
                                                </li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/order/bingo')): ?>
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#hand-lottery" ng-click="handDealLottery(item.match_num, item.code)">
                                                    <span class="text-secondary">手动开奖</span>
                                                </li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="drawNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div>
        </div>
    </div>
    <!--竞彩赛果-->
    {include file="view/lottery/detail" /}
    <!--手动开奖-->
    {include file="view/lottery/handLottery" /}
</div>

