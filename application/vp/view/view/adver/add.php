<!-- Modal -->
<div class="modal" id="adver-add" role="dialog" aria-labelledby="addAdminModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">新增广告</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="card-body">
                    <div class="form-group row">
                        <label for="selectRole" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>类型</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" name="selectRole" id="selectRole">
                                <option selected value="">请选择</option>
                                <option value="{{ son.id }}" ng-repeat="son in roles">{{ son.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="status" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>状态:</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" id="status" name="status">
                                <option value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addName" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>标题</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请填写广告标题" class="form-control form-control-sm" id="addName" name="addName">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addAbstract" class="col-sm-2 col-form-label"><!--<sup class="text-danger">*</sup>-->描述</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请填写广告描述" class="form-control form-control-sm" id="addAbstract" name="addAbstract">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addUrl" class="col-sm-2 col-form-label"><!--<sup class="text-danger">*</sup>-->链接</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请填写广告链接" class="form-control form-control-sm" id="addUrl">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addPhoto" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>广告图</label>
                        <div class="col-sm-10">
                            <input type="hidden" id="base64">
                            <input type="file" name="file" class="form-control form-control-sm" id="addPhoto">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="addAdver()">保存</button>
            </div>
        </div>
    </div>
</div>
