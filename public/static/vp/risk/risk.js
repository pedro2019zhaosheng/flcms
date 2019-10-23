// angular部分(风险控制)
;!function ($, angular, window) {
    "use strict";

    angular.module("myApp").controller("riskCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        // 监听angular自动风控列表渲染完成
        $scope.$on('ngRepeatFinished', function () {
            // 初始化开关插件
            $(".autoRisk").lc_switch("已开", "已关");
        });

        // 监听angular手动风控列表渲染完成
        $scope.$on('ngRepeatFinished2', function () {
            $(".handSelect").select2({
                width: "350px"
            });
        });

        // 预设列表
        $.busyLoadFull('show', {animation: "fade"});
        $scope.requestApi = function (url, param) {
            url = url || '/vp/risk/preList';
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.riskList = result.data.list.data || [];
                $scope.riskList.length ? $scope.riskNoData = false : $scope.riskNoData = true;
                $scope.riskJsPage = result.data.page;
                $scope.riskCurrentPage = result.data.list.current_page;
                $scope.riskTotalPage = result.data.list.total;
                $scope.riskPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 初始化预设列表
        ($scope.initList = function () {
            $scope.requestApi(null, {
                params: {
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        })();

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

        // 预设列表分页事件
        $scope.getTpPage = function (url) {
            $scope.requestApi(url, {
                params: {
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 获取自动风控列表
        ($scope.initAuto = function () {
            $http.get('/vp/risk/riskList').success(function (result) {
                $scope.riskConfigList = result.data;
            });
        })();

        // 获取手动风控列表
        ($scope.initHand = function () {
            $http.get('/vp/risk/handList').success(function (result) {
                $scope.riskHandList = result.data;
            });
        })();

        // 手动风控清空
        $scope.clear = function ($e) {
            let that = $e.currentTarget;
            $(that).parent().find("input").val("");
        };

        // 手动风控提交
        $scope.submit = function ($e, ctype) {
            let expect = $($e.currentTarget).parent().find("select").val(); // 期号
            let openCode = $($e.currentTarget).parent().find("input").val(); // 开奖号码
            let isPass = true;
            let msg = '';
            switch (ctype) {
                case 3: // 澳彩
                    if (openCode.length !== 3) {
                        isPass = false;
                        msg = '请正确输入澳彩开奖号码';
                    }

                    break;
                case 4: // 葡彩
                    if (openCode.length !== 5) {
                        isPass = false;
                        msg = '请正确输入葡彩开奖号码';
                    }

                    break;
                default:
                    isPass = false;
                    msg = '未定义的彩种';
            }

            if (!isPass) {
                $tip.warning(msg);
                return;
            }

            let openCodeStr = openCode.split("").join(','); // 开奖号码
            $http.post('/vp/risk/setCode', $.param({
                number: expect, // 期号
                ctype: ctype, // 数字彩种类型
                openCode: openCodeStr
            })).success(function (result) {
                if (result.code === 1) {
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 监听自动风控开启/关闭事件
        angular.element(document).ready(function () {
            $('body').delegate('.autoRisk', 'lcs-statuschange', function () {
                let status = ($(this).is(':checked')) ? 1 : 0;
                let sign = $(this).data("sign");
                $http.get('/vp/risk/inConfig?var=' + sign + '&value=' + status).success(function (result) {
                    if (result.code === 1) {
                        $tip.success(result.msg);
                        $scope.initAuto();
                    }
                });
            });
        });

    }]);
}(jQuery, angular, window);