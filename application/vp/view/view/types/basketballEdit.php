<!-- Modal -->
<div class="modal" id="basketballEdit" role="dialog" aria-labelledby="addAgentModalCenterTitle" aria-hidden="true">
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
                            <label class="col-form-label col-sm-3" for="basketballManual">手动截止时间</label>
                            <div class="controls col-sm-8">
                                <input type="text" name="basketballManual" id="basketballManual" autocomplete="off" class="form-control form-control-sm col-sm-12">
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary px-2" ng-click="clearHandTime()">清空</button>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="basketballNum">赛事编号</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" disabled="disabled" id="basketballNum" name="basketballNum" ng-model="editMatchNum">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="basketballMatchName">联赛名称</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" disabled="disabled" id="basketballMatchName" name="basketballMatchName" ng-model="editMatchName">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="basketballMaster">主队名称</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" disabled="disabled" id="basketballMaster" name="basketballMaster" ng-model="editHostName">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="basketballGuest">客队名称</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" disabled="disabled" id="basketballGuest" name="basketballGuest" ng-model="editGuestName">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="basketballSystem">系统截止时间</label>
                            <div class="controls col-sm-9">
                                <input type="text" name="basketballSystem" id="basketballSystem" disabled="disabled" class="form-control form-control-sm col-sm-12" placeholder="请选择系统截止时间" ng-model="editSysTime">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="basketballComity">让球</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" id="basketballComity" name="basketballComity" ng-model="editRqs">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="basketballAllMaster">主队得分</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" id="basketballAllMaster" name="basketballAllMaster" ng-model="hostScore">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-sm-3" for="basketballAllGuest">客队得分</label>
                            <div class="controls col-sm-9">
                                <input type="text" class="form-control form-control-sm col-sm-12" id="basketballAllGuest" name="basketballAllGuest" ng-model="guestScore">
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

