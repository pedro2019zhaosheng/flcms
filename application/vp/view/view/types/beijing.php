<div class="container-fluid" ng-controller="beijingCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <div class="btn-group">
                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/bei_jing/toggle')): ?>
                        <button type="button" ng-click="batchNormal(1)" class="btn btn-sm btn-success">批量开售</button>
                        <button type="button" ng-click="batchNormal(0)" class="btn btn-sm btn-warning">批量停售</button>
                        <?php endif; ?>

                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/bei_jing/delete')): ?>
                        <button type="button" ng-click="batchDel()" class="btn btn-sm btn-danger">批量删除</button>
                        <?php endif; ?>

                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/bei_jing/export')): ?>
                            <button type="button" class="btn btn-sm btn-secondary" ng-click="export()">导出Excel</button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label for="beijingState" class="col-form-label">出售状态:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <select class="form-control form-control-sm mx-sm-3 select2" id="beijingState" name="beijingState">
                                    <option value="">请选择</option>
                                    <option value="1">出售中</option>
                                    <option value="0">已停售</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label>比赛日期:</label>
                            <input type="text" id="from" name="from" autocomplete="off" placeholder="请选择开始日期" class="form-control form-control-sm mx-sm-3">

                            <label for="to">至</label>
                            <input type="text"  id="to" name="to" autocomplete="off" placeholder="请选择结束日期" class="form-control form-control-sm mx-sm-3">

                            <label for="beijingName" class="col-form-label">名称筛选：</label>
                            <input type="text" id="beijingName" name="beijingName" placeholder="主队/联赛/客队名称" class="form-control form-control-sm mx-sm-3">

                            <label for="mchNum" class="col-form-label">赛事编号：</label>
                            <input type="text" id="mchNum" name="mchNum" placeholder="请输入赛事编号" class="form-control form-control-sm mx-sm-3">
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
                                    <th>竞彩日期</th>
                                    <th>赛事编号</th>
                                    <th>联赛名称</th>
                                    <th>开赛时间</th>
                                    <th>主队名称</th>
                                    <th>客队名称</th>
                                    <th>系统截止时间</th>
                                    <th>手动截止时间</th>
                                    <th>销售状态</th>
                                    <th>赛事状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="item in beijingList" on-finish-render="ngRepeatFinished"><!--此处是北京单场列表数据-->
                                    <td class="check-child">
                                        <div class="checkbox checkbox-primary">
                                            <input data-value="{{ item.id }}" id="lotCheckbox" class="styled" type="checkbox">
                                            <label for="lotCheckbox" class="position_lable"></label>
                                        </div>
                                    </td>
                                    <td ng-bind="item.jc_date | default : '无'"></td>
                                    <td ng-bind="item.match_num | default : '无'"></td>
                                    <td ng-bind="item.league_name | default : '无'"></td>
                                    <td ng-bind="item.start_time | default : '无'"></td>
                                    <td ng-bind="item.host_name | default : '无'"></td>
                                    <td ng-bind="item.guest_name | default : '无'"></td>
                                    <td ng-bind="item.sys_cutoff_time | default : '无'"></td>
                                    <td ng-bind="item.cutoff_time | default : '无'"></td>
                                    <td>
                                        <span class="badge badge-success" ng-if="item.sale_status === 1">出售中</span>
                                        <span class="badge badge-danger" ng-if="item.sale_status === 0">已停售</span>
                                    </td>
                                    <td>
                                        <span ng-if="item.match_status === 0" class="badge badge-success">正常</span>
                                        <span ng-if="item.match_status === 1" class="badge badge-secondary">取消</span>
                                        <span ng-if="item.match_status === 2" class="badge badge-warning">延期</span>
                                        <span ng-if="item.match_status === 3" class="badge badge-danger">斩腰</span>
                                    </td>
                                    <td>
                                        <div class="dropleft">
                                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-fw fa-cogs" aria-label="icon"></i>
                                            </button>
                                            <div class="dropdown-menu" role="menu" aria-labelledby="dropdownMenuButton">
                                                <ul class="list-group">
                                                    <?php if(\app\common\model\AdminAuth::hasAuth('/vp/bei_jing/detail')): ?>
                                                    <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#beijingMatch" ng-click="matchDetail(item.match_num, item.league_name, item.host_name, item.guest_name)">赔率查看</li>
                                                    <?php endif; ?>

                                                    <?php if(\app\common\model\AdminAuth::hasAuth('/vp/bei_jing/save')): ?>
                                                    <li class="list-group-item dropdown-item cp text-primary" data-toggle="modal" data-target="#beijingEdit" ng-click="matchEdit(item)">编辑赛果</li>
                                                    <?php endif; ?>

                                                    <?php if(\app\common\model\AdminAuth::hasAuth('/vp/bei_jing/delete')): ?>
                                                    <li class="list-group-item dropdown-item cp text-danger" ng-click="deleteOne(item.id)">删除</li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="beijingNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div>
        </div>
    </div>
    {include file="view/types/beijingMatch" /}
    {include file="view/types/beijingEdit" /}
</div>

