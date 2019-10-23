// angular数据交互逻辑
!function ($, angular) {
    "use strict";
    angular.module("myApp").controller("patchCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        // 开启遮罩
        $.busyLoadFull('show', {animation: "fade"});
        // 数据接口
        $scope.requestApi = function (url, param) {
            url = url || '/vp/patchlog/page';
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, {
                params: param
            }).success(function (result) {
                // 分页数据
                $scope.patchList = result.data.list.data || [];
                $scope.patchList.length ? $scope.patchNoData = false : $scope.patchNoData = true;
                // 当前页
                $scope.patchCurrentPage = result.data.list.current_page;
                // 总页数
                $scope.patchTotalPage = result.data.list.total;
                // 每页数据条数
                $scope.patchPerPage = result.data.list.per_page;
                // 分页模板
                $scope.patchJsPage = result.data.page;

                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 初始化列表
        ($scope.init = function () {
            // 初始化日期
            $("#fromDate").val("");
            $("#toDate").val("");
            $scope.czName = "";

            $scope.requestApi(null, {
                perPage: $scope.cleverPerPageModel // 每页数据条数
            });
        })();

        // 清空搜索
        $scope.clearSearch = function () {
            $scope.init();
        };

        // 获取每页数据条数设置
        $scope.cleverChangePerPage = function (perpage) {
            $scope.param.perPage = perpage;

            // 发送请求
            $scope.requestApi(null, $scope.param);
        };

        // 查询
        $scope.searchSubmit = function () {
            let startDate = $("#fromDate");
            let endDate = $("#toDate");
            $scope.requestApi('/vp/patchlog/page', {
                startDate: startDate.val() ? startDate.val() + ' 00:00:00' : '',
                endDate: endDate.val() ? endDate.val() + ' 23:59:59' : '',
                name: $scope.czName || '',
                perPage: $scope.cleverPerPageModel // 每页数据条数
            })
        };

        // 分页
        $scope.getPatchLogPage = function (url) {
            let startDate = $("#fromDate");
            let endDate = $("#toDate");
            $scope.requestApi(url, {
                startDate: startDate.val() ? startDate.val() + ' 00:00:00' : '',
                endDate: endDate.val() ? endDate.val() + ' 23:59:59' : '',
                name: $scope.czName || '',
                perPage: $scope.cleverPerPageModel // 每页数据条数
            });
        };

        // 错误详情
        $scope.patchDetail = function (id, status) {
            if (status === 1) {
                return;
            }

            $("#patch-detail").modal("show");
            $http.get('/vp/patchlog/detail?id=' + id).success(function (result) {
                if (result.code === 1) {
                    $scope.errorDetail = result.data;
                }
            });
        };

        // 清空所有
        $scope.truncateLog = function () {
            $.swal('确定要清空所有爬取日志么?', function () {
                $http.get('/vp/patchlog/truncate').success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess();
                        $scope.requestApi($scope.url, $scope.param);
                    }
                })
            }, '清空后将无法恢复');
        };

    }]);
}(jQuery, angular);

// jQuery逻辑

~function () {
    "use strict";
    $(function () {
        // 初始化日历插件
        $.datePicker({
            from: "#fromDate",
            to: "#toDate"
        });
        // 初始化日期
        $("#fromDate").val("");
        $("#toDate").val("");
    });
}(jQuery);