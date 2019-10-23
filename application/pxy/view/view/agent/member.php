<!-- Modal -->
<div class="modal" id="transfer-member" tabindex="-1" role="dialog" aria-labelledby="addAgentModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                 <h6 class="modal-title">转移会员</h6>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
            </div>
            <div class="modal-body">
                <form class="card-body">
                    <div class="form-group row">
                        <label for="proxyAccountM" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>代理账号</label>
                        <div class="col-sm-9">
                            <input type="text" maxlength="11" onkeyup="this.value = this.value.replace(/\D/gui, '')" class="form-control form-control-sm proxyAccountM" name="proxyAccountM" placeholder="请输入代理账号" ng-model="angentUserName">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="operationPasswordM" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>操作密码</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control form-control-sm operationPasswordM" name="operationPasswordM" autocomplete="new-password" placeholder="操作密码既是登陆密码" ng-model="angentPassword">
                        </div>
                    </div>
                </form>
            </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="transferAgent('',AgentId)">确定</button>
             </div>
        </div>
    </div>
</div>
