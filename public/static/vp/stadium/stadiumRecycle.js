// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("newsRecycleCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});
        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/stadium/recycle";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.newsList = result.data.list.data;
                $scope.newsList.length ? $scope.newsNoData = false : $scope.newsNoData = true;
                $scope.newRecyclePage = result.data.page;
                $scope.newRecycleCurrentPage = result.data.list.current_page;
                $scope.newRecycleTotalPage = result.data.list.total;
                $scope.newRecyclePerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 初始化列表
        $scope.init = function () {
            $("#from").val("");
            $("#to").val("");
            $scope.newsTitle = "";

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
                    name: $scope.newsTitle,
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
                    name: $scope.newsTitle,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 删除管理员
        $scope.deleteNews = function (adverId, status) {
            if (status === 1) {
                $.swal("确定要还原么", function () {
                    $http.get("/vp/stadium/delete?id=" + adverId + '&status=1').success(function (result) {
                        $.swalSuccess();
                        // 刷新接口
                        $scope.requestApi($scope.url, $scope.param);
                    });
                });
            } else {
                $.swal("确定要删除么", function () {
                    $http.get("/vp/stadium/delete?id=" + adverId + '&is_del=1').success(function (result) {
                        $.swalSuccess();
                        // 刷新接口
                        $scope.requestApi($scope.url, $scope.param);
                    });
                });
            }
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
    });
}(jQuery);

