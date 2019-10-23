// angular数据交互 --推单列表
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("betExtendCtrl", ["$scope", "$http", "$tip","$location" ,function ($scope, $http, $tip,$location) {
        $.busyLoadFull('show', {animation: "fade"});
        //初始化checkbox
        $scope.initCheckboxAll = function () {
            let $checkAll = $('table thead tr').find('input');
            $checkAll.prop('checked', false);
        };

        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/pxy/bet/pushOrder"; // 推单列表接口
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.betExtendList = result.data.list.data;
                $scope.betExtendList.length ? $scope.betExtendNoData = false : $scope.betExtendNoData = true;
                $scope.betExtendPage = result.data.page;
                $scope.betExtendCurrentPage = result.data.list.current_page;
                $scope.betExtendTotalPage = result.data.list.total;
                $scope.betExtendPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });

            // 初始化checkbox
            $scope.initCheckboxAll();
        };

        // 初始化
        ($scope.init = function () {
            $scope.orderNo = ''; // 推单号
            $scope.username = ''; // 用户名
            $("#lotteryType").val("-1").trigger("change"); // 彩种
            $("#orderState").val("-1").trigger("change"); // 订单状态
            $("#supStatus").val("-1").trigger("change"); // 推单审核状态
            $("#orderType").val("-1").trigger("change"); // 订单类型
            $("#from").val("").trigger("change"); // 开始日期
            $("#to").val("").trigger("change"); // 结束日期
            $("#usersType").val("").trigger("change");

            $scope.requestApi(null, {
                params: {
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        })();

        // 获取所有彩种
        ($scope.getAllBets = function () {
            $http.get("/pxy/lottery/all2").success(function (result) {
                $scope.betsList = result.data || [];
            });
        })();

        // 清空
        $scope.clearSearch = function () {
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
            let role = $("#usersType").val();
            let lower = $("#agentUsers").val();

            $scope.requestApi(undefined, {
                params: {
                    beginDate: fromInputVal ? fromInputVal + ' 00:00:00' : '', // 开始日期
                    overDate: toInputVal ? toInputVal + ' 23:59:59' : '', // 结束日期
                    role: role,
                    lower: lower,
                    lottery_id: $("#lotteryType").val(), // 彩种ID
                    username: $scope.username, // 用户名
                    order_no: $scope.orderNo, // 推单号
                    status: $("#orderState").val(), // 订单状态
                    authStatus: $("#supStatus").val(), // 推单审核状态
                    is_moni: $("#orderType").val(), // 是否模拟
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 导出Excel
        $scope.export = function () {
            let fromInputVal = $("#from").val();
            let toInputVal = $("#to").val();
            let role = $("#usersType").val();
            let lower = $("#agentUsers").val();

            let query = $.param({
                beginDate: fromInputVal ? fromInputVal + ' 00:00:00' : '', // 开始日期
                overDate: toInputVal ? toInputVal + ' 23:59:59' : '', // 结束日期
                role: role,
                lower: lower,
                lottery_id: $("#lotteryType").val(), // 彩种ID
                username: $scope.username, // 用户名
                order_no: $scope.orderNo, // 推单号
                status: $("#orderState").val(), // 订单状态
                authStatus: $("#supStatus").val(), // 推单审核状态
                is_moni: $("#orderType").val() // 是否模拟
            });

            // 导出
            window.location.href = '/pxy/bet/exportPush?' + query;
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            let fromInputVal = $("#from").val();
            let toInputVal = $("#to").val();
            let role = $("#usersType").val();
            let lower = $("#agentUsers").val();

            $scope.requestApi(url, {
                params: {
                    beginDate: fromInputVal ? fromInputVal + ' 00:00:00' : '', // 开始日期
                    overDate: toInputVal ? toInputVal + ' 23:59:59' : '', // 结束日期
                    role: role,
                    lower: lower,
                    lottery_id: $("#lotteryType").val(), // 彩种ID
                    username: $scope.username, // 用户名
                    order_no: $scope.orderNo, // 推单号
                    status: $("#orderState").val(), // 订单状态
                    authStatus: $("#supStatus").val(), // 推单审核状态
                    is_moni: $("#orderType").val(), // 是否模拟
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 批量审核推单
        $scope.batchAudit = function (state) {
            let $tbr = $('table#pushOrderList tbody tr');
            let checkboxNode = $tbr.find('input:checked');
            let ids = [];

            if (!checkboxNode.length) {
                $tip.warning("请选择您要审核的订单!");
                return;
            }

            checkboxNode.each(function () {
                let id = $(this).data('value');
                ids.push(id);
            });
            ids = ids.join(',');

            // 发送审核请求
            $http.get('/pxy/bet/audit?id=' + ids + '&state=' + state).success(function (result) {
                if (result.code === 1) {
                    $tip.success(result.msg);

                    // 刷新列表
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 跟单明细
        $scope.detail = function (id) {
            // 发送ajax
            $http.get("/pxy/bet/flowList?id=" + id).success(function (result) {
                $scope.flowOrder = result.data || [];
                $scope.flowOrder.length === 0 ? $scope.flowOrderNoData = true : $scope.flowOrderNoData = false;
            });
        };

        // 推单详情
        $scope.pushDetail = function (id, code) {
            let isPassd = true;
            switch (code) {
                case "JC": // 竞彩类型彩票, 竞彩足球.竞彩篮球.北京单场
                    $("#push-detail").modal("show");
                    break;
                case "SZC": // 数字彩, 排列三.排列五.普彩.澳彩
                    $("#num-push-detail").modal("show");
                    break;
                default:
                    isPassd = false;
                    $tip.warning('未知彩种');
            }

            if (!isPassd) {
                return;
            }

            $http.get("/pxy/bet/pushInfo?id=" + id).success(function (result) {
                $scope.betDetail = result.data;
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

// jQuery部分
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

        // 初始化select2插件
        $("#orderState").select2({
            width: "130px"
        });

        $("#supStatus").select2({
            width: "130px"
        });

        $("#lotteryType").select2({
            width: "130px"
        });

        $("#orderType").select2({
            width: "130px"
        });

        // 初始化select2插件，该插件在模态框中使用时，请将modal中的 tabindex="-1" 属性删除
        $("#usersType").select2({
            width: "150px"
        });
        $("#agentUsers").select2({
            width: "150px"
        });

    });
}(jQuery);

