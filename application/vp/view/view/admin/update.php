<!-- Modal -->
<div class="modal" id="admin-update" role="dialog" aria-labelledby="updateAdminModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">修改基本信息</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form>
                        <div class="form-group row">
                            <label for="roleUpdate" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>角色</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="roleUpdate" id="roleUpdate">
                                    <option selected value="">请选择角色</option>
                                    <option value="{{ son.id }}" ng-repeat="son in roles">{{ son.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="updateUsername" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>用户名</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="请填写用户名" class="form-control form-control-sm" id="updateUsername">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="updateNickName" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>昵称</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="请填写昵称" class="form-control form-control-sm" id="updateNickName">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="updatePhone" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>手机号</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="请填写手机号" class="form-control form-control-sm" id="updatePhone">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="updatePwd" class="col-sm-2 col-form-label"><sup class="text-danger"></sup>密码</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="请填写密码(不填则不修改)" class="form-control form-control-sm" id="updatePwd">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="updateAdmin(updateAdminId)">保存</button>
            </div>
        </div>
    </div>
</div>
