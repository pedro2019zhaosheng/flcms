// angular数据交互 --会员列表
~function ($, angular, window) {
    "use strict";
    angular.module("myApp").controller("memberCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        // 初始化checkbox
        $scope.initCheckboxAll = function () {
            let $checkAll = $('table thead tr').find('input');
            $checkAll.prop('checked', false);
        };

        $.busyLoadFull('show', {animation: "fade"});
        $scope.requestApi = function (url, param) {
            url = url || "/vp/member/index";
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

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });

            $scope.initCheckboxAll();
        };
        // 初始化
        $scope.init = function () {
            $("#from").val("");
            $("#to").val("");
            $scope.username = "";
            $scope.keyword = "";

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
            if (startDate !== '' && endDate !== '') {
                startDate = startDate + ' 00:00:00';
                endDate = endDate + ' 23:59:59';
            }
            $scope.requestApi(null, {
                params: {
                    keyword: $scope.keyword, // 关键词
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
            if (startDate !== '' && endDate !== '') {
                startDate = startDate + ' 00:00:00';
                endDate = endDate + ' 23:59:59';
            }
            $scope.requestApi(url, {
                params: {
                    keyword: $scope.keyword, // 关键词
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

            window.location.href = '/vp/member/export?' + query;
        };

        // 查看会员详情
        $scope.getUserDetail = function(uid){
            $.busyLoadFull('show', {animation: "fade"});
            $http.get('/vp/member/detail?uid=' + uid).success(function (result) {
                $scope.memberDetail = result.data;
                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 批量更新二维码
        $scope.upAllQRcode = function () {
            if (window.resetQr) {
                $("#qrSubmit").removeClass('btn-primary').addClass('btn-secondary').addClass('cna').text("更新中...");
                window.resetQr = false;
                $("#qr-tip").css('display', 'none');
                $("#qr-toolbar").css('display', 'block').attr('src', '/vp/member/upAllQRcode');
                window.qrTimer = setInterval(function () {
                    document.getElementById('qr-toolbar').contentWindow.scrollTo(0, 999999999);
                }, 100);
            }
        };

        // 关闭更新qr modal
        $("#update-qr").on('hidden.bs.modal', function () {
            window.resetQr = true;
            // 再次检查心跳器
            if (window.qrTimer) {
                clearInterval(window.resetQr);
            }
            // 重置modal样式
            $("#qrSubmit").removeClass('btn-secondary').removeClass('cna').addClass('btn-primary').text("确认并立即更新");
            $("#qr-tip").css('display', 'block');
            $("#qr-toolbar").css('display', 'none').removeAttr('src');
        });

        // 关闭添加会员模态框事件
        $('#member-add').on('hidden.bs.modal', function () {
            $("#addUsername").val("").trigger("change");
            $("#addNickName").val("").trigger("change");
            $("#addPassword").val("").trigger("change");
            $("#draw").val('').trigger("change");
            $("#status").val('').trigger("change");
            $("#devStatus").val('').trigger("change");
            $("#addPhoto").val("").trigger("change"); // 初始化头像
            $("#base64").val("").trigger("change"); // 清空base64容器
        });

        //关闭转移会员模态框事件
        $('#transfer-member').on('hidden.bs.modal', function () {
            $scope.angentUserName = '';
            $scope.angentPassword = '';
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

        //关闭升级代理模态框事件
        $('#up-agent').on('hidden.bs.modal', function () {
            $(this).removeData("bs.modal");
            $scope.operpassword4 = '';
        });

        //关闭修改模态框事件
        $('#updata-member').on('hidden.bs.modal', function () {
            $scope.upUsername = '';
            $scope.upNickName = '';
            $scope.memberId = '';
            $("#updraw").val();
            $("#upstatus").val();
            $("#updevStatus").val();
        });

        //关闭修改密码模态框事件
        $('#setPassword-member').on('hidden.bs.modal', function () {
            $scope.angentPassword = ''; // 操作密码
            $scope.rePassword = ""; // 重复密码
            $scope.password = ''; // 新密码
            $scope.memberId = ''; // 会员ID
        });

        //添加会员
        $scope.addMember = function (url) {
            let addUsername = $("#addUsername").val();
            if (!addUsername) {
                $tip.warning("请填写手机号！");
                return;
            }

            if (!(/^1[34578]\d{9}$/.test(addUsername))) {
                $tip.warning("手机号格式错误！");
                return;
            }

            let addNickName = $("#addNickName").val();
            if (!addNickName) {
                $tip.warning("请填写昵称！");
                return;
            }

            let addPassword = $("#addPassword").val();
            if (!addPassword) {
                $tip.warning("请填写密码！");
                return;
            }

            if (addPassword.length < 6) {
                $tip.warning("请输入长度大于6位的密码！");
                return;
            }

            let draw = $("#draw").val();
            if (!draw) {
                $tip.warning("请选择是否开启提现！");
                return;
            }

            let status = $("#status").val();
            if (!status) {
                $tip.warning("请选择会员状态！");
                return;
            }

            let devStatus = $("#devStatus").val();
            if (!devStatus) {
                $tip.warning("请选择发展下级！");
                return;
            }

            url = url || "/vp/member/add";
            $http.post(url,
                $.param({
                    userName: addUsername,
                    nickName: addNickName,
                    passWord: addPassword,
                    draw: draw,
                    status: status,
                    devStatus: devStatus,
                    file: $("#base64").val()
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

        //转移会员
        $scope.transferMember = function (url, memberId) {
            if (!$scope.angentUserName) {
                $tip.warning("代理商账号不能为空");
                return;
            }

            if (!$scope.angentPassword) {
                $tip.warning("请输入操作密码");
                return;
            }

            url = url || '/vp/member/transferMember';
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

        //修改彩金
        $scope.reviseGold = function (url, memberId) {
            if (!$scope.modifyGold) {
                $tip.warning("请填写修改金额");
                return;
            }

            if (!$scope.remarks) {
                $tip.warning("请填写修改备注");
                return;
            }

            if (!$scope.operationPassword2) {
                $tip.warning("请输入操作密码");
                return;
            }

            url = url || '/vp/member/reviseGold';
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
                $tip.warning("请填写修改金额");
                return;
            }

            if (!$scope.remarks2) {
                $tip.warning("请填写修改备注");
                return;
            }

            if (!$scope.operationPassword3) {
                $tip.warning("请输入操作密码");
                return;
            }

            url = url || '/vp/member/reviseBalance';
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
                $http.get("/vp/member/deletMember?id=" + memberId).success(function (result) {
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
            $http.get("/vp/member/getUPAgent").success(function (result) {
                $scope.lottery = result.data;
                $scope.memberId = memberId;
            });
        };

        //升级代理
        $scope.upAgent = function (lottery, memberId) {
            let lotteryData = [];
            let ids = '';
            $.each(lottery, function (index, value) {
                ids = value.id;
                lotteryData.push({key: ids, value: $('#' + ids).val()});
            });
            $http.post('/vp/member/upAgent',
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
            $http.get("/vp/member/toggle?id=" + adminId + "&frozen=" + frozen).success(function (result) {
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
                $http.post("/vp/member/toggles", $.param({
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
                    $("#updraw").val(data.is_return_money).select2();
                    $("#upstatus").val(data.frozen).select2();
                    $("#updevStatus").val(data.dev_status).select2();
                    $("#updata-member").modal('show');
                }
            });
        };

        // 添加会员的银行卡信息
        $scope.addMembercard = function (id) {
            $http.get("/vp/member/addBankCard?memberid=" + id).success(function (result) {
                if (result.code === 1) {
                    $scope.memberid = id;
                    $scope.bankList = result.data.banklist;
                    $("#card-add").modal('show');
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
            $http.post("/vp/member/saveBankCard", $.param({
                    member_id: $scope.memberid,
                    bank: bankArr[1],
                    bank_code: bankArr[0],
                    bank_num: addCardNum,
                    cardholder: addCardname,
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $('#card-add').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            });

        };

        //获取修改时会员的银行卡信息
        $scope.getMembercard = function (id) {
            $http.get("/vp/member/getBankCard?memberid=" + id).success(function (result) {
                if (result.code === 1) {
                    $scope.bankData = result.data.bankData;
                    $scope.bankList = result.data.banklist;
                    $scope.memberid = id;
                    $("#card-member").modal('show');
                }

            });
        };
        // 修改银行卡
        $scope.setCard = function (id, data) {
            var rdata = '';
            $.each(data, function (k, v) {
                rdata += '|';
                rdata += 'id:' + v['id'] + ',';
                rdata += 'cardholder:' + $("#addNickName" + v['id']).val() + ',';
                var code = $("#bank_code" + v['id']).val();
                if (code != '') {
                    rdata += 'bank_code:' + $("#bank_code" + v['id']).val() + ',';
                } else {
                    rdata += 'bank_code:' + v['bank_code'] + ',';
                }

                rdata += 'bank_num:' + $("#bank_num" + v['id']).val() + ',';
            });

            $http.post('/vp/member/upBankCard', $.param({
                rdata: rdata,
            })).success(function (result) {
                if (result.code === 1) {
                    $('#card-member').modal('hide');
                    $tip.success("修改成功");
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        //修改会员
        $scope.updataMember = function (id) {
            $http.post('/vp/member/updataMember', $.param({
                memberId: $scope.memberId,
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

        //修改密码赋值
        $scope.setPasswordMethod = function (memberId) {
            $scope.memberId = memberId;
        };

        //修改密码
        $scope.setPassword = function (id) {
            if (!$scope.password) {
                $tip.warning("请填写新密码");
                return;
            }

            if (!$scope.rePassword) {
                $tip.warning("请重复密码");
                return;
            }

            if ($scope.password !== $scope.rePassword) {
                $tip.warning("输入的两次密码不一致");
                return;
            }

            if (!$scope.angentPassword) {
                $tip.warning("请填写操作密码");
                return;
            }
            $http.post('/vp/member/setPassword', $.param({
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

        //总充值
        $scope.rechargeList = function (username, role) {
            window.location.href = '/vp/capital/recharge?paramUserName=' + username + '&paramRole=' + role;
        };

        //总输赢
        $scope.winloseList = function (username, role) {
            window.location.href = '/vp/capital/rake_back?paramUserName=' + username + '&paramRole=' + role;
        };

        //总提现
        $scope.withdrawList = function (username, role) {
            window.location.href = '/vp/capital/cash?paramUserName=' + username + '&paramRole=' + role;
        };

        //推荐的下级会员
        $scope.recommend = function (id) {
            window.location.href = '/vp/recoMember?id=' + id;
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
}(jQuery, angular, window);

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
        $("#updevStatus").select2();
        $("#upstatus").select2();
        $("#updraw").select2();
        // 文件上传
        $('#addPhoto').filer({
            limit: 1, // 上传数量1
            maxSize: 1, // 最大尺寸1M
            extensions: ['jpg', 'jpeg', 'png', 'gif', 'psd'], // 支持的文件类型
            changeInput: true, // 创建一个新的input元素
            showThumbs: true, // 显示文件预览
            addMore: false, // 不新增更多
            clipBoardPaste: true, // 允许复制粘贴文件
            captions: { // 编辑器
                button: "选择头像",
                feedback: "请上传头像",
                feedback2: "图片已选择",
                drop: "删除上传文件",
                removeConfirmation: "你确定要删除该图片么？",
                errors: {
                    filesLimit: "只 {{fi-limit}} 图片",
                    filesType: "只允许上传图片",
                    filesSize: "{{fi-name}} 太大了! 请保证图片尺寸不大于 {{fi-maxSize}} MB.",
                    filesSizeAll: "图片太大了，请保证图片尺寸不大于 {{fi-maxSize}} MB."
                }
            },
            onRemove: function () { // 移除图片时触发
                $("#base64").val("");
            },
            onSelect: function () { // 选择图片之后触发
                let photoNode = $("#addPhoto");
                if (photoNode.val()) {
                    let fileReader = new FileReader();
                    fileReader.readAsDataURL(photoNode[0].files[0]);
                    fileReader.onload = function (e) {
                        $("#base64").val(e.target.result);
                    };
                }
            }
        });

        // iframe reset
        window.resetQr = true;
        document.getElementById('qr-toolbar').onload = function () {
            // 该操作放入主线程的下次tick中
            var timer = setTimeout(function () {
                clearInterval(window.qrTimer);
                clearTimeout(timer);
                window.resetQr = true;
                $("#qrSubmit").removeClass('btn-secondary').removeClass('cna').addClass('btn-primary').text("重新更新");
            }, 300);
        };
    });
}(jQuery);
