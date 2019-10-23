// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("agentCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});
        // 初始化checkbox
        $scope.initCheckboxAll = function () {
            let $checkAll = $('table thead tr').find('input');
            $checkAll.prop('checked', false);
        };
        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/Agent/index";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.agentList = result.data.list.data || [];
                $scope.agentList.length ? $scope.agentNoData = false : $scope.agentNoData = true;
                $scope.agentPage = result.data.page;
                $scope.agentCurrentPage = result.data.list.current_page;
                $scope.agentTotalPage = result.data.list.total;
                $scope.agentPerPage = result.data.list.per_page;
                $.busyLoadFull('hide', {animation: "fade"});//关闭页面遮罩
            });

            $scope.initCheckboxAll();
        };

        $scope.test = function (url, param) {
            url = url || "/vp/Agent/addAgent";
            $http.get(url).success(function (result) {

            });
        };

        // 初始化
        $scope.init = function () {
            $("#phone").val("");
            $("#nickname").val("");
            $("#userstate").val("").trigger('change');

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
            let username = $("#phone").val();
            let chn_name = $("#nickname").val();
            let state = $("#userstate").val();
            $scope.requestApi(null, {
                params: {
                    username: username ? username : '',
                    chn_name: chn_name ? chn_name : '',
                    state: state ? state : '',
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 导出Excel
        $scope.export = function () {
            let username = $("#phone").val();
            let chn_name = $("#nickname").val();
            let state = $("#userstate").val();

            let query = $.param({
                username: username ? username : '',
                chn_name: chn_name ? chn_name : '',
                state: state ? state : ''
            });

            // 导出
            window.location.href = '/vp/agent/export?' + query;
        };

        // 分页事件
        $scope.getTpPage = function (url) {
            let username = $("#phone").val();
            let chn_name = $("#nickname").val();
            let state = $("#userstate").val();
            $scope.requestApi(url, {
                params: {
                    username: username ? username : '',
                    chn_name: chn_name ? chn_name : '',
                    state: state ? state : '',
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 查看更多代理商信息
        $scope.getAgentMore = function(uid){
            $.busyLoadFull('show', {animation: "fade"});
            $http.get('/vp/member/detail?type=2&uid=' + uid).success(function (result) {
                $scope.agentDetail = result.data;
                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        //关闭新增会员模态框事件
        $('#agent-add').on('hidden.bs.modal', function () {
            $("#addPhone").val("");
            $("#addNickName").val("");
            $("#addPassword").val("");
            $("#addCashWithdrawal").val("").trigger("change");
            $("#addLower").val("").trigger("change");
            $("#addStatus").val("").trigger("change");
        });

        //关闭转移会员模态框事件
        $('#transfer-member').on('hidden.bs.modal', function () {
            $scope.angentUserName = '';
            $scope.angentPassword = '';
            $("#isSelf").val("").trigger("change");
        });

        //关闭修改彩金模态框事件
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

        //修改数据赋值
        $scope.updataAgentMethod = function (AgentId) {
            $scope.AgentId = AgentId;
        };

        //关闭修改密码模态框事件
        $('#setPassword-member').on('hidden.bs.modal', function () {
            $scope.angentPassword = '';
            $scope.password = '';
            $scope.memberId = '';
        });

        //添加代理商
        $scope.addAgent = function (lottery) {
            let addPhone = $("#addPhone").val();
            if (!addPhone) {
                $tip.warning("请填写手机号");
                return;
            }

            if (!/^1[3-9][0-9]\d{8}$/u.test(addPhone)) {
                $tip.warning("手机号格式错误");
                return;
            }

            let addNickName = $("#addNickName").val();
            if (!addNickName) {
                $tip.warning("请填写昵称");
                return;
            }

            let addPassword = $("#addPassword").val();
            if (!addPassword) {
                $tip.warning("请填写密码");
                return;
            }

            let passwordLength = addPassword.length;
            if (passwordLength < 6 || passwordLength > 18) {
                $tip.warning("密码长度不合法");
                return;
            }

            let withdraw = $("#addCashWithdrawal").val();
            if (!withdraw) {
                $tip.warning("请选择提现权限");
                return;
            }

            let devLev = $("#addLower").val();
            if (!devLev) {
                $tip.warning("请选择是否发展下线");
                return;
            }

            let status = $("#addStatus").val();
            if (!status) {
                $tip.warning("请选择状态");
                return;
            }

            let lotteryData = [];
            let ids = '';

            $.each(lottery, function (index, value) {
                ids = value.id;
                lotteryData.push({key: ids, value: $('#' + ids).val(), status: $("#lotteryStatue" + ids).val()});
            });

            $http.post('/vp/Agent/addAgent',
                $.param({
                    Lottery: lotteryData,
                    username: addPhone,
                    nickname: addNickName,
                    password: addPassword,
                    witdraw: withdraw,
                    lower: devLev,
                    status: status,
                })).success(function (result) {
                if (result.code === 1) {
                    $('#agent-add').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            })
        };

        //总充值
        $scope.rechargeList = function (username, role) {
            window.location.href = '/vp/capital/recharge?paramUserName=' + username + '&paramRole=' + role;
        };

        //总输赢
        $scope.winloseList = function (username, role) {
            window.location.href = '/vp/capital/rake_back?paramUserName=' + username + '&paramRole=' + role;
        };

        //推荐的下级会员
        $scope.recommend = function (id) {
            window.location.href = '/vp/recoMember?id=' + id;
        };

        //转移会员
        $scope.transferAgent = function (url, AgentId) {
            let isSelf = $("#isSelf").val();
            if (!isSelf) {
                $tip.warning("请选择是否转移自身!");
                return;
            }

            if (!$scope.angentUserName) {
                $tip.warning("请填写代理商账号!");
                return;
            }

            if (!$scope.angentPassword) {
                $tip.warning("请填写操作密码!");
                return;
            }

            url = url || '/vp/agent/transferAgent';
            $http.post(url,
                $.param({
                    isSelf: isSelf,
                    userName: $scope.angentUserName,
                    passWord: $scope.angentPassword,
                    id: AgentId,
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $('#transfer-member').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            })
        };

        //修改彩金
        $scope.reviseGold = function (url, AgentId) {
            url = url || '/vp/agent/reviseGold';
            $http.post(url,
                $.param({
                    id: AgentId,
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
        $scope.reviseBalance = function (url, AgentId) {
            url = url || '/vp/agent/reviseBalance';
            $http.post(url,
                $.param({
                    id: AgentId,
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

        //获取修改时代理商的信息
        $scope.getAgentInfo = function (id) {
            $http.get("/vp/agent/getAgentInfo?agentid=" + id).success(function (result) {
                if (result.code === 1) {
                    let data = result.data;
                    $scope.upUsername = data.username;
                    $scope.upNickName = data.chn_name;
                    $scope.upmemberId = data.id;
                    $("#updraw").val(data.is_return_money).select2();
                    $("#upstatus").val(data.frozen).select2();
                    $("#updevStatus").val(data.dev_status).select2();
                    $("#updata-member").modal('show');
                }
            });
        };

        //修改代理商
        $scope.updataAgent = function (id) {
            $http.post('/vp/agent/updataAgent', $.param({
                memberId: $scope.upmemberId,
                nickName: $scope.upNickName,
                draw: $("#updraw").val(),
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

        // 添加代理的银行卡信息
        $scope.addAgentcard = function (id) {
            $http.get("/vp/agent/addBankCard?agentid=" + id).success(function (result) {
                if (result.code === 1) {
                    $scope.agentid = id;
                    $scope.bankList = result.data.banklist;
                    $("#agentCard-add").modal('show');
                }

            });

        };

        // 保存会员的银行卡信息
        $scope.saveBankCard = function (id) {
            $scope.memberid = id;
            var addCardname = $("#addCardname").val();
            if (!addCardname) {
                $tip.warning("请填写真实姓名！");
                return;
            }

            let bank_code = $("#bank_code").val();
            var bankArr = bank_code.split('|');
            if (!bank_code) {
                $tip.warning("请选择银行！");
                return;
            }

            let addCardNum = $("#addCardNum").val();
            if (!addCardNum) {
                $tip.warning("请填写银行卡号！");
                return;
            }

            let addPhoneNum = $("#addPhoneNum").val();
            if (!addPhoneNum) {
                $tip.warning("请填写手机号！");
                return;
            }

            if (!(/^1[34578]\d{9}$/.test(addPhoneNum))) {
                $tip.warning("手机号格式错误！");
                return;
            }
            $http.post("/vp/agent/saveBankCard", $.param({
                    member_id: $scope.memberid,
                    bank: bankArr[1],
                    bank_code: bankArr[0],
                    bank_num: addCardNum,
                    cardholder: addCardname,
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $('#agentCard-add').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            });

        };

        //获取修改时代理的银行卡信息
        $scope.getAgentcard = function (id) {
            $http.get("/vp/agent/getBankCard?agentid=" + id).success(function (result) {
                if (result.code === 1) {
                    $scope.bankData = result.data.bankData;
                    $scope.bankList = result.data.banklist;
                    $scope.agentid = id;
                    $("#card-agent").modal('show');
                }

            });
        };
        //修改银行卡
        $scope.setCard = function (id, data) {
            var rdata = '';
            $.each(data, function (k, v) {
                rdata = rdata + '|';
                rdata = rdata + 'id:' + v['id'] + ',';
                rdata = rdata + 'cardholder:' + $("#addNickName" + v['id']).val() + ',';
                var code = $("#bank_code" + v['id']).val();
                if (code != '') {
                    rdata = rdata + 'bank_code:' + $("#bank_code" + v['id']).val() + ',';
                } else {
                    rdata = rdata + 'bank_code:' + v['bank_code'] + ',';
                }
                rdata = rdata + 'bank_num:' + $("#bank_num" + v['id']).val() + ',';
            });
            $http.post('/vp/agent/upBankCard', $.param({
                rdata: rdata,
            })).success(function (result) {
                if (result.code === 1) {
                    $('#card-agent').modal('hide');
                    $tip.success("修改成功");
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 删除代理商
        $scope.deleteAgent = function (agentId) {
            $.swal("确定要删除么", function () {
                $http.get("/vp/agent/deleteAgent?id=" + agentId).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess();
                        // 刷新接口
                        $scope.requestApi($scope.url, $scope.param);
                    }
                });
            });
        };

        // 冻结和解冻
        $scope.toggle = function (agentId, frozen) {
            $http.get("/vp/agent/toggle?id=" + agentId + "&frozen=" + frozen).success(function (result) {
                if (result.code === 1) {
                    if (frozen === 0) {
                        $tip.success("禁用成功");
                    } else {
                        $tip.success("启用成功");
                    }
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        //获取代理商下面的彩种信息
        $scope.getLotteryId = function () {
            $http.get("/vp/agent/getLotteryId").success(function (result) {
                $scope.lottery = result.data;
                $scope.$on('ngRepeatFinished', function (ngRepeatFinishedEvent) {
                    // $scope.initTableCheckbox();
                    for (var i = 0; i < $scope.lottery.length; i++) {
                        $("#lotteryStatue" + $scope.lottery[i].id).select2();
                    }
                });
            });
        };

        //修改密码赋值
        $scope.setPasswordMethod = function (memberId) {
            $scope.memberId = memberId;
        };

        //修改密码
        $scope.setPassword = function (id) {
            $http.post('/vp/agent/setPassword', $.param({
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

        //批量禁用启用
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
                $http.post("/vp/Agent/toggles", $.param({
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
        $scope.$on('ngRepeatFinished', function (ngRepeatFinishedEvent) {
            $scope.initTableCheckbox();
        });

    }]);
}(jQuery, angular);

// jQuery部分
!function ($) {
    "use strict";
    $(function () {
        // 初始化select2插件
        $("#userstate").select2({width: "150px"});
        $("#addCashWithdrawal").select2();
        $("#addStatus").select2();
        $("#addLower").select2();
        $("#isSelf").select2();
    });
}(jQuery);
