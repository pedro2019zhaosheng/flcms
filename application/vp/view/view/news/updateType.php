<!-- Modal -->
<div class="modal" id="news-update" role="dialog" aria-labelledby="updateAdminModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">修改新闻类型</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group row">
                        <label for="updateStatus" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>状态:</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="updateStatus" name="updateStatus">
                                <option value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="updateName" class="col-sm-2 col-form-label px-1"><sup class="text-danger">*</sup>类型名称</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="" class="form-control form-control-sm" id="updateName" placeholder="请输入类型名称">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="updateType(updateTypeId)">保存</button>
            </div>
        </div>
    </div>
</div>
