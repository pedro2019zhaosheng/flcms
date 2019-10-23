<!-- Modal -->
<div class="modal" id="role-update" role="dialog" aria-labelledby="updateAdminModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">修改角色</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form>
                        <div class="form-group row">
                            <label for="updateRole" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>角色</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="请填写角色名" ng-model="updateRole" class="form-control form-control-sm" id="updateRole">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="updateDesc" class="col-sm-2 col-form-label"><sup class="text-danger">&nbsp;</sup>描述</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="请填写角色描述" ng-model="updateDesc" class="form-control form-control-sm" id="updateDesc">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="updateSaveRole(roleId)">保存</button>
            </div>
        </div>
    </div>
</div>