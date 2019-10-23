// angular数据交互 --手动派奖
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("lotteryManualCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        // 遮罩框开始
        $.busyLoadFull('show', {animation: "fade"});

        //初始化checkbox
        $scope.initCheckboxAll = function () {
            let $checkAll = $('table thead tr').find('input');
            $checkAll.prop('checked', false);
        };

        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/order/bingoPage";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.bingoList = result.data.list.data || [];
                $scope.bingoList.length ? $scope.bingoNoData = false : $scope.bingoNoData = true;
                $scope.manualJsPage = result.data.page;
                $scope.manualCurrentPage = result.data.list.current_page;
                $scope.manualTotalPage = result.data.list.total;
                $scope.manualPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });

            // 初始化checkbox控件
            $scope.initCheckboxAll();
        };

        // 初始化
        ($scope.init = function () {
            $scope.username = ""; // 会员账号
            $scope.betNum = ''; // 注单编号
            $("#moni").val("-1").trigger("change"); // 是否模拟
            $("#manualClassify").val("-1").trigger("change"); // 彩种
            $("#manualState").val("-1").trigger("change"); // 订单状态

            $scope.requestApi(null, {
                params: {
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        })();

        // 彩种列表
        ($scope.getLotteryAll = function () {
            $http.get('/vp/lottery/all2').success(function (result) {
                $scope.lotteryList = result.data;
            });
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
            $scope.requestApi(null, {
                params: {
                    order_no: $scope.betNum,
                    is_moni: $("#moni").val(),
                    lottery_id: $("#manualClassify").val(),
                    settle_status: $("#manualState").val(),
                    accountName: $scope.username,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 导出Excel
        $scope.export = function () {
            let query = $.param({
                order_no: $scope.betNum,
                is_moni: $("#moni").val(),
                lottery_id: $("#manualClassify").val(),
                settle_status: $("#manualState").val()
            });

            // 导出
            window.location.href = '/vp/order/export?' + query;
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            $scope.requestApi(url, {
                params: {
                    order_no: $scope.betNum,
                    is_moni: $("#moni").val(),
                    lottery_id: $("#manualClassify").val(),
                    settle_status: $("#manualState").val(),
                    accountName: $scope.username,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 立即派奖
        $scope.atOnceAward = function (orderId, orderType) {
            let data;
            if (orderType === 1 || orderType === 2) {
                data = orderType + '@' + orderId;
            } else {
                $tip.warning("未知彩种类型");
                return;
            }

            $.swal('确定派发奖金么', function () {
                $http.post('/vp/order/sendPrize', $.param({data: data})).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess(result.msg);
                        $scope.requestApi($scope.url, $scope.param);
                    }
                });
            }, '奖金派发后,将无法追回,请谨慎操作!');
        };

        // 批量派奖
        $scope.batchManual = function () {
            let $tbr = $('table#manualList tbody tr');
            let checkboxNode = $tbr.find('input:checked');
            let data = [];

            if (!checkboxNode.length) {
                $tip.warning("请选择您要派奖的订单!");
                return;
            }

            let oid, oty, temp;
            checkboxNode.each(function () {
                oid = $(this).data('value');
                oty = $(this).data('iden');
                temp = oty + '@' + oid;
                data.push(temp);
            });

            data = data.join(',');
            $.swal('确定派发奖金么', function () {
                $http.post('/vp/order/sendPrize', $.param({data: data})).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess(result.msg);
                        $scope.requestApi($scope.url, $scope.param);
                    }
                });
            }, '奖金派发后,将无法追回,请谨慎操作!');
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

        // 监听angular列表渲染完成
        $scope.$on('ngRepeatFinished', function () {
            $scope.initTableCheckbox();
        });

    }]);
}(jQuery, angular);

// jQuery部分
!function ($) {
    "use strict";
    $(function () {
        // 初始化select2插件
        $("#manualClassify").select2({
            width: "120px"
        });
        $("#manualState").select2({
            width: "120px"
        });
        $("#moni").select2({
            width: "120px"
        });
    });
}(jQuery);

