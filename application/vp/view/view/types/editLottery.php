<!-- Modal -->
<div class="modal" id="edit-lottery" role="dialog" aria-labelledby="addAgentModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">编辑彩种</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form>
                        <div class="form-group row">
                            <label for="lotteryNameEdit" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>彩种名称</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm" id="lotteryNameEdit" name="lotteryNameEdit" placeholder="请输入彩种名称">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="lotteryCodeEdit" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>彩种CODE</label>
                            <div class="col-sm-9">
                                <input type="text" ng-disabled="isRun === 1" class="form-control form-control-sm" id="lotteryCodeEdit" name="lotteryCodeEdit" placeholder="请输入彩种CODE">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="addPhoto" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>上传图标</label>
                            <div class="col-sm-9">
                                <input type="hidden" id="base64Edit">
                                <input type="file" name="file" class="form-control form-control-sm" id="updateImgIcon">
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-sm btn-primary" ng-click="editLottery(lotteryEditId)">保存</button>
            </div>
        </div>
    </div>
</div>
