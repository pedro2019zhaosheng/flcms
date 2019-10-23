!function ($, window, toastr) {
    "use strict";
    // toastr插件配置
    toastr.options = {
        "closeButton": false,//显示关闭按钮
        "debug": false,//启用debug
        "progressBar": true, // 显示进度条
        "positionClass": "toast-top-center",//弹出的位置
        "showDuration": "300",//显示的时间
        "hideDuration": "1000",//消失的时间
        "timeOut": "2000",//停留的时间
        "extendedTimeOut": "1000",//控制时间
        "showEasing": "swing",//显示时的动画缓冲方式
        "hideEasing": "linear",//消失时的动画缓冲方式
        "showMethod": "fadeIn",//显示时的动画方式
        "hideMethod": "fadeOut",//消失时的动画方式
        "iconClasses": {
            error: "toast-error",
            info: "toast-info",
            success: "toast-success",
            warning: "toast-black"
        }
    };

    // 初始化页面和登录数据提交
    $(function () {
        let SlideVerifyPlug = window.slideVerifyPlug;
        let slideVerify = new SlideVerifyPlug('#verify-wrap');

        $('.login-form input[type="text"], .login-form input[type="password"], .login-form textarea').on('focus', function () {
            $(this).removeClass('input-error');
        });

        $('.login-form').on('submit', function (e) {
            // 阻止提交
            e.preventDefault();
            // 默认验证状态是success
            var verifyState = true;
            // 处理error样式
            $(this).find('input[type="text"], input[type="password"], textarea').each(function () {
                if (!$(this).val()) {
                    $(this).addClass('input-error');
                    verifyState = false;
                } else {
                    $(this).removeClass('input-error');
                }
            });
            // 判断验证状态
            if (!slideVerify.slideFinishState || verifyState === false) {
                // 重置
                slideVerify.resetVerify();
                return;
            }

            $.post("/vp/login/submit", {
                username: $('input[type="text"]').val(),
                password: $('input[type="password"]').val(),
                _aid: $('input[type="hidden"]').val()
            }, function (result) {
                if (result.code === 1){
                    window.location.href = '/vp/';
                }else if(result.code === 403){
                    slideVerify.resetVerify();
                    alert(result.msg);
                    window.location.reload(true);
                }else{
                    toastr.warning(result.msg);
                    slideVerify.resetVerify();
                }
            });
        });
    });
}(jQuery, window, window.toastr);