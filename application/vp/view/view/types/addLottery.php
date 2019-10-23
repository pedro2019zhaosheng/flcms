<!-- Modal -->
<div class="modal" id="add-lottery" tabindex="-1" role="dialog" aria-labelledby="addAgentModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">新增彩种</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form>
                        <div class="form-group row">
                            <label for="addLotteryName" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>彩种名称</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm" id="addLotteryName" name="addLotteryName" autocomplete="off" placeholder="请输入彩种名称">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="lotteryCode" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>彩种CODE</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control form-control-sm" id="lotteryCode" name="lotteryCode" autocomplete="off" placeholder="请输入彩种CODE">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="addState" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>状态</label>
                            <div class="col-sm-9 pt-2">
                             <span class="radio radio-info mr-3">
                                 <input type="radio" name="lotteryChoice" id="lotteryChoice0" checked value="0">
                                 <label for="lotteryChoice0" class="cp">正常</label>
                             </span>
                                <span class="radio radio-info">
                                 <input type="radio" name="lotteryChoice" id="lotteryChoice1" value="1">
                                 <label for="lotteryChoice1" class="cp">停售</label>
                             </span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="addPhoto" class="col-sm-3 px-1 col-form-label"><sup class="text-danger">*</sup>上传图标</label>
                            <div class="col-sm-9">
                                <input type="hidden" id="base64">
                                <input type="file" name="file" class="form-control form-control-sm" id="addImgIcon">
                            </div>
                        </div>

                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-sm btn-primary" ng-click="addLottery()">保存</button>
            </div>
        </div>
    </div>
</div>
