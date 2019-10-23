<!-- Modal -->
<div class="modal" id="agent-promote" tabindex="-1" role="dialog" aria-labelledby="addAgentModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                 <h6 class="modal-title">提升代理</h6>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
            </div>
            <div class="modal-body">
                <form class="card-body">
                    <div class="form-group row">
                        <label for="commissionRatio" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>返佣比例</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" id="commissionRatio" placeholder="请输入返佣比例">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="developingSubordinates" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>允许发展下级</label>
                        <div class="col-sm-9">
                            <input type="radio" name="developingSubordinates" value="1" checked="checked">  是&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" name="developingSubordinates" value="0">  否
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="allowWithdrawals" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>允许提现</label>
                        <div class="col-sm-9">
                            <input type="radio" name="allowWithdrawals" value="1" checked="checked">  是&nbsp;&nbsp;&nbsp;&nbsp;
                            <input type="radio" name="allowWithdrawals" value="0">  否
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="operationPasswordP" class="col-sm-3 col-form-label"><sup class="text-danger">*</sup>操作密码</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control form-control-sm" id="operationPasswordP" placeholder="操作密码既是登陆密码">
                        </div>
                    </div>
                </form>
            </div>
             <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-sm">确定</button>
             </div>
        </div>
    </div>
</div>
