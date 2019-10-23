<div class="container-fluid" ng-controller="adverCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline">
                        <div class="form-group">
                            <label>创建日期:</label>
                            <input type="text" id="from" name="from" placeholder="请选择开始日期" class="form-control form-control-sm mx-sm-3">
                            <label for="to">至</label>
                            <input type="text"  id="to" name="to" placeholder="请选择结束日期" class="form-control form-control-sm mx-sm-3">
                            <label for="adverTitle">广告标题:</label>
                            <input type="text" id="adverTitle" name="adverTitle" placeholder="请输入广告标题" ng-model="adverTitle" class="form-control form-control-sm mx-sm-3">
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
                               <th>广告标题</th>
                                <th>广告类型</th>
                                <th>广告描述</th>
                                <th>广告图</th>
                                <th>广告链接</th>
                                <th>创建时间</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                             <tr ng-repeat="item in adverList">
                                <td ng-bind="item.name | default : '无'"></td>
                                <td ng-bind="item.ad_type | default : '无'"></td>
                                <td ng-bind="item.abstract | default : '无'"></td>
                                <td><img ng-src="{{item.img | default : ''}}" style="width:2rem;height:2rem;" alt></td>
                                <td ng-bind="item.url | default : '无'"></td>
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
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#admin-update" ng-click="deleteadver(item.id, 1)">还原</li>
                                                <li ng-click="deleteadver(item.id, 0)" class="list-group-item dropdown-item cp text-danger">永久删除</li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="adverNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div><!-- end card-->
        </div>
    </div>
</div>
