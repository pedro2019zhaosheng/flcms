// angular write By CleverStone
// angular write By CleverStone
!function ($, window, angular) {
    "use strict";
    angular.module("myApp").controller("nodeCtrl", ["$scope", '$http', "$tip", function ($scope, $http, $tip) {
        // 新增顶级节点
        $scope.addTopNode = function () {
            $("#parentNodeId").val("0");
            $("#belongNode").val("顶级节点");
        };

        // 初始化属性
        $scope.initAttr = function () {
            $("#parentNodeId").val("");
            $("#belongNode").val("");
            $("#linkType").val("").trigger("change");
            $("#nodeTitle").val("");
            $("#nodeIcon").val("");
            $("#nodeModule").val("");
            $("#nodeController").val("");
            $("#nodeAction").val("");
            $("#nodePath").val("");
        };

        // 关闭模态框事件
        $('#node-add').on('hidden.bs.modal', function () {
            $scope.initAttr();
        });

        // 保存节点
        $scope.saveNode = function () {
            let parentNode = $("#parentNodeId");
            if (!parentNode.val()) {
                $tip.warning("父节点丢失");
                return;
            }

            let linkType = $("#linkType");
            if (!linkType.val()) {
                $tip.warning("请选择菜单类型");
                return;
            }

            let nodeTitle = $("#nodeTitle");
            if (!nodeTitle.val()) {
                $tip.warning("请填写菜单标题");
                return;
            }

            if (nodeTitle.val().length > 15) {
                $tip.warning("菜单标题不能大于15个字符");
                return;
            }

            let moduleVal = "", ctrlVal = "", actionVal = "", pathVal = "";
            if (linkType.val() !== "module") {
                let nodeModule = $("#nodeModule");
                if (!nodeModule.val()) {
                    $tip.warning("请填写模块");
                    return;
                }

                if (!/^[a-zA-Z]+$/u.test(nodeModule.val())) {
                    $tip.warning("模块名不合法");
                    return;
                }

                moduleVal = nodeModule.val();

                let nodeController = $("#nodeController");
                if (!nodeController.val()) {
                    $tip.warning("请填写控制器");
                    return;
                }

                if (!/^[a-zA-Z]+$/u.test(nodeController.val())) {
                    $tip.warning("控制器名不合法");
                    return;
                }

                ctrlVal = nodeController.val();

                let nodeAction = $("#nodeAction");
                if (!nodeAction.val()) {
                    $tip.warning("请填写方法");
                    return;
                }

                if (!/^[a-zA-Z]+$/u.test(nodeAction.val())) {
                    $tip.warning("方法名不合法");
                    return;
                }

                actionVal = nodeAction.val();

                let nodePath = $("#nodePath");
                if (!nodePath.val()) {
                    $tip.warning("请填写实际路由");
                    return;
                }

                pathVal = nodePath.val();
            }

            $http.post("/vp/node/add", $.param({
                pid: parentNode.val(),
                title: nodeTitle.val(),
                icon: $("#nodeIcon").val(),
                module: moduleVal,
                controller: ctrlVal,
                action: actionVal,
                menu_type: linkType.val(),
                url_value: pathVal,
                sort: 50
            })).success(function (result) {
                if (result.code === 1) {
                    $tip.success("添加成功");
                    $("#node-add").modal("hide");
                    // 初始化菜单树
                    $("#menuTree").jstree(true).refresh();

                }
            });
        };

        // 修改保存节点
        $scope.updateNode = function () {
            let menuId = $("#curMenuId").val();
            let linkType = $("#linkTypeUpdate").val();
            let title = $("#nodeTitleUpdate").val();
            let icon = $("#nodeIconUpdate").val();
            let module = $("#nodeModuleUpdate").val();
            let ctrl = $("#nodeControllerUpdate").val();
            let action = $("#nodeActionUpdate").val();
            let path = $("#nodePathUpdate").val();
            if (!linkType) {
                $tip.warning("请选择菜单类型");
                return;
            }

            if (!title) {
                $tip.warning("请填写菜单标题");
                return;
            }

            if (title.length > 15) {
                $tip.warning("菜单标题不能大于15个字符");
                return;
            }

            let moduleVal = "", ctrlVal = "", actionVal = "", pathVal = "";
            if (linkType !== "module") {
                if (!module) {
                    $tip.warning("请填写模块");
                    return;
                }

                if (!/^[a-zA-Z]+$/u.test(module)) {
                    $tip.warning("模块名不合法");
                    return;
                }

                moduleVal = module;

                if (!ctrl) {
                    $tip.warning("请填写控制器");
                    return;
                }

                if (!/^[a-zA-Z]+$/u.test(ctrl)) {
                    $tip.warning("控制器名不合法");
                    return;
                }

                ctrlVal = ctrl;

                if (!action) {
                    $tip.warning("请填写方法");
                    return;
                }

                if (!/^[a-zA-Z]+$/u.test(action)) {
                    $tip.warning("方法名不合法");
                    return;
                }

                actionVal = action;

                if (!path) {
                    $tip.warning("请填写实际路由");
                    return;
                }

                pathVal = path;
            }

            $http.post('/vp/node/set', $.param({
                id: menuId,
                title: title,
                icon: icon,
                menu_type: linkType,
                module: moduleVal,
                controller: ctrlVal,
                action: actionVal,
                url_value: pathVal,
            })).success(function (result) {
                if (result.code === 1) {
                    $tip.success("更新成功");
                    $("#node-update").modal("hide");
                    // 初始化菜单树
                    $("#menuTree").jstree(true).refresh();
                }
            });
        };
    }]);
}(window.jQuery, window, angular);

