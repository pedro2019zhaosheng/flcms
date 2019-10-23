<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>会员注册</title>
    <link rel="stylesheet" href="/static/lib/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/lib/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/static/lib/layer-mobile/layer.css">
    <link rel="stylesheet" href="/static/web/webreg/reg.css">
</head>
<body>
<div class="card text-white bg-secondary">
    <div class="card-header">
        会员注册
    </div>
    <div class="card-body">
        <form>
            <div class="form-group row">
                <label for="username" class="col-sm-3 col-form-label">
                    <i class="fa fa-user-o fa-fw" aria-hidden="true"></i>
                    手机号:
                </label>
                <div class="col-sm-9">
                    <input type="text" id="username" class="form-control" maxlength="11" placeholder="请输入手机号" autocomplete="off" oninput="this.value = this.value.replace(/[^\d]/g, '');">
                </div>
            </div>
            <div class="form-group row">
                <label for="nickname" class="col-form-label col-sm-3">
                    <i class="fa fa-user-circle fa-fw" aria-hidden="true"></i>
                    昵称:
                </label>
                <div class="col-sm-9">
                    <input type="text" id="nickname" maxlength="20" class="form-control" placeholder="请输入昵称" autocomplete="off">
                </div>
            </div>
            <div class="form-group row">
                <label for="password" class="col-form-label col-sm-3">
                    <i class="fa fa-lock fa-fw" aria-hidden="true"></i>
                    密码:
                </label>
                <div class="col-sm-9">
                    <input type="password" id="password" class="form-control" placeholder="请输入密码" autocomplete="new-password">
                </div>
            </div>
            <div class="form-group row">
                <label for="repassword" class="col-form-label col-sm-3">
                    <i class="fa fa-lock fa-fw" aria-hidden="true"></i>
                    重复密码:
                </label>
                <div class="col-sm-9">
                    <input type="password" id="repassword" class="form-control" placeholder="请重复密码" autocomplete="new-password">
                </div>
            </div>
            <div class="form-group row">
                <label for="verifyCode" class="col-form-label col-sm-3">
                    <i class="fa fa-check-circle-o fa-fw" aria-hidden="true"></i>
                    验证码:
                </label>
                <div class="col-sm-9 btn-group">
                    <input type="text" id="verifyCode" class="form-control" placeholder="请输入验证码" autocomplete="off">
                    <button type="button" id="codeBtn" class="btn btn-outline-success">
                        验证码
                    </button>
                </div>
            </div>
            <div class="form-group row">
                <label for="inviteCode" class="col-form-label col-sm-3">
                    <i class="fa fa-address-card fa-fw" aria-hidden="true"></i>
                    邀请码:
                </label>
                <div class="col-sm-9">
                    <input type="text" id="inviteCode" class="form-control" value="{$ic}" placeholder="输入邀请码" disabled="disabled" autocomplete="off">
                </div>
            </div>
            <div class="form-group row justify-content-center pt-3">
                <button type="button" id="onlySubmit" class="btn btn-outline-light">
                    &emsp;马上注册&emsp;
                </button>
            </div>
        </form>
    </div>
</div>
{if($isWx)}
<div class="tip-bg"></div>
{/if}
<script src="/static/lib/js/jquery.min.js"></script>
<script src="/static/lib/js/bootstrap.min.js"></script>
<script src="/static/lib/layer-mobile/layer.js"></script>
<script src="/static/web/webreg/reg.js"></script>
</body>
</html>