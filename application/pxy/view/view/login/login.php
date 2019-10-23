<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>登录</title>
    <link rel="stylesheet" href="/static/lib/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/lib/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/static/lib/css/toastr.min.css">
    <link rel="stylesheet" href="/static/pxy/login/slide.css">
    <link rel="stylesheet" href="/static/pxy/login/login.css">
</head>
<body>

<div class="top-content">
    <div class="inner-bg">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 m-auto text">
                    <h2>代理商后台管理</h2>
                    <div class="description">
                        <!-- <p>足篮竞彩平台是专业的网上买彩票的平台。提供足球彩票、竞彩足球、体育彩票、福利彩票、高频 彩等多种彩票投注、彩票合买、彩票开奖、彩票预测等服务,并有实时足球(足彩)</p> -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 m-auto form-box">
                    <div class="form-top">
                        <div class="form-top-left">
                            <h3>管理后台登录</h3>
                            <p>请在下面输入您的账号和密码</p>
                        </div>
                        <div class="form-top-right">
                            <i class="fa fa-lock"></i>
                        </div>
                    </div>
                    <div class="form-bottom">
                        <form role="form"
                              action=""
                              method="post" class="login-form">
                            <div class="form-group">
                                <label class="sr-only" for="form-username">账号</label>
                                <input type="text" name="form-username" autocomplete="off" placeholder="请输入账号" class="form-username form-control" id="form-username">
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="form-password">密码</label>
                                <input type="password" name="form-password" autocomplete="off" placeholder="请输入密码" class="form-password form-control" id="form-password">
                            </div>
                            <input type="hidden" name="_aid" value="{$_aid}">
                            <div class="form-group">
                                <label class="sr-only">验证</label>
                                <div class="verify-wrap" id="verify-wrap"></div>
                            </div>
                            <button type="submit" class="btn">确认登录</button>
                        </form>
                    </div>
                </div>
            </div>
          <!--  <div class="row">
                <div class="col-sm-6 social-login">
                    <h3>关于我们</h3>
                    <div class="social-login-buttons">
                        <a class="btn btn-link-2"
                           href="">
                            <i class="fa fa-facebook"></i> Facebook
                        </a>
                        <a class="btn btn-link-2"
                           href="">
                            <i class="fa fa-twitter"></i> Twitter
                        </a>
                        <a class="btn btn-link-2"
                           href="">
                            <i class="fa fa-google-plus"></i> Google Plus
                        </a>
                    </div>
                </div>
            </div>-->
        </div>
    </div>
</div>
<!--第三方库-->
<script src="/static/lib/js/jquery.min.js"></script>
<script src="/static/lib/js/bootstrap.min.js"></script>
<script src="/static/lib/js/toastr.min.js"></script>
<script src="/static/lib/js/jquery.backstretch.min.js"></script>
<script src="/static/pxy/login/slide.js"></script>

<!--登录js-->
<script src="/static/pxy/login/login.js"></script>

<div class="backstretch" style="left: 0px; top: 0px; overflow: hidden; margin: 0px; padding: 0px; height: 937px; width: 1903px; z-index: -999999; position: fixed;">
    <img src="/static/pxy/images/login-bg.jpg" style="position: absolute; margin: 0px; padding: 0px; border: none; width: 1903px; height: 1268.67px; max-height: none; max-width: none; z-index: -999999; left: 0px; top: -165.833px;">
</div>
</body>
</html>