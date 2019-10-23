<!-- Modal -->
<div class="modal" id="card-add" role="dialog" aria-labelledby="addAdminModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">添加银行卡</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form>
                        <div class="form-group row">
                            <label for="addCardname" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>真实姓名:</label>
                            <div class="col-sm-9">
                                <input type="text"  placeholder="持卡人姓名"  class="form-control form-control-sm" id="addCardname">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bank_code" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>开户行</label>
                            <div class="col-sm-9">
                                <select class="form-control form-control-sm select2" id="bank_code" name="bank_code">
                                    <option value="">请选择银行</option>
                                    <option value="{{itemlist.code +'|'+itemlist.name}}" ng-repeat="itemlist in bankList" ng-bind="itemlist.name"></option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="addCardNum" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>卡号</label>
                            <div class="col-sm-9">
                                <input type="text"  placeholder="请输入银行卡号"   class="form-control form-control-sm" id="addCardNum">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="draw" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>手机号码</label>
                            <div class="col-sm-9">
                                <input type="text" maxlength="11" placeholder="11位手机号码"  class="form-control form-control-sm" id="addPhoneNum">
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="saveBankCard(memberid)">保存</button>
            </div>
        </div>
    </div>
</div>
