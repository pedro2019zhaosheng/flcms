<!-- Modal -->
<div class="modal" id="setPassword-member" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                 <h6 class="modal-title">修改密码</h6>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form>
                        <div class="form-group row">
                            <label for="proxyAccount" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>新密码:</label>
                            <div class="col-sm-9">
                                <input type="password" maxlength="18" class="form-control form-control-sm" autocomplete="new-password" placeholder="请输入新密码" ng-model="password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="repeatPassword" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>重复密码:</label>
                            <div class="col-sm-9">
                                <input type="password" id="repeatPassword" maxlength="18" autocomplete="new-password" class="form-control form-control-sm"  placeholder="请重复密码" ng-model="rePassword">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="execPassword" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>操作密码:</label>
                            <div class="col-sm-9">
                                <input type="password" id="execPassword" class="form-control form-control-sm" autocomplete="new-password" placeholder="操作密码即是登陆密码" ng-model="angentPassword">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="setPassword(memberId)">确定</button>
             </div>
        </div>
    </div>
</div>
