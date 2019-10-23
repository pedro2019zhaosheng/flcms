// angular数据交互 --彩种列表
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("lotteryCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        // 初始化checkbox
        $scope.initCheckboxAll = function () {
            let $checkAll = $('table thead tr').find('input');
            $checkAll.prop('checked', false);
        };
        $.busyLoadFull('show', {animation: "fade"});
        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/lottery/index";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.lotList = result.data.list.data || [];
                $scope.lotList.length ? $scope.lotNoData = false : $scope.lotNoData = true;
                $scope.lotJsPage = result.data.page;
                $scope.lotCurrentPage = result.data.list.current_page;
                $scope.lotTotalPage = result.data.list.total;
                $scope.lotPerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });

            $scope.initCheckboxAll();
        };
        // 初始化
        $scope.init = function () {
            $scope.lotteryName = "";
            $("#couponState").val("").trigger("change");

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
            $scope.requestApi('/vp/lottery/index', {
                params: {
                    name: $scope.lotteryName,
                    status: $("#couponState").val(),
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            })
        };

        // 分页事件
        $scope.getLotPage = function (url) {
            $scope.requestApi(url, {
                params: {
                    name: $scope.lotteryName,
                    status: $("#couponState").val(),
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 新增彩种模态框关闭事件
        $('#add-lottery').on('hidden.bs.modal', function () {
            $("#addLotteryName").val(""); // 彩种名称
            $("#lotteryCode").val(""); // 彩种代码
            $("input#lotteryChoice0").prop("checked", true); // 选中正常状态
            $("#base64").val(""); // 初始化图标
            $("#addImgIcon").val("").trigger("change"); // 初始化图标
        });

        //新增彩种
        $scope.addLottery = function (url) {
            let addLotName = $("#addLotteryName");
            if (!addLotName.val()) {
                // 彩种名称字段
                $tip.warning("请填写彩种名称！");
                return;
            }
            //彩种CODE字段
            let lotCode = $("#lotteryCode");
            if (!lotCode.val()) {
                $tip.warning("请填写彩种CODE！");
                return;
            }

            if (!/^[0-9a-zA-Z_\-]+$/ui.test(lotCode.val())) {
                $tip.warning("彩种CODE规则错误！");
                return;
            }

            let imgIcon = $('#base64').val();
            if (!imgIcon) {
                $tip.warning("请上传彩种图标！");
                return;
            }

            //接口url
            url = '/vp/lottery/add';
            $http.post(url,
                $.param({
                    name: addLotName.val(),
                    code: lotCode.val(),
                    status: $('input[name="lotteryChoice"]:checked').val(),
                    file: imgIcon
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $('#add-lottery').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 修改彩种模态框关闭事件
        $('#edit-lottery').on('hidden.bs.modal', function () {
            $("#base64Edit").val(""); // 初始化图标
            $("#updateImgIcon").val("").trigger("change"); // 初始化图标
        });

        // 获取修改详情
        $scope.editLotteryShow = function (id) {
            $scope.lotteryEditId = id;
            $http.get('/vp/lottery/info?id=' + id).success(function (result) {
                if (result.code === 1) {
                    $("#lotteryNameEdit").val(result.data.name);
                    $("#lotteryCodeEdit").val(result.data.code);
                    $scope.isRun = result.data.is_run;
                }
            });
        };

        //编辑保存彩种
        $scope.editLottery = function (id) {
            let lotteryNameEdit = $("#lotteryNameEdit").val();
            if (!lotteryNameEdit) {//彩种名称字段
                $tip.warning("请填写彩种名称！");
                return;
            }

            //彩种CODE字段
            let lotteryCodeEdit = $("#lotteryCodeEdit").val();
            if (!lotteryCodeEdit) {
                $tip.warning("请填写彩种CODE！");
                return;
            }

            if (!/^[0-9a-zA-Z_\-]+$/ui.test(lotteryCodeEdit)) {
                $tip.warning("彩种CODE规则错误！");
                return;
            }

            $http.post('/vp/lottery/edit',
                $.param({
                    id: id,
                    name: lotteryNameEdit,
                    code: lotteryCodeEdit,
                    file: $("#base64Edit").val() || ''
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $('#edit-lottery').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 删除彩种
        $scope.deleteLottery = function (id) {
            $.swal('您确定要删除该彩种么', function () {
                $http.get('/vp/lottery/delete?id=' + id).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess();
                        // 刷新
                        $scope.requestApi($scope.url, $scope.param);
                    }
                });
            }, '删除后将无法恢复，请谨慎操作！');
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

            $.swal('您确定要批量删除彩种么', function () {
                $http.get('/vp/lottery/delete?id=' + ids).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess(result.msg);
                        $scope.requestApi($scope.url, $scope.param);
                    }
                });
            }, '删除后将无法恢复，请谨慎操作！');
        };

        // 批量正常
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

            $.swal('您确定要批量恢复正常么?', function () {
                $http.post("/vp/lottery/toggle", $.param({
                    ids: ids,
                    status: status
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
        $("#couponState").select2({
            width: "100px"
        });

        // 文件上传
        $('#addImgIcon').filer({
            limit: 1, // 上传数量1
            maxSize: 1, // 最大尺寸1M
            extensions: ['jpg', 'jpeg', 'png', 'gif', 'psd'], // 支持的文件类型
            changeInput: true, // 创建一个新的input元素
            showThumbs: true, // 显示文件预览
            addMore: false, // 不新增更多
            clipBoardPaste: true, // 允许复制粘贴文件
            captions: { // 编辑器
                button: "选择图标",
                feedback: "请上传图标",
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
                let photoNode = $("#addImgIcon");
                if (photoNode.val()) {
                    let fileReader = new FileReader();
                    fileReader.readAsDataURL(photoNode[0].files[0]);
                    fileReader.onload = function (e) {
                        $("#base64").val(e.target.result);
                    };
                }
            }
        });

        // 文件上传
        $('#updateImgIcon').filer({
            limit: 1, // 上传数量1
            maxSize: 1, // 最大尺寸1M
            extensions: ['jpg', 'jpeg', 'png', 'gif', 'psd'], // 支持的文件类型
            changeInput: true, // 创建一个新的input元素
            showThumbs: true, // 显示文件预览
            addMore: false, // 不新增更多
            clipBoardPaste: true, // 允许复制粘贴文件
            captions: { // 编辑器
                button: "选择图标",
                feedback: "请上传图标",
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
                $("#base64Edit").val("");
            },
            onSelect: function () { // 选择图片之后触发
                let photoNode = $("#updateImgIcon");
                if (photoNode.val()) {
                    let fileReader = new FileReader();
                    fileReader.readAsDataURL(photoNode[0].files[0]);
                    fileReader.onload = function (e) {
                        $("#base64Edit").val(e.target.result);
                    };
                }
            }
        });
    });
}(jQuery);

