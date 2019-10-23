<!-- Modal -->
<div class="modal" id="news-add" role="dialog" aria-labelledby="addAdminModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xlg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">新增场馆</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="card-body clearfix">
                    <div class="form-group row">
                        <label for="selectRole" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>场馆类型</label>
                        <div class="col-sm-10">
                            <select class="form-control form-control-sm" name="selectRole" id="selectRole">
                                <option selected value="">请选择</option>
                                <option value="{{ son.id }}" ng-repeat="son in roles">{{ son.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="newsStatus" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>场馆状态</label>
                        <div class="col-sm-10">
                            <select class="form-control form-control-sm select2" id="newsStatus" name="newsStatus">
                                <option value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addName" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>场馆名称</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请填写场馆名称" class="form-control form-control-sm" id="addName" name="addName">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addLocation" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>场馆位置</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请填写场馆位置" class="form-control form-control-sm" id="addLocation" name="addLocation">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addCost" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>场馆费用</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请填写场馆费用" class="form-control form-control-sm" id="addCost" name="addCost">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addLinkman" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>联系人</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请填写联系人" class="form-control form-control-sm" id="addLinkman" name="addLinkman">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addTel" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>联系电话</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请填写联系电话" class="form-control form-control-sm" id="addTel" name="addTel">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addAbstract" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>场馆描述</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请填写场馆描述" class="form-control form-control-sm" id="addAbstract" name="addAbstract">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addContent" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>场馆内容</label>
                        <div class="col-sm-10">
                            <div id="addContent"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addPhoto" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>场馆图片</label>
                        <div class="col-sm-10">
                            <input type="hidden" id="base64">
                            <input type="file" class="form-control form-control-sm" id="addPhoto" name="addPhoto">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="addNews()">保存</button>
            </div>
        </div>
    </div>
</div>
