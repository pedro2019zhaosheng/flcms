<div class="container-fluid" ng-controller="roleCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline">
                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/role/add')): ?>
                        <button type="button" class="btn btn-sm btn-primary mr-3" data-toggle="modal" data-target="#role-add">添加角色</button>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline">
                        <div class="form-group row pl-2">
                            <label for="roleName">角色名</label>
                            <input type="text" id="roleName" name="roleName" placeholder="请输入角色名" ng-model="roleName" class="form-control form-control-sm mx-sm-3">
                        </div>

                        <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning mx-1">清空</button>
                        <button type="button" ng-click="searchSubmit()" class="btn btn-sm btn-primary">搜索</button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>排序</th>
                                <th>角色名称</th>
                                <th>描述</th>
                                <th>角色类型</th>
                                <th>创建时间</th>
                                <th>修改时间</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="item in roleList">
                                <td>
                                    <a href="" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="false"
                                       aria-expanded="false" ng-bind="item.sort | default : 0"></a>
                                    <div class="dropdown-menu">
                                        <form class="form-inline">
                                            <div class="input-group m-1">
                                                <label for="updateSort" class="sr-only"></label>
                                                <input id="updateSort" ng-model="updateSort" class="form-control form-control-sm mr-2" min="0" type="number">
                                                <button type="button" class="btn btn-primary btn-sm" ng-click="updateRoleSort(item.id, updateSort)">保存</button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <p ng-if="item.name !== '超级管理员'">{{ item.name | default : '无' }}</p>
                                    <p ng-if="item.name === '超级管理员'">{{ item.name }} <small><code>【系统内置角色】</code></small></p>
                                </td>
                                <td ng-bind="item.description | default : '无'"></td>
                                <td ng-if="item.roletype === 0">
                                    超管
                                    <small><code>【默认拥有最高权限】</code></small>
                                </td>
                                <td ng-if="item.roletype === 1">普管</td>
                                <td>{{ item.create_time | default : '无'}}</td>
                                <td>{{ item.update_time | default : '无'}}</td>
                                <td>
                                    <span ng-if="item.status === 1" class="badge badge-success">正常</span>
                                    <span ng-if="item.status === 0" class="badge badge-danger">禁用</span>
                                </td>
                                <td>
                                    <div class="dropleft">
                                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-fw fa-cogs" aria-label="icon"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <ul class="list-group">
                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/role/update')): ?>
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#role-update" ng-click="updateRoleMethod(item.id)">编辑</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/role/toggle')): ?>
                                                <li ng-if="item.status === 0" class="list-group-item dropdown-item cp" ng-click="disableRole(item.id, 1)">启用</li>
                                                <li ng-if="item.status === 1" class="list-group-item dropdown-item cp" ng-click="disableRole(item.id, 0)">禁用</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/role/delete')): ?>
                                                <li class="list-group-item dropdown-item cp" ng-click="deleteRole(item.id)">删除</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/role/auth')): ?>
                                                <li ng-if="item.roletype === 1" class="list-group-item dropdown-item cp" ng-click="giveAuth(item.id)">访问授权</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="roleNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div><!-- end card-->
        </div>
    </div>
    <!--新增角色模态框-->
    {include file="view/role/add" /}
    <!--修改角色模态框-->
    {include file="view/role/update" /}
    <!--访问授权-->
    {include file="view/role/auth" /}
</div>