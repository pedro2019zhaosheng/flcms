// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("memberCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});
        //请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/simulation/index";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.memberList = result.data.list.data || [];
                $scope.memberList.length ? $scope.simulNoData = false : $scope.simulNoData = true;
                $scope.memberPage = result.data.page;
                $scope.memberCurrentPage = result.data.list.current_page;
                $scope.memberTotalPage = result.data.list.total;
                $scope.memberPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };
        // 初始化
        ($scope.init = function () {
            $("#from").val("");
            $("#to").val("");
            $scope.username = "";
            $scope.nickName = "";

            $scope.requestApi(null, {
                params: {
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
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
            var startDate = $("#from").val();
            var endDate = $("#to").val();
            if (startDate !== '' && endDate !== '') {
                startDate = startDate + ' 00:00:00';
                endDate = endDate + ' 23:59:59';
            }
            $scope.requestApi(null, {
                params: {
                    startDate: startDate, // 开始时间
                    endDate: endDate, // 结束时间
                    username: $scope.username, // 账号
                    keyword: $scope.nickName, // 昵称
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            let startDate = $("#from").val();
            let endDate = $("#to").val();
            if (startDate !== '' && endDate !== '') {
                startDate = startDate + ' 00:00:00';
                endDate = endDate + ' 23:59:59';
            }
            $scope.requestApi(url, {
                params: {
                    startDate: startDate, // 开始时间
                    endDate: endDate, // 结束时间
                    username: $scope.username, // 账号
                    keyword: $scope.nickName, // 昵称
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 关闭添加会员模态框事件
        $('#member-add').on('hidden.bs.modal', function () {
            $("#addUsername").val("").trigger("change");
            $("#addNickName").val("").trigger("change");
            $("#addPassword").val("").trigger("change");
            $("#status").val("").trigger("change");
        });

        // 关闭修改彩金模态框事件
        $('#revise-gold').on('hidden.bs.modal', function () {
            $scope.modifyGold = '';
            $scope.operationPassword2 = '';
            $scope.remarks = '';
        });

        //关闭修改余额模态框事件
        $('#revise-balance').on('hidden.bs.modal', function () {
            $scope.modifyBalance = '';
            $scope.operationPassword3 = '';
            $scope.remarks2 = '';
        });

        //关闭修改密码模态框事件
        $('#setPassword-member').on('hidden.bs.modal', function () {
            $scope.angentPassword = '';
            $scope.password = '';
            $scope.rePassword = '';
            $scope.memberId = '';
        });

        //添加会员
        $scope.addMember = function (url) {
            let addUsername = $("#addUsername").val();
            if (!addUsername) {
                $tip.warning("请填写账号");
                return;
            }

            if (/[^\d]/g.test(addUsername)) {
                $tip.warning("账号必须是数字");
                return;
            }

            let addNickName = $("#addNickName").val();
            if (!addNickName) {
                $tip.warning("请填写昵称");
                return;
            }

            let addPassword = $("#addPassword").val();
            if (!addPassword) {
                $tip.warning("请填写登录密码");
                return;
            }

            url = url || "/vp/simulation/add";
            $http.post(url,
                $.param({
                    userName: addUsername,
                    nickName: addNickName,
                    passWord: addPassword,
                    status: $("#status").val(),
                })
            ).success(function (result) {
                if (result.code === 1) {
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

        //修改彩金
        $scope.reviseGold = function (url, memberId) {
            if (!$scope.modifyGold) {
                $tip.warning("修改金额不能为空");
                return;
            }

            if (!$scope.remarks) {
                $tip.warning("备注不能为空");
                return;
            }

            if (!$scope.operationPassword2) {
                $tip.warning("操作密码不能为空");
                return;
            }

            url = url || '/vp/simulation/reviseGold';
            $http.post(url,
                $.param({
                    id: memberId,
                    gold: $scope.modifyGold,
                    passWord: $scope.operationPassword2,
                    remarks: $scope.remarks,
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $('#revise-gold').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            })
        };

        //修改余额
        $scope.reviseBalance = function (url, memberId) {
            if (!$scope.modifyBalance) {
                $tip.warning("修改金额不能为空");
                return;
            }

            if (!$scope.remarks2) {
                $tip.warning("备注不能为空");
                return;
            }

            if (!$scope.operationPassword3) {
                $tip.warning("操作密码不能为空");
                return;
            }

            url = url || '/vp/simulation/reviseBalance';
            $http.post(url,
                $.param({
                    id: memberId,
                    balance: $scope.modifyBalance,
                    passWord: $scope.operationPassword3,
                    remarks: $scope.remarks2,
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $('#revise-balance').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            })
        };

        // 删除会员
        $scope.deleteMember = function (memberId) {
            $.swal("确定要删除么", function () {
                $http.get("/vp/simulation/deletMember?id=" + memberId).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess();
                    }
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                });
            });
        };

        // 批量操作
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
                $http.post("/vp/simulation/toggles", $.param({
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

        //获取修改时会员的信息
        $scope.getMemberinfo = function (id) {
            $http.get("/vp/member/getMemberinfo?memberid=" + id).success(function (result) {
                if (result.code === 1) {
                    let data = result.data;
                    $scope.upUsername = data.username;
                    $scope.upNickName = data.chn_name;
                    $scope.memberId = data.id;
                    // $("#updraw").val(data.is_return_money).select2();  // 模拟账号不能提现
                    $("#upstatus").val(data.frozen).select2();
                    $("#updevStatus").val(data.dev_status).select2();
                    $("#updata-member").modal('show');
                }
            });
        };

        //修改会员
        $scope.updataMember = function (id) {
            $http.post('/vp/simulation/updataMember', $.param({
                memberId: $scope.memberId,
                nickName: $scope.upNickName,
                draw: "0", // 不能提现
                frozen: $("#upstatus").val(),
                status: $("#updevStatus").val(),
            })).success(function (result) {
                if (result.code === 1) {
                    $('#updata-member').modal('hide');
                    $tip.success("修改成功");
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        //修改密码赋值
        $scope.setPasswordMethod = function (memberId) {
            $scope.memberId = memberId;
        };

        //修改密码
        $scope.setPassword = function (id) {
            if (!$scope.password) {
                $tip.warning("新密码不能为空");
                return;
            }

            if (!$scope.rePassword) {
                $tip.warning("重复密码不能为空");
                return;
            }

            if ($scope.password !== $scope.rePassword) {
                $tip.warning("两次密码不一致");
                return;
            }

            if (!$scope.angentPassword) {
                $tip.warning("操作密码不能为空");
                return;
            }

            $http.post('/vp/simulation/setPassword', $.param({
                memberId: id,
                passWord: $scope.password,
                angentPassword: $scope.angentPassword,
            })).success(function (result) {
                if (result.code === 1) {
                    $('#setPassword-member').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 冻结和解冻
        $scope.toggle = function (adminId, frozen) {
            $http.get("/vp/simulation/toggle?id=" + adminId + "&frozen=" + frozen).success(function (result) {
                if (result.code === 1) {
                    if (frozen === 0) {
                        $tip.success("冻结成功");
                    } else {
                        $tip.success("解冻成功");
                    }
                    // 刷新接口
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

        //总输赢
        $scope.winloseList = function (username, role) {
            window.location.href = '/vp/capital/rake_back?paramUserName=' + username + '&paramRole=' + role;
        }
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
