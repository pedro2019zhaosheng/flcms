!function (window, $, angular) {
    "use strict";
    angular.module("myApp").controller("roleCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        $.busyLoadFull('show', {animation: "fade"});
        //请求接口
        $scope.requestApi = function (url, param) {
            url = url || '';
            param = param || {};
            $scope.url = url;
            $scope.param = param;

            $http.get(url, param).success(function (result) {
                $scope.roleList = result.data.list.data;
                !$scope.roleList.length ? $scope.roleNoData = true : $scope.roleNoData = false; // 不显示nodata
                $scope.rolePage = result.data.page;
                $scope.roleCurrentPage = result.data.list.current_page;
                $scope.roleTotalPage = result.data.list.total;
                $scope.rolePerPage = result.data.list.per_page;

                // 关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 初始化
        $scope.init = function () {
            $scope.roleName = ""; // 初始化搜索框

            $scope.requestApi("/vp/role/role", {
                params: {
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

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
            $scope.requestApi('/vp/role/role', $scope.param);
        };

        // 点击搜索
        $scope.searchSubmit = function () {
            $scope.requestApi("/vp/role/role", {
                params: {
                    roleName: $scope.roleName,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 分页
        $scope.getRolePage = function (url) {
            $scope.requestApi(url, {
                params: {
                    roleName: $scope.roleName,
                    perPage: $scope.cleverPerPageModel // 每页数据条数
                }
            });
        };

        // 关闭模态框事件
        $('#role-add').on('hidden.bs.modal', function () {
            $("#roleType").val("").trigger("change");
            $("#addRole").val("");
            $("#addDesc").val("");
            $("#addSort").val(50);
        });

        // 新增角色
        $scope.saveRole = function () {
            let role = $("#addRole").val();
            if (!role) {
                $tip.warning("请填写角色名");
                return;
            }

            let roleType = $("#roleType");
            if (!roleType.val()) {
                $tip.warning("请选择角色类型");
                return;
            }

            let sort = $("#addSort").val();
            if (!sort) {
                $tip.warning("请填写排序字段");
                return;
            }

            $http.post("/vp/role/add", $.param({
                role: role,
                desc: $("#addDesc").val() || '',
                sort: sort,
                roletype: roleType.val()
            })).success(function (result) {
                if (result.code === 1) {
                    $("#role-add").modal("hide");
                    $tip.success("新增成功");
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 删除角色
        $scope.deleteRole = function (roleId) {
            $.swal("确定要删除么", function () {
                $http.get("/vp/role/delete?id=" + roleId).success(function (result) {
                    if (result.code === 1) {
                        $.swalSuccess();
                        // 初始化接口
                        $scope.requestApi($scope.url, $scope.param);
                    }
                });
            });
        };

        // 修改排序
        $scope.updateRoleSort = function (roleId, sort) {
            if (!sort) {
                return;
            }

            $http.get("/vp/role/sort?id=" + roleId + '&sort=' + sort).success(function (result) {
                if (result.code === 1) {
                    // 初始化接口
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 禁用启用
        $scope.disableRole = function (roleId, status) {
            $http.get("/vp/role/toggle?id=" + roleId + '&status=' + status).success(function (result) {
                if (result.code === 1) {
                    // 初始化接口
                    $tip.success(result.msg);
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };
        // 修改获取数据
        $scope.updateRoleMethod = function (roleId) {
            $http.get("/vp/role/info?id=" + roleId).success(function (result) {
                if (result.code === 1) {
                    $scope.updateRole = result.data.name;
                    $scope.orginRole = result.data.name;
                    $scope.updateDesc = result.data.description;
                    $scope.roleId = result.data.id;
                }
            });
        };

        // 保存修改过的数据
        $scope.updateSaveRole = function (roleId) {
            if (!$scope.updateRole) {
                $tip.warning("请填写角色");
                return;
            }

            if ($scope.orginRole === "超级管理员") {
                $tip.warning("系统角色“超级管理员”不能被修改!");
                return;
            }

            $http.post("/vp/role/update", $.param({
                id: roleId,
                role: $scope.updateRole,
                desc: $scope.updateDesc || '',
            })).success(function (result) {
                if (result.code === 1) {
                    $("#role-update").modal("hide");
                    $tip.success("修改成功");
                    $scope.requestApi($scope.url, $scope.param);
                }
            });
        };

        // 访问授权
        $scope.giveAuth = function (roleId) {
            $scope.authRoleId = roleId;
            $http.get('/vp/role/auth?id=' + roleId).success(function (result) {
                if (result.code === 1) {
                    // 清除state存储
                    window.localStorage.removeItem("jstree");
                    // jstree
                    let tree = $('#authTree');
                    tree.jstree(true).settings.core.data = result.data;
                    // 初始化菜单树，更新dom节点和清空state存储
                    tree.jstree(true).refresh(false, true);
                    $("#access-auth").modal("show");
                }
            });
        };

        // 保存权限
        $scope.saveAuth = function (roleId) {
            let data = {};
            let jsTreeState = window.localStorage.jstree;
            if (!jsTreeState) {
                $tip.warning("请选择节点");
                return;
            }

            let stateObj = JSON.parse(jsTreeState);
            let selectedId = stateObj.state.core.selected;
            if (!selectedId || selectedId.length === 0) {
                $tip.warning("请选择节点");
                return;
            }

            data['clicked'] = selectedId;
            let tempCheckedId, undetermined = [];
            $(".jstree-undetermined").each(function () {
                tempCheckedId = $(this).parent().parent().attr('id');
                undetermined.push(tempCheckedId);
            });

            data['undetermined'] = undetermined;
            $http.post('/vp/role/connect', $.param({
                id: roleId,
                auth: data
            })).success(function (result) {
                if (result.code === 1) {
                    $tip.success(result.msg);
                }

                $("#access-auth").modal('hide');
            });
        };
    }]);
}(window, jQuery, angular);

!function ($) {
    "use strict";
    $(function () {
        // 初始化select2
        $("#roleType").select2();

        // 初始化jstree
        $('#authTree').jstree({
            'core': { // 核心配置
                'themes': { // 主题
                    "stripes": true,
                    "variant": "large",
                    "ellipsis": true
                },
                // 数据包
                'core': {data: null}
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
            'plugins': ['types', 'checkbox', 'state', "themes", "unique"]
        });
    });
}(jQuery);
