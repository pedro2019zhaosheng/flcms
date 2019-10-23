// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("basketballCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});

        //初始化checkbox
        $scope.initCheckboxAll = function () {
            let $checkAll = $('table thead tr').find('input');
            $checkAll.prop('checked', false);
        };

        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/basketball/lister";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.basketList = result.data.list.data || [];
                $scope.basketList.length ? $scope.basketNoData = false : $scope.basketNoData = true;
                $scope.basketJsPage = result.data.page;
                $scope.basketCurrentPage = result.data.list.current_page;
                $scope.basketTotalPage = result.data.list.total;
                $scope.basketPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
            $scope.initCheckboxAll();
        };

        // 初始化
        $scope.init = function () {
            $("#basketballState").val("1").trigger("change");

            $scope.requestApi(null, {
                params: {
                    status: 1, // 出售中
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 初始化调用
        $scope.init();

        // 清空搜索
        $scope.clearSearch = function () {
            $scope.matchName = "";
            $scope.matchNum = "";
            $("#basketballState").val("").trigger("change");
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
                    status: $("#basketballState").val(),
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            let fromInputVal = $("#from").val();
            let toInputVal = $("#to").val();

            $scope.requestApi(url, {
                params: {
                    startDate: fromInputVal ? fromInputVal + ' 00:00:00' : '',
                    endDate: toInputVal ? toInputVal + ' 23:59:59' : '',
                    match_name: $scope.matchName,
                    match_num: $scope.matchNum,
                    status: $("#basketballState").val(),
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 批量开售
        $scope.batchNormal = function (status) {
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
                $http.get('/vp/basketball/toggle?ids=' + ids + '&status=' + status).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess(msg + '成功');
                        // 初始化列表
                        $scope.requestApi($scope.url, $scope.param);
                    }
                })
            });
        };

        // 批量删除
        $scope.batchDelete = function () {
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
                $http.get('/vp/basketball/delete?ids=' + ids).success(function (result) {
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
                $http.get('/vp/basketball/delete?ids=' + id).success(function (result) {
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
            $http.get('/vp/basketball/detail?matchId=' + matchId).success(function (result) {
                if (result.code === 1) {
                    $scope.detailMatchName = leagueName; // 联赛名称
                    $scope.detailHost = hostName; // 主队
                    $scope.detailGuest = guestName; // 客队
                    $scope.spSf = result.data.sp_sf; // 胜负奖金指数
                    $scope.spSfVarIndex = result.data.sp_sf_var; // 胜负奖金指数变化
                    $scope.spRfsf = result.data.sp_rfsf; // 让分胜负奖金指数
                    $scope.spRfsfVarIndex = result.data.sp_rfsf_var; // 让分胜负奖金指数变化
                    $scope.spSfc = result.data.sp_sfc; // 胜分差奖金指数
                    $scope.spSfcVarIndex = result.data.sp_sfc_var; // 胜分差奖金指数变化
                    $scope.spDxf = result.data.sp_dxf; // 大小分奖金指数
                    $scope.spDxfVarIndex = result.data.sp_dxf_var; // 大小分奖金指数变化
                }
            });
        };

        // 清空手动截止时间
        $scope.clearHandTime = function(){
            $("#basketballManual").val("").trigger("change"); // 手动截止时间
        };

        // 赛事编辑
        $scope.matchEdit = function (item) {
            $scope.editMatchNum = item.match_num; // 赛事编号
            $scope.editMatchName = item.league_name; // 联赛名称
            $scope.editHostName = item.host_name; // 主队名称
            $scope.editGuestName = item.guest_name; // 客队名称
            $scope.editRqs = item.rqs; // 让球数
            $scope.editSysTime = item.sys_cutoff_time; // 系统截止时间
            $("#basketballManual").val(item.cutoff_time || "").trigger("change"); // 手动截止时间

            $http.get('/vp/basketball/reDetail?matchNum=' + item.match_num).success(function (result) {
                let data = result.data || {};
                let hostScore = data.host_score || '';
                let guestScore = data.guest_score || '';
                $scope.hostScore = hostScore;
                $scope.guestScore = guestScore;
            });
        };

        // 保存编辑后的比赛数据
        $scope.saveEdit = function () {
            let handTime = $("#basketballManual").val();
            let formData = {};
            // 手动截止时间
            formData.cutoff_time = handTime || '';

            if ($scope.editRqs && /^\-?\d+\.?\d*$/.test($scope.editRqs)) {
                formData.rqs = $scope.editRqs;
            }

            if ($scope.hostScore) {
                formData.hostScore = $scope.hostScore;
            }

            if ($scope.guestScore) {
                formData.guestScore = $scope.guestScore;
            }

            formData.match_num = $scope.editMatchNum;
            $http.post('/vp/basketball/save', $.param(formData)).success(function (result) {
                if (result.code === 1) {
                    $tip.success('保存成功');
                    $("#basketballEdit").modal("hide");
                    $scope.requestApi($scope.url, $scope.param);
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
                status: $("#basketballState").val()
            });

            // 导出
            window.location.href = '/vp/basketball/export?' + query;
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
        // 初始化select2
        $("#basketballState").select2({
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

        // 初始化赛事编辑日历插件
        let date = new Date();
        date.setDate(date.getDate() + 7); // 最大日期是未来7天后
        $.datePicker({
            from: "#basketballManual",
            timePicker: true,
            timePicker24Hour: true,
            // timePickerSeconds: true,
            format: "YYYY-MM-DD HH:mm:ss",
            maxDate: moment(date)
        });
    });
}(jQuery);