// write By CleverStone jQuery
~function ($) {
    "use strict";
    $(function () {

        // 清除state存储
        window.localStorage.removeItem("jstree");

        // 初始化select2
        $("#linkType").select2();

        // jstree
        $('#menuTree').jstree({
            'core': { // 核心配置
                'themes': { // 主题
                    "stripes": true,
                    "variant": "large",
                    "ellipsis": true
                },
                // 数据包
                'data': {
                    "url": "/vp/node/tree",
                    "dataType": "json"
                }
            },
            'contextmenu': { // 右键弹出菜单
                "select_node": false,
                "show_at_node": true,
                "items": {
                    "add": { // 新增
                        "separator_before": false,
                        "separator_after": false,
                        "_disabled": false,
                        "label": "新增节点", // 名称
                        "icon": "fa fa-plus", // 图标
                        "action": function (data) { // 执行方法
                            let inst = $.jstree.reference(data.reference), obj = inst.get_node(data.reference);
                            $("#node-add").modal("show");
                            $("#parentNodeId").val(obj.id);
                            $("#belongNode").val(obj.text);
                        }
                    },
                    "update": { // 修改
                        "separator_before": false,
                        "separator_after": false,
                        "_disabled": false,
                        "label": "修改节点",
                        "shortcut_label": 'F2',
                        "icon": "fa fa-pencil-square-o",
                        "action": function (data) {
                            let inst = $.jstree.reference(data.reference), obj = inst.get_node(data.reference);
                            $("#parentNodeId").val(obj.id);
                            $("#belongNode").val(obj.text);
                            $.get("/vp/node/info?id=" + obj.id, function (result) {
                                if (result.code === 1) {
                                    $("#belongNodeUpdate").val(result.data.topTitle);
                                    $("#linkTypeUpdate").val(result.data.menu_type).trigger("change");
                                    $("#nodeTitleUpdate").val(result.data.title);
                                    $("#nodeIconUpdate").val(result.data.icon);
                                    $("#nodeModuleUpdate").val(result.data.module);
                                    $("#nodeControllerUpdate").val(result.data.controller);
                                    $("#nodeActionUpdate").val(result.data.action);
                                    $("#nodePathUpdate").val(result.data.url_value);
                                    $("#curMenuId").val(result.data.id);
                                    // 打开模态框
                                    $("#node-update").modal("show");
                                } else {
                                    console.warn(result);
                                    window.toastr.warn("请求错误~~");
                                }
                            });
                        }
                    },
                    "delete": { // 删除
                        "separator_before": false,
                        "separator_after": false,
                        "_disabled": false, //(this.check("delete_node", data.reference, this.get_parent(data.reference), "")),
                        "label": "删除节点",
                        "icon": "fa fa-trash-o",
                        "action": function (data) {
                            let inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                            $.swal("您确定要删除该节点么", function () {
                                $.get('/vp/node/del?id=' + obj.id, function (result) {
                                    if (result.code === 1) {
                                        $.swalSuccess();
                                        // 初始化菜单树
                                        $("#menuTree").jstree(true).refresh();
                                    } else {
                                        $.swalerror(result.msg);
                                    }
                                });
                            }, '删除后，所有角色将不能访问该节点!');
                        }
                    },
                    "toggle": { // 禁用或启用
                        "separator_before": false,
                        "separator_after": false,
                        "_disabled": false, //(this.check("delete_node", data.reference, this.get_parent(data.reference), "")),
                        "label": "禁用/启用",
                        "icon": "fa fa-toggle-off",
                        "action": function (data) {
                            let inst = $.jstree.reference(data.reference),
                                obj = inst.get_node(data.reference);
                            let status = 0;
                            let msg = "禁用";
                            if (parseInt(obj.data.status) === 0) {
                                status = 1;
                                msg = "启用";
                            }
                            $.swal("您确定" + msg + "该节点么", function () {
                                $.get('/vp/node/toggle?id=' + obj.id + '&status=' + status, function (result) {
                                    if (result.code === 1) {
                                        $.swalSuccess();
                                    } else {
                                        $.swalerror();
                                        console.warn(result);
                                    }
                                    // 清除state存储
                                    window.localStorage.removeItem("jstree");
                                    // 初始化菜单树，更新dom节点和清空state存储
                                    $("#menuTree").jstree(true).refresh(false, true);
                                });
                            }, msg + "该节点后，其子节点也将被" + msg + "!");
                        }
                    }
                }
            },
            'types': { // icon类型
                'default': {
                    'icon': false // 关闭默认图片
                },
                'menu': {
                    'icon': 'fa fa-align-justify'
                }
            },
            // 注册配置
            'plugins': ['types', 'state', "themes", "contextmenu", "unique"]
        });
    });
}(window.jQuery);
