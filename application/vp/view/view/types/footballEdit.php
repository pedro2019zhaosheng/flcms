<!-- Modal -->
<div class="modal" id="footballEdit" role="dialog" aria-hidden="true">
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
                            <label class="col-form-label col-sm-3" for="footballManual">手动截止时间</label>
                            <div class="controls col-sm-8">
                                <input type="text" name="footballManual" id="footballManual" autocomplete="off" class="form-control form-control-sm col-sm-12">
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary px-2" ng-click="clearHandTime()">清空</button>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="footballNum">赛事编号</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" disabled="disabled" id="footballNum" name="footballNum" ng-model="editMatchNum">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="footballMatchName">联赛名称</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" disabled="disabled" id="footballMatchName" name="footballMatchName" ng-model="editMatchName">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="footballMaster">主队名称</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" disabled="disabled" id="footballMaster" name="footballMaster" ng-model="editHostName">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="footballGuest">客队名称</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" disabled="disabled" id="footballGuest" name="footballGuest" ng-model="editGuestName">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="footballSystem">系统截止时间</label>
                            <div class="controls col-sm-9">
                                <input type="text" name="footballSystem" id="footballSystem" disabled="disabled" ng-model="editSysTime" class="form-control form-control-sm col-sm-12">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="footballComity">让球</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" autocomplete="off" id="footballComity" name="footballComity" ng-model="editRqs">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="footballHalfMaster">主队半场得分</label>
                            <div class="controls col-sm-9">
                                <input type="text" ng-model="hostHalfScore" autocomplete="off" class="form-control form-control-sm col-sm-12" id="footballHalfMaster" name="footballHalfMaster">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="footballHalfGuest">客队半场得分</label>
                            <div class="controls col-sm-9">
                                <input type="text" ng-model="guestHalfScore" autocomplete="off" class="form-control form-control-sm col-sm-12" id="footballHalfGuest" name="footballHalfGuest">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="footballAllMaster">主队得分</label>
                            <div class="controls col-sm-9">
                                <input type="text" ng-model="hostTotalScore" autocomplete="off" class="form-control form-control-sm col-sm-12" id="footballAllMaster" name="footballAllMaster">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="footballAllGuest">客队得分</label>
                            <div class="controls col-sm-9">
                                <input type="text" ng-model="guestTotalScore" autocomplete="off" class="form-control form-control-sm col-sm-12" id="footballAllGuest" name="footballAllGuest">
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

