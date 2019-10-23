// angular数据交互 --后台消息列表
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("msgCtrl", ["$scope", "$http", "$tip", "$location", function ($scope, $http, $tip, $location) {
        // 加载遮罩框开启
        $.busyLoadFull('show', {animation: "fade"});
        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/msg/msgList";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.msgList = result.data.list.data || [];
                $scope.msgList.length ? $scope.msgNoData = false : $scope.msgNoData = true;
                $scope.msgJsPage = result.data.page;
                $scope.msgCurrentPage = result.data.list.current_page;
                $scope.msgTotalPage = result.data.list.total;
                $scope.msgPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 初始化列表
        ($scope.init = function () {
            $("#from").val("").trigger("change");
            $("#to").val("").trigger("change");
            $("#msgType").val("-1").trigger("change");
            $("#bodyType").val("-1").trigger("change");
            $("#status").val("-1").trigger("change");
            $("#username").val("");

            let id = $location.search().id;
            if (id) {
                $scope.requestApi('/vp/msg/msgList?id=' + id);
                // 不显示筛选项
                $scope.isShowNav = false;
            } else {
                $scope.requestApi(null, {
                    params: {
                        perPage: $scope.cleverPerPageModel // 每页数据条数
                    }
                });
                // 显示筛选项
                $scope.isShowNav = true;
            }
        })();

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
                    startDate: fromInputVal ? fromInputVal + ' 00:00:00' : '', // 开始日期
                    endDate: toInputVal ? toInputVal + ' 23:59:59' : '', // 结束日期
                    account: $("#username").val(), // 账号
                    msg_type: $("#msgType").val(), // 消息类型
                    body_type: $("#bodyType").val(), // 内容类型
                    read_state: $("#status").val(), // 状态
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
                    startDate: fromInputVal ? fromInputVal + ' 00:00:00' : '', // 开始日期
                    endDate: toInputVal ? toInputVal + ' 23:59:59' : '', // 结束日期
                    account: $("#username").val(), // 账号
                    msg_type: $("#msgType").val(), // 消息类型
                    body_type: $("#bodyType").val(), // 内容类型
                    read_state: $("#status").val(), // 状态
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

    }]).config(['$locationProvider', function ($locationProvider) {
        // $locationProvider.html5Mode(true);
        $locationProvider.html5Mode({
            enabled: true,
            requireBase: false
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

        // 初始化select2插件，该插件在模态框中使用时，请将modal中的 tabindex="-1" 属性删除
        $("#msgType").select2({
            width: "100px"
        });
        $("#bodyType").select2({
            width: "100px"
        });
        $("#status").select2({
            width: "100px"
        });
    });
}(jQuery);

