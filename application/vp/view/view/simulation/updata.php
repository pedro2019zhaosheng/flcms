<!-- Modal -->
<div class="modal" id="updata-member" tabindex="-1" role="dialog" aria-labelledby="addAdminModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">修改会员</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                <form>
                    <div class="form-group row">
                        <label for="upUsername" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>账号:</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请输入手机号" readonly autocomplete="off" class="form-control form-control-sm" id="upUsername" ng-model="upUsername">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="upNickName" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>昵称</label>
                        <div class="col-sm-10">
                            <input type="text" placeholder="请输入昵称" class="form-control form-control-sm" id="upNickName" ng-model="upNickName">
                        </div>
                    </div>
                  <!--  <div class="form-group row">
                        <label for="updraw" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>提现</label>
                        <div class="col-sm-10">
                            <select class="form-control form-control-sm select2" id="updraw" name="updraw">
                                <option selected value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>-->
                    <div class="form-group row">
                        <label for="upstatus" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>状态</label>
                        <div class="col-sm-10">
                            <select class="form-control form-control-sm" id="upstatus" name="upstatus">
                                <option selected value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="updevStatus" class="col-sm-2  px-1 col-form-label"><sup class="text-danger">*</sup>发展下级</label>
                        <div class="col-sm-10">
                            <select class="form-control form-control-sm" id="updevStatus" name="updevStatus">
                                <option selected value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden"  id="upmemberId" ng-model="upmemberId">
                </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="updataMember()">保存</button>
            </div>
        </div>
    </div>
</div>
