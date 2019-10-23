<div class="container-fluid" ng-controller="newsRecycleCtrl">
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
                            <label for="newsTitle">新闻标题:</label>
                            <input type="text" id="newsTitle" name="newsTitle" placeholder="请输入新闻标题" ng-model="newsTitle" class="form-control form-control-sm mx-sm-3">
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
                               <th>新闻标题</th>
                                <th>新闻类型</th>
                                <th>新闻简介</th>
                                <th>新闻主图</th>
                                <th>创建时间</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                             <tr ng-repeat="item in newsList">
                                <td ng-bind="item.title | default : '无'"></td>
                                <td ng-bind="item.news_type | default : '无'"></td>
                                <td ng-bind="item.abstract | default : '无'"></td>
                                <td><img ng-src="{{item.img | default : '' }}" style="width:2rem;height:2rem;" alt></td>
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
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#admin-update" ng-click="deleteNews(item.id, 1)">还原</li>
                                                <li ng-click="deleteNews(item.id, 0)" class="list-group-item dropdown-item cp text-danger">永久删除</li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="newsNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div><!-- end card-->
        </div>
    </div>
</div>
