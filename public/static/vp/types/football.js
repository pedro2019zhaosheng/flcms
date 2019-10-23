// angular数据交互 --竞彩足球
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("footballCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        // 遮罩框
        $.busyLoadFull('show', {animation: "fade"});

        // 初始化checkbox
        $scope.initCheckboxAll = function () {
            let $checkAll = $('table thead tr').find('input');
            $checkAll.prop('checked', false);
        };

        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/football/page";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.fbList = result.data.list.data;
                $scope.fbList.length ? $scope.footballNoData = false : $scope.footballNoData = true;
                $scope.footJsPage = result.data.page;
                $scope.footCurrentPage = result.data.list.current_page;
                $scope.footTotalPage = result.data.list.total;
                $scope.footPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
            // 初始化checkbox
            $scope.initCheckboxAll();
        };

        // 初始化
        ($scope.init = function () {
            $("#footballState").val("1").trigger("change");

            $scope.requestApi(null, {
                params: {
                    status: 1, // 出售中
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        })();

        // 清空
        $scope.clearSearch = function () {
            $scope.matchName = "";
            $scope.matchNum = "";
            $("#footballState").val("").trigger("change");
            $("#from").val("");
            $("#to").val("");

            $scope.requestApi(null, {
                params: {
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
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
            let fromInputVal = $("#from").val();
            let toInputVal = $("#to").val();

            $scope.requestApi(null, {
                params: {
                    startDate: fromInputVal ? fromInputVal + ' 00:00:00' : '',
                    endDate: toInputVal ? toInputVal + ' 23:59:59' : '',
                    match_name: $scope.matchName,
                    match_num: $scope.matchNum,
                    status: $("#footballState").val(),
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 导出Excel
        $scope.export = function () {
            let fromInputVal = $("#from").val();
            let toInputVal = $("#to").val();

            let query = $.param({
                startDate: fromInputVal ? fromInputVal + ' 00:00:00' : '',
                endDate: toInputVal ? toInputVal + ' 23:59:59' : '',
                match_name: $scope.matchName,
                match_num: $scope.matchNum,
                status: $("#footballState").val()
            });

            // 导出
            window.location.href = '/vp/football/export?' + query;
        };

        // 分页事件
        $scope.getFootballPage = function (url) {
            let fromInputVal = $("#from").val();
            let toInputVal = $("#to").val();

            $scope.requestApi(url, {
                params: {
                    startDate: fromInputVal ? fromInputVal + ' 00:00:00' : '',
                    endDate: toInputVal ? toInputVal + ' 23:59:59' : '',
                    match_name: $scope.matchName,
                    match_num: $scope.matchNum,
                    status: $("#footballState").val(),
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 批量开售
        $scope.footballNormal = function (status) {
            let $tbr = $('table tbody tr');
            let checkboxNode = $tbr.find('input:checked');
            let ids = [];
            let msg;
            status === 0 ? msg = '停售' : msg = '出售';

            if (!checkboxNode.length) {
                $tip.warning("请选择您要" + msg + "的数据!");
                return;
            }

            checkboxNode.each(function () {
                let id = $(this).data('value');
                ids.push(id);
            });
            ids = ids.join(',');

            $.swal('您确定要' + msg + '么?', function () {
                $http.get('/vp/football/toggle?ids=' + ids + '&status=' + status).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess(msg + '成功');
                        // 初始化列表
                        $scope.requestApi($scope.url, $scope.param);
                    }
                })
            });
        };

        // 批量删除
        $scope.footballDel = function () {
            let $tbr = $('table tbody tr');
            let checkboxNode = $tbr.find('input:checked');
            let ids = [];

            if (!checkboxNode.length) {
                $tip.warning("请选择您要删除的数据!");
                return;
            }

            checkboxNode.each(function () {
                let id = $(this).data('value');
                ids.push(id);
            });

            ids = ids.join(',');
            $.swal('您确定删除么?', function () {
                $http.get('/vp/football/delete?ids=' + ids).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess('删除成功');
                        // 初始化列表
                        $scope.requestApi($scope.url, $scope.param);
                    }
                })
            });
        };

        // 删除一条数据
        $scope.deleteOne = function (id) {
            $.swal('您确定删除么?', function () {
                $http.get('/vp/football/delete?ids=' + id).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess('删除成功');
                        // 初始化列表
                        $scope.requestApi($scope.url, $scope.param);
                    }
                })
            });
        };

        // 赛事查看
        $scope.matchDetail = function (matchId, leagueName, hostName, guestName) {
            $http.get('/vp/football/detail?matchId=' + matchId).success(function (result) {
                if (result.code === 1) {
                    $scope.detailMatchName = leagueName; // 联赛名称
                    $scope.detailHost = hostName; // 主队
                    $scope.detailGuest = guestName; // 客队
                    $scope.spSpfNewest = result.data.sp_spf; // 胜平负奖金指数
                    $scope.spSpfVarIndex = result.data.sp_spf_var; // 胜平负奖金指数变化
                    $scope.rqspfNewest = result.data.sp_rqspf; // 让球胜平负奖金指数
                    $scope.rqspfVarIndex = result.data.sp_rqspf_var; // 让球胜平负奖金指数变化
                    $scope.jqsNewest = result.data.sp_jqs; // 进球数奖金指数
                    $scope.jqsVarIndex = result.data.sp_jqs_var; // 进球数奖金指数变化
                    $scope.bfNewest = result.data.sp_bf; // 全场比分奖金指数
                    $scope.bfVarIndex = result.data.sp_bf_var; // 全场比分奖金指数变化
                    $scope.bqcNewest = result.data.sp_bqc; // 半场奖金指数
                    $scope.bqcVarIndex = result.data.sp_bqc_var; // 半场奖金指数变化
                }
            });
        };

        // 赛事编辑
        $scope.matchEdit = function (item) {
            $scope.editMatchNum = item.match_num; // 赛事编号
            $scope.editMatchName = item.league_name; // 联赛名称
            $scope.editHostName = item.host_name; // 主队名称
            $scope.editGuestName = item.guest_name; // 客队名称
            $scope.editRqs = item.rqs; // 让球数
            $scope.editSysTime = item.sys_cutoff_time; // 系统截止时间
            $("#footballManual").val(item.cutoff_time || "").trigger("change"); // 手动截止时间

            $http.get('/vp/football/reDetail?matchNum=' + item.match_num).success(function (result) {
                let data = result.data || {};
                let halfWhole = data.half_score || '';
                let totalWhole = data.normal_score || '';
                $scope.hostHalfScore = halfWhole.split("-")[0];
                $scope.guestHalfScore = halfWhole.split("-")[1];
                $scope.hostTotalScore = totalWhole.split("-")[0];
                $scope.guestTotalScore = totalWhole.split("-")[1];
            });
        };

        // 清除手动时间
        $scope.clearHandTime = function () {
            $("#footballManual").val("").trigger("change"); // 手动截止时间
        };

        // 保存编辑后的比赛数据
        $scope.saveEdit = function () {
            let handTime = $("#footballManual").val();
            let formData = {};
            // 手动截止时间
            formData.cutoff_time = handTime || '';

            if ($scope.editRqs && /(^\d$)|(^-\d$)/.test($scope.editRqs)) {
                formData.rqs = $scope.editRqs;
            }

            let hostHalfSore = $scope.hostHalfScore;
            let guestHalfSore = $scope.guestHalfScore;
            if (hostHalfSore && guestHalfSore) {
                formData.half_score = hostHalfSore + '-' + guestHalfSore;
            }

            let hostTotalSore = $scope.hostTotalScore;
            let guestTotalSore = $scope.guestTotalScore;
            if (hostTotalSore && guestTotalSore) {
                formData.normal_score = hostTotalSore + '-' + guestTotalSore;
            }

            formData.match_num = $scope.editMatchNum;

            $http.post('/vp/football/save', $.param(formData)).success(function (result) {
                if (result.code === 1) {
                    $tip.success('保存成功');
                    $("#footballEdit").modal("hide");
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
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
!function ($, moment) {
    "use strict";
    $(function () {
        // 初始化select2
        $("#footballState").select2({
            width: "120px"
        });

        // 初始化daterangepicker
        $.datePicker({
            from: "#from",
            to: "#to"
        });

        $("#from").val("");
        $("#to").val("");

        // 阻止事件冒泡
        $(".cart-left").bind('click', function (event) {
            event.stopPropagation();
        });

        // 日历初始化
        let date = new Date();
        date.setDate(date.getDate() + 7); // 最大日期是未来7天后
        $.datePicker({
            from: "#footballManual",
            timePicker: true,
            timePicker24Hour: true,
            // timePickerSeconds: true,
            format: "YYYY-MM-DD HH:mm:ss",
            maxDate: moment(date)
        });
    });
}(jQuery, moment);

