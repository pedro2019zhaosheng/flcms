// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("agentReturnCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});
        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/pxy/Agent/AgentReturnIndex";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.agentRetrunList = result.data.list.data || [];
                $scope.agentRetrunList.length ? $scope.agentRetrunNoData = false : $scope.agentRetrunNoData = true;
                $scope.agentPage = result.data.page;
                $scope.agentCurrentPage = result.data.list.current_page;
                $scope.agentTotalPage = result.data.list.total;
                $scope.agentPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        //获取彩种类型
        $scope.getLotteryId = function (url) {
            url = url || '/pxy/Agent/getLotteryId';
            $http.get(url).success(function (result) {
                $scope.LotteryData = result.data;
            });
        };

        //获取彩种ID
        $scope.getAgentRebates = function (url) {
            url = url || '/pxy/Agent/getAgentRebates';
            $http.get(url).success(function (result) {

            });
        };

        // 初始化
        $scope.init = function () {
            $scope.requestApi(null, {
                params: {
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });

            $scope.getLotteryId();
        };

        // 初始化调用
        $scope.init();

        // 清空搜索
        $scope.clearSearch = function () {
            $scope.agentPhone = "";
            $('#product').val("").trigger('change');
            $('#userstate').val("").trigger('change');
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

            $scope.requestApi(null, {
                params: {
                    agentPhone: $scope.agentPhone,
                    product: $('#product').val(),
                    status: $('#userstate').val(),
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        //获取添加代理商返点数据
        $scope.getaddAgentRebates = function () {
            $http.get("/pxy/agent/getaddAgentRebates").success(function (result) {
                $scope.lottery = result.data.lottery;
                $scope.agentList = result.data.agentlist;
            });
        };

        $scope.addAgentReturn = function (lottery) {
            var lotteryData = [];
            var ids = '';
            $.each(lottery, function (index, value) {
                ids = value.id;
                lotteryData.push({key: ids, value: $('#' + ids).val(), state: $("#lotteryStatue" + ids).val()});
            });

            $http.post('/pxy/Agent/setAgentrebate',
                $.param({
                    Lottery: lotteryData,
                    id: $("#choiceAgent").val(),
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $('#agent-return-add').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            })
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            $scope.requestApi(url, {
                params: {
                    agentPhone: $scope.agentPhone,
                    product: $('#product').val(),
                    status: $('#userstate').val(),
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 删除管理员
        $scope.deleteAgent = function (agentId) {
            $.swal("确定要删除么", function () {
                $http.get("/pxy/Agent/agentRetrunDel?id=" + agentId).success(function (result) {
                    if (result.code) {
                        $.swalSuccess();
                    }
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                });
            });
        };

        // 禁用和启用
        $scope.toggle = function (agentId, status) {
            $http.get("/pxy/Agent/agentRetrunStatus?id=" + agentId + "&status=" + status).success(function (result) {
                if (result.code === 1) {
                    if (status === 0) {
                        $tip.success("禁用成功");
                    } else {
                        $tip.success("启动成功");
                    }
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        $scope.$on('ngRepeatFinished', function (ngRepeatFinishedEvent) {
            for (var i = 0; i < $scope.lottery.length; i++) {
                $("#lotteryStatue" + $scope.lottery[i].id).select2();
            }
        });
    }]);
}(jQuery, angular);

// jQuery部分
!function ($) {
    "use strict";
    $(function () {
        // 初始化select2插件
        $("#userstate").select2({
            width: "150px"
        });

        //初始化addAgent模态框中的select2插件
        $("#choiceAgent").select2();
        $("#choiceProduct").select2();
        $("#product").select2({
            width: "150px"
        });
        $("#setRatio").select2();
    });
}(jQuery);
