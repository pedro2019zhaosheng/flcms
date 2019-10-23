<div class="container-fluid" ng-controller="memberDetailCtrl">
    <div class="row betDetail">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 col-xl-12">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row-fluid">
                        <div class="span12">
                            <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                            <h6 class="page-title">个人中心</h6>
                            <!-- END PAGE TITLE & BREADCRUMB-->
                        </div>
                        <hr />
                    </div>
                    <div class="row">
                        <form class="form col-7">
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2" for="headImg">头像</label>
                                <style type="text/css">
                                    .jFiler-input{width: 25% !important; border-color: #6c757d !important; }
                                    .jFiler-input-caption{width: 100% !important; text-align: center !important; padding: 0 !important; line-height: 35px;}
                                    .jFiler-input-button{display: none !important;}
                                </style>
                                <img class="mx-2" style="width: 2rem; height: 2rem;" ng-src="{{ memberDetail.photo | default : '/static/lib/images/admin.png' }}" alt id="head_photo">
                                <div class="controls col-sm-5">
                                    <input type="hidden" id="base64_img">
                                    <input type="file" name="img" class="form-control form-control-sm cp" placeholder="请上传头像" id="headImg">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-sm-2" for="username">用户名</label>
                                <div class="controls col-sm-5">
                                    <input type="text" class="form-control form-control-sm col-sm-12 cna" id="username" name="username" ng-value="memberDetail.username" placeholder="请输入用户名" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-2" for="id_card">身份证号</label>
                                <div class="controls col-sm-5">
                                    <input type="text" class="form-control form-control-sm col-sm-12 cna" id="id_card" name="id_card" ng-value="memberDetail.id_card | default : '无'" placeholder="请输入用户名" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-2" for="nickname">昵称</label>
                                <div class="controls col-sm-5">
                                    <input type="text" class="form-control form-control-sm col-sm-12" id="nickname" name="nickname" autocomplete="off" ng-value="memberDetail.chn_name" placeholder="请输入昵称">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-2" for="myTop">我的上级</label>
                                <div class="controls col-sm-5">
                                    <input type="text" autocomplete="off" class="form-control form-control-sm col-sm-12" id="myTop" name="myTop" ng-value="memberDetail.top_username | default : '无'" placeholder="我的上级" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-2" for="pwd">密码</label>
                                <div class="controls col-sm-5">
                                    <input type="password" autocomplete="new-password" class="form-control form-control-sm col-sm-12" id="pwd" name="pwd" value="" placeholder="请输入密码（不填写则不修改）">
                                </div>
                                <span class="text-danger">密码需要包含6-18个英文字符，数字，下划线等</span>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-2" for="rpwd">重复密码</label>
                                <div class="controls col-sm-5">
                                    <input type="password" autocomplete="new-password" class="form-control form-control-sm col-sm-12" id="rpwd" name="rpwd" value="" placeholder="请重复密码">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-2" for="balance">余额</label>
                                <div class="controls col-sm-5">
                                    <input type="text" class="form-control form-control-sm col-sm-12" id="balance" name="balance" ng-value="memberDetail.balance" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-2" for="hansel">彩金</label>
                                <div class="controls col-sm-5">
                                    <input type="text" class="form-control form-control-sm col-sm-12" id="hansel" name="hansel" ng-value="memberDetail.hadsel" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-2" for="recharge">总充值</label>
                                <div class="controls col-sm-5">
                                    <input type="text" class="form-control form-control-sm col-sm-12" id="recharge" name="recharge" ng-value="memberDetail.recharge" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-2" for="totalDeposit">总提现</label>
                                <div class="controls col-sm-5">
                                    <input type="text" class="form-control form-control-sm col-sm-12" id="totalDeposit" name="totalDeposit" ng-value="memberDetail.withdraw_deposit" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-2" for="profit">总输赢</label>
                                <div class="controls col-sm-5">
                                    <input type="text" class="form-control form-control-sm col-sm-12" id="profit" name="profit" ng-value="memberDetail.profit" disabled>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-2" for="registerTime">注册时间</label>
                                <div class="controls col-sm-5">
                                    <input type="text" class="form-control form-control-sm col-sm-12 cna" id="registerTime" name="registerTime" ng-value="memberDetail.create_at | default : '无'" disabled="disabled">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-sm-2" for="updateTime">修改时间</label>
                                <div class="controls col-sm-5">
                                    <input type="text" class="form-control form-control-sm col-sm-12 cna" id="updateTime" name="updateTime" ng-value="memberDetail.update_at | default : '无'" disabled="disabled">
                                </div>
                            </div>

                            <div class="text-center col-7">
                                <button type="button" ng-click="updateAdminDetail()" class="btn btn-outline-primary btn-sm mr-3">
                                    <i class="fa fa-save mr-1" aria-hidden="true"></i>
                                    保存
                                </button>

                                <button type="button" class="btn btn-outline-secondary btn-sm mr-3" ng-click="noSave()">
                                    <i class="fa fa-reply mr-1" aria-hidden="true"></i>
                                    返回
                                </button>
                            </div>
                        </form>
                        <div class="col-5" style="border-left: 1px dashed #cecece; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                            <h6 class="text-secondary">我的邀请码&nbsp;:&emsp;<big class="text-purple" ng-bind="memberDetail.agent_invite_code"></big></h6>
                            <img class="mt-2" style="border: 1px solid #cecece; width: 30%;" ng-src="{{ memberDetail.invite_code_head }}" alt>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>