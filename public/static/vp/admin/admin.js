// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("adminCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        // 请求接口
        $.busyLoadFull('show', {animation: "fade"});
        $scope.requestApi = function (url, param) {
            url = url || "/vp/admin/index";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.adminList = result.data.list.data;
                $scope.adminList.length ? $scope.adminNoData = false : $scope.adminNoData = true;
                $scope.adminPage = result.data.page;
                $scope.adminCurrentPage = result.data.list.current_page;
                $scope.adminTotalPage = result.data.list.total;
                $scope.adminPerPage = result.data.list.per_page;
                $.busyLoadFull('hide', {animation: "fade"});//关闭页面遮罩
            });
        };

        // 获取角色
        $scope.getRoles = function () {
            $http.get("/vp/role/all").success(function (result) {
                $scope.roles = result.data;
            });
        };

        $scope.getRoles();

        // 初始化列表
        $scope.init = function () {
            $("#from").val("");
            $("#to").val("");
            $scope.username = "";

            $scope.requestApi(null, {
                params: {
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 初始化调用
        $scope.init();

        // 清空搜索
        $scope.clearSearch = function () {
            // 初始化
            $scope.init();
        };

        // 获取每页数据条数设置
        $scope.cleverChangePerPage = function (perpage) {
            if ($scope.param.params !== undefined) {
                $scope.param.params.perPage = perpage;
            } else {
                $scope.param = {
                    params: {
                        perPage: perpage
                    }
                };
            }

            // 发送请求
            $scope.requestApi(null, $scope.param);
        };

        // 搜索
        $scope.searchSubmit = function () {
            let fromInputVal = $("#from").val();
            let toInputVal = $("#to").val();
            $scope.requestApi(null, {
                params: {
                    startDate: fromInputVal ? fromInputVal + ' 00:00:00' : '',
                    endDate: toInputVal ? toInputVal + ' 23:59:59' : '',
                    username: $scope.username,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            let fromInputVal = $("#from").val();
            let toInputVal = $("#to").val();
            $scope.requestApi(url, {
                params: {
                    startDate: fromInputVal ? fromInputVal + ' 00:00:00' : '',
                    endDate: toInputVal ? toInputVal + ' 23:59:59' : '',
                    username: $scope.username,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 下载Excel
        $scope.export = function () {
            let fromInputVal = $("#from").val();
            let toInputVal = $("#to").val();

            let query = $.param({
                startDate: fromInputVal ? fromInputVal + ' 00:00:00' : '',
                endDate: toInputVal ? toInputVal + ' 23:59:59' : '',
                username: $scope.username
            });

            window.location.href = '/vp/admin/export?' + query;
        };

        // 删除管理员
        $scope.deleteAdmin = function (adminId) {
            $.swal("确定要删除么", function () {
                $http.get("/vp/admin/delete?id=" + adminId).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess();
                        // 刷新接口
                        $scope.requestApi($scope.url, $scope.param);
                    }
                });
            });
        };

        // 冻结和解冻
        $scope.toggle = function (adminId, frozen) {
            $http.get("/vp/admin/toggle?id=" + adminId + "&frozen=" + frozen).success(function (result) {
                if (result.code === 1) {
                    if (frozen === 0) {
                        $tip.success("解冻成功");
                    } else {
                        $tip.success("冻结成功");
                    }
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 初始化新增模态框
        $scope.initAddModal = function () {
            $("#addUsername").val(""); // 初始化用户名
            $("#addNickName").val(""); // 初始化昵称
            $("#addPhone").val(""); // 初始化手机号
            $("#addPwd").val(""); // 初始化密码
            $("#base64").val(""); // 初始化头像
            $("#selectRole").val("").trigger("change"); // 初始化角色名
            $("#addPhoto").val("").trigger("change"); // 初始化头像
        };

        // 关闭模态框事件
        $('#admin-add').on('hidden.bs.modal', function () {
            // 初始化新增模态框
            $scope.initAddModal();
        });

        // 新增保存
        $scope.submitAdmin = function () {
            let roleNode = $("#selectRole");
            if (!roleNode.val()) {
                $tip.warning("请选择角色");
                return;
            }

            let usernameNode = $("#addUsername");
            if (!usernameNode.val()) {
                $tip.warning("请填写用户名");
                return;
            }

            if (!/^[A-Za-z0-9\-\_]+$/.test(usernameNode.val())) {
                $tip.warning("用户名存在非法字符");
                return;
            }

            let nickName = $("#addNickName");
            if (!nickName.val()) {
                $tip.warning("请填写昵称");
                return;
            }

            let phoneNode = $("#addPhone");
            if (!phoneNode.val()) {
                $tip.warning("请填写手机号");
                return;
            }

            if (!/^1[3-9][0-9]\d{8}$/u.test(phoneNode.val())) {
                $tip.warning("手机号格式错误");
                return;
            }

            let pwd = $("#addPwd");
            if (!pwd.val()) {
                $tip.warning("请填写密码");
                return;
            }

            let photoUrl = $("#base64").val();

            $http.post("/vp/admin/add", $.param({
                roleId: roleNode.val(),
                username: usernameNode.val(),
                nickName: nickName.val(),
                phone: phoneNode.val(),
                pwd: pwd.val(),
                file: photoUrl,
            })).success(function (result) {
                if (result.code === 1) {
                    $tip.success(result.msg);
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                    $("#admin-add").modal("hide");
                }
            });
        };

        // 修改获取详情
        $scope.updateToggle = function (adminId) {
            $http.get("/vp/admin/info?id=" + adminId).success(function (result) {
                if (result.code === 1) {
                    let roleId = result.data.role || '';
                    $("#roleUpdate").val(roleId).trigger('change');
                    $("#updateUsername").val(result.data.username);
                    $scope.updateUser = result.data.username;
                    $("#updateNickName").val(result.data.nick_name);
                    $("#updatePhone").val(result.data.phone);
                    $scope.updateAdminId = adminId;
                }
            });
        };
        // 修改保存
        $scope.updateAdmin = function () {
            if ($scope.updateUser === "admin") {
                $tip.warning("系统账号“admin”不能被修改!");
                return;
            }

            let updateRoleNode = $("#roleUpdate");
            if (!updateRoleNode.val()) {
                $tip.warning("请选择角色");
                return;
            }

            let updateUsername = $("#updateUsername");
            if (!updateUsername.val()) {
                $tip.warning("请填写用户名");
                return;
            }

            if (!/^[A-Za-z0-9\-\_]+$/.test(updateUsername.val())) {
                $tip.warning("用户名存在非法字符");
                return;
            }

            let updateNickName = $("#updateNickName");
            if (!updateNickName.val()) {
                $tip.warning("请填写昵称");
                return;
            }

            let updatePhone = $("#updatePhone");
            if (!updatePhone.val()) {
                $tip.warning("请填写手机号");
                return;
            }

            if (!/^1[3-9][0-9]\d{8}$/u.test(updatePhone.val())) {
                $tip.warning("手机号格式错误");
                return;
            }

            $http.post("/vp/admin/modify", $.param({
                id: $scope.updateAdminId,
                roleId: updateRoleNode.val(),
                username: updateUsername.val(),
                nickName: updateNickName.val(),
                phone: updatePhone.val(),
                pwd: $("#updatePwd").val(),
            })).success(function (result) {
                if (result.code === 1) {
                    $tip.success("修改成功");
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                    $("#admin-update").modal("hide");
                }
            });
        };

        // 修改排序
        $scope.updateAdminSort = function (adminId, sort) {
            $http.post('/vp/admin/sort', $.param({
                id: adminId,
                sort: sort
            })).success(function (result) {
                if (result.code === 1) {
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

    }]);
}(jQuery, angular);


!function ($) {
    "use strict";
    $(function () {

        // 初始化日历插件
        $.datePicker({
            from: "#from",
            to: "#to"
        });
        $("#from").val("");
        $("#to").val("");

        // 初始化select2插件，该插件在模态框中使用时，请将modal中的 tabindex="-1" 属性删除
        $("#selectRole").select2();

        // 初始化
        $("#roleUpdate").select2();

        // 文件上传
        $('#addPhoto').filer({
            limit: 1, // 上传数量1
            maxSize: 1, // 最大尺寸1M
            extensions: ['jpg', 'jpeg', 'png', 'gif', 'psd'], // 支持的文件类型
            changeInput: true, // 创建一个新的input元素
            showThumbs: true, // 显示文件预览
            addMore: false, // 不新增更多
            clipBoardPaste: true, // 允许复制粘贴文件
            captions: { // 编辑器
                button: "选择头像",
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
                $("#base64").val("");
            },
            onSelect: function () { // 选择图片之后触发
                let photoNode = $("#addPhoto");
                if (photoNode.val()) {
                    let fileReader = new FileReader();
                    fileReader.readAsDataURL(photoNode[0].files[0]);
                    fileReader.onload = function (e) {
                        $("#base64").val(e.target.result);
                    };
                }
            }
        });
    });
}(jQuery);

