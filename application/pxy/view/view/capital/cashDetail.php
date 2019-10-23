<!-- Modal -->
<div class="modal" id="cash-detail" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">充值记录详情</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <table class="table table-bordered table-hover text-center">
                        <thead>
                            <tr>
                                <th><small>会员账号</small></th>
                                <th><small>真实姓名</small></th>
                                <th><small>到账银行</small></th>
                                <th><small>银行卡号</small></th>
                                <th><small>持卡人姓名</small></th>
                                <th><small>身份证号</small></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td ng-bind="datas.username | default : '无'"></td>
                                <td ng-bind="datas.real_name | default : '无'"></td>
                                <td ng-bind="datas.bank | default : '无'"></td>
                                <td ng-bind="datas.bank_num | default : '无'"></td>
                                <td ng-bind="datas.cardholder | default : '无'"></td>
                                <td ng-bind="datas.id_card | default : '无'"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
