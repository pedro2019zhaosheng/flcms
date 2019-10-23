<div class="container-fluid" ng-controller="basketballCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="btn-group row">
                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/basketball/toggle')): ?>
                            <button type="button" ng-click="batchNormal(1)" class="btn btn-sm btn-success">批量开售</button>
                            <button type="button" ng-click="batchNormal(0)" class="btn btn-sm btn-warning">批量停售</button>
                            <?php endif; ?>

                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/basketball/delete')): ?>
                            <button type="button" ng-click="batchDelete()" class="btn btn-sm btn-danger">批量删除</button>
                            <?php endif; ?>

                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/basketball/export')): ?>
                                <button type="button" class="btn btn-sm btn-secondary" ng-click="export()">导出Excel</button>
                            <?php endif; ?>
                        </div>

                    </form>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="from-group row">
                            <label for="basketballState" class="col-form-label">出售状态：</label>
                            <div>
                                <select class="form-control form-control-sm mx-sm-3 select2" id="basketballState" name="basketballState">
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

                            <label for="footballName" class="col-form-label">名称筛选：</label>
                            <input type="text" ng-model="matchName" id="footballName" name="footballName" placeholder="主队/联赛/客队名称" class="form-control form-control-sm mx-sm-3">

                            <label for="footNum" class="col-form-label">赛事编号：</label>
                            <input type="text" ng-model="matchNum" id="footNum" name="footNum" placeholder="请输入赛事编号" class="form-control form-control-sm mx-sm-3">
                        </div>

                        <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning ml-4 mr-1">清空</button>
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
                                    <th>竞彩编号</th>
                                    <th>赛事编号</th>
                                    <th>联赛名称</th>
                                    <th>开赛时间</th>
                                    <th>主队名称</th>
                                    <th>客队名称</th>
                                    <th>系统截止时间</th>
                                    <th>手动截止时间</th>
                                    <th>状态</th>
                                    <th>赛事状态</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="item in basketList" on-finish-render="ngRepeatFinished"><!--此处是竞彩篮球列表数据-->
                                    <td class="check-child">
                                        <div class="checkbox checkbox-primary">
                                            <input data-value="{{ item.id }}" id="lotCheckbox" class="styled" type="checkbox">
                                            <label for="lotCheckbox" class="position_lable"></label>
                                        </div>
                                    </td>
                                    <td ng-bind="item.jc_num | default : '无'"></td>
                                    <td ng-bind="item.match_num | default : '无'"></td>
                                    <td ng-bind="item.league_name | default : '无'"></td>
                                    <td ng-bind="item.start_time | default : '无'"></td>
                                    <td ng-bind="item.host_name | default : '无'"></td>
                                    <td ng-bind="item.guest_name | default : '无'"></td>
                                    <td ng-bind="item.sys_cutoff_time | default : '无'"></td>
                                    <td ng-bind="item.cutoff_time | default : '无'"></td>
                                    <td>
                                        <span class="badge badge-danger" ng-if="item.sale_status === 0">已停售</span>
                                        <span class="badge badge-success" ng-if="item.sale_status === 1">出售中</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-success" ng-if="item.match_status === 0">正常</span>
                                        <span class="badge badge-secondary" ng-if="item.match_status === 1">取消</span>
                                        <span class="badge badge-warning" ng-if="item.match_status === 2">延期</span>
                                        <span class="badge badge-info" ng-if="item.match_status === 3">腰斩</span>
                                    </td>
                                    <td>
                                        <div class="dropleft">
                                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-fw fa-cogs" aria-label="icon"></i>
                                            </button>
                                            <div class="dropdown-menu" role="menu" aria-labelledby="dropdownMenuButton">
                                                <ul class="list-group">
                                                    <?php if(\app\common\model\AdminAuth::hasAuth('/vp/basketball/detail')): ?>
                                                    <li class="list-group-item dropdown-item cp text-secondary" data-toggle="modal" data-target="#basketballMatch" ng-click="matchDetail(item.match_num, item.league_name, item.host_name, item.guest_name)">赔率查看</li>
                                                    <?php endif; ?>

                                                    <?php if(\app\common\model\AdminAuth::hasAuth('/vp/basketball/save')): ?>
                                                    <li class="list-group-item dropdown-item cp text-secondary" data-toggle="modal" data-target="#basketballEdit" ng-click="matchEdit(item)">编辑赛果</li>
                                                    <?php endif; ?>

                                                    <?php if(\app\common\model\AdminAuth::hasAuth('/vp/basketball/delete')): ?>
                                                    <li class="list-group-item dropdown-item cp text-danger" ng-click="deleteOne(item.id)">删除</li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="basketNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div>
        </div>
    </div>
    {include file="view/types/basketballMatch" /}
    {include file="view/types/basketballEdit" /}
</div>

