<!-- Modal -->
<div class="modal" id="member-add" role="dialog" aria-labelledby="addAdminModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">新增模拟账号</h6>
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
                            <input type="text" maxlength="18" placeholder="请输入账号" autocomplete="off" class="form-control form-control-sm" id="addUsername">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addNickName" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>昵称</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请输入昵称" class="form-control form-control-sm" id="addNickName" ng-model="addNickName">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addPassword" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>密码</label>
                        <div class="col-sm-10">
                            <input type="password"  placeholder="请输入密码" autocomplete="new-password" class="form-control form-control-sm" id="addPassword" ng-model="addPassword">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="status" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>状态</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="status" name="status">
                                <option value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
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