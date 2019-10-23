<!-- Modal -->
<div class="modal" id="num-draw" role="dialog" aria-hidden="true">
    <style rel="stylesheet" type="text/css">
        /* modal-xlg */
        @media screen and (min-width: 992px) {  .modal-xlg {max-width: 1000px;}  }
    </style>
    <div class="modal-dialog modal-dialog-centered modal-xlg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">手动开奖</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <style type="text/css">
                #numHandLotteryTable::-webkit-scrollbar{width: 5px;}
                #numHandLotteryTable::-webkit-scrollbar-thumb{background-color: #cccccc;}
                #numHandLotteryTable::-webkit-scrollbar-track{background-color: #ffffff;}
            </style>
            <div class="modal-body">
                <div class="container-fluid ml-1 mb-3">
                    <b class="text-primary">数字彩注单</b>
                </div>
                <div class="card-body pt-0" id="numHandLotteryTable" style="max-height: 500px !important; overflow-y: auto;">
                    <table class="table table-hover text-center">
                        <thead class="text-secondary">
                        <tr>
                            <th>序号</th>
                            <th>注单号</th>
                            <th>会员</th>
                            <th>期号</th>
                            <th>下注金额</th>
                            <th>倍数</th>
                            <th>下注日期</th>
                            <th>支付状态</th>
                            <th>开奖状态</th>
                        </tr>
                        </thead>
                        <tbody class="text-secondary">
                        <tr ng-repeat="item in curNumOrderList">
                            <td ng-bind="$index + 1"></td>
                            <td ng-bind="item.order_no | default : '无'"></td>
                            <td ng-bind="item.username | default : '无'"></td>
                            <td ng-bind="item.number | default : '无'"></td>
                            <td ng-bind="item.amount | default : '无'"></td>
                            <td ng-bind="item.multiple | default : '无'"></td>
                            <td ng-bind="item.create_time | default : '无'"></td>
                            <td>
                                <span ng-if="item.pay_status === 1" class="badge badge-primary">已支付</span>
                                <span ng-if="item.pay_status === 0" class="badge badge-warning">支付中</span>
                                <span ng-if="item.pay_status === -1" class="badge badge-secondary">未支付</span>
                            </td>
                            <td>
                                <b ng-if="item.status === 1">待开奖</b>
                                <b ng-if="item.status === 2">未中奖</b>
                                <b ng-if="item.status === 3">已中奖</b>
                                <b ng-if="item.status === 4">已派奖</b>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    {include file="public/nodata" nodata="numBingoOrderNoData"/}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button"
                        ng-class="{true: 'btn btn-primary btn-sm', false : 'btn btn-secondary cna btn-sm'}[curNumOrderList.length != 0]"
                        ng-click="openDraw(expect, ctype)">
                    确认并立即开奖
                </button>
            </div>
        </div>
    </div>
</div>
