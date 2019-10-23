// angular数据交互
~function ($, angular) {
    "use strict";
    angular.module("myApp").controller("newsCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});
        // 初始化checkbox
        $scope.initCheckboxAll = function () {
            let $checkAll = $('table thead tr').find('input');
            $checkAll.prop('checked', false);
        };
        //初始化富文本
        let E = window.wangEditor;
        let eidtorDom = new E('#addContent');
        eidtorDom.create();
        // 请求接口
        $scope.requestApi = function (url, param) {
            url = url || "/vp/stadium/index";
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                console.log(result);
                $scope.newsList = result.data.list.data;
                $scope.newsList.length ? $scope.newsNoData = false : $scope.newsNoData = true;
                $scope.stadiumPage = result.data.page;
                $scope.stadiumCurrentPage = result.data.list.current_page;
                $scope.stadiumTotalPage = result.data.list.total;
                $scope.stadiumPerPage = result.data.list.per_page;
                $.busyLoadFull('hide', {animation: "fade"});//关闭页面遮罩
            });

            $scope.initCheckboxAll();
        };

        // 获取场馆类型
        $scope.getRoles = function () {
            $http.get("/vp/stadium/getTypeAll").success(function (result) {
                $scope.roles = result.data;
            });
        };

        $scope.getRoles();

        // 初始化列表
        $scope.init = function () {
            $("#from").val("");
            $("#to").val("");
            $scope.newsTitle = "";

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
            let fromInputVal = $("#from").val();
            let toInputVal = $("#to").val();
            $scope.requestApi(null, {
                params: {
                    startDate: fromInputVal ? fromInputVal + ' 00:00:00' : '',
                    endDate: toInputVal ? toInputVal + ' 23:59:59' : '',
                    name: $scope.newsTitle,
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
                    name: $scope.newsTitle,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 删除场馆
        $scope.deleteNews = function (newsId) {
            $.swal("确定要删除么", function () {
                $http.get("/vp/stadium/delete?id=" + newsId).success(function (result) {
                    $.swalSuccess();
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                });
            });
        };

        //设置启用禁用状态
        $scope.setStatus = function (status, url) {
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
            url = url || "/vp/stadium/setStatus";
            let msg;
            if (status === 0) {
                msg = "禁用";
            } else {
                msg = "启用";
            }

            $.swal("确认要" + msg + "么", function () {
                $http.post(url,
                    $.param({
                        id: ids,
                        status: status,
                    })
                ).success(function (result) {
                    if (result.code === 1) {
                        $tip.success(msg + '成功');
                        $scope.requestApi($scope.url, $scope.param);
                    }
                });
            })
        };

        // 初始化新增模态框
        $scope.initAddModal = function () {
            $("#selectRole").val("").trigger("change"); // 初始化角色名
            $("#newsStatus").val("").trigger("change"); // 初始化角色名
            $("#addName").val(""); // 初始化标题
            $("#addAbstract").val(""); // 初始化描述
            // $("#addContent").val(""); // 初始化内容
            $("#base64").val(""); // 初始化头像
            $("#addPhoto").val("").trigger("change"); // 初始化头像
            eidtorDom.txt.html('');
        };

        // 关闭模态框事件
        $('#news-add').on('hidden.bs.modal', function () {
            // 初始化新增模态框
            $scope.initAddModal();
        });

        // 新增新闻
        $scope.addNews = function (url) {
            //判断条件
            let type = $("#selectRole").val();
            if (!type) {
                $tip.warning("场馆类型不能为空！");
                return;
            }

            let status = $("#newsStatus").val();
            if (!status) {
                $tip.warning("场馆状态不能为空！");
                return;
            }

            let addName = $("#addName").val();
            if (!addName) {
                $tip.warning("场馆名称不能为空！");
                return;
            }

            let addLocation = $("#addLocation").val();
            if (!addLocation) {
                $tip.warning("场馆位置不能为空！");
                return;
            }

            let addCost = $("#addCost").val();
            if (!addCost) {
                $tip.warning("场馆费用不能为空！");
                return;
            }

            let addLinkman = $("#addLinkman").val();
            if (!addLinkman) {
                $tip.warning("场馆联系人不能为空！");
                return;
            }

            let addTel = $("#addTel").val();
            if (!addTel) {
                $tip.warning("场馆联系电话不能为空！");
                return;
            }

            let addAbstract = $("#addAbstract").val();
            if (!addAbstract) {
                $tip.warning("场馆描述不能为空！");
                return;
            }

            let addContent = eidtorDom.txt.text();
            if (!addContent) {
                $tip.warning("场馆详情不能为空！");
                return;
            }

            let imgVal = $("#base64").val();
            if (!imgVal) {
                $tip.warning("场馆图不能为空！");
                return;
            }
            url = url || "/vp/stadium/add";
            $http.post(url,
                $.param({
                    type: type,
                    title: addName,
                    location: addLocation,
                    cost: addCost,
                    linkman: addLinkman,
                    tel: addTel,
                    abstract: addAbstract,
                    file: imgVal,
                    content: addContent,
                    status: status,
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $('#news-add').modal('hide');
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 关闭修改模态框事件
        $('#news-update').on('hidden.bs.modal', function () {
            // 初始化修改模态框
            $("#update_base64").val('').trigger('change');
            $("#updatePhoto").val('').trigger('change');
        });

        // 修改获取详情
        $scope.updateToggle = function (newsId) {
            $http.get("/vp/stadium/info?id=" + newsId).success(function (result) {
                if (result.code === 1) {
                    $("#roleUpdate").val(result.data.stadiumType).select2();
                    $("#updateName").val(result.data.name);
                    $("#updateLocation").val(result.data.location);
                    $("#updateCost").val(result.data.cost);
                    $("#updateLinkman").val(result.data.linkman);
                    $("#updateTel").val(result.data.tel);
                    $("#updateAbstract").val(result.data.abstract);
                    $("#updateStatus").val(result.data.status).select2();
                    $scope.updateNewsId = newsId;
                }
            });
        };

        // 编辑保存
        $scope.updateNews = function (id) {
            let updateRoleNode = $("#roleUpdate");
            if (!updateRoleNode.val()) {
                $tip.warning("请选择类型");
                return;
            }

            let updateName = $("#updateName");
            if (!updateName.val()) {
                $tip.warning("请填写场馆名称");
                return;
            }

            let updateLocation = $("#updateLocation");
            if (!updateLocation.val()) {
                $tip.warning("请填写位置");
                return;
            }

            let updateCost = $("#updateCost");
            if (!updateCost.val()) {
                $tip.warning("请填写费用");
                return;
            }

            let updateLinkman = $("#updateLinkman");
            if (!updateLinkman.val()) {
                $tip.warning("请填写联系人");
                return;
            }

            let updateTel = $("#updateTel");
            if (!updateTel.val()) {
                $tip.warning("请填写联系电话");
                return;
            }

            let updateAbstract = $("#updateAbstract");
            if (!updateAbstract.val()) {
                $tip.warning("请填写描述");
                return;
            }

            let updateStatus = $("#updateStatus");
            if (!updateStatus.val()) {
                $tip.warning("请选择状态");
                return;
            }

            let updateImgVal = $("#update_base64").val();
            $http.post("/vp/stadium/modify", $.param({
                id: id,
                type: updateRoleNode.val(),
                name: updateName.val(),
                location: updateLocation.val(),
                cost: updateCost.val(),
                linkman: updateLinkman.val(),
                tel: updateTel.val(),
                abstract: updateAbstract.val(),
                status: updateStatus.val(),
                file: updateImgVal
            })).success(function (result) {
                if (result.code === 1) {
                    $tip.success("修改成功");
                    // 刷新接口
                    $scope.requestApi($scope.url, $scope.param);
                    $("#news-update").modal("hide");
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
        $("#selectRole").select2();
        $("#roleUpdate").select2();
        $("#newsStatus").select2();
        $("#updateStatus").select2();

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
                button: "选择图片",
                feedback: "请上传图片",
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
        //编辑新闻
        $('#updatePhoto').filer({
            limit: 1, // 上传数量1
            maxSize: 1, // 最大尺寸1M
            extensions: ['jpg', 'jpeg', 'png', 'gif', 'psd'], // 支持的文件类型
            changeInput: true, // 创建一个新的input元素
            showThumbs: true, // 显示文件预览
            addMore: false, // 不新增更多
            clipBoardPaste: true, // 允许复制粘贴文件
            captions: { // 编辑器
                button: "选择图片",
                feedback: "请上传图片",
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
                $("#update_base64").val("");
            },
            onSelect: function () { // 选择图片之后触发
                let photoNode = $("#updatePhoto");
                if (photoNode.val()) {
                    let fileReader = new FileReader();
                    fileReader.readAsDataURL(photoNode[0].files[0]);
                    fileReader.onload = function (e) {
                        $("#update_base64").val(e.target.result);
                    };
                }
            }
        });
        //wangEditor富文本
        $(".w-e-menu").css({"z-index":"0"});
    });
}(jQuery);

