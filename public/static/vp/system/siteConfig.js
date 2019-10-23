// angular数据交互
~function ($, angular, window) {
    "use strict";
    angular.module("myApp").controller("siteConfigCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        //开启页面遮罩
        $.busyLoadFull('show', {animation: "fade"});

        // 请求接口封装
        $scope.requestApi = function (url, param) {
            url = url || "/vp/system/siteConfig";
            param = param || {};
            $http.get(url, param).success(function (result) {
                $scope.list = result.data;
                // 初始化富文本
                angular.element(document).ready(function () {
                    // 初始化富文本
                    let E = window.wangEditor;
                    window.eidtorDom = new E('#editor');
                    window.editorDom2 = new E('#editor2');
                    // 1
                    eidtorDom.create();
                    eidtorDom.customConfig.zIndex = 10;
                    // 2
                    editorDom2.create();
                    editorDom2.customConfig.zIndex = 10;
                    $(".w-e-menu").css({"z-index":"0"});
                    // 插入html
                    eidtorDom.txt.html(result.data.agreement);
                    editorDom2.txt.html(result.data.clause);
                });

                //关闭页面遮罩
                $.busyLoadFull('hide', {animation: "fade"});
            });
        };

        // 初始化
        ($scope.init = function () {
            $scope.requestApi();
        }());

        //提交网站信息
        $scope.addSubmit = function (url) {
            let arr = $('.webSwitch').find('input:checked').val();

            if (!eidtorDom || !editorDom2) {
                $tip.warning("请等待富文本工具框加载完毕！");
                return;
            }

            let user = eidtorDom.txt.html();
            let privacy = editorDom2.txt.html();

            if (!arr) {
                $tip.warning("请选择网站开关！");
                return;
            }

            let webname = $("#webname").val();
            if (!webname) {
                $tip.warning("网站名称不能为空！");
                return;
            }

            let reg = /^.*\.(cn|com|org|net).*$/ui;
            let webdns = $("#webdns").val();
            if (!webdns) {
                $tip.warning("网站域名不能为空！");
                return;
            }

            if (!reg.test(webdns)) {
                $tip.warning("网站域名格式错误！");
                return;
            }

            let webinfo = $("#webinfo").val();
            if (!webinfo) {
                $tip.warning("备案信息不能为空！");
                return;
            }

            let pwcompany_named = $("#pwcompany_named").val();
            if (!pwcompany_named) {
                $tip.warning("公司名称不能为空！");
                return;
            }

            let pwcompany_address = $("#pwcompany_address").val();
            if (!pwcompany_address) {
                $tip.warning("公司地址不能为空！");
                return;
            }

            let emailT = "[\\w!#$%&'*+/=?^_`{|}~-]+(?:\\.[\\w!#$%&'*+/=?^_`{|}~-]+)*@(?:[\\w](?:[\\w-]*[\\w])?\\.)+[\\w](?:[\\w-]*[\\w])?";
            let email = $("#pwcompany_email").val();
            if (!email) {
                $tip.warning("公司邮箱不能为空！");
                return;
            }

            if (!email.match(emailT)) {
                $tip.warning("公司邮箱格式错误！");
                return;
            }

            let prize_size = $("#prize_size").val();
            if (!prize_size) {
                $tip.warning("加奖比例不能为空！");
                return;
            }

            let full = $("#full").val();
            if (!full) {
                $tip.warning("充值设置不能为空！");
                return;
            }

            let reduce = $("#reduce").val();
            if (!reduce) {
                $tip.warning("充值设置不能为空！");
                return;
            }

            let commission = $("#commission").val();
            if (!commission) {
                $tip.warning("返佣比例不能为空！");
                return;
            }

            if (!user) {
                $tip.warning("用户协议不能为空！");
                return;
            }

            if (!privacy) {
                $tip.warning("隐私条款不能为空！");
                return;
            }

            url = url || "/vp/system/saveSiteConfig";
            $http.post(url,
                $.param({
                    webname: webname,
                    webdns: webdns,
                    webkey: $("#webkey").val(),
                    describe: $("#describe").val(),
                    file: $("#base64_img").val(),
                    webinfo: webinfo,
                    pwcompany_named: pwcompany_named,
                    pwcompany_address: pwcompany_address,
                    pwcompany_email: email,
                    prize_size: prize_size,
                    recharge_full: full,
                    recharge_give: reduce,
                    commission: commission,
                    minimum_amount: $("#minimum_amount").val(),
                    service_charge: $("#service_charge").val(),
                    optradio: arr,
                    agreement: user,
                    clause: privacy
                })
            ).success(function (result) {
                if (result.code === 1) {
                    $tip.success(result.msg);
                }
            });
        }
    }]);
}(jQuery, angular, window);

// jQuery
!function ($, window) {
    "use strict";
    $(function () {
        // 新增网站LOGO
        $('#uploadimg').filer({
            limit: 1, // 上传数量1
            maxSize: 1, // 最大尺寸1M
            extensions: ['jpg', 'jpeg', 'png', 'gif', 'psd'], // 支持的文件类型
            changeInput: true, // 创建一个新的input元素
            showThumbs: false, // 显示文件预览
            addMore: false, // 不新增更多
            clipBoardPaste: true, // 允许复制粘贴文件
            captions: { // 编辑器
                button: "选择图片",
                feedback: "请上传logo",
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
                $("#base64_img").val("");
            },
            onSelect: function () { // 选择图片之后触发
                let photoNode = $("#uploadimg");
                if (photoNode.val()) {
                    let fileReader = new FileReader();
                    fileReader.readAsDataURL(photoNode[0].files[0]);
                    fileReader.onload = function (e) {
                        $("#base64_img").val(e.target.result);
                        $("#preview-img").attr('src', e.target.result);
                    };
                }
            }
        });

        let site_arr = [], cardDom = $(".site .card");
        cardDom.each(function (index, value) {
            site_arr[index] = $(this).height();
        });
        let site_max = Math.max.apply(null, site_arr);
        cardDom.height(site_max + "px");
    });
}(jQuery, window);


