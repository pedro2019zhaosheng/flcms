<div class="container-fluid" ng-controller="adverTypeCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0 clearfix">
                    <div class="btn-group float-left">
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#adverType-add">添加类型</button>
                        <button type="button" ng-click="setStatus(0)" class="btn btn-sm btn-warning">批量禁用</button>
                        <button type="button" ng-click="setStatus(1)" class="btn btn-sm btn-success">批量启用</button>
                    </div>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline pl-2">
                        <div class="form-group row">
                            <label>创建日期：</label>
                            <input type="text" id="from" name="from" placeholder="请选择开始日期" class="form-control form-control-sm mx-sm-3">

                            <label for="to">至</label>
                            <input type="text"  id="to" name="to" placeholder="请选择结束日期" class="form-control form-control-sm mx-sm-3">

                            <label for="adverType">类型名称：</label>
                            <input type="text" id="adverType" name="adverType" placeholder="请输入名称" ng-model="adverType" class="form-control form-control-sm mx-sm-3">
                        </div>

                        <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning mx-1">清空</button>
                        <button type="button" ng-click="searchSubmit()" class="btn btn-sm btn-primary">搜索</button>&nbsp;
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
                                <th>类型名称</th>
                                <th>创建时间</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                             <tr ng-repeat="item in adverTypeList" on-finish-render="ngRepeatFinished">
                                <td class="check-child">
                                     <div class="checkbox checkbox-primary">
                                         <input data-value="{{ item.id }}" id="{{item.id}}" class="styled" type="checkbox">
                                         <label for="{{item.id}}" class="position_lable"></label>
                                     </div>
                                 </td>
                                <td ng-bind="item.name | default : '无'"></td>
                                <td>{{ item.create_time  | default : '无'}}</td>
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
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#adminType-update" ng-click="updateToggle(item.id)">编辑</li>
                                                <li ng-click="deleteadver(item.id)" class="list-group-item dropdown-item cp text-danger">删除</li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="adverTypeNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div><!-- end card-->
        </div>
    </div>
    <!--新增广告类型模态框-->
    {include file="view/adver/addType" /}
    <!--修改广告类型模态框-->
    {include file="view/adver/updateType" /}
</div>
