<div class="container-fluid" ng-controller="agentCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0">
                    <form class="form-inline justify-content-between">
                        <div class="btn-group">
                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/Agent/addAgent')): ?>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#agent-add" ng-click="getLotteryId()">新增代理</button>
                            <?php endif; ?>

                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/Agent/toggles')): ?>
                            <button type="button" ng-click="batchHandle(1)" class="btn btn-sm btn-success">批量启用</button>
                            <button type="button" ng-click="batchHandle(0)" class="btn btn-sm btn-warning">批量禁用</button>
                            <?php endif; ?>

                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/agent/export')): ?>
                            <button type="button" class="btn btn-secondary btn-sm" ng-click="export()">导出Excel</button>
                            <?php endif; ?>
                        </div>
                        <div class="btn-group">
                            <?php if(\app\common\model\AdminAuth::hasAuth('/vp/agent/return')): ?>
                            <button class="btn btn-sm btn-primary" type="button" onclick="window.location.href = '/vp/agent/return';">
                                代理商返点设置
                            </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="card-body pb-0">
                    <form class="form-inline col-sm-12">
                        <div class="form-group row col-sm-8">
                            <div class="form-group row pull-left">
                                <label for="userstate" class="col-form-label">状态:</label>
                                <div class="mx-sm-3">
                                    <select class="form-control form-control-sm select2" id="userstate" name="userstate">
                                        <option value="">请选择</option>
                                        <option value="0">禁用</option>
                                        <option value="1">启用</option>
                                    </select>
                                </div>

                                <label for="phone" class="col-form-label">账号:</label>
                                <input type="text" id="phone" name="phone" placeholder="请输入账号" ng-model="phone" class="form-control form-control-sm mx-sm-3">

                                <label for="nickname" class="col-form-label">昵称:</label>
                                <input type="text" id="nickname" name="nickname" placeholder="请输入昵称" ng-model="nickname" class="form-control form-control-sm mx-sm-3">

                                <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning mr-1">清空</button>
                                <button type="button" ng-click="searchSubmit()" class="btn btn-sm btn-primary">搜索</button>
                            </div>
                        </div>
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
                                <th>账号</th>
                                <th>昵称</th>
                                <th>上级代理</th>
                                <th>总充值</th>
                                <th>总输赢</th>
                                <th>余额</th>
                                <th>彩金</th>
                                <th>推荐会员数</th>
                                <th>代理商状态</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="item in agentList" on-finish-render="ngRepeatFinished">
                                <td class="check-child">
                                    <div class="checkbox checkbox-primary">
                                        <input data-value="{{ item.id }}" id="lotCheckbox" class="styled" type="checkbox">
                                        <label for="lotCheckbox" class="position_lable"></label>
                                    </div>
                                </td>
                                <td ng-bind="item.username | default : '无'"></td>
                                <td ng-bind="item.chn_name | default : '无'"></td>
                                <td ng-bind="item.top_username | default : '无'"></td>
                                <td ng-if="item.recharge == 0" ng-bind="item.recharge | currency : '￥'" class="text-danger"></td>
                                <td ng-if="item.recharge != 0" ng-click="rechargeList(item.username, 1)" class="font-style cp">
                                   <u ng-bind="item.recharge | currency : '￥'"></u>
                                </td>
                                <td ng-bind="item.profit  | currency : '￥'" class="text-danger"></td>
                                <!--<td ng-if="item.profit != 0" ng-click="winloseList(item.username, 1)" class="font-style cp">
                                    <u ng-bind="item.profit  | currency : '￥'"></u>
                                </td>-->
                                <td class="text-danger" ng-bind="item.balance | currency : '￥'"></td>
                                <td class="text-danger" ng-bind="item.hadsel | currency : '￥'"></td>
                                <td ng-if="item.RecUserNumber == 0" ng-bind="item.RecUserNumber"></td>
                                <td ng-if="item.RecUserNumber !== 0" ng-click="recommend(item.id)" class="font-style cp">
                                    <u ng-bind="item.RecUserNumber"></u>
                                </td>
                                <td>
                                    <span ng-if="item.frozen === 0" class="badge badge-danger ng-scope">冻结</span>
                                    <span ng-if="item.frozen === 1" class="badge badge-success ng-scope">正常</span>
                                </td>
                                <td>
                                    <div class="dropleft">
                                        <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-fw fa-cogs" aria-label="icon"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <ul class="list-group">
                                                <!--该功能不受权限控制-->
                                                <li class="list-group-item dropdown-item cp" data-toggle="modal" data-target="#agent-detail" ng-click="getAgentMore(item.id)">
                                                    查看更多
                                                </li>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/agent/transferAgent')): ?>
                                                <li class="list-group-item dropdown-item cp">
                                                    <div data-toggle="modal" data-target="#transfer-member" ng-click="updataAgentMethod(item.id)">转移我的下级</div>
                                                </li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/agent/reviseGold')): ?>
                                                <li class="list-group-item dropdown-item cp">
                                                    <div data-toggle="modal" data-target="#revise-gold" ng-click="updataAgentMethod(item.id)">修改彩金</div>
                                                </li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/agent/reviseBalance')): ?>
                                                <li class="list-group-item dropdown-item cp">
                                                    <div data-toggle="modal" data-target="#revise-balance" ng-click="updataAgentMethod(item.id)">修改余额</div>
                                                </li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/agent/setPassword')): ?>
                                                <li class="list-group-item dropdown-item cp"  data-toggle="modal" data-target="#setPassword-member" ng-click="setPasswordMethod(item.id)">修改密码</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/agent/updataAgent')): ?>
                                                    <li class="list-group-item dropdown-item cp"  ng-click="getAgentInfo(item.id)">修改代理商</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/agent/addCard')): ?>
                                                    <li class="list-group-item dropdown-item cp" ng-click="addAgentcard(item.id)">添加银行卡</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/agent/card')): ?>
                                                    <li class="list-group-item dropdown-item cp" ng-click="getAgentcard(item.id)">修改银行卡</li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/agent/deleteAgent')): ?>
                                                <li class="list-group-item dropdown-item cp" ng-click="deleteAgent(item.id)">
                                                    删除代理商
                                                </li>
                                                <?php endif; ?>

                                                <?php if(\app\common\model\AdminAuth::hasAuth('/vp/agent/toggle')): ?>
                                                <li ng-if="item.frozen === 0" ng-click="toggle(item.id, 1)" class="list-group-item dropdown-item cp">解冻</li>
                                                <li ng-if="item.frozen === 1" ng-click="toggle(item.id, 0)" class="list-group-item dropdown-item cp">冻结</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="agentNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div>
        </div>
    </div>
    <!--新增代理商模态框-->
    {include file="view/agent/add" /}
    <!--转移会员模态框-->
    {include file="view/agent/member" /}
    <!--修改彩金模态框-->
    {include file="view/agent/gold" /}
    <!--修改余额模态框-->
    {include file="view/agent/balance" /}
    <!--修改代理商密码模态框-->
    {include file="view/agent/changeMember" /}
    <!--修改基本信息模态框-->
    {include file="view/agent/updata" /}
    <!--修改代理银行卡信息模态框-->
    {include file="view/agent/card" /}
    <!--添加代理银行卡信息模态框-->
    {include file="view/agent/addCard" /}
    <!--查看更多代理商信息-->
    {include file="view/agent/agentDetail" /}
</div>
