// --充值记录
~function ($, angular, window) {
    "use strict";
    angular.module("myApp").config(['$locationProvider', function ($locationProvider) {
        // $locationProvider.html5Mode(true);
        $locationProvider.html5Mode({
            enabled: true,
            requireBase: false
        });
    }]);

    angular.module("myApp").controller("capitalRechargeCtrl", ["$scope", "$http", "$tip", "$location", function ($scope, $http, $tip, $location) {
        $.busyLoadFull('show', {animation: "fade"});
        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/capital/index?model=FundCharge";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.rechargList = result.data.list.data;
                $scope.rechargList.length ? $scope.capitalNoData = false : $scope.capitalNoData = true;
                $scope.rechargePage = result.data.page;
                $scope.rechargeCurrentPage = result.data.list.current_page;
                $scope.rechargeTotalPage = result.data.list.total;
                $scope.rechargePerPage = result.data.list.per_page;

                //关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 初始化
        $scope.init = function () {
            $scope.orderNum = ""; // 订单号
            $("#from").val("");
            $("#to").val("");
            $("#userAccount").val("");
            $("#agentAccount").val("");
            $("#usersType").val("").trigger("change");
            $("#agentUsers").val("").trigger("change");
            $("#payWay").val("").trigger("change");
            //获取url的参数
            let paramerUserName = $location.search().paramUserName;
            let paramRole = $location.search().paramRole;
            if (paramerUserName && paramRole) {
                $("#usersType").val(paramRole);
                if (paramRole == 1) {
                    $(".agentChoice").css("display", "none");
                    $(".userAccountText").css("display", "inline-block");
                    $('#userAccount').val(paramerUserName);
                }
                if (paramRole == 2) {
                    $(".agentChoice").css("display", "inline-block");
                    $(".userAccountText").css("display", "none");
                    $('#agentAccount').val(paramerUserName);
                }
                $scope.requestApi('', {
                    params: {
                        role: paramRole,
                        name: $("#userAccount").val(), //会员账号
                        agentName: $("#agentAccount").val(), // 代理商账号
                        perPage: $scope.cleverPerPageModel // 每页数据条数
                    }
                })
            } else {
                $scope.requestApi(null, {
                    params: {
                        perPage: $scope.cleverPerPageModel // 每页数据条数
                    }
                });
            }
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
            let startTime = $("#from").val();
            let endTime = $("#to").val();
            let role = $("#usersType").val();
            let lower = $("#agentUsers").val();
            $scope.requestApi(null, {
                params: {
                    startDate: startTime ? startTime + ' 00:00:00' : '',
                    endDate: endTime ? endTime + ' 23:59:59' : '',
                    type: $("#payWay").val(),
                    role: role,
                    lower: lower,
                    name: $("#userAccount").val(),
                    agentName: $("#agentAccount").val(),
                    orderNum: $scope.orderNum, // 订单号
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            let startTime = $("#from").val();
            let endTime = $("#to").val();
            let role = $("#usersType").val();
            let lower = $("#agentUsers").val();
            $scope.requestApi(url + "&model=FundCharge", {
                params: {
                    startDate: startTime ? startTime + ' 00:00:00' : '',
                    endDate: endTime ? endTime + ' 23:59:59' : '',
                    type: $("#payWay").val(),
                    role: role,
                    lower: lower,
                    name: $("#userAccount").val(),
                    agentName: $("#agentAccount").val(),
                    orderNum: $scope.orderNum, // 订单号
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 数据导出
        $scope.export = function () {
            let startTime = $("#from").val();
            let endTime = $("#to").val();
            let role = $("#usersType").val();
            let lower = $("#agentUsers").val();

            let query = $.param({
                startDate: startTime ? startTime + ' 00:00:00' : '',
                endDate: endTime ? endTime + ' 23:59:59' : '',
                type: $("#payWay").val(),
                role: role,
                lower: lower,
                name: $("#userAccount").val(),
                agentName: $("#agentAccount").val(),
                orderNum: $scope.orderNum // 订单号
            });

            window.location.href = '/vp/capital/export?model=FundCharge&' + query;
        };

    }]);
}(jQuery, angular, window);

// jQuery部分
!function ($) {
    "use strict";
    $(function () {
        $.datePicker({
            from: "#from",
            to: "#to"
        });
        $("#from").val("");
        $("#to").val("");
        // 初始化select2插件
        $("#usersType").select2({
            width: "150px"
        });
        $("#agentUsers").select2({
            width: "150px"
        });
        $("#payWay").select2({
            width: "150px"
        });

        $("#usersType").change(function () {
            if ($("#usersType").val() == "2") {
                $(".agentChoice").css("display", "inline-block");
                $(".userAccountText").css("display", "none");
                $('#userAccount').val('');
            } else if ($("#usersType").val() == "1") {
                $(".agentChoice").css("display", "none");
                $(".userAccountText").css("display", "inline-block");
                $('#agentAccount').val('');
            } else {
                $(".agentChoice").css("display", "none");
                $(".userAccountText").css("display", "none");
                $('#userAccount').val('');
                $('#agentAccount').val('');
            }
        })
    });
}(jQuery);
