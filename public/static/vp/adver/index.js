// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("adverCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        // 初始化checkbox
        $scope.initCheckboxAll = function () {
            let $checkAll = $('table thead tr').find('input');
            $checkAll.prop('checked', false);
        };

        $.busyLoadFull('show', {animation: "fade"});
        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/adver/index";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.adverList = result.data.list.data;
                $scope.adverList.length ? $scope.adverNoData = false : $scope.adverNoData = true;
                $scope.adverPage = result.data.page;
                $scope.adverCurrentPage = result.data.list.current_page;
                $scope.adverTotalPage = result.data.list.total;
                $scope.adverPerPage = result.data.list.per_page;

                $.busyLoadFull('hide', {animation: "fade"});//关闭页面遮罩
            });

            $scope.initCheckboxAll();
        };

        // 获取广告类型
        $scope.getRoles = function () {
            $http.get("/vp/adver/getTypeAll").success(function (result) {
                $scope.roles = result.data;
            });
        };

        $scope.getRoles();

        // 初始化列表
        $scope.init = function () {
            $("#from").val("");
            $("#to").val("");
            $scope.averTitle = "";

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
                    name: $scope.averTitle,
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
                    name: $scope.averTitle,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 删除广告
        $scope.deleteadver = function (adverId) {
            $.swal("确定要删除么", function () {
                $http.get("/vp/adver/delete?id=" + adverId).success(function (result) {
                    $.swalSuccess();
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                });
            });
        };
        //设置启用禁用状态
        $scope.setStatus = function (status, url) {
            let $tbr = $('table tbody tr');
            let checkboxNode = $tbr.find('input:checked');
            let ids = [];

            if (!checkboxNode.length) {
                $tip.warning("请选择您要操作的数据!");
                return;
            }

            checkboxNode.each(function () {
                let id = $(this).data('value');
                ids.push(id);
            });
            ids = ids.join(',');
            url = url || "/vp/adver/setStatus";
            let msg = '启用';
            if (status === 0) {
                msg = "禁用";
            }

            $.swal("确认要" + msg + "么", function () {
                $http.post(url,
                    $.param({
                        id: ids,
                        status: status,
                    })
                ).success(function (result) {
                    if (result.code === 1) {
                        $('#adver-add').modal('hide');
                        $tip.success(result.msg);
                        $scope.requestApi($scope.url, $scope.param);
                    }
                });
            })
        };

        //选中则存储id
        var adverId = [];
        $scope.count = function (id) {
            adverId.push(id);
        };

        // 初始化新增模态框
        $scope.initAddModal = function () {
            $("#selectRole").val("").trigger("change"); // 初始化广告类型
            $("#status").val("").trigger("change");
            // 初始化广告状态
            $("#addName").val(""); // 初始化广告标题
            $("#addAbstract").val(""); // 初始化广告描述
            $("#addUsername").val(""); // 初始化用户名
            $("#addUrl").val(""); // 初始化广告链接
            $("#base64").val(""); // 初始化头像
            $("#addPhoto").val("").trigger("change"); // 初始化头像
        };

        // 关闭模态框事件
        $('#adver-add').on('hidden.bs.modal', function () {
            // 初始化新增模态框
            $scope.initAddModal();
        });

        //添加广告
        $scope.addAdver = function (url) {
            //判断条件
            let type = $("#selectRole").val();
            if (!type) {
                $tip.warning("广告类型不能为空！");
                return;
            }

            let status = $("#status").val();
            if (!status) {
                $tip.warning("广告状态不能为空！");
                return;
            }

            let adverUrl = $("#addUrl").val();
            /*if (!adverUrl) {
                $tip.warning("广告链接不能为空！");
                return;
            }*/

            /*if (!/^.*\.(cn|com|org|net).*$/ui.test(adverUrl)) {
                $tip.warning("广告链接格式错误！");
                return;
            }*/

            let addName = $("#addName").val();
            if (!addName) {
                $tip.warning("广告标题不能为空！");
                return;
            }

            let abstract = $("#addAbstract").val();
            /*if (!abstract) {
                $tip.warning("广告描述不能为空！");
                return;
            }*/

            url = url || "/vp/adver/add";
            $http.post(url,
                $.param({
                    type: type,
                    name: addName,
                    abstract: abstract,
                    url: adverUrl,
                    file: $("#base64").val(),
                    status: status,
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $('#adver-add').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 修改获取详情
        $scope.updateToggle = function (adverId) {
            $http.get("/vp/adver/info?id=" + adverId).success(function (result) {
                if (result.code === 1) {
                    let roleId = result.data.role || '';
                    $("#roleUpdate").val(result.data.adType).select2();
                    $("#updateName").val(result.data.name);
                    $("#updateAbstract").val(result.data.abstract);
                    $("#updateStatus").val(result.data.status).select2();
                    $scope.updateAdverId = adverId;
                }
            });
        };

        // 修改
        $scope.updateAdver = function () {
            let updateRoleNode = $("#roleUpdate");
            if (!updateRoleNode.val()) {
                $tip.warning("请选择类型");
                return;
            }

            let updateName = $("#updateName");
            if (!updateName.val()) {
                $tip.warning("请填写标题");
                return;
            }

            let updateAbstract = $("#updateAbstract");
            /*if (!updateAbstract.val()) {
                $tip.warning("请填写描述");
                return;
            }*/

            let updateStatus = $("#updateStatus");
            if (!updateStatus.val()) {
                $tip.warning("请选择状态");
                return;
            }

            $http.post("/vp/adver/modify", $.param({
                id: $scope.updateAdverId,
                type: updateRoleNode.val(),
                name: updateName.val(),
                abstract: updateAbstract.val(),
                status: updateStatus.val(),
            })).success(function (result) {
                if (result.code === 1) {
                    $tip.success("修改成功");
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                    $("#adver-update").modal("hide");
                }
            });
        };

        // 初始化checkbox
        $scope.initTableCheckbox = function () {

            let $tbr = $('table tbody tr');
            let $thr = $('table thead tr');
            let $checkAll = $thr.find('input');

            $checkAll.off('click');

            $checkAll.click(function (event) {
                // 阻止默认事件和插件的其他click事件监听器
                event.preventDefault();
            });

            $tbr.find('input').click(function (event) {
                // 阻止默认事件和插件的其他click事件监听器
                event.preventDefault();
            });

            $tbr.find('input').parent().parent().click(function () {
                let inputNode = $(this).find('input');
                inputNode.prop('checked', !inputNode.is(":checked"));
                $checkAll.prop('checked', $tbr.find('input:checked').length === $tbr.length);
            });

            $checkAll.parent().parent().off('click');

            $checkAll.parent().parent().click(function () {
                $tbr.find('input').prop('checked', !$checkAll.is(':checked'));
                $checkAll.prop('checked', !$checkAll.is(":checked"));
            });
        };

        // 监听angular列表渲染完成
        $scope.$on('ngRepeatFinished', function () {
            $scope.initTableCheckbox();
        });

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
        $("#status").select2();
        $("#addStatus").select2();

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
                $("#base64").val("");
            },
            onSelect: function () { // 选择图片之后触发
                let photoNode = $("#addPhoto");
                if (photoNode.val()) {
                    let fileReader = new FileReader();
                    fileReader.readAsDataURL(photoNode[0].files[0]);
                    fileReader.onload = function (e) {
                        $("#base64").val(e.target.result);
                        // alert($("#base64").val());
                    };
                }
            }
        });
    });
}(jQuery);

