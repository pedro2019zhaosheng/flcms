// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("adverTypeCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});
        //初始化checkbox
        $scope.initCheckboxAll = function () {
            let $checkAll = $('table thead tr').find('input');
            $checkAll.prop('checked', false);
        };
        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/adver/index?type=1";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.adverTypeList = result.data.list.data;
                $scope.adverTypeList.length ? $scope.adverTypeNoData = false : $scope.adverTypeNoData = true;
                $scope.adverPage = result.data.page;
                $scope.adverCurrentPage = result.data.list.current_page;
                $scope.adverTotalPage = result.data.list.total;
                $scope.adverPerPage = result.data.list.per_page;
                $.busyLoadFull('hide', {animation: "fade"});//关闭页面遮罩
            });

            $scope.initCheckboxAll();
        };

        // 初始化列表
        $scope.init = function () {
            $("#from").val("");
            $("#to").val("");
            $scope.adverType = "";

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
                    name: $scope.adverType,
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
                    name: $scope.adverType,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 删除广告类型
        $scope.deleteadver = function (adverId) {
            $.swal("确定要删除么", function () {
                $http.get("/vp/adver/deleteType?id=" + adverId).success(function (result) {
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
                        $('#adverType-add').modal('hide');
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

        // 冻结和解冻
        $scope.toggle = function (adverId, frozen) {
            $http.get("/vp/adver/toggle?id=" + adverId + "&frozen=" + frozen).success(function (result) {
                if (frozen === 0) {
                    $tip.success("解冻成功");
                } else {
                    $tip.success("冻结成功");
                }
                // 刷新接口
                $scope.requestApi($scope.url, $scope.param);
            });
        };

        // 初始化新增模态框
        $scope.initAddModal = function () {
            $("#addStatus").val("").trigger("change"); // 初始化状态
            $("#addName").val(""); // 初始化名称
        };

        // 关闭模态框事件
        $('#adverType-add').on('hidden.bs.modal', function () {
            // 初始化新增模态框
            $scope.initAddModal();
        });

        // 新增广告类型
        $scope.addAdverType = function (url) {
            let addStatus = $("#addStatus").val();
            if (!addStatus) {
                $tip.warning("类型状态不能为空！");
                return;
            }

            let addName = $("#addName").val();
            if (!addName) {
                $tip.warning("类型名称不能为空！");
                return;
            }
            url = url || "/vp/adver/addType";
            $http.post(url,
                $.param({
                    name: addName,
                    status: addStatus,
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $tip.success(result.msg);
                    $('#adverType-add').modal('hide');
                    // 初始化接口
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 修改获取详情
        $scope.updateToggle = function (adverId) {
            $http.get("/vp/adver/info?id=" + adverId + '&type=1').success(function (result) {
                if (result.code === 1) {
                    $("#updateName").val(result.data.name);
                    $("#updateStatus").val(result.data.status).select2();
                    $scope.updateAdverId = adverId;
                }
            });
        };

        // 修改保存
        $scope.updateAdverType = function (id) {
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

            $http.post("/vp/adver/modify", $.param({
                id: id,
                name: updateName.val(),
                status: updateStatus.val(),
                adverType: 1
            })).success(function (result) {
                if (result.code === 1) {
                    $("#adminType-update").modal('hide');
                    $tip.success("修改成功");
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
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
        // ng-repeat列表渲染完成事件
        $scope.$on('ngRepeatFinished', function (ngRepeatFinishedEvent) {
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
        $("#addStatus").select2();
        $("#updateStatus").select2();
    });
}(jQuery);

