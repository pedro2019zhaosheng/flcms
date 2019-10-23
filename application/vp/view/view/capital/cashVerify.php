<!-- Modal -->
<div class="modal" id="cash-verify" tabindex="-1" role="dialog" aria-labelledby="addAgentModalCenterTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">提现审核</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="card-body">
                    <div class="form-group row">
                        <label for="pass" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>审核</label>
                         <div class="col-sm-9">
                             <span class="radio radio-info" id="status">
                                 <input type="radio" name="pass" id="doPass" value="1" checked>
                                 <label for="doPass">通过</label>
                             </span>
                             <span class="radio radio-info">
                                 <input type="radio" name="pass" id="notPass" value="0">
                                 <label for="notPass">驳回</label>
                             </span>
                         </div>
                    </div>
                    <div class="form-group row">
                        <label for="addommissionRate" class="col-sm-3 col-form-label"><sup class="text-danger">&nbsp;</sup>备注</label>
                        <div class="col-sm-9">
                            <textarea class="form-control form-control-sm" id="addommissionRate" rows="3"></textarea>
                        </div>
                    </div>
                    <input type="hidden" id="verifyID">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-primary btn-sm" ng-click="submitCash()">确定</button>
            </div>
        </div>
    </div>
</div>
