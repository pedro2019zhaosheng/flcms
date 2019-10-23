// --资金流水记录
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("capitalFlowCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});
        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/capital/index?model=FundLog&type=all";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.waterList = result.data.list.data;
                $scope.waterList.length ? $scope.capitalNoData = false : $scope.capitalNoData = true;
                $scope.waterPage = result.data.page;
                $scope.waterCurrentPage = result.data.list.current_page;
                $scope.waterTotalPage = result.data.list.total;
                $scope.waterPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 获取类型
        $scope.getTypes = function () {
            $http.get("/vp/capital/getType?model=FundLog").success(function (result) {
                $scope.types = result.data.list;
            });
        };

        $scope.getTypes();

        // 初始化
        $scope.init = function () {
            $("#memberAccount").val("");
            $("#from").val("");
            $("#to").val("");
            $("#changeType").val("").trigger("change");

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
            let startTime = $("#from").val();
            let endTime = $("#to").val();
            let status = $("#changeType").val();
            let name = $("#memberAccount").val();
            $scope.requestApi(null, {
                params: {
                    startDate: startTime ? startTime + ' 00:00:00' : '',
                    endDate: endTime ? endTime + ' 23:59:59' : '',
                    status: status ? status : '',
                    username: name ? name : '',
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 导出Excel
        $scope.export = function () {
            let startTime = $("#from").val();
            let endTime = $("#to").val();
            let status = $("#changeType").val();
            let name = $("#memberAccount").val();

            let query = $.param({
                startDate: startTime ? startTime + ' 00:00:00' : '',
                endDate: endTime ? endTime + ' 23:59:59' : '',
                status: status ? status : '',
                username: name ? name : ''
            });

            // 导出
            window.location.href = '/vp/capital/export?model=FundLog&type=all&' + query;
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            let startTime = $("#from").val();
            let endTime = $("#to").val();
            let status = $("#changeType").val();
            let name = $("#memberAccount").val();
            $scope.requestApi(url + '&model=FundLog&type=all', {
                params: {
                    startDate: startTime ? startTime + ' 00:00:00' : '',
                    endDate: endTime ? endTime + ' 23:59:59' : '',
                    status: status ? status : '',
                    username: name ? name : '',
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };
    }]);
}(jQuery, angular);

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
        // 初始化select2插件，该插件在模态框中使用时，请将modal中的 tabindex="-1" 属性删除
        $("#changeType").select2({
            width: "100px"
        });
    });
}(jQuery);
