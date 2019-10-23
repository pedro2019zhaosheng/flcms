<div class="container-fluid" ng-controller="serverCtrls">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <div class="btn-group">
                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/system/addService')): ?>
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#system-addSite">添加客服</button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body pb-0"> 
                    <form class="form-inline pl-2">
                        <div class="form-group row">
                            <label for="site_status">状态:</label>
                            <div class="mx-sm-3 form-control-sm">
                                <select class="form-control form-control-sm select2" id="site_status" name="site_status">
                                    <option value="">请选择</option>
                                    <option value="0">已禁用</option>
                                    <option value="1">已启用</option>
                                </select>
                            </div>

                            <label>注册日期:</label>
                            <input type="text" id="start_time" name="from" placeholder="请选择开始日期" class="form-control form-control-sm mx-sm-3">

                            <label for="to">至</label>
                            <input type="text"  id="end_time" name="to" placeholder="请选择结束日期" class="form-control form-control-sm mx-sm-3">

                            <label>客服名称:</label>
                            <input type="text" id="nickname" name="from" placeholder="请输入客服名称" class="form-control form-control-sm mx-sm-3">
                        </div>

                        <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning mr-1">清空</button>
                        <button type="button" ng-click="searchSubmit()" class="btn btn-sm btn-primary">搜索</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>客服名称</th>
                                <th>客服图标</th>
                                <th>客服编号</th>
                                <th>二维码图片</th>
                                <th>状态</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                             <tr ng-repeat="(index, item) in seviceList">
                                <td ng-bind="(systemCurrentPage - 1) * systemPerPage + index + 1"></td>
                                <td ng-bind="item.name"></td>
                                <td><img style="width: 2rem; height: 2rem;" ng-src="{{ item.icon}}" alt></td>
                                <td ng-bind="item.num"></td>
                                <td><img style="width: 2rem; height: 2rem;" ng-src="{{ item.img}}" alt></td>
                                 <td>
                                     <span ng-if="item.status === 1" class="badge badge-success">启用</span>
                                     <span ng-if="item.status === 0" class="badge badge-danger">禁用</span>
                                 </td>
                                <td ng-bind="item.create_time" class="font-style"></td>
                                <td>
                                    <div class="dropleft">
                                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-fw fa-cogs" aria-label="icon"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <ul class="list-group">
                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/system/editService')): ?>
                                                <li class="list-group-item dropdown-item cp text-dange" data-toggle="modal" data-target="#system-editSite" ng-click="updateToggle(item.id)">编辑</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/system/delSite')): ?>
                                                <li ng-click="deleteadver(item.id)" class="list-group-item dropdown-item cp text-danger">删除</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="serviceNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div><!-- end card-->
        </div>
    </div>
    <!--新增客服模态框-->
    {include file="view/system/addSite" /}
    <!--修改客服模态框-->
   {include file="view/system/editSite" /}
</div>
