// angular数据交互--数字彩开奖
;!function ($, angular) {
    "use strict";

    angular.module('myApp').controller("numLotteryCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});

        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/lottery/draw?code=NUM_LOTTERY";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.numLotteryList = result.data.list.data || [];
                $scope.numLotteryList.length ? $scope.numLotteryNoData = false : $scope.numLotteryNoData = true;
                $scope.numLotteryJsPage = result.data.page;
                $scope.numLotteryCurrentPage = result.data.list.current_page;
                $scope.numLotteryTotalPage = result.data.list.total;
                $scope.numLotteryPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        ($scope.init = function () {
            $scope.requestApi(null, {
                params: {
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });

            $("#lotteryClassify").val("").trigger("change");
            $("#numDate").val("");
            $("#openTime").val("");
        })();

        // 清空搜索
        $scope.clearSearch = function () {
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
                    ctype: $("#lotteryClassify").val(), // 彩种
                    expect: $("#numDate").val(), // 期号
                    openTime: $("#openTime").val(), // 开奖时间
                    code: 'NUM_LOTTERY', // 数字彩代码
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            $scope.requestApi(url, {
                params: {
                    ctype: $("#lotteryClassify").val(), // 彩种
                    expect: $("#numDate").val(), // 期号
                    openTime: $("#openTime").val(), // 开奖时间
                    code: 'NUM_LOTTERY', // 数字彩代码
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 编辑开奖号码
        $scope.editNum = function (openCode, id, ctype) {
            $scope.alreadyDraw = !!openCode.length;
            switch (ctype) {
                case 1: // 排三
                case 3: // 澳彩
                    if (openCode.length === 0) {
                        openCode = ['', '', ''];
                    }

                    break;
                case 2: // 排五
                case 4: // 葡彩
                    if (openCode.length === 0) {
                        openCode = ['', '', '', '', ''];
                    }

                    break;
                case 5: // 幸运飞艇
                    if (openCode.length === 0) {
                        openCode = ['', '', '', '', '', '', '', '', '', ''];
                    }

                    break;
            }

            $scope.openCode = openCode;
            $scope.numId = id;
        };

        // 关闭编辑模态框, 初始化$scope.openCode
        $('#num-edit').on('hidden.bs.modal', function (e) {
            $scope.$apply(function () {
                $scope.openCode = [];
            });

            $("#nextOpenDate").val("").trigger("change");
            $("#nextOpenNumber").val("").trigger("change");
        });

        // 确认修改
        $scope.editSubmit = function (id) {
            let nextOpenDate = '';
            let nextOpenNum = '';
            if (!$scope.alreadyDraw){
                // 未开奖流程控制
                nextOpenDate = $("#nextOpenDate").val();
                nextOpenNum = $("#nextOpenNumber").val();

                if (!nextOpenDate){
                    $tip.warning("请填写下期开奖日期");
                    return;
                }

                if (!nextOpenNum){
                    $tip.warning("请填写下期期号");
                    return;
                }
            }
            let tempCon = [];
            let isPass = true;
            $("#editModalForm").find('input').each(function () {
                let that = $(this);
                if (!/^\d{1,2}$/.test(that.val())) {
                    isPass = false;
                }

                tempCon.push(that.val());
            });

            if (!isPass) {
                $tip.warning("请填写当前彩期开奖结果(1-10数字)");
                return;
            }

            tempCon = tempCon.join(',');
            $http.post('/vp/lottery/editSubmit', $.param({
                numId: id,
                open_code: tempCon,
                next_date: nextOpenDate,
                next_number: nextOpenNum
            })).success(function (result) {
                if (result.code === 1) {
                    $tip.success(result.msg);
                    $("#num-edit").unbind('hidden.bs.modal').modal("hide");
                    $scope.openCode = [];
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 编辑开奖号码和开奖时间
        $scope.editNumBase = function(numId, expect, opentime){
            $scope.numTwoId = numId;
            $scope.originExpect = expect;
            $("#tagOpenDate").val(opentime);
        };

        // 确认修改
        $scope.numDataSubmit = function(numId){
            let submitOpenDate = $("#tagOpenDate").val();
            if(!numId){
                $tip.warning("数据主键ID不能为空,系统错误");
                return;
            }

            if (!$scope.originExpect){
                $tip.warning("期号不可为空");
                return;
            }

            if (!submitOpenDate){
                $tip.warning("开奖日期不可为空");
                return;
            }

            $http.post('/vp/lottery/editBase', $.param({
                numId: numId, // 数字彩开奖表ID
                expect: $scope.originExpect, // 期号
                open_date: submitOpenDate // 开奖日期
            })).success(function (result) {
                if (result.code === 1){
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                    $("#num-tag").modal("hide");
                }
            });
        };

        // 手动开奖
        $scope.handDraw = function (expect, ctype) {
            $scope.expect = expect;
            $scope.ctype = ctype;
            $http.get('/vp/order/numBingo?ctype=' + ctype + '&expect=' + expect).success(function (result) {
                $scope.curNumOrderList = result.data || [];
                $scope.curNumOrderList.length === 0 ? $scope.numBingoOrderNoData = true : $scope.numBingoOrderNoData = false;
            });
        };

        // 立即开奖
        $scope.openDraw = function (expect, ctype) {
            if ($scope.numBingoOrderNoData) {
                return;
            }

            $http.get('/vp/order/numDraw?ctype=' + ctype + '&expect=' + expect).success(function (result) {
                if (result.code === 1) {
                    $tip.success(result.msg);
                    $("#num-draw").modal("hide");
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 导出Excel
        $scope.export = function () {
            let query = $.param({
                date: $("#openTime").val(),
                code: $("#lotteryClassify").val(),
                number: $("#numDate").val()
            });

            // 导出
            window.location.href = '/vp/lottery/exportNumberDarw?' + query;
        };

        // 风险控制跳转
        $scope.riskCtrl = function () {
            window.location.href = "/vp/risk";
        };

    }]);
}(jQuery, angular);

// jQuery部分
~function ($, moment) {
    "use strict";
    $(function () {
        $("#lotteryClassify").select2({
            width: '120px'
        });

        // 日期初始化
        $.datePicker({
            from: "#openTime"
        });

        $("#openTime").val("");
        // 日期初始化
        let date = new Date();
        date.setDate(date.getDate() + 7); // 最大日期是未来7天后
        $.datePicker({
            from: "#nextOpenDate",
            timePicker: true,
            timePicker24Hour: true,
            format: "YYYY-MM-DD HH:mm:ss",
            maxDate: moment(date)
        });

        $("#nextOpenDate").val("");

        // 日期初始化
        let twoDate = new Date();
        twoDate.setDate(twoDate.getDate() + 7); // 最大日期是未来7天后
        $.datePicker({
            from: "#tagOpenDate",
            timePicker: true,
            timePicker24Hour: true,
            format: "YYYY-MM-DD HH:mm:ss",
            maxDate: moment(twoDate)
        });

        $("#tagOpenDate").val("");
    });
}(jQuery, moment);