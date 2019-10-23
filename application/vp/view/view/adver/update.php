<!-- Modal -->
<div class="modal" id="adver-update" role="dialog" aria-labelledby="updateAdminModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">修改广告信息</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group row">
                        <label for="roleUpdate" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>类型</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="roleUpdate" id="roleUpdate">
                                <option selected value="">请选择广告类型</option>
                                <option value="{{ son.id }}" ng-repeat="son in roles">{{ son.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="updateName" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>标题</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="" class="form-control form-control-sm" id="updateName">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="updateAbstract" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>描述</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请填写广告描述" class="form-control form-control-sm" id="updateAbstract">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="updateStatus" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>状态:</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="updateStatus" name="updateStatus">
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="updateAdver(updateAdverId)">保存</button>
            </div>
        </div>
    </div>
</div>
