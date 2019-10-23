<div class="container-fluid" ng-controller="memberCtrl">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                 <div class="card-body pb-0">
                     <form class="form-inline pl-2">
                        <div class="form-group row">
                            <label>创建日期:</label>
                            <input type="text" id="sms_startTime" name="start_date" placeholder="请选择开始日期" class="form-control form-control-sm mx-sm-3">

                            <label >至</label>
                            <input type="text"  id="sms_endTime" name="end_date" placeholder="请选择结束日期" class="form-control form-control-sm mx-sm-3">

                            <label >手机号码:</label>
                            <input type="text" id="sms_phone" name="phone" placeholder="请输入手机号码" ng-model="username" class="form-control form-control-sm mx-sm-3">
                        </div>

                        <button type="button" ng-click="clearSearch()" class="btn btn-sm btn-warning mx-1">清空</button>
                        <button type="button" ng-click="searchSubmit()" class="btn btn-sm btn-primary">搜索</button>&nbsp;

                    </form><br>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>接收手机</th>
                                <th>短信类型</th>
                                <th>订单号</th>
                                <th>发送内容</th>
                                <th>发送时间</th>
                            </thead>
                            <tbody>
                            <tr ng-repeat="(index, item) in smsList">
                                <td ng-bind="(smsCurrentPage - 1) * smsPerPage + index + 1"></td>
                                <td ng-bind="item.phone | default : '无'"></td>
                                <td>
                                    <span class="badge badge-primary" ng-if="item.tpltype === 0">普通短信</span>
                                    <span class="badge badge-success" ng-if="item.tpltype === 1">订单短信</span>
                                </td>
                                <td ng-bind="item.orderid | default : '无'"></td>
                                <td ng-bind="item.content | default : '无'"></td>
                                <td ng-bind="item.create_time | default : '无'"></td>
                            </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="smsNoData"/}
                    </div>
                    {include file="public/page" /}
                </div>
            </div><!-- end card-->
        </div>
    </div>
</div>