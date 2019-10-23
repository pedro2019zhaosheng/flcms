// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("memberCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});
        $scope.requestApi = function (url, param) {
            url = url || "/vp/system/smslog";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.smsList = result.data.list.data;
                $scope.smsList.length ? $scope.smsNoData = false : $scope.smsNoData = true;
                $scope.smsPage = result.data.page;
                $scope.smsCurrentPage = result.data.list.current_page;
                $scope.smsTotalPage = result.data.list.total;
                $scope.smsPerPage = result.data.list.per_page;

                $.busyLoadFull('hide', {animation: "fade"});//关闭页面遮罩
            });
        };

        // 初始化
        $scope.init = function () {
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
            $('#sms_startTime').val("");
            $('#sms_endTime').val("");
            $('#sms_phone').val("");
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
            let starTime = $('#sms_startTime').val();
            let endTime = $('#sms_endTime').val();
            let phone = $('#sms_phone').val();
            $scope.requestApi(null, {
                params: {
                    start_date: starTime,
                    end_date: endTime,
                    phone: phone,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            let starTime = $('#sms_startTime').val();
            let endTime = $('#sms_endTime').val();
            let phone = $('#sms_phone').val();
            $scope.requestApi(url, {
                params: {
                    start_date: starTime,
                    end_date: endTime,
                    phone: phone,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };
    }]);
}(jQuery, angular);

!function ($) {
    "use strict";

    $(function () {

        // 初始化select2插件
        $("#userstate").select2();

        // 初始化日历插件
        $.datePicker({
            from: "#sms_startTime",
            to: "#sms_endTime"
        });

        $("#sms_startTime").val("");
        $("#sms_endTime").val("");
    });
}(jQuery);
