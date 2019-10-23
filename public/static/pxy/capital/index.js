// angular数据交互 --提现记录
~function ($, angular, window) {
    "use strict";
    angular.module("myApp").config(['$locationProvider', function ($locationProvider) {
        // $locationProvider.html5Mode(true);
        $locationProvider.html5Mode({
            enabled: true,
            requireBase: false
        });
    }]);
    angular.module("myApp").controller("cashCtrl", ["$scope", "$http", "$tip", "$location", function ($scope, $http, $tip, $location, $compile) {
        $.busyLoadFull('show', {animation: "fade"});
        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/pxy/capital/index?model=FundWithdraw";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.adverList = result.data.list.data;
                $scope.adverList.length ? $scope.capitalNoData = false : $scope.capitalNoData = true;
                $scope.adverPage = result.data.page;
                $scope.adverCurrentPage = result.data.list.current_page;
                $scope.adverTotalPage = result.data.list.total;
                $scope.adverPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 初始化列表
        $scope.init = function () {
            $("#from").val("");
            $("#to").val("");
            $("#userAccount").val("");
            $("#agentAccount").val("");
            $("#usersType").val("").trigger("change");
            $("#agentUsers").val("").trigger("change");
            //获取url的参数
            var paramerUserName = $location.search().paramUserName;
            var paramRole = $location.search().paramRole;
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
                        name: $("#userAccount").val(), // 会员账号
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
                    role: role,
                    lower: lower,
                    name: $("#userAccount").val(),
                    agentName: $("#agentAccount").val(),
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 导出Excel
        $scope.export = function () {
            let startTime = $("#from").val();
            let endTime = $("#to").val();
            let role = $("#usersType").val();
            let lower = $("#agentUsers").val();

            let query = $.param({
                startDate: startTime ? startTime + ' 00:00:00' : '',
                endDate: endTime ? endTime + ' 23:59:59' : '',
                role: role,
                lower: lower,
                name: $("#userAccount").val(),
                agentName: $("#agentAccount").val()
            });

            // 导出
            window.location.href = '/pxy/capital/export?model=FundWithdraw&' + query;
        };

        // 提交审核
        $scope.submitCash = function () {
            let status;
            if ($("#status input:checked").val() == 1) {
                status = 2;
            } else {
                status = 3;
            }
            $.post("/pxy/capital/editVerify", $.param({
                id: $('#verifyID').val(),
                model: 'FundWithdraw',
                status: status,
                remark: $("#addommissionRate").val(),
            })).success(function (result) {
                if (result.code === 1) {
                    // 刷新接口
                    $("#cash-verify").modal("hide");
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            let startTime = $("#from").val();
            let endTime = $("#to").val();
            let role = $("#usersType").val();
            let lower = $("#agentUsers").val();
            $scope.requestApi(url + '&model=FundWithdraw', {
                params: {
                    startDate: startTime ? startTime + ' 00:00:00' : '',
                    endDate: endTime ? endTime + ' 23:59:59' : '',
                    role: role,
                    lower: lower,
                    name: $("#userAccount").val(),
                    agentName: $("#agentAccount").val(),
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 关闭模态框事件
        $('#adver-add').on('hidden.bs.modal', function () {
            // 初始化新增模态框
            $scope.initAddModal();
        });

        // 获取详情
        $scope.getDetail = function (memberId, fundId) {
            $http.get("/pxy/capital/info?memberId=" + memberId + "&model=FundWithdraw&fundId=" + fundId).success(function (result) {
                if (result.code === 1) {
                    $scope.datas = result.data;
                }
            });
        };

        // 点击审核事件
        $scope.getVerify = function (id) {
            $("#verifyID").val(id);
        };
    }]);
}(jQuery, angular, window);

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
        $("#usersType").select2({
            width: "150px"
        });
        $("#agentUsers").select2({
            width: "150px"
        });
        $("#usersType").change(function () {
            if ($("#usersType").val() == "2") {
                $(".agentChoice").css("display", "inline-block");
                $(".userAccountText").css("display", "none");
            } else if ($("#usersType").val() == "1") {
                $(".agentChoice").css("display", "none");
                $(".userAccountText").css("display", "inline-block");
            } else {
                $(".agentChoice").css("display", "none");
                $(".userAccountText").css("display", "none");
            }
        })
    });
}(jQuery);

