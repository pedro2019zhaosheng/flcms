<!-- Modal -->
<div class="modal" id="beijingEdit" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">赛事编辑</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <form class="form-horizontal">
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="beijingManual">手动截止时间</label>
                            <div class="controls col-sm-8">
                                <input type="text" name="beijingManual" id="beijingManual" autocomplete="off" class="form-control form-control-sm col-sm-12">
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary px-2" ng-click="clearHandTime()">清空</button>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="beijingNum">赛事编号</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" disabled="disabled" id="beijingNum" name="beijingNum" ng-model="editMatchNum">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="beijingMatchName">联赛名称</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" disabled="disabled" id="beijingMatchName" name="beijingMatchName" ng-model="editMatchName">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="beijingMaster">主队名称</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" disabled="disabled" id="beijingMaster" name="beijingMaster" ng-model="editHostName">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="beijingGuest">客队名称</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" disabled="disabled" id="beijingGuest" name="beijingGuest" ng-model="editGuestName">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="beijingSystem">系统截止时间</label>
                            <div class="controls col-sm-9">
                                <input type="text" name="beijingSystem" id="beijingSystem" disabled="disabled" ng-model="editSysTime" class="form-control form-control-sm col-sm-12">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="beijingComity">让球</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" autocomplete="off" id="beijingComity" name="beijingComity" ng-model="editRqs">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="beijingHalfMaster">主队半场得分</label>
                            <div class="controls col-sm-9">
                                <input type="text" ng-model="hostHalfScore" autocomplete="off" class="form-control form-control-sm col-sm-12" id="beijingHalfMaster" name="beijingHalfMaster">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="beijingHalfGuest">客队半场得分</label>
                            <div class="controls col-sm-9">
                                <input type="text" ng-model="guestHalfScore" autocomplete="off" class="form-control form-control-sm col-sm-12" id="beijingHalfGuest" name="beijingHalfGuest">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="beijingAllMaster">主队得分</label>
                            <div class="controls col-sm-9">
                                <input type="text" ng-model="hostTotalScore" autocomplete="off" class="form-control form-control-sm col-sm-12" id="beijingAllMaster" name="beijingAllMaster">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="beijingAllGuest">客队得分</label>
                            <div class="controls col-sm-9">
                                <input type="text" ng-model="guestTotalScore" autocomplete="off" class="form-control form-control-sm col-sm-12" id="beijingAllGuest" name="beijingAllGuest">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-sm btn-primary" ng-click="saveEdit()">保存</button>
            </div>
        </div>
    </div>
</div>