<!-- Modal -->
<div class="modal" id="num-detail" role="dialog" aria-hidden="true">
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
                                <span ng-if="betDetail.status === 0" class="badge badge-secondary">
                                    <small>待出票</small>
                                </span>
                                <span ng-if="betDetail.status === 1" class="badge badge-primary">
                                    <small>已出票</small>
                                </span>
                                <span ng-if="betDetail.status === 2" class="badge badge-warning">
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
                                <td>购买期数：
                                    <b class="text-danger" ng-bind="betDetail.beishu | default : '1'"></b>
                                    期
                                </td>
                            </tr>
                            <tr>
                                <td>单期注数：<span ng-bind="betDetail.zhu | default: '无'"></span></td>
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
                            中奖后停止追号&nbsp;:&nbsp;
                            <span ng-if="betDetail.is_yh === 0" class="badge badge-success">是</span>
                            <span ng-if="betDetail.is_yh === 1" class="badge badge-success">否</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>期号</th>
                                <th>开奖号码</th>
                                <th>投注金额</th>
                                <th>倍数</th>
                                <th>奖金</th>
                                <th>嘉奖彩金</th>
                                <th>开奖状态</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="item in betDetail.number_detail">
                                <td>
                                    <span ng-bind="item.number | default : '无'"></span>
                                    <small ng-if="item.is_newest === 1" class="text-purple">当前期</small>
                                </td>
                                <td>
                                    <span class="badge badge-light"
                                          ng-repeat="x in item.open_result track by $index"
                                          ng-bind="x">
                                    </span>
                                </td>
                                <td>
                                    <span ng-bind="item.amount | default : '0.00'"></span>
                                </td>
                                <td>
                                    <span ng-bind="item.multiple | default : '1'"></span>
                                </td>
                                <td>
                                    <b class="text-danger" ng-bind="item.bonus | default : '0.00'"></b>
                                    元
                                </td>
                                <td>
                                    <b class="text-danger" ng-bind="item.bounty | default : '0.00'"></b>
                                    元
                                </td>
                                <td>
                                    <span ng-if="item.status === 1" class="badge badge-warning">待开奖</span>
                                    <span ng-if="item.status === 2" class="badge badge-secondary">未中奖</span>
                                    <span ng-if="item.status === 3" class="badge badge-success">已中奖</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card mt-3">
                    <div class="card-header card-left-border">
                        <div class="float-left">
                            投注列表
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover text-center">
                            <thead>
                            <tr>
                                <th>玩法</th>
                                <th>投注项</th>
                                <th>注数</th>
                                <th>投注金额 <small class="text-secondary">(元)</small></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr ng-repeat="item in betDetail.bet_list">
                                <td class="align-middle">
                                    <span ng-bind="item.play | default : '无'"></span>
                                </td>
                                <td class="align-middle" ng-if="betDetail.lottery_code != 'FT'">
                                    <div class="mb-1" ng-repeat="son in item.bet">
                                        <span class="badge badge-light define-badge-red" ng-repeat="i in son track by $index" ng-bind="i"></span>
                                    </div>
                                </td>
                                <td class="align-middle" ng-if="betDetail.lottery_code == 'FT'">
                                    <div class="mb-1" ng-repeat="son in item.bet">
                                        <span class="text-danger" ng-bind="son"></span>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <span ng-bind="item.zhu | default : '无'"></span>
                                </td>
                                <td class="align-middle">
                                    <b class="text-danger" ng-bind="item.amount | default : '0.00'"></b>
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

