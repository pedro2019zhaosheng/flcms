// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("betListCtrl", ["$scope", "$http", "$tip", "$location", function ($scope, $http, $tip,$location) {
        $.busyLoadFull('show', {animation: "fade"});

        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/pxy/bet/index";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.betList = result.data.list.data;
                $scope.betList.length ? $scope.betNoData = false : $scope.betNoData = true;
                $scope.betPage = result.data.page;
                $scope.betCurrentPage = result.data.list.current_page;
                $scope.betTotalPage = result.data.list.total;
                $scope.betPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 获取所有彩种
        ($scope.getAllBets = function () {
            $http.get("/pxy/lottery/all2").success(function (result) {
                $scope.betsList = result.data || [];
            });
        })();

        // 初始化
        ($scope.init = function () {
            $("#betType").val("-1").trigger("change"); // 彩种
            $('#settlementState').val("").trigger("change"); // 结算状态
            $('#orderState').val("-1").trigger("change"); // 订单状态
            $("#orderType").val("-1").trigger("change"); // 订单类型
            $("#from").val("").trigger("change"); // 开始日期
            $("#to").val("").trigger("change"); // 结束日期
            $("#usersType").val("").trigger("change");
            $scope.orderNo = ""; // 注单编号
            $scope.username = ""; // 会员账号


            $scope.requestApi(null, {
                params: {
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        })();

        // 清空搜索
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
            $scope.requestApi(null, {
                params: {
                    startDate: fromInputVal ? fromInputVal + ' 00:00:00' : '', // 开始日期
                    endDate: toInputVal ? toInputVal + ' 23:59:59' : '', // 结束日期
                    role: role,
                    lower: lower,
                    lottery_id: $("#betType").val(), // 彩种ID
                    order_no: $('#betNumber').val(), // 注单编号
                    username: $scope.username, // 用户名
                    pay_status: $('#settlementState').val(), // 结算状态
                    status: $('#orderState').val(), // 订单状态
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
                startDate: fromInputVal ? fromInputVal + ' 00:00:00' : '', // 开始日期
                endDate: toInputVal ? toInputVal + ' 23:59:59' : '', // 结束日期
                role: role,
                lower: lower,
                name: $("#userAccount").val(),
                agentName: $("#agentAccount").val(),
                lottery_id: $("#betType").val(), // 彩种ID
                order_no: $('#betNumber').val(), // 注单编号
                username: $scope.username, // 用户名
                pay_status: $('#settlementState').val(), // 结算状态
                status: $('#orderState').val(), // 订单状态
                is_moni: $("#orderType").val() // 是否模拟
            });

            window.location.href = '/pxy/bet/export?' + query;
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            let fromInputVal = $("#from").val();
            let toInputVal = $("#to").val();
            let role = $("#usersType").val();
            let lower = $("#agentUsers").val();
            $scope.requestApi(url, {
                params: {
                    startDate: fromInputVal ? fromInputVal + ' 00:00:00' : '', // 开始日期
                    endDate: toInputVal ? toInputVal + ' 23:59:59' : '', // 结束日期
                    role: role,
                    lower: lower,
                    lottery_id: $("#betType").val(), // 彩种ID
                    order_no: $('#betNumber').val(), // 注单编号
                    username: $('#memberNumber').val(), // 用户名
                    pay_status: $('#settlementState').val(), // 结算状态
                    status: $('#orderState').val(), // 订单状态
                    is_moni: $("#orderType").val(), // 是否模拟
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 查看详情
        $scope.detail = function (id, code) {
            let isPassd = true;
            switch (code) {
                case "JC": // 竞彩类型彩票, 竞彩足球.竞彩篮球.北京单场
                    $("#bet-detail").modal("show");
                    break;
                case "SZC": // 数字彩, 排列三.排列五.普彩.澳彩
                    $("#num-detail").modal("show");
                    break;
                default:
                    isPassd = false;
                    $tip.warning('未知彩种');
            }

            if (!isPassd) {
                return;
            }

            $http.get("/pxy/bet/info?id=" + id).success(function (result) {
                $scope.betDetail = result.data;
            });
        };


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
        $("#settlementState").select2({
            width: "130px"
        });

        $("#orderState").select2({
            width: "130px"
        });

        $("#betType").select2({
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
