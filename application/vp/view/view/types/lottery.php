<div class="container-fluid" ng-controller="lotteryCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <div class="btn-group">
                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/lottery/add')): ?>
                        <button type="button" data-toggle="modal" data-target="#add-lottery" class="btn btn-sm btn-primary">新增彩种</button>
                        <?php endif; ?>

                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/lottery/toggle')): ?>
                        <button type="button" ng-click="batchHandle(0)" class="btn btn-sm btn-success">批量出售</button>
                        <button type="button" ng-click="batchHandle(1)" class="btn btn-sm btn-warning">批量停售</button>
                        <?php endif; ?>

                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/lottery/delete')): ?>
                        <button type="button" ng-click="batchDelete()" class="btn btn-sm btn-danger">批量删除</button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline pl-2">
                        <div class="form-group row">
                            <label for="lotteryType" class="col-form-label">彩种名称:</label>
                            <input type="text" id="lotteryType" ng-model="lotteryName" name="lotteryType" placeholder="请输入彩种名称" class="form-control form-control-sm mx-sm-3">
                            <label for="couponState" class="col-form-label mx-2">状态:</label>
                            <div class="mx-sm-3">
                                <select class="form-control form-control-sm select2" id="couponState" name="couponState">
                                    <option value="">请选择</option>
                                    <option value="0">已出售</option>
                                    <option value="1">已停售</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning mr-1 ml-3">清空</button>
                        <button type="button" ng-click="searchSubmit()" class="btn btn-sm btn-primary">搜索</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                                <tr>
                                    <th class="check-parent">
                                        <div class="checkbox checkbox-primary cp">
                                            <input id="checkAll" name="checkAll" class="styled" type="checkbox">
                                            <label for="checkAll" class="position_lable"></label>
                                        </div>
                                    </th>
                                    <th>图标</th>
                                    <th>彩种名称</th>
                                    <th>彩种代码</th>
                                    <th>赛事数据爬取</th>
                                    <th>赛事结果爬取</th>
                                    <th>创建时间</th>
                                    <th>状态</th>
                                    <th>是否上线</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="item in lotList" on-finish-render="ngRepeatFinished"><!--此处是注单列表数据-->
                                    <td class="check-child">
                                        <div class="checkbox checkbox-primary">
                                            <input data-value="{{ item.id }}" id="lotCheckbox" class="styled" type="checkbox">
                                            <label for="lotCheckbox" class="position_lable"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <img style="width: 2rem; height: 2rem;" ng-src="{{ item.img | default : '/static/lib/images/logo.png' }}" alt="">
                                    </td>
                                    <td ng-bind="item.name | default : '无'"></td>
                                    <td ng-bind="item.code | default : '无'"></td>
                                    <td  ng-class="{true : 'text-primary', false : 'text-danger' }[item.match === 1]" ng-bind="item.match === 1 ? '已开始' : '已停止'"></td>
                                    <td  ng-class="{true : 'text-primary',false : 'text-danger' }[item.result === 1]" ng-bind="item.result === 1 ? '已开始' : '已停止'"></td>
                                    <td ng-bind="item.create_at | default : '无'"></td>
                                    <td>
                                        <span ng-if="item.status ===0" class="badge badge-success">出售中</span>
                                        <span ng-if="item.status ===1" class="badge badge-danger">已停售</span>
                                    </td>
                                    <td>
                                        <span ng-if="item.is_run === 0" class="badge badge-secondary">未上线</span>
                                        <span ng-if="item.is_run === 1" class="badge badge-primary">已上线</span>
                                    </td>
                                    <td>
                                        <div class="dropleft">
                                            <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-fw fa-cogs" aria-label="icon"></i>
                                            </button>
                                            <div class="dropdown-menu" role="menu" aria-labelledby="dropdownMenuButton">
                                                <ul class="list-group">
                                                    <?php if(\app\common\model\AdminAuth::hasAuth('/vp/lottery/edit')): ?>
                                                    <li class="list-group-item dropdown-item cp" ng-click="editLotteryShow(item.id)" data-toggle="modal" data-target="#edit-lottery">
                                                        <div class="text-secondary">编辑</div>
                                                    </li>
                                                    <?php endif; ?>

                                                    <?php if(\app\common\model\AdminAuth::hasAuth('/vp/lottery/delete')): ?>
                                                    <li class="list-group-item dropdown-item cp" ng-click="deleteLottery(item.id)">
                                                        <div class="text-danger">删除</div>
                                                    </li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="lotNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div>
        </div>
    </div>
    <!--新增彩种模态框-->
    {include file="view/types/addLottery" /}
    <!--编辑彩种模态框-->
    {include file="view/types/editLottery" /}
</div>
