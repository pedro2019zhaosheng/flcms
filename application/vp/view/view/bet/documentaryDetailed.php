<!-- Modal -->
<div class="modal" id="documentary-detailed" role="dialog" aria-labelledby="documentaryDetailedModalCenterTitle" aria-hidden="true">
    <style rel="stylesheet" type="text/css">
        /* modal-xlg */
        @media screen and (min-width: 992px) {  .modal-xlg {max-width: 1000px;}  }
    </style>
    <div class="modal-dialog modal-dialog-centered modal-xlg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">跟单明细</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <style rel="stylesheet" type="text/css">
                #flowTable::-webkit-scrollbar{width: 5px;}
                #flowTable::-webkit-scrollbar-thumb{background-color: #cccccc;}
                #flowTable::-webkit-scrollbar-track{background-color: #ffffff;}
            </style>
            <div class="modal-body">
                <div class="card-body">
                    <div id="flowTable" style="max-height: 500px !important; overflow-y: auto;">
                        <table class="table table-hover">
                            <thead class="text-center">
                            <tr>
                                <th>序号</th>
                                <th>跟单单号</th>
                                <th>用户账户</th>
                                <th>下注金额(元)</th>
                                <th>奖金</th>
                                <th>嘉奖彩金</th>
                                <th>订单类型</th>
                                <th>支付时间</th>
                            </tr>
                            </thead>
                            <tbody class="text-center text-secondary">
                                <tr ng-repeat="(index, item) in flowOrder">
                                    <td>
                                        <span ng-bind="index + 1"></span>
                                    </td>
                                    <td>
                                        <span ng-bind="item.order_no | default : '无'"></span>
                                    </td>
                                    <td>
                                        <span ng-bind="item.member | default : '无'"></span>
                                    </td>
                                    <td>
                                        <span ng-bind="item.amount | default : '无'"></span>
                                    </td>
                                    <td>
                                        <span ng-bind="item.bonus | default : '无'"></span>
                                    </td>
                                    <td>
                                        <span ng-bind="item.bounty | default : '无'"></span>
                                    </td>
                                    <td>
                                        <span class="text-primary" ng-if="item.is_moni === 0">实单</span>
                                        <span class="text-secondary" ng-if="item.is_moni === 1">模拟</span>
                                    </td>
                                    <td>
                                        <span ng-bind="item.pay_time | default : '无'"></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        {include file="public/nodata" nodata="flowOrderNoData"/}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
