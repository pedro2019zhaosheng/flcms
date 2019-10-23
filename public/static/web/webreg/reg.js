;!function ($, layer) {
    "use strict";
    let webReg = function () {
        // 用户名
        this.username = $("#username");
        // 昵称
        this.nickname = $("#nickname");
        // 密码
        this.password = $("#password");
        // 重复密码
        this.rePassword = $("#repassword");
        // 验证码
        this.verifyCode = $("#verifyCode");
        // 邀请码
        this.inviteCode = $("#inviteCode");
        // 提示
        this.tip = function (content) {
            content = content || '错误提示';
            layer.open({
                content: content,
                skin: 'msg',
                time: 2 // 2秒后自动关闭
            });
        };

        // 聚焦用户名
        this.userFocus = function () {
            this.username.focus();
        };

        // 获取验证码
        this.sendCode = function () {
            let accessCtrl;
            let self = this;
            $("#codeBtn").on("click", function (e) {
                if (accessCtrl === true) {
                    return;
                }

                if (!self.username.val()) {
                    self.tip('请填写手机号');
                    return;
                }

                if (!/^1[3-9][0-9]\d{8}$/.test(self.username.val())) {
                    self.tip('手机号格式不正确');
                    return;
                }

                $.post('/api/send', {
                    mobile: self.username.val()
                }, function (result) {
                    if (result.code === 0) {
                        self.tip('验证码发送失败');
                    }
                });

                let that = $(this);
                let i = 60;
                let n;

                accessCtrl = true;
                that.text('60s');
                that.removeClass("btn-outline-success").addClass("btn-dark");

                let timer = setInterval(function () {
                    i--;
                    n = i;
                    if (n.toString().length === 1) {
                        n = "0" + n;
                    }

                    that.text(n + 's');
                    if (i <= 0) {
                        clearInterval(timer);
                        that.removeClass("btn-dark").addClass("btn-outline-success");
                        that.text("重新发送");
                        accessCtrl = undefined;
                    }
                }, 1000);
            });
        };

        // 表单提交
        this.submit = function () {
            let that = this;
            $("#onlySubmit").on("click", function (e) {

                if (!that.username.val()) {
                    that.tip('请填写手机号');
                    return;
                }

                if (!/^1[3-9][0-9]\d{8}$/.test(that.username.val())) {
                    that.tip('手机号格式不正确');
                    return;
                }

                if (!that.nickname.val()) {
                    that.tip('请填写昵称');
                    return;
                }

                if (that.nickname.val().length > 20) {
                    that.tip('昵称不能大于20个字符');
                    return;
                }

                if (!that.password.val()) {
                    that.tip('请填写密码');
                    return;
                }

                if (
                    that.password.val().length < 6
                    || that.password.val().length > 20
                ) {
                    that.tip('密码长度请大于6个字符并且小于20个字符');
                    return;
                }

                if (!that.rePassword.val()) {
                    that.tip('请重复密码');
                    return;
                }

                if (that.password.val() !== that.rePassword.val()) {
                    that.tip('两次填写的密码不一致');
                    return;
                }

                if (!that.verifyCode.val()) {
                    that.tip('请填写验证码');
                    return;
                }

                if (!that.inviteCode.val()) {
                    that.tip('邀请码不能为空');
                    return;
                }

                $.post('/web/submit', {
                    username: that.username.val(), // 手机号
                    nickname: that.nickname.val(), // 昵称
                    password: that.password.val(), // 密码
                    verifyCode: that.verifyCode.val(), // 验证码
                    inviteCode: that.inviteCode.val() // 邀请码
                }, function (result) {
                    if (result.code === 1) {
                        // 下载App接口
                        layer.open({
                            content: '注册成功!前往下载APP'
                            ,btn: ['马上下载', '取消']
                            ,yes: function(index){
                                window.location.replace("http://ri29.taizhouyechou.cn:81/554Tf1");
                                layer.close(index);
                            }
                        });
                    } else {
                        that.tip(result.msg);
                    }
                });
            });
        };

        // 运行
        this.run = function () {
            // 聚焦用户名
            this.userFocus();
            // 发送邀请码
            this.sendCode();
            // 提交表单
            this.submit();
        };
    };

    (new webReg).run();
}(jQuery, layer);