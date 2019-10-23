<!-- Modal -->
<div class="modal" id="revise-balance" tabindex="-1" role="dialog" aria-labelledby="addAgentModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                 <h6 class="modal-title">修改余额</h6>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
            </div>
            <div class="modal-body">
                <form class="card-body">
                    <div class="form-group row">
                        <label for="modifyBalance" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>修改余额</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" id="modifyBalance" placeholder="请输入修改金额" ng-model="modifyBalance">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="remarksB" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>备注</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" id="remarksB" placeholder="请填写备注" ng-model="remarks2">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="operationPasswordB" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>操作密码</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control form-control-sm" id="operationPasswordB" autocomplete="new-password" placeholder="操作密码既是登陆密码" ng-model="operationPassword3">
                        </div>
                    </div>
                </form>
            </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="reviseBalance('',AgentId)">确定</button>
             </div>
        </div>
    </div>
</div>
