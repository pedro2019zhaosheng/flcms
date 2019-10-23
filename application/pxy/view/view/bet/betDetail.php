<!-- Modal -->
<div class="modal" id="bet-detail" role="dialog" aria-labelledby="betDetailModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xlg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    注单详情
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- 注单详情start -->
                <div class="card">
                    <div class="card-header card-left-border">
                        <div class="float-left">
                            <span class="col-1">
                                注单号&nbsp;:&nbsp;
                                <small class="text-secondary" ng-bind="betDetail.order_no  | default : '无'"></small>
                            </span>
                            <span class="col-1">
                                注单状态&nbsp;:&nbsp;
                                <span ng-if="betDetail.status === 0" class="badge badge-warning">
                                    <small>待出票</small>
                                </span>
                                <span ng-if="betDetail.status === 1" class="badge badge-primary">
                                    <small>已出票</small>
                                </span>
                                <span ng-if="betDetail.status === 2" class="badge badge-info">
                                    <small>待开奖</small>
                                </span>
                                <span ng-if="betDetail.status === 3" class="badge badge-secondary">
                                    <small>未中奖</small>
                                </span>
                                <span ng-if="betDetail.status === 4" class="badge badge-success">
                                    <small>已中奖</small>
                                </span>
                            </span>
                            <span class="col-1">
                                注单标题&nbsp;:&nbsp;
                                <small class="text-secondary" ng-bind="betDetail.order_title | default : '无'"></small>
                            </span>
                            <span class="col-1">
                                注单备注&nbsp;:&nbsp;
                                <small class="text-secondary" ng-bind="betDetail.beizhu | default : '无'"></small>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-left">
                            <tbody>
                            <tr>
                                <td>投注金额：<span ng-bind="betDetail.amount | default : '0.00'"></span>元</td>
                            </tr>
                            <tr>
                                <td class="text-danger">
                                    <b>中奖金额：
                                        <span ng-bind="betDetail.bonus | default : '0.00'"></span>
                                        元
                                    </b>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    投注信息：<span ng-bind="betDetail.zhu | default : '0'"></span>注<span ng-bind="betDetail.beishu | default : '1'"></span>倍
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    支付时间：<span ng-bind="betDetail.pay_time | default : '无' "></span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header card-left-border">
                        <div class="float-left">
                            注单方案
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>场次</th>
                                <th>赛事编号</th>
                                <th>主队VS客队</th>
                                <th>投注项</th>
                                <th>开奖结果</th>
                                <th>胆</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="item in betDetail.bet_content">
                                    <td class="align-middle">
                                        <span ng-bind="item.jc_num | default : '无'"></span>
                                        <br>
                                        <span ng-bind="item.start_time | default : '无'"></span>
                                    </td>
                                    <td class="align-middle">
                                        <span ng-bind="item.match_num | default : '无'"></span>
                                    </td>
                                    <td class="align-middle">
                                        <span ng-bind="item.host_name | default : '无'"></span>
                                        <b class="text-danger"><em>VS</em></b>
                                        <span ng-bind="item.guest_name | default : '无'"></span>
                                    </td>
                                    <td class="align-middle">
                                        <small class="d-block text-purple" ng-repeat="son1 in item.bet_body">
                                            <b ng-bind="son1.bet_item"></b>
                                        </small>
                                    </td>
                                    <td class="align-middle">
                                        <span ng-if="item.draw_result.length == 0" class="badge badge-secondary">未开奖</span>
                                        <small class="d-block text-danger" ng-if="item.draw_result.length != 0" ng-repeat="son2 in item.draw_result">
                                            <b ng-bind="son2"></b>
                                        </small>
                                    </td>
                                    <td class="align-middle">
                                        <span ng-if="item.single === 0" class="badge badge-secondary">否</span>
                                        <span ng-if="item.single === 1" class="badge badge-success">是</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header card-left-border">
                        <div class="float-left">
                            出票详情
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">
                            <tbody ng-repeat="(i, body) in betDetail.bet_detail">
                                <tr>
                                    <th class="text-purple" rowspan="0" style="vertical-align: middle;">
                                        <span ng-bind="i + 1"></span>
                                    </th>
                                </tr>
                                <tr ng-repeat="item in body.list">
                                    <td>
                                        <span ng-bind="item.jc_num | default : '无'"></span>
                                        &emsp;
                                        <span ng-bind="item.start_time | default : '无'"></span>
                                    </td>
                                    <td>
                                        <span ng-bind="item.host_name | default : '无'"></span>
                                        <b class="text-danger"><em>VS</em></b>
                                        <span ng-bind="item.guest_name | default : '无'"></span>
                                    </td>
                                    <td>
                                        <span ng-bind="item.bet_item | default : '无'" class="text-secondary"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <span ng-bind="body.extra.chuan | default : '无'"></span>
                                        &emsp;
                                        1注<span ng-bind="body.extra.bei | default : '1'"></span>倍
                                    </td>
                                    <td>金额：<b class="text-danger" ng-bind="body.extra.bei * 2"></b>
                                        元
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <span class="badge badge-secondary" ng-if="body.extra.status === 0">
                                            待开奖
                                        </span>
                                        <span class="badge badge-success" ng-if="body.extra.status === 1">
                                            已中奖
                                        </span>
                                        <span class="badge badge-secondary" ng-if="body.extra.status === 2">
                                            未中奖
                                        </span>
                                    </td>
                                    <td>奖金：<b class="text-danger" ng-bind="body.extra.bonus | default : '0'"></b>
                                        元
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

