<!-- Modal -->
<div class="modal" id="agent-return-add" role="dialog" aria-labelledby="addAgentModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">新增代理商返点</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="card-body">
                     <div class="form-group row">
                        <label for="choiceAgent" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>选择代理商</label>
                        <div class="col-sm-9">
                            <select id="choiceAgent" name="choiceAgent" class="form-control form-control-sm select2">
                              <option value="">请选择</option>
                              <option value="{{agentItem.id}}" ng-repeat="agentItem in agentList" ng-bind="agentItem.username"></option>
                            </select>
                        </div>
                     </div>
                     <div ng-repeat="lotteryList in lottery">
                         <div class="form-group row">
                             <label for="rebate" class="col-sm-3 col-form-label"><span ng-bind="lotteryList.name"></span></label>
                             <div class="col-sm-6">
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
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="addAgentReturn(lottery)">新增</button>
            </div>
        </div>
    </div>
</div>
