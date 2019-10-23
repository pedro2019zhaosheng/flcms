<div class="container-fluid" ng-controller="msgCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body pb-0" ng-show="isShowNav">
                    <form class="form-inline">
                        <div class="form-group">
                            <label>日期:</label>
                            <input type="text" id="from" name="from" autocomplete="off" placeholder="请选择开始日期" class="form-control form-control-sm mx-sm-3">

                            <label for="to">至</label>
                            <input type="text"  id="to" name="to" autocomplete="off" placeholder="请选择结束日期" class="form-control form-control-sm mx-sm-3">

                            <label for="username">用户账号:</label>
                            <input type="text" id="username" name="username" autocomplete="off" placeholder="请输入用户账号" class="form-control form-control-sm mx-sm-3">

                            <label for="msgType" class="mr-3">消息类型:</label>
                            <select name="msgType" id="msgType" class="form-control form-control-sm mx-sm-3">
                                <option value="-1">请选择</option>
                                <option value="1">系统消息</option>
                                <option value="2">代理商消息</option>
                                <option value="3">会员消息</option>
                                <option value="4">其他</option>
                            </select>

                            <label for="bodyType" class="mx-3">内容类型:</label>
                            <select name="bodyType" id="bodyType" class="form-control form-control-sm mx-sm-3">
                                <option value="-1">请选择</option>
                                <option value="1">资金提现</option>
                                <option value="2">会员注单</option>
                                <option value="3">资金充值</option>
                                <option value="4">其他</option>
                            </select>

                            <label for="status" class="mx-3">状态:</label>
                            <select name="status" id="status" class="form-control form-control-sm mx-sm-3">
                                <option value="-1">请选择</option>
                                <option value="0">未读</option>
                                <option value="1">已读</option>
                            </select>
                        </div>

                        <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning ml-3 mr-1">清空</button>
                        <button type="button" ng-click="searchSubmit()" class="btn btn-sm btn-primary">搜索</button>
                    </form>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>头像</th>
                                <th>用户</th>
                                <th>账号</th>
                                <th class="text-left">消息内容</th>
                                <th>消息类型</th>
                                <th>内容类型</th>
                                <th>日期</th>
                                <th>状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="item in msgList">
                                <td>
                                    <img style="width: 2rem; height: 2rem;" ng-src="{{ item.icon ? item.icon : '/static/lib/images/msgicon.jpg' }}" alt>
                                </td>
                                <td>
                                    <span ng-bind="item.name | default : '无'"></span>
                                </td>
                                <td>
                                    <span ng-bind="item.account | default : '无'"></span>
                                </td>
                                <td class="text-left">
                                    <span ng-bind="item.desc | default : '无'"></span>
                                </td>
                                <td>
                                    <span ng-if="item.msg_type === 1">系统消息</span>
                                    <span ng-if="item.msg_type === 2">代理商消息</span>
                                    <span ng-if="item.msg_type === 3">会员消息</span>
                                    <span ng-if="item.msg_type === 4">其他</span>
                                </td>
                                <td>
                                    <span ng-if="item.body_type === 1">资金提现</span>
                                    <span ng-if="item.body_type === 2">会员注单</span>
                                    <span ng-if="item.body_type === 3">资金充值</span>
                                    <span ng-if="item.body_type === 4">其他</span>
                                </td>
                                <td>
                                    <span ng-bind="item.send_time | default : '无'"></span>
                                </td>
                                <td>
                                    <span ng-if="item.read_state === 1" class="badge badge-success">已读</span>
                                    <span ng-if="item.read_state === 0" class="badge badge-secondary">未读</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="msgNoData"/}
                    </div>
                    <div class="ifShowWrap" ng-show="isShowNav">
                        {include file="public/page" /}
                    </div>
                </div>
            </div><!-- end card-->
        </div>
    </div>
</div>