// 公用导航和左侧菜单JS write By CleverStone
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("headerCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        // 获取当前登录管理员详情
        $scope.init = function () {
            $http.get("/pxy/home").success(function (result) {
                $scope.nickName = result.data.nick_name; // 昵称
                $scope.photo = result.data.photo; // 头像
                $scope.balance = result.data.balance; // 余额
                $scope.hadsel = result.data.hadsel; // 彩金
            }).error(function (error) {
                console.warn(error);
            });
        };
        $scope.init();

        // 退出
        $scope.logout = function ($e) {
            $e.preventDefault();
            $.cookie('currentTopMenu', '', {path: '/', expires: -1});
            window.location.href = '/pxy/logout';
        };

        // 代充值
        $scope.agentRecharge = function () {
            window.location.href = '/pxy/agentRecharge';
        };
    }]);
}(jQuery, angular);