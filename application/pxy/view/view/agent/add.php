<!-- Modal -->
<div class="modal" id="agent-add" tabindex="-1" role="dialog" aria-labelledby="addAgentModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">新增代理商</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="card-body">
                     <div class="form-group row">
                        <label for="addPhone" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>手机号</label>
                        <div class="col-sm-10">
                            <input type="text" maxlength="11" onkeyup="this.value = this.value.replace(/\D/gui, '')" class="form-control form-control-sm" id="addPhone" ng-model="addPhone" placeholder="请输入手机号">
                        </div>
                     </div>
                    <div class="form-group row">
                        <label for="addNickName" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>昵称</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control form-control-sm" id="addNickName" autocomplete="off" ng-model="addNickName" placeholder="请输入昵称">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addPassword" class="col-sm-2 col-form-label"><sup class="text-danger">*</sup>密码</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control form-control-sm" autocomplete="new-password" id="addPassword" ng-model="addPassword" placeholder="请输入密码">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addCashWithdrawal" class="col-sm-2 col-form-label px-1"><sup class="text-danger">*</sup>提现权限</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" id="addCashWithdrawal" name="addCashWithdrawal" ng-model="addCashWithdrawal">
                                <option selected value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addLower" class="col-sm-2 col-form-label px-1"><sup class="text-danger">*</sup>发展下级</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" id="addLower" name="addLower" ng-model="addCashWithdrawal">
                                <option selected value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="addStatus" class="col-sm-2 col-form-label px-1"><sup class="text-danger">*</sup>状态</label>
                        <div class="col-sm-10">
                            <select class="form-control select2" id="addStatus" name="addStatus" ng-model="addCashWithdrawal">
                                <option selected value="">请选择</option>
                                <option value="1">开启</option>
                                <option value="0">关闭</option>
                            </select>
                        </div>
                    </div>
                    <div ng-repeat="lotteryList in lottery" on-finish-render="ngRepeatFinished">
                        <div class="form-group row">
                            <label for="rebate" class="col-sm-2 col-form-label px-1"><span ng-bind="lotteryList.name"></span></label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control form-control-sm" placeholder="返佣比例(百分比 例:7)" id="{{lotteryList.id}}">
                            </div>
                            <div class="col-sm-3">
                                 <select id="lotteryStatue{{lotteryList.id}}" name="lotteryStatue{{lotteryList.id}}" class="form-control form-control-sm select2"  on-finish-render="ngRepeatFinished">
                                   <option value="">请选择</option>
                                   <option value="1" selected>启用</option>
                                   <option value="0">禁用</option>
                                 </select>
                             </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="addAgent(lottery)">保存</button>
            </div>
        </div>
    </div>
</div>
