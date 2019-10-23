// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("serverCtrls", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        // 打开遮罩框
        $.busyLoadFull('show', {animation: "fade"});
        // 公共请求方法
        $scope.requestApi = function (url, param) {
            url = url || "/vp/system/serviceList";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.seviceList = result.data.list.data;
                $scope.seviceList.length ? $scope.serviceNoData = false : $scope.serviceNoData = true;
                $scope.systemPage = result.data.page;
                $scope.systemCurrentPage = result.data.list.current_page;
                $scope.systemTotalPage = result.data.list.total;
                $scope.systemPerPage = result.data.list.per_page;
                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 初始化
        $scope.init = function () {
            $('#nickname').val(""); // 客服名称
            $('#site_status').val("").trigger("change"); // 状态
            $('#start_time').val(""); // 开始时间
            $('#end_time').val(""); // 结束时间

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

        // 删除客服
        $scope.deleteadver = function (adverId) {
            $.swal("确定要删除么", function () {
                $http.get("/vp/system/delSite?id=" + adverId).success(function (result) {
                    $.swalSuccess();
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                });
            });
        };

        // 切换客服编码控件状态
        $scope.toggleDisable = function (disVal, btnVal) {
            $scope.disabledVal = !disVal;
            if (btnVal === '手动填写') {
                $scope.btnVal = '系统生成';
                $scope.disClass = 'form-control';
            } else {
                $scope.disClass = 'form-control-plaintext';
                $scope.btnVal = '手动填写';
            }
        };

        // 搜索
        $scope.searchSubmit = function () {
            let name = $('#nickname').val();
            let start_time = $('#start_time').val();
            let end_time = $('#end_time').val();
            let status = $('#site_status').val();
            $scope.requestApi(null, {
                params: {
                    start_time: start_time ? start_time + " 00:00:00" : "",
                    end_time: end_time ? end_time + " 23:59:59" : "",
                    name: name,
                    status: status,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            let name = $('#nickname').val();
            let start_time = $('#start_time').val();
            let end_time = $('#end_time').val();
            let status = $('#site_status').val();

            $scope.requestApi(url, {
                params: {
                    start_time: start_time ? start_time + " 00:00:00" : "",
                    end_time: end_time ? end_time + " 23:59:59" : "",
                    name: name,
                    status: status,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        //编辑
        $scope.updateToggle = function (id) {
            $http.get('/vp/system/editService?id=' + id).success(function (result) {
                $scope.siteList = result.data;
                $('#edit_name').val($scope.siteList.name);
                $('#edit_phone').val($scope.siteList.num);
                $scope.siteId = id;
            });
        };

        $scope.initAddModal = function () {
            $("#site_name").val("");
            $("#site_phone").val("");
            $("#site_select").val("").trigger("change");
            $("#base64_tag").val("").trigger("change");
            $("#site_tag").val("").trigger("change");
            $("#site_img_hidden").val("").trigger("change");
            $("#site_img").val("").trigger("change");
        };

        // 关闭模态框事件
        $('#system-addSite').on('hidden.bs.modal', function () {
            // 初始化新增模态框
            $scope.initAddModal();
        });

        //新增
        $scope.save = function (url) {
            url = url || "/vp/system/addService";
            $http.post(url,
                $.param({
                    name: $("#site_name").val(),
                    num: $("#site_phone").val(),
                    icon: $("#base64_tag").val(),
                    file: $("#site_img_hidden").val(),
                    status: $("#site_select option:selected").val(),
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $('#system-addSite').modal('hide');
                    $tip.success(result.msg);
                    // 初始化接口
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 关闭模态框事件
        $('#system-editSite').on('hidden.bs.modal', function () {
            // 初始化修改模态框
            $("#edit_img").val("").trigger('change');
            $("#edit_img_hidden").val("").trigger('change');

            $("#edit_icon").val("").trigger('change');
            $("#edit_icon_hidden").val("").trigger('change');
        });

        //修改
        $scope.editsave = function () {
            $http.post('/vp/system/editService',
                $.param({
                    id: $scope.siteId,
                    name: $("#edit_name").val(),
                    num: $("#edit_phone").val(),
                    file: $("#edit_img_hidden").val(),
                    icon: $("#edit_icon_hidden").val()
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $("#system-editSite").modal("hide");
                    $tip.success(result.msg);
                    // 初始化接口
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        }
    }]);
}(jQuery, angular);

// jQuery部分
!function ($) {
    "use strict";
    $(function () {
        // 初始化select2插件
        $("#site_status").select2({width: "100px"});
        $("#site_select").select2();

        // 初始化日历插件
        $.datePicker({
            from: "#start_time",
            to: "#end_time"
        });

        $("#start_time").val("");
        $("#end_time").val("");

        // 新增icon
        $('#site_tag').filer({
            limit: 1, // 上传数量1
            maxSize: 1, // 最大尺寸1M
            extensions: ['jpg', 'jpeg', 'png', 'gif', 'psd'], // 支持的文件类型
            changeInput: true, // 创建一个新的input元素
            showThumbs: true, // 显示文件预览
            addMore: false, // 不新增更多
            clipBoardPaste: true, // 允许复制粘贴文件
            captions: { // 编辑器
                button: "选择二维码",
                feedback: "请上传二维码",
                feedback2: "二维码已选择",
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
                $("#base64_tag").val("");
            },
            onSelect: function () { // 选择图片之后触发
                let photoNode = $("#site_tag");
                if (photoNode.val()) {
                    let fileReader = new FileReader();
                    fileReader.readAsDataURL(photoNode[0].files[0]);
                    fileReader.onload = function (e) {
                        $("#base64_tag").val(e.target.result);
                    };
                }
            }
        });

        // 新增图片
        $('#site_img').filer({
            limit: 1, // 上传数量1
            maxSize: 1, // 最大尺寸1M
            extensions: ['jpg', 'jpeg', 'png', 'gif', 'psd'], // 支持的文件类型
            changeInput: true, // 创建一个新的input元素
            showThumbs: true, // 显示文件预览
            addMore: false, // 不新增更多
            clipBoardPaste: true, // 允许复制粘贴文件
            captions: { // 编辑器
                button: "选择图片",
                feedback: "请上传图片",
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
                $("#site_img_hidden").val("");
            },
            onSelect: function () { // 选择图片之后触发
                let photoNode = $("#site_img");
                if (photoNode.val()) {
                    let fileReader = new FileReader();
                    fileReader.readAsDataURL(photoNode[0].files[0]);
                    fileReader.onload = function (e) {
                        $("#site_img_hidden").val(e.target.result);
                    };
                }
            }
        });
        // 修改icon
        $('#edit_icon').filer({
            limit: 1, // 上传数量1
            maxSize: 1, // 最大尺寸1M
            extensions: ['jpg', 'jpeg', 'png', 'gif', 'psd'], // 支持的文件类型
            changeInput: true, // 创建一个新的input元素
            showThumbs: true, // 显示文件预览
            addMore: false, // 不新增更多
            clipBoardPaste: true, // 允许复制粘贴文件
            captions: { // 编辑器
                button: "选择图片",
                feedback: "请上传图片",
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
                $("#edit_icon_hidden").val("");
            },
            onSelect: function () { // 选择图片之后触发
                let photoNode = $("#edit_icon");
                if (photoNode.val()) {
                    let fileReader = new FileReader();
                    fileReader.readAsDataURL(photoNode[0].files[0]);
                    fileReader.onload = function (e) {
                        $("#edit_icon_hidden").val(e.target.result);
                    };
                }
            }
        });
        // 修改图片
        $('#edit_img').filer({
            limit: 1, // 上传数量1
            maxSize: 1, // 最大尺寸1M
            extensions: ['jpg', 'jpeg', 'png', 'gif', 'psd'], // 支持的文件类型
            changeInput: true, // 创建一个新的input元素
            showThumbs: true, // 显示文件预览
            addMore: false, // 不新增更多
            clipBoardPaste: true, // 允许复制粘贴文件
            captions: { // 编辑器
                button: "选择二维码",
                feedback: "请上传二维码",
                feedback2: "二维码已选择",
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
                $("#edit_img_hidden").val("");
            },
            onSelect: function () { // 选择图片之后触发
                let photoNode = $("#edit_img");
                if (photoNode.val()) {
                    let fileReader = new FileReader();
                    fileReader.readAsDataURL(photoNode[0].files[0]);
                    fileReader.onload = function (e) {
                        $("#edit_img_hidden").val(e.target.result);
                    };
                }
            }
        });
    });
}(jQuery);
