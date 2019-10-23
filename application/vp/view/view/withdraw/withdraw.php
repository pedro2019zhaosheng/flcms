<!-- Modal -->
<div class="modal" id="withdraw-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">代充值</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <style rel="stylesheet" type="text/css">
                .row-header{border-right: 1px dotted #CCCCCC; display: flex; justify-content: center; align-items: center;}
                .header-img{width: 6rem; height: 6rem; border: 1px dashed #eeeeee;}
            </style>
            <div class="modal-body text-secondary">
                <div class="row">
                    <div class="col-3 row-header">
                        <img class="header-img" ng-src="{{ memberDetail.photo }}" alt>
                    </div>
                    <div class="col-9">
                        <div class="card-body pt-0 pb-0 px-5">
                            <div class="row">
                                <p class="col text-secondary"><b>用户昵称</b>&nbsp;:&nbsp;<span ng-bind="memberDetail.chn_name | default : '无'"></span></p>
                                <p class="col"><b>用户账号</b>&nbsp;:&nbsp;<span ng-bind="memberDetail.username"></span></p>
                            </div>
                            <div class="row">
                                <p class="col text-secondary">
                                    <b>用户身份</b>&nbsp;:&nbsp;
                                    <span class="badge badge-primary" ng-if="memberDetail.role === 1">会员</span>
                                    <span class="badge badge-info" ng-if="memberDetail.role === 2">代理商</span>
                                </p>
                                <p class="col">
                                    <b>账号类型</b>&nbsp;:&nbsp;
                                    <label class="badge badge-secondary" ng-if="memberDetail.is_moni === 0">模拟账号</label>
                                    <label class="badge badge-primary" ng-if="memberDetail.is_moni === 1">真实账号</label>
                                </p>
                            </div>
                            <div class="row">
                                <p class="col text-secondary">
                                    <b>会员状态</b>&nbsp;:&nbsp;
                                    <span class="badge badge-danger" ng-if="memberDetail.frozen === 0">冻结</span>
                                    <span class="badge badge-success" ng-if="memberDetail.frozen === 1">正常</span>
                                </p>
                                <p class="col">
                                    <b>账户余额</b>&nbsp;:&nbsp;
                                    <span class="text-danger" ng-bind="memberDetail.balance"></span>
                                </p>
                            </div>
                            <div class="row">
                                <p class="col text-secondary">
                                    <b>冻结资金</b>&nbsp;:&nbsp;
                                    <span class="text-danger" ng-bind="memberDetail.frozen_capital"></span>
                                </p>
                                <p class="col">
                                    <b>账户彩金</b>&nbsp;:&nbsp;
                                    <span class="text-danger" ng-bind="memberDetail.hadsel"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <form class="form-inline">
                    <label for="withdrawAmount" class="text-secondary">提现金额:</label>
                    <input id="withdrawAmount" type="text" class="form-control form-control-sm mx-sm-3" autocomplete="off" ng-model="withdrawAmount" placeholder="请输入提现金额">
                    <button type="submit" class="btn btn-primary btn-sm" ng-click="withdrawSave()">立即提现</button>
                </form>
            </div>
        </div>
    </div>
</div>