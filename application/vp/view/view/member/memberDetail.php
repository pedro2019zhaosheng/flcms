<!-- Modal -->
<div class="modal" id="user-detail" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">会员详情</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="row">
                        <div class="col-3 my-row">
                            <img class="detail-img" ng-src="{{ memberDetail.photo }}" alt>
                        </div>
                        <div class="col-9 px-5">
                            <p class="my-define-row">
                                <span>
                                    <b>真实姓名:&nbsp;</b>
                                    <span ng-bind="memberDetail.real_name | default : '空'"></span>
                                </span>
                                <span>
                                    <b>身份证号:&nbsp;</b>
                                    <span ng-bind="memberDetail.id_card | default : '空'"></span>
                                </span>
                            </p>
                            <p class="my-define-row">
                                <span>
                                    <b>实名状态:&nbsp;</b>
                                    <span class="badge badge-secondary" ng-if="memberDetail.real_status === 0">未实名</span>
                                    <span class="badge badge-success" ng-if="memberDetail.real_status === 1">已实名</span>
                                </span>
                                <span>
                                    <b>是否允许提现:&nbsp;</b>
                                    <span class="badge badge-secondary" ng-if="memberDetail.is_return_money == 0">不允许</span>
                                    <span class="badge badge-success" ng-if="memberDetail.is_return_money == 1">允许</span>
                                </span>
                            </p>
                            <p class="my-define-row">
                                <span>
                                    <b>是否允许发展下线:&nbsp;</b>
                                    <span class="badge badge-secondary" ng-if="memberDetail.dev_status == 0">不允许</span>
                                    <span class="badge badge-success" ng-if="memberDetail.dev_status == 1">允许</span>
                                </span>
                                <span>
                                    <b>邀请码:&nbsp;</b>
                                    <span ng-bind="memberDetail.agent_invite_code | default : '空'"></span>
                                </span>
                            </p>
                            <p class="my-define-row">
                                <span>
                                    <b>注册时间:&nbsp;</b>
                                    <span ng-bind="memberDetail.create_at | default : '空'"></span>
                                </span>
                                <span>
                                    <b>更新时间:&nbsp;</b>
                                    <span ng-bind="memberDetail.update_at | default : '空'"></span>
                                </span>
                            </p>
                            <p class="my-define-row">
                                <span>
                                    <b>最后登录App时间:&nbsp;</b>
                                    <span ng-bind="memberDetail.last_login_time | default : '空'"></span>
                                </span>
                                <span>
                                    <b>最后登录App IP:&nbsp;</b>
                                    <span ng-bind="memberDetail.last_login_ip | default : '空'"></span>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
