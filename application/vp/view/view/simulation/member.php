<div class="container-fluid" ng-controller="memberCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline">
                        <div class="btn-group mr-3">
                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/simulation/add')): ?>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#member-add">添加模拟会员</button>
                            <?php endif; ?>

                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/simulation/toggles')): ?>
                            <button type="button" ng-click="batchHandle(1)" class="btn btn-sm btn-success">批量正常</button>
                            <button type="button" ng-click="batchHandle(0)" class="btn btn-sm btn-warning">批量冻结</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                <div class="card-body pb-0">
                    <form class="form-inline">
                        <div class="form-group row pl-2">
                            <label>日期:</label>
                            <input type="text" name="from" placeholder="开始日期" class="form-control form-control-sm mx-sm-3" id="from">

                            <label for="to">至</label>
                            <input type="text" name="to" placeholder="结束日期" class="form-control form-control-sm mx-sm-3" id="to">

                            <label for="nickName">昵称:</label>
                            <input id="nickName" name="nickName" type="text"  ng-model="nickName" class="form-control form-control-sm mx-sm-3" autocomplete="off" placeholder="请输入昵称">

                            <label for="username">账号:</label>
                            <input id="username" name="username" type="text"  ng-model="username" class="form-control form-control-sm mx-sm-3" autocomplete="off" placeholder="请输入账号">
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
                                <th class="check-parent">
                                    <div class="checkbox checkbox-primary cp">
                                        <input id="checkAll" name="checkAll" class="styled" type="checkbox">
                                        <label for="checkAll" class="position_lable"></label>
                                    </div>
                                </th>
                                <th>昵称</th>
                                <th>头像</th>
                                <th>账号</th>
                                <th>总输赢</th>
                                <th>余额</th>
                                <th>彩金</th>
                                <th>注册时间</th>
                                <th>最后登录APP时间</th>
                                <th>最后登录APP IP</th>
                                <th>会员状态</th>
                                <th>操作</th></tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="member in memberList" on-finish-render="ngRepeatFinished">
                                <td class="check-child">
                                    <div class="checkbox checkbox-primary">
                                        <input data-value="{{ member.id }}" id="lotCheckbox" class="styled" type="checkbox">
                                        <label for="lotCheckbox" class="position_lable"></label>
                                    </div>
                                </td>
                                <td ng-bind="member.chn_name"></td>
                                <td><img style="width: 2rem; height: 2rem;" ng-src="{{ member.photo | default : '/static/lib/images/msgicon.jpg' }}" alt=""></td>
                                <td ng-bind="member.username"></td>
                                <td ng-bind="member.profit | currency : '￥'" class="text-danger"></td>
                                <!--<td ng-if="member.profit != 0" ng-click="winloseList(member.username,1)" class="font-style cp">
                                    <ins ng-bind="member.profit | currency : '￥'"></ins>
                                </td>-->
                                <td ng-bind="member.balance | currency : '￥'" class="text-danger"></td>
                                <td ng-bind="member.hadsel | currency : '￥'" class="text-danger"></td>
                                <td ng-bind="member.create_at | default : '无'"></td>
                                <td ng-bind="member.last_login_time| default : '无'"></td>
                                <td ng-bind="member.last_login_ip| default : '无'"></td>
                                <td>
                                    <span ng-if="member.frozen === 1" class="badge badge-success">正常</span>
                                    <span ng-if="member.frozen === 0" class="badge badge-danger">冻结</span>
                                </td>
                                <td>
                                    <div class="dropleft">
                                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-fw fa-cogs" aria-label="icon"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <ul class="list-group">
                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/simulation/reviseGold')): ?>
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#revise-gold" ng-click="updataMemberMethod(member.id)">修改彩金</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/simulation/reviseBalance')): ?>
                                                <li class="list-group-item dropdown-item cp"  data-toggle="modal" data-target="#revise-balance" ng-click="updataMemberMethod(member.id)">修改余额</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/simulation/setPassword')): ?>
                                                <li class="list-group-item dropdown-item cp"  data-toggle="modal" data-target="#setPassword-member" ng-click="setPasswordMethod(member.id)">修改密码</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/simulation/toggle')): ?>
                                                <li ng-if="member.frozen === 0" ng-click="toggle(member.id, 1)" class="list-group-item dropdown-item cp">解冻</li>
                                                <li ng-if="member.frozen === 1" ng-click="toggle(member.id, 0)" class="list-group-item dropdown-item cp">冻结</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/simulation/updataMember')): ?>
                                                <li class="list-group-item dropdown-item cp"  ng-click="getMemberinfo(member.id)">修改基本信息</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/simulation/deletMember')): ?>
                                                <li class="list-group-item dropdown-item cp" ng-click="deleteMember(member.id)">删除会员</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="simulNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div><!-- end card-->
        </div>
    </div>
    <!--新增会员模态框-->
    {include file="view/simulation/add" /}
    <!--修改彩金模态框-->
    {include file="view/simulation/gold" /}
    <!--修改余额模态框-->
    {include file="view/simulation/balance" /}
    <!--修改会员密码模态框-->
    {include file="view/simulation/changeMember" /}
    <!--修改基本信息模态框-->
    {include file="view/simulation/updata" /}
</div>
