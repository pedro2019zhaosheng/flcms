<!-- Modal -->
<div class="modal" id="system-editSite" tabindex="-1" role="dialog" aria-labelledby="addSiteModalCenterTitle"
     aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered" role="document" id="addSite1">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-center">编辑客服</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="card-body">
                     <div class="form-group row">
                        <label for="edit_name" class="col-sm-2 col-form-label px-1"><sup class="text-danger">*</sup>客服名称</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" id="edit_name" placeholder="请输入客服名称" value="{{list.name}}">
                        </div>
                     </div>
                    <div class="form-group row">
                        <label for="edit_phone" class="col-sm-2 col-form-label px-1"><sup class="text-danger">*</sup>客服号码</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请输入客服号" class="form-control form-control-sm" autocomplete="new-password" id="edit_phone">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="edit_icon" class="col-sm-2 col-form-label px-1"><sup class="text-danger">*</sup>客服图标</label>
                        <div class="col-sm-10">
                            <input type="hidden" id="edit_icon_hidden">
                            <input type="file" name="file" class="form-control form-control-sm" id="edit_icon">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="edit_img" class="col-sm-2 col-form-label px-1"><sup class="text-danger">*</sup>二维码</label>
                        <div class="col-sm-10">
                            <input type="hidden" id="edit_img_hidden">
                            <input type="file" name="file" class="form-control form-control-sm" id="edit_img">
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click='editsave(id)'>保存</button>
            </div>
        </div>
    </div>
</div>
