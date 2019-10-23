<!-- Modal -->
<div class="modal" id="system-addSite" tabindex="-1" role="dialog" aria-labelledby="addSiteModalCenterTitle" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-centered" role="document" id="addSite1">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">新增客服</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="card-body">
                     <div class="form-group row">
                        <label for="site_name" class="col-sm-2 col-form-label px-1"><sup class="text-danger">*</sup>客服名称</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" autocomplete="off" id="site_name" placeholder="请输入客服名称">
                        </div>
                     </div>
                    <div class="form-group row">
                        <label for="site_phone" class="col-sm-2 col-form-label px-1"><sup class="text-danger">&nbsp;</sup>客服编号</label>
                        <div class="col-sm-7">
                            <input type="text" placeholder="默认系统生成客服编号" ng-init="disabledVal = true" ng-disabled="disabledVal" class="{{ disClass | default : 'form-control-plaintext' }} form-control-sm" autocomplete="off" id="site_phone">
                        </div>
                        <div class="col-sm-3 text-right">
                            <button class="btn btn-sm btn-outline-primary" ng-click="toggleDisable(disabledVal, btnVal)" type="button" ng-init="btnVal = '手动填写'" ng-bind="btnVal"></button>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label for="site_select" class="col-sm-2 col-form-label px-1"><sup class="text-danger">*</sup>状态</label>
                        <div class="col-sm-10">
                            <select class="form-control form-control-sm" name="site_select" id="site_select">
                                <option selected value="">请选择</option>
                                <option value="1" >启用</option>
                                <option value="0" >禁用</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row upload_img">
                        <label for="site_tag" class="col-sm-2 col-form-label px-1"><sup class="text-danger">*</sup>客服图标</label>
                        <div class="col-sm-10" id="server_tag">
                            <input type="hidden" id="base64_tag"  class="form-control form-control-sm">
                            <input type="file" name="icon" class="form-control form-control-sm" id="site_tag" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="site_img" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>二维码</label>
                        <div class="col-sm-10">
                            <input type="hidden" id="site_img_hidden"  class="form-control form-control-sm">
                            <input type="file" name="img" class="form-control form-control-sm" id="site_img">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click='save()'>保存</button>
            </div>
        </div>
    </div>
</div>
