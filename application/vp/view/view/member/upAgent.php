<!-- Modal -->
<div class="modal" id="up-agent" role="dialog" aria-labelledby="addAdminModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">升级代理</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                <form>
                    <div ng-repeat="lotteryList in lottery">
                    <div class="form-group row">
                        <label for="rebate" class="col-sm-3 col-form-label"><span ng-bind="lotteryList.name"></span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" placeholder="返佣比例(百分比 例:7)" id="{{lotteryList.id}}">
                        </div>
                    </div>
                    </div>
                    <div class="form-group row">
                        <label for="lowlevel" class="col-sm-3 col-form-label">发展下级:</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="lowlevel" name="lowlevel">
                                <option selected value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="withdraw" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>提现权限:</label>
                        <div class="col-sm-9">
                            <select class="form-control form-control-sm select2" id="withdraw" name="withdraw">
                                <option selected value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="operpassword4" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>操作密码:</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control form-control-sm" autocomplete="new-password" id="operpassword4" placeholder="操作密码即是登陆密码"ng-model="operpassword4">
                        </div>
                    </div>
                </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="upAgent(lottery,memberId)">保存</button>
            </div>
        </div>
    </div>
</div>
