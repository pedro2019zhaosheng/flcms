<!-- Modal -->
<div class="modal" id="transfer-member" tabindex="-1" role="dialog" aria-labelledby="addAgentModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                 <h6 class="modal-title">转移我的下级</h6>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
            </div>
            <div class="modal-body">
                <form class="card-body">
                    <div class="form-group row">
                        <label for="proxyAccountM" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>目标代理</label>
                        <div class="col-sm-9">
                            <input type="text" maxlength="11" class="form-control form-control-sm proxyAccountM" name="proxyAccountM" placeholder="请输入代理账号" ng-model="angentUserName">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="isSelf" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>转移自身</label>
                        <div class="col-sm-9">
                            <select name="isSelf" id="isSelf" class="form-control form-control-sm">
                                <option value="">请选择</option>
                                <option value="0">否</option>
                                <option value="1">是</option>
                            </select>
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
                <button type="button" class="btn btn-primary btn-sm" ng-click="transferAgent('', AgentId)">确定</button>
             </div>
        </div>
    </div>
</div>
