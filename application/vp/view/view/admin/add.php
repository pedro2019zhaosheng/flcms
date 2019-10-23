<!-- Modal -->
<div class="modal" id="admin-add" role="dialog" aria-labelledby="addAdminModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">新增管理员</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form>
                        <div class="form-group row">
                            <label for="selectRole" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>角色</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="selectRole" id="selectRole">
                                    <option selected value="">请选择角色</option>
                                    <option value="{{ son.id }}" ng-repeat="son in roles">{{ son.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="addUsername" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>用户名</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="请填写用户名" class="form-control form-control-sm" id="addUsername" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="addNickName" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>昵称</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="请填写昵称" class="form-control form-control-sm" id="addNickName" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="addPhone" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>手机号</label>
                            <div class="col-sm-10">
                                <input type="text" maxlength="11" placeholder="请填写手机号" autocomplete="off" class="form-control form-control-sm" id="addPhone">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="addPwd" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>密码</label>
                            <div class="col-sm-10">
                                <input type="password" placeholder="请填写密码" class="form-control form-control-sm" id="addPwd" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="addPhoto" class="col-sm-2 px-1 col-form-label">上传头像</label>
                            <div class="col-sm-10">
                                <input type="hidden" id="base64">
                                <input type="file" name="file" class="form-control form-control-sm" id="addPhoto">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="submitAdmin()">保存</button>
            </div>
        </div>
    </div>
</div>