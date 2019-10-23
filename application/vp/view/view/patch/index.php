<div class="container-fluid" ng-controller="patchCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="btn-group row">
                            <button type="button" class="btn btn-sm btn-danger" ng-click="truncateLog()">清空所有日志</button>
                        </div>
                    </form>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label>爬取日期:</label>
                            <input type="text" id="fromDate" name="from" placeholder="请选择开始日期" class="form-control form-control-sm mx-sm-3">
                            <label for="to">至</label>
                            <input type="text"  id="toDate" name="to" placeholder="请选择结束日期" class="form-control form-control-sm mx-sm-3">
                            <label for="czName">彩种代码:</label>
                            <input type="text" id="czName" name="czName" autocomplete="off" placeholder="请输入彩种代码" ng-model="czName" class="form-control form-control-sm mx-sm-3">
                        </div>
                        <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning mx-1">清空</button>
                        <button type="button" ng-click="searchSubmit()" class="btn btn-sm btn-success">搜索</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>彩种代码</th>
                                <th>日志描述</th>
                                <th>爬取状态</th>
                                <th>爬取日期</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="(index, item) in patchList">
                                <td ng-bind="(patchCurrentPage - 1) * patchPerPage + index + 1"></td>
                                <td ng-bind="item.code | default : '无'"></td>
                                <td ng-bind="item.desc | default : '无'"></td>
                                <td>
                                    <span ng-if="item.status === 1" class="badge-success badge">成功</span>
                                    <span ng-if="item.status === 0" class="badge-danger badge">失败</span>
                                </td>
                                <td ng-bind="item.date  | default : '无'"></td>
                                <td>
                                    <button ng-class="{true: 'btn btn-sm btn-outline-primary', false: 'btn btn-sm btn-secondary disabled cna'}[item.status === 0]" ng-click="patchDetail(item.id, item.status)">错误详情</button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="patchNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div><!-- end card-->
        </div>
    </div>
    {include file="view/patch/detail"}
</div>