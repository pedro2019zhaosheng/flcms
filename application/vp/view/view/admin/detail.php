<div class="container-fluid" ng-controller="adminDetail">
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
                    <div class="row-fluid">
                        <div class="span12">
                            <form class="form-horizontal">
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-1" for="headImg">头像</label>
                                    <style type="text/css">
                                        .jFiler-input{width: 25% !important; border-color: #6c757d !important; }
                                        .jFiler-input-caption{width: 100% !important; text-align: center !important; padding: 0 !important; line-height: 35px;}
                                        .jFiler-input-button{display: none !important;}
                                    </style>
                                    <img class="mx-2" style="width: 2rem; height: 2rem;" ng-src="{{ adminDetail.photo | default : '/static/lib/images/admin.png' }}" alt id="head_photo">
                                    <div class="controls col-sm-3">
                                        <input type="hidden" id="base64_img">
                                        <input type="file" name="img" class="form-control form-control-sm cp" placeholder="请上传头像" id="headImg">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-1" for="username">用户名</label>
                                    <div class="controls col-sm-3">
                                        <input type="text" class="form-control form-control-sm col-sm-12 cna" id="username" name="username" ng-value="adminDetail.username" placeholder="请输入用户名" disabled>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-1" for="nickname">昵称</label>
                                    <div class="controls col-sm-3">
                                        <input type="text" class="form-control form-control-sm col-sm-12" id="nickname" name="nickname" autocomplete="off" ng-value="adminDetail.nick_name" placeholder="请输入昵称">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-1" for="email">邮箱</label>
                                    <div class="controls col-sm-3">
                                        <input type="text" autocomplete="off" class="form-control form-control-sm col-sm-12" id="email" name="email" ng-value="adminDetail.email" placeholder="请输入邮箱">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-1" for="pwd">密码</label>
                                    <div class="controls col-sm-3">
                                        <input type="password" autocomplete="new-password" class="form-control form-control-sm col-sm-12" id="pwd" name="pwd" value="" placeholder="请输入密码（不填写则不修改）">
                                    </div>
                                    <span class="text-danger">密码需要包含6-18个英文字符，数字，下划线等</span>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-1" for="rpwd">重复密码</label>
                                    <div class="controls col-sm-3">
                                        <input type="password" autocomplete="new-password" class="form-control form-control-sm col-sm-12" id="rpwd" name="rpwd" value="" placeholder="请重复密码">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-1" for="role_name">角色</label>
                                    <div class="controls col-sm-3">
                                        <input type="text" class="form-control form-control-sm col-sm-12 cna" id="role_name" name="role_name" ng-value="adminDetail.role_name" disabled="disabled">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-1" for="phone">手机号</label>
                                    <div class="controls col-sm-3">
                                        <input type="text" autocomplete="off" class="form-control form-control-sm col-sm-12" id="phone" name="phone" ng-value="adminDetail.phone | default : ''" placeholder="请输入手机号">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-1" for="registerTime">注册时间</label>
                                    <div class="controls col-sm-3">
                                        <input type="text" class="form-control form-control-sm col-sm-12 cna" id="registerTime" name="registerTime" ng-value="adminDetail.create_at | default : '无'" disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-1" for="updateTime">修改时间</label>
                                    <div class="controls col-sm-3">
                                        <input type="text" class="form-control form-control-sm col-sm-12 cna" id="updateTime" name="updateTime" ng-value="adminDetail.update_at | default : '无'" disabled="disabled">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-form-label col-sm-1" for="registerIp">注册IP</label>
                                    <div class="controls col-sm-3">
                                        <input type="text" class="form-control form-control-sm col-sm-12 cna" id="registerIp" name="registerIp" ng-value="adminDetail.signup_ip | default : '无'" disabled="disabled">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label col-sm-1" for="newIp">最新登录IP</label>
                                    <div class="controls col-sm-3">
                                        <input type="text" class="form-control form-control-sm col-sm-12 cna" id="newIp" name="newIp" ng-value="adminDetail.last_login_ip | default : '无'" disabled="disabled">
                                    </div>
                                </div>

                                <div class="text-center">

                                    <button type="button" ng-click="updateAdminDetail()" class="btn btn-outline-primary btn-sm mr-3">
                                        <i class="fa fa-save mr-1" aria-hidden="true"></i>保存
                                    </button>

                                    <button type="button" class="btn btn-outline-secondary btn-sm mr-3" ng-click="noSave()">
                                        <i class="fa fa-reply mr-1" aria-hidden="true"></i>上一页
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

