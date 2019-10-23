<!-- Modal -->
<div class="modal" id="member-add" role="dialog" aria-labelledby="addAdminModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">新增会员</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                <form>
                    <div class="form-group row">
                        <label for="addUsername" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>账号:</label>
                        <div class="col-sm-10">
                            <input type="text" maxlength="11" placeholder="请输入手机号" autocomplete="off" class="form-control form-control-sm" id="addUsername">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addNickName" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>昵称</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请输入昵称" class="form-control form-control-sm" id="addNickName">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addPassword" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>密码</label>
                        <div class="col-sm-10">
                            <input type="password"  placeholder="请输入密码"  autocomplete="new-password" class="form-control form-control-sm" id="addPassword">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="draw" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>提现</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" id="draw" name="draw">
                                <option selected value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="status" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>状态</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="status" name="status">
                                <option selected value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="devStatus" class="col-sm-2  px-1 col-form-label"><sup class="text-danger">*</sup>发展下级</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="devStatus" name="devStatus">
                                <option selected value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
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
                <button type="button" class="btn btn-primary btn-sm" ng-click="addMember()">保存</button>
            </div>
        </div>
    </div>
</div>
