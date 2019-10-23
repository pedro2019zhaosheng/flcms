// 公用导航和左侧菜单JS write By CleverStone
~function ($, angular, window) {
    "use strict";
    angular.module("myApp").controller("headerCtrl", ["$scope", "$http", "$tip", function ($scope, $http, $tip) {
        // 获取当前登录管理员详情
        ($scope.init = function () {
            $http.get("/vp/home").success(function (result) {
                $scope.nickName = result.data.nick_name; // 昵称
                $scope.lastLoginTime = result.data.last_login_time; // 最后一次登录时间
                $scope.photo = result.data.photo; // 头像
            }).error(function (error) {
                console.warn(error);
            });
        })();

        // 退出
        $scope.logout = function ($e) {
            $e.preventDefault();
            $.cookie('currentTopMenu', '', {path: '/', expires: -1});
            window.location.href = '/vp/logout';
        };

        // 跳转到所有消息列表
        $scope.dumpAllSmg = function () {
            window.location.href = '/vp/msg';
        };

        // 代充值
        $scope.agentRecharge = function () {
            window.location.href = '/vp/agentRecharge';
        };

        // 代提现
        $scope.agentWithdraw = function () {
            window.location.href = '/vp/agentWithdraw';
        };

    }]);
}(jQuery, angular, window);

// jQuery部分
!function (window, $) {
    "use strict";
    // 页面刷新获取消息列表和数量
    let temp = function (msgType, desc, datetime, user, img, id) {
        return '   <a onclick="window.location.href = \'/vp/msg?id=' + id + '\'; return false;" href="#" class="dropdown-item notify-item">\n' +
            '                                <div class="notify-icon bg-faded">\n' +
            '                                    <img src="' + img + '" alt class="rounded-circle img-fluid">\n' +
            '                                </div>\n' +
            '                                <p class="notify-details">\n' +
            '                                    <b>' + msgType + '</b>\n' +
            '                                    <span class="text-secondary">' + desc + '</span>\n' +
            '                                    <small class="text-muted clearfix mt-1">\n' +
            '                                        <span class="float-left">' + datetime + '</span>\n' +
            '                                        <span class="float-right">' + user + '</span>\n' +
            '                                    </small>\n' +
            '                                </p>\n' +
            '                            </a>';
    };

    $.get('/vp/msg/newestMsg', function (result) {
        let data = result.data.data || [];
        let newMsgCount = result.data.msgCount || 0;
        let html = '';
        if (data.length === 0) {
            html = '   <div id="noJsMsg" class="container text-center pt-1 pb-1">\n' +
                '                <img class="d-block w-25 m-auto" src="/static/lib/images/nomsg.png" alt>\n' +
                '            <small class="mt-3 text-secondary">暂无消息...</small>\n' +
                '            </div>';
        } else {
            data.forEach(function (item) {
                html += temp(item.msg_type, item.desc, item.send_time, item.account, item.icon, item.id);
            });
        }

        $("#item-wrap").html(html);
        if (parseInt(newMsgCount) === 0) {
            $(".notif-bullet").hide();
        }

        $("#jsMsgCount").text(parseInt(newMsgCount));
    });

    // socket.io消息推送
    let htmlReverseStr = function (str) {
        if (str.length === 0) {
            return "";
        }

        str = str.replace(/&amp;/g, "&");
        str = str.replace(/&lt;/g, "<");
        str = str.replace(/&gt;/g, ">");
        str = str.replace(/&nbsp;/g, " ");
        str = str.replace(/&#39;/g, "\'");
        str = str.replace(/&quot;/g, "\"");

        return str;
    };

    // 初始化io对象
    let socket = io('http://' + document.domain + ':2120');
    // uid 可以为网站用户的uid，作为例子这里用session_id代替
    let uid = window.UID;
    // 当socket连接后发送登录请求
    socket.on('connect', function () {
        socket.emit('login', uid);
    });
    // 当服务端推送来消息时触发，这里简单的aler出来，用户可做成自己的展示效果
    socket.on('new_msg', function (data) {
        // 转义html实体字符
        data = htmlReverseStr(data);
        // json字符串转json对象
        data = eval('(' + data + ')');
        let newestHtml = temp(data.msg_type, data.desc, data.send_time, data.account, data.icon, data.id);
        let itemWrapNode = $("#item-wrap");
        let noMsgNode = $("#noJsMsg");
        // 消息提醒
        window.toastr.info(data.desc, data.body_type + '消息通知:');
        // 根据内容类型, 获取对应的语音链接
        let audioLink = '';
        switch (data.body_type) {
            case 1: // 资金提现
                audioLink = '/static/lib/audio/withdraw.mp3';
                break;
            case 2: // 会员注单
                audioLink = '/static/lib/audio/order.mp3';
                break;
            case 3: // 资金充值
                audioLink = '/static/lib/audio/recharge.mp3';
                break;
            default: // 其他
                audioLink = '/static/lib/audio/other.mp3';
        }
        // 语音提醒
        let soundTip = document.getElementById('soundTip');
        soundTip.setAttribute('src', audioLink);
        let timer = setInterval(function () {
            if (soundTip.ended === true) {
                // 语音播放完成, 清除语音连接
                soundTip.removeAttribute('src');
                // 销毁定时器
                clearInterval(timer);
            }
        }, 100);
        // 删除暂无数据
        if (noMsgNode.length) {
            noMsgNode.remove();
        }
        // 显示消息提醒
        $(".notif-bullet").show();
        // 新增消息
        itemWrapNode.prepend(newestHtml);
        // 增加新消息数量
        let msgCountNode = $("#jsMsgCount");
        let newestMsgCount = msgCountNode.text();
        msgCountNode.text(parseInt(newestMsgCount) + 1);
        // 保存消息个数为3
        if ($("#item-wrap .notify-item").length > 3) {
            $("#item-wrap .notify-item:last").remove();
        }
    });

}(window, window.jQuery);