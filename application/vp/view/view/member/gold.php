<!-- Modal -->
<div class="modal" id="revise-gold" tabindex="-1" role="dialog" aria-labelledby="addAgentModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                 <h6 class="modal-title">修改彩金</h6>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                <form>
                    <div class="form-group row">
                        <label for="modifyGold" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>修改彩金</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" id="modifyGold" placeholder="请输入修改金额" ng-model="modifyGold">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="remarks" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>备注</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" id="remarks" placeholder="请填写备注" ng-model="remarks">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="operationPassword2" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>操作密码</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control form-control-sm" autocomplete="new-password" id="operationPassword2" placeholder="操作密码即是登陆密码" ng-model="operationPassword2">
                        </div>
                    </div>
                </form>
                </div>
            </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="reviseGold('',memberId)">确定</button>
             </div>
        </div>
    </div>
</div>