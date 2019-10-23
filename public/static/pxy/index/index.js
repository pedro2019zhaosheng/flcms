// 首页 angular JS write By CleverStone  --首页
~function ($, angular, Chart) {
    "use strict";
    angular.module("myApp").controller("homeCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        // 显示遮罩框
        $.busyLoadFull('show', {animation: "fade"});
        // 初始化页面, 获取后台统计
        ($scope.init = function () {
            $http.get('/pxy/home/counter').success(function (result) {
                $scope.counterData = result.data || {};
                // counter-up 动态计数器
                angular.element(document).ready(function () {
                    $('.counter').counterUp({
                        delay: 10,
                        time: 600
                    });

                    // 最近十五天订单走势图
                    let canvas1 = document.getElementById("comboBarLineChartOrder").getContext('2d');
                    let canvasModel1 = new Chart(canvas1, {
                        type: 'bar',
                        data: {
                            labels: $scope.counterData.orderLine.x,
                            datasets: [
                                {
                                    type: 'line',
                                    label: '订单走势图',
                                    borderWidth: 2,
                                    borderColor: '#007bff',
                                    fill: false,
                                    data: $scope.counterData.orderLine.y
                                },
                                {
                                    type: 'bar',
                                    label: '订单树状图',
                                    borderWidth: 0,
                                    borderColor: '#ffffff',
                                    backgroundColor: '#007bff',
                                    data: $scope.counterData.orderLine.y
                                }
                            ],
                            borderWidth: 1
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            }
                        }
                    });

                    // 最近十五天入金走势图
                    let canvas2 = document.getElementById("comboBarLineChartRecharge").getContext('2d');
                    let canvasModel2 = new Chart(canvas2, {
                        type: 'bar',
                        data: {
                            labels: $scope.counterData.chargeLine.x,
                            datasets: [
                                {
                                    type: 'line',
                                    label: '入金走势图',
                                    borderWidth: 2,
                                    borderColor: '#ffc107',
                                    fill: false,
                                    data: $scope.counterData.chargeLine.y
                                },
                                {
                                    type: 'bar',
                                    label: '入金树状图',
                                    borderWidth: 0,
                                    borderColor: '#ffffff',
                                    backgroundColor: '#ffc107',
                                    data: $scope.counterData.chargeLine.y
                                }
                            ],
                            borderWidth: 1
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                            }
                        }
                    });

                    // 关闭遮罩框
                    $.busyLoadFull('hide', {animation: "fade"});
                });
            });
        })();

    }]);
}(jQuery, angular, Chart);