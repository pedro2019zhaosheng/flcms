// angular数据交互 --赛事开奖
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("lotteryMatchCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});

        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/lottery/draw";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.drawList = result.data.list.data || [];
                $scope.drawList.length ? $scope.drawNoData = false : $scope.drawNoData = true;
                $scope.drawJsPage = result.data.page;
                $scope.drawCurrentPage = result.data.list.current_page;
                $scope.drawTotalPage = result.data.list.total;
                $scope.drawPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 初始化
        $scope.init = function () {
            $scope.distinguishCode = 'ZC';
            let code = '';
            $http.get('/vp/lottery/all').success(function (result) {
                let data = result.data || [];
                $scope.lotteryList = data;
                if (data.length === 0) {
                    // 关闭页面遮罩
                    $.busyLoadFull('hide', {animation: "fade"});
                    return;
                }

                let initData = data[0];
                code = initData.code;
                $scope.requestApi(null, {
                    params: {
                        code: code,
                        perPage: $scope.cleverPerPageModel // 每页数据条数
                    }
                });
            });

            $("#lotteryClassify").val(code).trigger("change");
            $("#lotteryState").val("").trigger("change");
            // 初始化调用, 默认是竞彩足球
            $scope.lotteryZcCode = true;
            $scope.lotteryLcCode = false;
        };

        // 初始化调用
        $scope.init();

        // 清空搜索
        $scope.clearSearch = function () {
            // 竞彩日期
            $("#jcDate").val("").trigger("change");
            // 比赛编号
            $("#mchNum").val("").trigger("change");
            // 球队名称
            $("#teamName").val("").trigger("change");
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
            let czCode = $("#lotteryClassify").val();
            if (czCode === 'ZC'||czCode === 'BJ') {
                $scope.lotteryZcCode = true;
                $scope.lotteryLcCode = false;
            }
            if (czCode === 'LC') {
                $scope.lotteryLcCode = true;
                $scope.lotteryZcCode = false;
            }

            $scope.requestApi(null, {
                params: {
                    date: $("#jcDate").val(), // 竞彩日期
                    code: czCode, // 彩种代码
                    status: $("#lotteryState").val(), // 赛事状态
                    matchNum: $("#mchNum").val(), // 比赛编号
                    teamName: $("#teamName").val(), // 球队名称
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 导出Excel
        $scope.export = function () {
            let query = $.param({
                date: $("#jcDate").val(),
                code: $("#lotteryClassify").val(),
                status: $("#lotteryState").val()
            });

            // 导出
            window.location.href = '/vp/lottery/exportDraw?' + query;
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            $scope.requestApi(url, {
                params: {
                    date: $("#jcDate").val(),
                    code: $("#lotteryClassify").val(),
                    status: $("#lotteryState").val(),
                    matchNum: $("#mchNum").val(), // 比赛编号
                    teamName: $("#teamName").val(), // 球队名称
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 竞彩赛果
        $scope.lookDetail = function (item) {
            $scope.detailLeagueName = item.league_name;
            $scope.detailHostName = item.host_name;
            $scope.detailGuestName = item.guest_name;

            $http.get('/vp/lottery/jcResult?code=' + item.code + '&matchNum=' + item.match_num).success(function (result) {
                if (result.code === 1) {
                    console.log($scope.lotteryZcCode);
                    console.log($scope.lotteryLcCode);
                    if ($scope.lotteryZcCode) {//足彩
                        $scope.reSpf = result.data.spf; // 胜平负
                        $scope.reRqspf = result.data.rqspf; // 让球胜平负
                        $scope.reJqs = result.data.jqs; // 进球数
                        $scope.reBqc = result.data.bqc; // 半全场胜平负
                        $scope.reQcbf = result.data.bf; // 全场比分
                    }
                    if ($scope.lotteryLcCode) {
                        $scope.sf = result.data.sf; //胜负
                        $scope.rfsf = result.data.rfsf; //让分胜负
                        $scope.ksfc = result.data.ksfc; //客胜负差
                        $scope.zsfc = result.data.zsfc; //主胜负彩
                        $scope.dxf = result.data.dxf; //大小分
                    }
                }
            });
        };

        // 手动开奖
        $scope.handDealLottery = function (matchNum, code) {
            $scope.submitMatchNum = matchNum;
            $scope.submitCode = code;
            $http.get('/vp/order/bingo?matchNum=' + matchNum + '&code=' + code).success(function (result) {
                $scope.bingoList = result.data || [];
                $scope.bingoList.length === 0 ? $scope.bingoOrderNoData = true : $scope.bingoOrderNoData = false;
            });
        };

        // 确认开奖
        $scope.submitBingo = function (matchNum, code) {
            // 开奖逻辑
            if ($scope.bingoList.length === 0) {
                return;
            }

            $.busyLoadFull('show', {animation: "fade"});
            $http.post('/vp/order/handBingo', $.param({
                code: code,
                matchNum: matchNum
            })).success(function (result) {
                if (result.code === 1) {
                    $tip.success(result.msg);
                    $("#hand-lottery").modal("hide");

                    // 刷新列表
                    $scope.requestApi($scope.url, $scope.param);
                }
                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };
    }]);
}(jQuery, angular);

// jQuery部分
!function ($) {
    "use strict";
    $(function () {
        // 初始化select2插件
        $("#lotteryClassify").select2({
            width: "120px",
        });

        $("#lotteryState").select2({
            width: "120px",
        });
        // 初始化日历插件
        $.datePicker({
            from: "#jcDate",
        });

        $("#jcDate").val("").trigger("change");
    });
}(jQuery);
