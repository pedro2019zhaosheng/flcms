<div class="container-fluid" ng-controller="memberCtrl">
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
                            <label class="mr-3" for="belong">所属平台:</label>
                            <select name="belong" id="belong" class="form-control form-control-sm mx-sm-3">
                                <option value="-1">请选择</option>
                                <option value="1">总后台</option>
                                <option value="2">代理商后台</option>
                                <option value="3">APP</option>
                            </select>

                            <label class="ml-3 mr-3" for="status">执行状态:</label>
                            <select name="status" id="status" class="form-control form-control-sm mx-sm-3">
                                <option value="-1">请选择</option>
                                <option value="0">失败</option>
                                <option value="1">成功</option>
                                <option value="2">未知</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row">
                            <label for="from">执行日期:</label>
                            <input type="text" id="from" name="start_date" placeholder="请选择开始日期" class="form-control form-control-sm mx-sm-3">

                            <label for="to">至</label>
                            <input type="text"  id="to" name="end_date" placeholder="请选择结束日期" class="form-control form-control-sm mx-sm-3">

                            <label for="username">执行人:</label>
                            <input type="text" id="name" name="name" placeholder="请输入执行人账号" ng-model="username" class="form-control form-control-sm mx-sm-3">

                            <label for="workName">业务名称:</label>
                            <input type="text" id="workName" name="workName" placeholder="请输入业务名称" ng-model="workName" class="form-control form-control-sm mx-sm-3">
                        </div>

                        <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning ml-3 mr-1">清空</button>
                        <button type="button" ng-click="searchSubmit()" class="btn btn-sm btn-primary">搜索</button>&nbsp;
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>平台</th>
                                <th>执行人</th>
                                <th>业务名称</th>
                                <th>备注</th>
                                <th>状态</th>
                                <th>执行时间</th>
                                <th>操作</th>
                            </thead>
                            <tbody>
                            <tr ng-repeat="(index, item) in systemLogList">
                                <td><span ng-bind="(systemLogCurrentPage - 1) * systemLogPerPage + index + 1"></span></td>
                                <td class="text-secondary">
                                    <span ng-if="item.belong === 1">总后台</span>
                                    <span ng-if="item.belong === 2">代理商后台</span>
                                    <span ng-if="item.belong === 3">APP</span>
                                </td>
                                <td>
                                    <span ng-bind="item.executor | default : '无'"></span>
                                </td>
                                <td>
                                    <span ng-bind="item.work_name | default : '无'"></span>
                                </td>
                                <td>
                                    <span ng-bind="item.remark | default : '无'"></span>
                                </td>
                                <td>
                                    <span class="badge badge-danger" ng-if="item.status === 0">失败</span>
                                    <span class="badge badge-success" ng-if="item.status === 1">成功</span>
                                    <span class="badge badge-info" ng-if="item.status === 2">未知</span>
                                </td>
                                <td>
                                    <span ng-bind="item.exec_time | default : '无'"></span>
                                </td>
                                <td>
                                    <span data-toggle="modal" data-target="#log-detail" class="btn btn-sm btn-outline-primary" ng-click="showInfo(item.id)">查看</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="systemLogNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div><!-- end card-->
        </div>
    </div>
    <!--日志信息-->
    {include file="view/system/logDetail" /}
</div>