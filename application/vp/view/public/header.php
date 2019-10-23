<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <title>{$title}</title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta name="keywords" content="{$webkey}">
    <meta name="description" content="{$describe}">
    {if isset($webdns) && !empty($webdns)}
    <meta http-equiv='x-dns-prefetch-control' content='on'>
    <link rel='dns-prefetch' href='{$webdns}'>
    {/if}
    <meta name="author" content="CleverStone">
    <meta name="github" content="https://www.github.com/cleverstone">
    <!-- Bootstrap CSS -->
    <link href="/static/lib/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <!-- Font Awesome CSS -->
    <link href="/static/lib/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <!-- Custom CSS -->
    <link href="/static/lib/css/style.css" rel="stylesheet" type="text/css"/>
    <!--提示插件-->
    <link href="/static/lib/css/toastr.min.css" rel="stylesheet" type="text/css"/>
    <!--日期插件-->
    <link href="/static/lib/css/daterangepicker.css" rel="stylesheet" type="text/css"/>
    <!--select2插件-->
    <link href="/static/lib/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <!--awesome-bootstrap-checkbox插件-->
    <link href="/static/lib/css/awesome-bootstrap-checkbox.css" rel="stylesheet" type="text/css"/>
    <!--animate.css css3动画库-->
    <link href="/static/lib/css/animate.min.css" rel="stylesheet" type="text/css"/>
    <!--mask遮罩插件-->
    <link href="/static/lib/mask/dist/mask.css" rel="stylesheet" type="text/css"/>
    {if isset($css)}
    {foreach name="css" item="cssItem"}
    <link href="{$cssItem}" rel="stylesheet" type="text/css"/>
    {/foreach}
    {/if}

    <script>
        window.UID = <?php echo UID; ?>
    </script>
</head>
<body class="adminbody" ng-app="myApp">
<div id="main">
    <!-- top bar navigation -->
    <div class="headerbar" ng-controller="headerCtrl">
        <!--语音提示DOM-->
        <audio id="soundTip" autoplay="autoplay"></audio>
        <!-- LOGO -->
        <div class="headerbar-left">
            <a href="/vp/" class="logo">
                <img src="{$webImg}" alt/>
                <span>&emsp;{$webname}</span>
            </a>
        </div>
        <nav class="navbar-custom">
            <ul class="list-inline float-right mb-0">
                <li class="list-inline-item dropdown notif">
                    <a class="nav-link dropdown-toggle arrow-none" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="fa fa-fw fa-bell-o"></i>
                        &nbsp;
                        <span class="notif-bullet"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-lg">
                        <!-- item-->
                        <div class="dropdown-item noti-title text-light">
                            <small>
                                <span>新消息</span>
                                <span id="jsMsgCount" class="label label-danger pull-xs-right"></span>
                            </small>
                        </div>

                        <!-- item-->
                        <div id="item-wrap"></div>

                        <!-- All-->
                        <a href="#" ng-click="dumpAllSmg()" class="dropdown-item notify-item notify-all">
                            <small>查看所有消息</small>
                        </a>

                    </div>
                </li>
                <li class="list-inline-item dropdown notif">
                    <a class="nav-link dropdown-toggle nav-user" data-toggle="dropdown" href="#" role="button"
                       aria-haspopup="false" aria-expanded="false">
                        <img id="header_photo" ng-src="{{ photo ? photo : '/static/lib/images/admin.png' }}" alt="" class="avatar-rounded">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                        <!-- item-->
                        <div class="dropdown-item noti-title">
                            <p class="text-overflow text-light mb-0">
                                <small>
                                    <span>您好&nbsp;,&nbsp;</span>
                                    <span id="header_nickname" ng-bind="nickName | default: '无'"></span>
                                </small>
                            </p>
                        </div>
                        <?php if(\app\common\model\AdminAuth::hasAuth('/vp/admin/detail')): ?>
                        <!-- item-->
                        <a target="_self" href="/vp/admin/detail" class="dropdown-item notify-item">
                            <i class="fa fa-user"></i> <span>个人中心</span>
                        </a>
                        <?php endif; ?>
                        <!-- item-->
                        <a target="_self" href="#" ng-click="logout($event)" class="dropdown-item notify-item">
                            <i class="fa fa-power-off"></i> <span>退出</span>
                        </a>
                    </div>
                </li>
            </ul>
            <ul class="list-inline menu-left mb-0">
                <li class="float-left">
                    <button class="button-menu-mobile open-left">
                        <i class="fa fa-fw fa-bars"></i>&nbsp;
                    </button>
                    <?php if(\app\common\model\AdminAuth::hasAuth('/vp/agentRecharge')): ?>
                    <button class="btn btn-sm btn-primary" ng-click="agentRecharge()">
                        &nbsp;代充值&nbsp;
                    </button>
                    <?php endif; ?>

                    <?php if(\app\common\model\AdminAuth::hasAuth('/vp/agentWithdraw')): ?>
                    <button class="btn btn-sm btn-warning" ng-click="agentWithdraw()">
                        &nbsp;代提现&nbsp;
                    </button>
                    <?php endif; ?>
                </li>
                <li class="float-right l-h-50 p-r-20 color-write xs-hide">
                    <small ng-bind="'最近一次登录&nbsp;:&nbsp;' + lastLoginTime"></small>
                </li>
            </ul>
        </nav>
    </div>
    <!-- End Navigation -->
    <!-- Left Sidebar -->

    {:html_entity_decode($menu)}

    <!-- End Sidebar -->
    <div class="content-page" ng-cloak> <!--ng-cloak 防止出现angular标签-->
        <!-- Start content -->
        <div class="content">
            {if isset($nav)}
            <div class="fix-50">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li>当前位置&nbsp;:&nbsp;</li>
                        {foreach name="nav" item="navItem"}
                        <li class="breadcrumb-item {:isset($navItem.active) && $navItem.active ? 'active' : ''}">
                            {if !isset($navItem['active']) || !$navItem['active']}
                            <a href="{:isset($navItem.link) ? $navItem.link : ''}">{$navItem.title}</a>
                            {else /}
                            {$navItem.title}
                            {/if}
                        </li>
                        {/foreach}
                        <li class="right-0">
                            <button onclick="window.history.go(-1);" type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="fa fa-fw fa-arrow-left" aria-hidden="true"></i>
                                返回上一页
                            </button>
                            <button onclick="window.location.reload()" type="button" class="btn btn-sm btn-outline-success">
                                <i class="fa fa-fw fa-refresh" aria-hidden="true"></i>
                                刷新
                            </button>
                        </li>
                    </ol>
                </nav>
            </div>
            {/if}