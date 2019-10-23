// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("newsTypeCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});
        // 初始化checkbox
        $scope.initCheckboxAll = function () {
            let $checkAll = $('table thead tr').find('input');
            $checkAll.prop('checked', false);
        };
        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/news/index?type=1";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.newTypeList = result.data.list.data;
                $scope.newTypeList.length ? $scope.newsTypeNoData = false : $scope.newsTypeNoData = true;
                $scope.typePage = result.data.page;
                $scope.typeCurrentPage = result.data.list.current_page;
                $scope.typeTotalPage = result.data.list.total;
                $scope.typePerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
            $scope.initCheckboxAll();
        };

        // 初始化列表
        $scope.init = function () {
            $("#from").val("");
            $("#to").val("");
            $scope.newsType = "";

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
                    name: $scope.newsType,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            let fromInputVal = $("#from").val();
            let toInputVal = $("#to").val();
            $scope.requestApi(url + '&type=1', {
                params: {
                    startDate: fromInputVal ? fromInputVal + ' 00:00:00' : '',
                    endDate: toInputVal ? toInputVal + ' 23:59:59' : '',
                    name: $scope.newsType,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 删除广告类型
        $scope.deleteType = function (adverId) {
            $.swal("确定要删除么", function () {
                $http.get("/vp/news/deleteType?id=" + adverId).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess();
                        // 刷新接口
                        $scope.requestApi($scope.url, $scope.param);
                    }
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
            url = url || "/vp/news/setStatus";
            let msg;
            if (status === 0) {
                msg = "禁用";
            } else {
                msg = "启用";
            }

            $.swal("确认要" + msg + "么", function () {
                $http.post(url,
                    $.param({
                        id: ids,
                        status: status,
                        type: 1
                    })
                ).success(function (result) {
                    if (result.code === 1) {
                        $tip.success(msg + '成功');
                        $scope.requestApi($scope.url, $scope.param);
                    }
                });
            })
        };

        // 初始化新增新闻类型模态框
        $scope.initAddModal = function () {
            $("#status").val("").trigger("change"); // 初始化状态
            $("#addName").val(""); // 初始化头像
        };

        // 关闭模态框事件
        $('#type-add').on('hidden.bs.modal', function () {
            // 初始化新增模态框
            $scope.initAddModal();
        });

        //添加类型
        $scope.addNewsType = function (url) {
            let status = $("#status").val();
            if (!status) {
                $tip.warning("类型状态不能为空！");
                return;
            }

            let addName = $("#addName").val();
            if (!addName) {
                $tip.warning("类型名称不能为空！");
                return;
            }
            url = url || "/vp/news/addType";
            $http.post(url,
                $.param({
                    name: addName,
                    status: status,
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $('#type-add').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 获取详情
        $scope.updateToggle = function (adverId) {
            $http.get("/vp/news/info?id=" + adverId + '&type=1').success(function (result) {
                if (result.code === 1) {
                    $("#updateName").val(result.data.name);
                    $("#updateStatus").val(result.data.status).select2();
                    $scope.updateTypeId = adverId;
                }
            });
        };

        // 编辑保存
        $scope.updateType = function (id) {
            let updateName = $("#updateName");
            if (!updateName.val()) {
                $tip.warning("请填写标题");
                return;
            }

            let updateStatus = $("#updateStatus");
            if (!updateStatus.val()) {
                $tip.warning("请选择状态");
                return;
            }

            $http.post("/vp/news/modify", $.param({
                id: id,
                name: updateName.val(),
                status: updateStatus.val(),
                newsType: 1
            })).success(function (result) {
                if (result.code === 1) {
                    $tip.success("修改成功");
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                    $("#news-update").modal("hide");
                }
            });
        };

        //全选
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

        // 初始化
        $("#status").select2();
        $("#updateStatus").select2();
    });
}(jQuery);

