<div class="container-fluid" ng-controller="adminCtrl">
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline">
                        <div class="btn-group">
                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/admin/add')): ?>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#admin-add">添加管理员</button>
                            <?php endif; ?>

                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/admin/export')): ?>
                            <button type="button" ng-click="export()" class="btn btn-secondary btn-sm">导出Excel</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline">
                        <div class="form-group row pl-2">
                            <label>注册日期:</label>
                            <input type="text" id="from" name="from" autocomplete="off" placeholder="请选择开始日期" class="form-control form-control-sm mx-sm-3">

                            <label for="to">至</label>
                            <input type="text"  id="to" name="to" autocomplete="off" placeholder="请选择结束日期" class="form-control form-control-sm mx-sm-3">

                            <label for="username">用户名:</label>
                            <input type="text" id="username" name="username" autocomplete="off" placeholder="请输入用户名" ng-model="username" class="form-control form-control-sm mx-sm-3">
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
                                <th>登录状态</th>
                                <th>头像</th>
                                <th>用户名</th>
                                <th>角色</th>
                                <th>昵称</th>
                                <th>手机号</th>
                                <th>账号状态</th>
                                <th>上一次登录时间</th>
                                <th>上一次登录IP</th>
                                <th>注册时间</th>
                                <th>注册IP</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="item in adminList">
                                <td>
                                    <a href="" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false" ng-bind="item.sort | default : 0"></a>
                                    <div class="dropdown-menu">
                                        <form class="form-inline">
                                            <div class="input-group m-1">
                                                <label for="updateSort" class="sr-only"></label>
                                                <input id="updateSort" ng-model="updateSort" class="form-control form-control-sm mr-2" min="0" type="number">
                                                <button type="button" class="btn btn-primary btn-sm" ng-click="updateAdminSort(item.id, updateSort)">保存</button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <i ng-if="item.login_status === 0" class="fa fa-times crossSign"></i>
                                    <i ng-if="item.login_status === 1" class="fa fa-check rightSign"></i>
                                </td>
                                <td><img style="width: 2rem; height: 2rem;" ng-src="{{ item.photo | default : '/static/lib/images/admin.png' }}" alt=""></td>
                                <td>
                                    <p ng-if="item.username !== 'admin'">{{ item.username | default : '无' }}</p>
                                    <p ng-if="item.username === 'admin'">
                                        {{ item.username }}
                                        <small><code>【系统账号】</code></small>
                                    </p>
                                </td>
                                <td ng-bind="item.role | default : '无'"></td>
                                <td ng-bind="item.nick_name | default : '无'"></td>
                                <td ng-bind="item.phone | default : '无'"></td>
                                <td>
                                    <span ng-if="item.frozen === 0" class="badge badge-success">正常</span>
                                    <span ng-if="item.frozen === 1" class="badge badge-danger">冻结</span>
                                </td>
                                <td>{{ item.last_login_time | default : '无'}}</td>
                                <td ng-bind="item.last_login_ip | default : '无'"></td>
                                <td>{{ item.create_at  | default : '无'}}</td>
                                <td>{{ item.signup_ip  | default : '无'}}</td>
                                <td>
                                    <div class="dropleft">
                                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-fw fa-cogs" aria-label="icon"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <ul class="list-group">
                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/admin/modify')): ?>
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#admin-update" ng-click="updateToggle(item.id)">编辑</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/admin/toggle')): ?>
                                                <li ng-if="item.frozen === 0" ng-click="toggle(item.id, 1)" class="list-group-item dropdown-item cp">冻结</li>
                                                <li ng-if="item.frozen === 1" ng-click="toggle(item.id, 0)" class="list-group-item dropdown-item cp">解冻</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/admin/delete')): ?>
                                                <li ng-click="deleteAdmin(item.id)" class="list-group-item dropdown-item cp">删除</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="adminNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div><!-- end card-->
        </div>
    </div>
    <!--新增管理员模态框-->
    {include file="view/admin/add" /}
    <!--修改管理员模态框-->
    {include file="view/admin/update" /}
</div>