<!-- Modal -->
<div class="modal" id="role-add" role="dialog" aria-labelledby="addAdminModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">新增角色</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form>
                        <div class="form-group row">
                            <label for="addRole" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>角色</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="请填写角色名" class="form-control form-control-sm" id="addRole">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="addDesc" class="col-sm-2 col-form-label"><sup class="text-danger">&nbsp;</sup>描述</label>
                            <div class="col-sm-10">
                                <input type="text" placeholder="请填写角色描述" class="form-control form-control-sm" id="addDesc">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="roleType" class="col-sm-2 px-1 col-form-label"><sup class="text-danger">*</sup>角色类型</label>
                            <div class="col-sm-10">
                                <select class="form-control form-control-sm" id="roleType">
                                    <option value="" selected>请选择角色类型</option>
                                    <option value="0">超管</option>
                                    <option value="1">普管</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="addSort" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>排序</label>
                            <div class="col-sm-10">
                                <input type="text" value="50" class="form-control form-control-sm" id="addSort">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="saveRole()">保存</button>
            </div>
        </div>
    </div>
</div>