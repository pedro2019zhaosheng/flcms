// angular部分 --代充值
!function ($, angular) {
    "use strict";
    angular.module("myApp").controller("rechargeCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        // 初始化
        ($scope.init = function () {
            // 初始化搜索
            $scope.searchUser = "";
        })();

        // 查询会员、代理商
        $scope.searchMember = function () {
            // 是否截断
            if (!$scope.searchUser) {
                $tip.warning("请输入您要充值的账号");
                return;
            }

            // 初始化充值金额
            $scope.rechargeAmount = '';
            $.busyLoadFull('show', {animation: "fade"});
            $http.get('/vp/recharge/member?amountNum=' + $scope.searchUser).success(function (result) {
                $.busyLoadFull('hide', {animation: "fade"});
                if (result.data.length === 0) {
                    $tip.warning("您输入的会员不存在");
                    return;
                }

                $scope.memberDetail = result.data;
                $scope.memberId = result.data.id;
                $("#recharge-modal").modal("show");
            });

        };

        // 清空input
        $scope.clearInput = function () {
            $scope.init();
        };

        // 确认充值
        $scope.rechargeSave = function () {
            if (!$scope.rechargeAmount) {
                $tip.warning("请输入您要充值的金额");
                return;
            }

            if (/[^\d]/g.test($scope.rechargeAmount)) {
                $tip.warning("请填写整数金额");
                return;
            }

            $http.post('/vp/recharge/recharge?id=' + $scope.memberId, $.param({
                amount: $scope.rechargeAmount
            })).success(function (result) {
                if (result.code === 1) {
                    $("#recharge-modal").modal("hide");
                    $tip.success("充值成功");
                }
            });
        };
    }]);
}(jQuery, angular);

// jQuery部分