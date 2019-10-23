<!-- Modal -->
<div class="modal" id="card-agent" role="dialog" aria-labelledby="addAdminModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">编辑银行卡</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form>
                        <div ng-repeat="item in bankData">
                        <div class="form-group row">
                            <label for="addNickName" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>姓名</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control form-control-sm" id="addNickName{{item.id}}" ng-value="item.cardholder">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bank_code" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>开户行</label>
                            <div class="col-sm-10">
                                <select class="form-control form-control-sm select2" id="bank_code{{item.id}}" name="bank_code">
                                    <option selected value="{{item.code}}" ng-bind="item.bank"></option>
                                    <option value="{{itemlist.code}}" ng-repeat="itemlist in bankList" ng-bind="itemlist.name"></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="bank_num" class="col-sm-2  px-1 col-form-label"><sup class="text-danger">*</sup>卡号</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control form-control-sm" id="bank_num{{item.id}}" ng-value="item.bank_num">
                            </div>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="setCard(agentid,bankData)">保存</button>
            </div>
        </div>
    </div>
</div>
