// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("memberDetailCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});
        // 请求接口
        $scope.requestApi = function (url) {
            url = url || '/pxy/member/detail';
            $http.get(url).success(function (result) {
                $scope.memberDetail = result.data;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 初始化
        $scope.init = function () {
            $scope.requestApi();
        };

        // 初始化调用
        $scope.init();
        //保存个人信息

        // 修改个人中心用户信息
        $scope.updateAdminDetail = function () {

            let nickName = $("#nickname");
            if (!nickName.val()) {
                $tip.warning("请选填写昵称");
                return;
            }

            let pwd = $("#pwd").val();
            if (pwd) {
                if (!/(^\d+[a-z_\-\+\.\=]+$)|(^[a-z]+[0-9_\-\+\.\=]+$)/i.test(pwd)) {
                    $tip.warning("密码格式不正确");
                    return;
                }

                let pwdL = pwd.length;
                if (pwdL < 6 || pwdL > 18) {
                    $tip.warning("密码长度在6-18位之间");
                    return;
                }

                let rPwd = $("#rpwd").val();
                if (pwd !== rPwd) {
                    $tip.warning("两次输入密码不一样");
                    return;
                }
            }

            let photoUrl = $("#base64_img").val();

            let dataObj = {
                nickName: nickName.val(),
                passWord: pwd
            };

            if (photoUrl) {
                dataObj.file = photoUrl || '';
            }

            $http.post("/pxy/member/modifyMember", $.param(dataObj)).success(function (result) {
                if (result.code === 1) {
                    $tip.success(result.msg);
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                    $('#header_nickname').text(nickName.val());
                }
            });
        };

        // 取消保存
        $scope.noSave = function () {
            window.history.go(-1);
        };
    }]);
}(jQuery, angular);

// jQuery部分
!function ($) {
    "use strict";
    $(function () {
        $('#headImg').filer({
            limit: 1, // 上传数量1
            maxSize: 1, // 最大尺寸1M
            extensions: ['jpg', 'jpeg', 'png', 'gif', 'psd'], // 支持的文件类型
            changeInput: true, // 创建一个新的input元素
            showThumbs: false, // 显示文件预览
            addMore: false, // 不新增更多
            clipBoardPaste: true, // 允许复制粘贴文件
            captions: { // 编辑器
                button: "请选择头像",
                feedback: "请上传头像",
                feedback2: "图片已选择",
                drop: "删除上传文件",
                removeConfirmation: "你确定要删除该图片么？",
                errors: {
                    filesLimit: "只 {{fi-limit}} 图片",
                    filesType: "只允许上传图片",
                    filesSize: "{{fi-name}} 太大了! 请保证图片尺寸不大于 {{fi-maxSize}} MB.",
                    filesSizeAll: "图片太大了，请保证图片尺寸不大于 {{fi-maxSize}} MB."
                }
            },
            onRemove: function () { // 移除图片时触发
                $("#base64_img").val("");
            },
            onSelect: function () { // 选择图片之后触发
                let photoNode = $("#headImg");
                $(".detail_btn").show();

                if (photoNode.val()) {
                    let fileReader = new FileReader();
                    fileReader.readAsDataURL(photoNode[0].files[0]);
                    fileReader.onload = function (e) {
                        $("#base64_img").val(e.target.result);
                        $("#head_photo").attr('src', e.target.result);
                        $("#header_photo").attr('src', e.target.result);
                    };
                }
            }
        });
    });
}(jQuery);
