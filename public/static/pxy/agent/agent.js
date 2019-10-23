// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("agentCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});
        // 初始化checkbox
        $scope.initCheckboxAll = function () {
            let $checkAll = $('table thead tr').find('input');
            $checkAll.prop('checked', false);
        };
        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/pxy/Agent/index";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.agentList = result.data.list.data || [];
                $scope.agentList.length ? $scope.agentNoData = false : $scope.agentNoData = true;
                $scope.agentPage = result.data.page;
                $scope.agentCurrentPage = result.data.list.current_page;
                $scope.agentTotalPage = result.data.list.total;
                $scope.agentPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
            $scope.initCheckboxAll();
        };

        //推荐的下级会员
        $scope.recommend = function(id){
            window.location.href = '/pxy/recoMember?id='+id;
        };

        //总充值
        $scope.rechargeList = function(username,role){
            window.location.href = '/pxy/capital/recharge?paramUserName='+username+'&paramRole='+role;
        };

        //总输赢
        $scope.winloseList = function(username,role){
            window.location.href = '/pxy/capital/rake_back?paramUserName='+username+'&paramRole='+role;
        };

        $scope.test = function (url, param) {
            url = url || "/pxy/Agent/addAgent";
            $http.get(url).success(function (result) {

            });
        };

        // 初始化
        $scope.init = function () {
            $("#phone").val("");
            $("#nickname").val("");
            $("#userstate").val("").trigger("change");

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
            let username = $("#phone").val();
            let chn_name = $("#nickname").val();
            let state = $("#userstate").val();
            $scope.requestApi(null, {
                params: {
                    username: username ? username : '',
                    chn_name: chn_name ? chn_name : '',
                    state: state ? state : '',
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            let username = $("#phone").val();
            let chn_name = $("#nickname").val();
            let state = $("#userstate").val();
            $scope.requestApi(url, {
                params: {
                    username: username ? username : '',
                    chn_name: chn_name ? chn_name : '',
                    state: state ? state : '',
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 导出Excel
        $scope.export = function () {
            let username = $("#phone").val();
            let chn_name = $("#nickname").val();
            let state = $("#userstate").val();

            let query = $.param({
                username: username ? username : '',
                chn_name: chn_name ? chn_name : '',
                state: state ? state : ''
            });

            // 导出
            window.location.href = '/pxy/agent/export?' + query;
        };

        //关闭转移会员模态框事件
        $('#transfer-member').on('hidden.bs.modal', function () {
            $scope.angentUserName = '';
            $scope.angentPassword = '';
        });

        //修改数据赋值
        $scope.updataAgentMethod = function (AgentId) {
            $scope.AgentId = AgentId;
        };

        //转移代理商
        $scope.transferAgent = function (url, AgentId) {
            url = url || '/pxy/Agent/transferAgent';
            $http.post(url,
                $.param({
                    userName: $scope.angentUserName,
                    passWord: $scope.angentPassword,
                    id: AgentId,
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $('#transfer-member').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            })
        };
        // 删除管理员
        $scope.deleteAgent = function (agentId) {
            $.swal("确定要删除么", function () {
                $http.get("/pxy/agent/deleteAgent?id=" + agentId).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess();
                        // 刷新接口
                        $scope.requestApi($scope.url, $scope.param);
                    }
                });
            });
        };

        // 冻结和解冻
        $scope.toggle = function (agentId, frozen) {
            $http.get("/pxy/agent/toggle?id=" + agentId + "&frozen=" + frozen).success(function (result) {
                if (result.code === 1) {
                    if (frozen === 0) {
                        $tip.success("解冻成功");
                    } else {
                        $tip.success("冻结成功");
                    }
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        //批量禁用启用
        $scope.batchHandle = function (status) {
            let $tbr = $('table tbody tr');
            let checkboxNode = $tbr.find('input:checked');
            let ids = [];

            if (!checkboxNode.length) {
                $tip.warning("请选择您要操作的数据!");
                return;
            }

            checkboxNode.each(function () {
                let id = $(this).data('value');
                ids.push(id);
            });
            ids = ids.join(',');
            $.swal('您确定要批量操作么?', function () {
                $http.post("/pxy/Agent/toggles", $.param({
                    id: ids,
                    frozen: status
                })).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess(result.msg);
                        $scope.requestApi($scope.url, $scope.param);
                    }
                });
            })
        };

        //全选
        $scope.initTableCheckbox = function () {

            let $tbr = $('table tbody tr');
            let $thr = $('table thead tr');
            let $checkAll = $thr.find('input');

            $checkAll.off('click');

            $checkAll.click(function (event) {
                // 阻止默认事件和插件的其他click事件监听器
                event.preventDefault();
            });

            $tbr.find('input').click(function (event) {
                // 阻止默认事件和插件的其他click事件监听器
                event.preventDefault();
            });

            $tbr.find('input').parent().parent().click(function () {
                let inputNode = $(this).find('input');
                inputNode.prop('checked', !inputNode.is(":checked"));
                $checkAll.prop('checked', $tbr.find('input:checked').length === $tbr.length);
            });

            $checkAll.parent().parent().off('click');

            $checkAll.parent().parent().click(function () {
                $tbr.find('input').prop('checked', !$checkAll.is(':checked'));
                $checkAll.prop('checked', !$checkAll.is(":checked"));
            });
        };
        $scope.$on('ngRepeatFinished', function (ngRepeatFinishedEvent) {
            $scope.initTableCheckbox();
        });
    }]);
}(jQuery, angular);

// jQuery部分
!function ($) {
    "use strict";
    $(function () {
        // 初始化select2插件
        $("#userstate").select2();
        $("#addCashWithdrawal").select2();
        $("#addStatus").select2();
        $("#addLower").select2();
    });
}(jQuery);
