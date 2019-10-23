<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>{$title}</title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <meta name="description" content="足彩, 篮彩, 足球彩票, 足彩竞猜, 篮球彩票, 篮球竞彩">
    <meta name="author" content="CleverStone">
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
    <!--bootstrap-table插件-->
    <link href="/static/lib/bootstrap-table/bootstrap-table.min.css" rel="stylesheet" type="text/css"/>
    <!--mask遮罩插件-->
    <link href="/static/lib/mask/dist/mask.css" rel="stylesheet" type="text/css"/>
    {if isset($css)}
    {foreach name="css" item="cssItem"}
    <link href="{$cssItem}" rel="stylesheet" type="text/css"/>
    {/foreach}
    {/if}
</head>

<body class="adminbody" ng-app="myApp">
<div id="main">
    <!-- top bar navigation -->
    <div class="headerbar" ng-controller="headerCtrl">
        <!-- LOGO -->
        <div class="headerbar-left" style="background-color: #23262E;">
            <a href="/" class="logo">
                <img alt="Logo" src="/static/lib/images/logo.png"/>
                <span class="ml-3">代理商后台</span>
            </a>
        </div>
        <nav class="navbar-custom" style="background-color: #23262E;">
            <ul class="list-inline float-right mb-0">
                <li class="list-inline-item dropdown notif">
                    <a class="nav-link dropdown-toggle nav-user" data-toggle="dropdown" href="#" role="button"
                       aria-haspopup="false" aria-expanded="false">
                        <img ng-src="{{ photo ? photo : '/static/lib/images/admin.png' }}" alt class="avatar-rounded">
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

                        <!-- item-->
                        <a target="_self" href="/pxy/personal" class="dropdown-item notify-item">
                            <i class="fa fa-user"></i> <span>个人中心</span>
                        </a>
                        <!-- item-->
                        <a href="#" ng-click="logout($event)" class="dropdown-item notify-item">
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
                    <button class="btn btn-sm btn-primary" ng-click="agentRecharge()">
                        代充值
                    </button>
                </li>
                <li class="float-right">
                    <span class="text-white l-h-50 mr-2" ng-bind="'我的余额&nbsp;:&nbsp;' + balance + '元'">
                        我的余额&nbsp;:&nbsp;0.00元
                    </span>
                    <span class="text-white l-h-50 mr-5" ng-bind="'彩金&nbsp;:&nbsp;' + hadsel + '元'">
                        彩金&nbsp;:&nbsp;0.00元
                    </span>
                </li>
            </ul>
        </nav>
    </div>
    <!-- End Navigation -->
    <!-- Left Sidebar -->

    <div class="left main-sidebar animated slideInLeft" style="background-color: #393D49;">
        <div class="sidebar-inner leftscroll">
            <div id="sidebar-menu">
                <ul>
                    <li class="submenu">
                        <a id="module_16" target="_self" href="/">
                            <i class="fa fa-fw fa-home"></i><span>首页</span>
                        </a>
                    </li>
                    <li class="submenu">
                        <a id="module_5" href="#"><i class="fa fa-fw fa-address-card-o">
                            </i><span>会员管理</span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li><a target="_self" href="/pxy/member">会员列表 </a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                        <a id="module_17" href="#"><i class="fa fa-fw fa-newspaper-o">
                            </i><span>资金管理</span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li><a target="_self" href="/pxy/capital/recharge">充值记录 </a></li>
                            <li><a target="_self" href="/pxy/capital/cash">提现记录 </a></li>
                            <li><a target="_self" href="/pxy/capital/correction">资金校正记录 </a></li>
                            <li><a target="_self" href="/pxy/capital/rake_back">投注返佣记录 </a></li>
                            <li><a target="_self" href="/pxy/capital/flow">资金流水记录 </a></li>
                        </ul>
                    </li>
                    <li class="submenu">
                        <a id="module_9" href="#"><i class="fa fa-fw fa-handshake-o">
                            </i><span>代理管理</span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li><a target="_self" href="/pxy/agent">代理商列表 </a></li>
                            <li><a target="_self" href="/pxy/agentReturn">代理商返点设置 </a></li>

                        </ul>
                    </li>
                    <li class="submenu">
                        <a id="module_18" href="#"><i class="fa fa-fw fa-file-o">
                            </i><span>注单管理</span><span class="menu-arrow"></span>
                        </a>
                        <ul class="list-unstyled">
                            <li><a target="_self" href="/pxy/bet/list">注单列表 </a></li>
                            <li><a target="_self" href="/pxy/bet/extend">推单列表 </a></li>
                        </ul>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

    <!-- End Sidebar -->
    <div class="content-page" ng-cloak> <!--ng-cloak 防止出现angular标签-->
        <!-- Start content -->
        <div class="content">
            {if isset($nav)}
            <div class="fix-50">
                <nav aria-label="breadcrumb">
                <ol class="breadcrumb position-relative">
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
                        <button onclick="window.history.go(-1)" type="button" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-fw fa-caret-square-o-left" aria-hidden="true"></i>
                            返回上一页
                        </button>
                        <button onclick="window.location.reload()" type="button" class="btn btn-sm btn-light">
                            <i class="fa fa-fw fa-refresh" aria-hidden="true"></i>
                            刷新
                        </button>
                    </li>
                </ol>
            </nav>
            </div>
            {/if}
