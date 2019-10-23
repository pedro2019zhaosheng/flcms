// angular数据交互 --系统日志
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("memberCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});
        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/system/systemLog";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.systemLogList = result.data.list.data;
                $scope.systemLogList.length ? $scope.systemLogNoData = false : $scope.systemLogNoData = true;
                $scope.systemLogPage = result.data.page;
                $scope.systemLogCurrentPage = result.data.list.current_page;
                $scope.systemLogTotalPage = result.data.list.total;
                $scope.systemLogPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 初始化
        ($scope.init = function () {
            $scope.requestApi(null, {
                params: {
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        })();

        // 清空搜索
        $scope.clearSearch = function () {
            $('#to').val("").trigger('change'); // 开始日期
            $('#from').val("").trigger('change'); // 结束日期
            $scope.username = ''; // 执行人
            $scope.workName = ''; // 业务名称
            $("#belong").val("-1").trigger('change'); // 所属平台
            $("#status").val("-1").trigger('change'); // 状态
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
            let start_date = $('#from').val();
            let end_date = $('#to').val();

            if (start_date) {
                start_date = start_date + ' 00:00:00';
            }

            if (end_date) {
                end_date = end_date + ' 23:59:59';
            }

            $scope.requestApi(null, {
                params: {
                    start_date: start_date,
                    end_date: end_date,
                    name: $scope.username,
                    workName: $scope.workName,
                    belong: $("#belong").val(),
                    status: $("#status").val(),
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            let start_date = $('#from').val();
            let end_date = $('#to').val();

            if (start_date) {
                start_date = start_date + ' 00:00:00';
            }

            if (end_date) {
                end_date = end_date + ' 23:59:59';
            }

            $scope.requestApi(url, {
                params: {
                    start_date: start_date,
                    end_date: end_date,
                    name: $scope.username,
                    workName: $scope.workName,
                    belong: $("#belong").val(),
                    status: $("#status").val(),
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 获取日志信息
        $scope.showInfo = function (id) {
            $http.get('/vp/system/logDetail?id=' + id).success(function (result) {
                $scope.logInfo = result.data || '';
                $scope.logInfo ? $scope.logInfoNoData = false : $scope.logInfoNoData = true;
            });
        };

        // 清空所有
        $scope.truncateLog = function () {
            $.swal('确定要清空所有系统日志么?', function () {
                $http.get('/vp/system/truncate').success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess();
                        $scope.requestApi($scope.url, $scope.param);
                    }
                })
            }, '清空后将无法恢复');
        };

    }]);
}(jQuery, angular);

// jQuery部分
!function ($) {
    "use strict";
    $(function () {
        // 初始化select2插件
        $("#status").select2({
            width: '120px'
        });

        $("#belong").select2({
            width: '120px'
        });

        // 初始化日历插件
        $.datePicker({
            from: "#from",
            to: "#to"
        });

        $("#from").val("");
        $("#to").val("");
    });
}(jQuery);
