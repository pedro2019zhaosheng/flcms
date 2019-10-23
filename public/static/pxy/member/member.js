// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("memberCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        // 初始化checkbox
        $scope.initCheckboxAll = function () {
            let $checkAll = $('table thead tr').find('input');
            $checkAll.prop('checked', false);
        };
        $.busyLoadFull('show', {animation: "fade"});
        $scope.requestApi = function (url, param) {
            url = url || "/pxy/member/index";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.memberList = result.data.list.data || [];
                $scope.memberList.length ? $scope.memberNoData = false : $scope.memberNoData = true;
                $scope.memberPage = result.data.page;
                $scope.memberCurrentPage = result.data.list.current_page;
                $scope.memberTotalPage = result.data.list.total;
                $scope.memberPerPage = result.data.list.per_page;
                $.busyLoadFull('hide', {animation: "fade"});//关闭页面遮罩
            });

            $scope.initCheckboxAll();
        };
        // 初始化
        $scope.init = function () {
            $("#from").val("");
            $("#to").val("");
            $scope.username = "";

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
            var startDate = $("#from").val();
            var endDate = $("#to").val();
            if (startDate != '' && endDate != '') {
                startDate = startDate + ' 00:00:00';
                endDate = endDate + ' 23:59:59';
            }
            $scope.requestApi(null, {
                params: {
                    startDate: startDate,
                    endDate: endDate,
                    username: $scope.username,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            var startDate = $("#from").val();
            var endDate = $("#to").val();
            if (startDate != '' && endDate != '') {
                startDate = startDate + ' 00:00:00';
                endDate = endDate + ' 23:59:59';
            }
            $scope.requestApi(url, {
                params: {
                    startDate: startDate,
                    endDate: endDate,
                    username: $scope.username,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 导出Excel
        $scope.exportMember = function () {
            let startDate = $("#from").val();
            let endDate = $("#to").val();
            if (startDate !== '' && endDate !== '') {
                startDate = startDate + ' 00:00:00';
                endDate = endDate + ' 23:59:59';
            }

            let query = $.param({
                keyword: $scope.keyword, // 关键词
                startDate: startDate,
                endDate: endDate,
                username: $scope.username
            });

            window.location.href = '/pxy/member/export?' + query;
        };

        //总充值
        $scope.rechargeList = function (username, role) {
            window.location.href = '/pxy/capital/recharge?paramUserName=' + username + '&paramRole=' + role;
        };

        //总输赢
        $scope.winloseList = function (username, role) {
            window.location.href = '/pxy/capital/rake_back?paramUserName=' + username + '&paramRole=' + role;
        };

        //总提现
        $scope.withdrawList = function (username, role) {
            window.location.href = '/pxy/capital/cash?paramUserName=' + username + '&paramRole=' + role;
        };

        // 关闭添加会员模态框事件
        $('#member-add').on('hidden.bs.modal', function () {
            $scope.addUsername = '';
            $scope.addNickName = '';
            $scope.addPassword = '';
            $("#draw").val('').select2();
            $("#status").val('').select2();
            $("#devStatus").val('').select2();
        });

        //关闭转移会员模态框事件
        $('#transfer-member').on('hidden.bs.modal', function () {
            $scope.angentUserName = '';
            $scope.angentPassword = '';
        });

        //关闭升级代理模态框事件
        $('#up-agent').on('hidden.bs.modal', function () {
            //document.getElementById("contentForm").reset();
            $(this).removeData("bs.modal");
            $scope.operpassword4 = '';
        });

        //添加会员
        $scope.addMember = function (url) {
            if (!$scope.addUsername) {
                $tip.warning("请填写手机号！");
                return;
            }
            if (!(/^1[34578]\d{9}$/.test($scope.addUsername))) {
                $tip.warning("手机号格式错误！");
                return;
            }
            if (!$scope.addNickName) {
                $tip.warning("请填写昵称！");
                return;
            }
            if (!$scope.addPassword) {
                $tip.warning("请填写密码！");
                return;
            }
            if ($scope.addPassword.length < 6) {
                $tip.warning("请输入长度大于6位的密码！");
                return;
            }
            if (!$("#draw").val()) {
                $tip.warning("请选择是否开启提现！");
                return;
            }
            if (!$("#status").val()) {
                $tip.warning("请选择会员状态！");
                return;
            }
            if (!$("#devStatus").val()) {
                $tip.warning("请选择发展下级！");
                return;
            }
            url = url || "/pxy/member/add";
            $http.post(url,
                $.param({
                    userName: $scope.addUsername,
                    nickName: $scope.addNickName,
                    passWord: $scope.addPassword,
                    draw: $("#draw").val(),
                    status: $("#status").val(),
                    devStatus: $("#devStatus").val(),
                })
            ).success(function (result) {
                if (result.code == 1) {
                    $('#member-add').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        //修改数据的赋值
        $scope.updataMemberMethod = function (memberId) {
            $scope.memberId = memberId;
        };

        //转移会员
        $scope.transferMember = function (url, memberId) {
            url = url || '/pxy/member/transferMember';
            $http.post(url,
                $.param({
                    userName: $scope.angentUserName,
                    passWord: $scope.angentPassword,
                    id: memberId,
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
        $scope.deleteMember = function (memberId) {
            $.swal("确定要删除么", function () {
                $http.get("/pxy/member/deletMember?id=" + memberId).success(function (result) {
                    if (result.code) {
                        $.swalSuccess();
                    }
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                });
            });
        };

        //获取升级代理的彩种信息
        $scope.getUpAgent = function (memberId) {
            $http.get("/pxy/member/getUPAgent").success(function (result) {
                $scope.lottery = result.data;
                $scope.memberId = memberId;
            });
        };

        //升级代理
        $scope.upAgent = function (lottery, memberId) {
            var lotteryData = [];
            var ids = '';
            $.each(lottery, function (index, value) {
                ids = value.id;
                lotteryData.push({key: ids, value: $('#' + ids).val()});
            });
            $http.post('/pxy/member/upAgent',
                $.param({
                    id: memberId,
                    Lottery: lotteryData,
                    lowlevel: $('#lowlevel').val(),
                    withdraw: $('#withdraw').val(),
                    passWord: $scope.operpassword4,
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $('#up-agent').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };
        // 冻结和解冻
        $scope.toggle = function (adminId, frozen) {
            $http.get("/pxy/member/toggle?id=" + adminId + "&frozen=" + frozen).success(function (result) {
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
        // 批量正常和删除
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
                $http.post("/pxy/member/toggles", $.param({
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
        //推荐的下级会员
        $scope.recommend = function (id) {
            window.location.href = '/pxy/recoMember?id=' + id;
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
        // 初始化日历插件
        $.datePicker({
            from: "#from",
            to: "#to"
        });
        $("#from").val("");
        $("#to").val("");
        // 初始化select2插件，该插件在模态框中使用时，请将modal中的 tabindex="-1" 属性删除
        $("#draw").select2();
        $("#status").select2();
        $("#agent").select2();
        $("#devStatus").select2();
        $("#lowlevel").select2();
        $("#withdraw").select2();
    });
}(jQuery);
