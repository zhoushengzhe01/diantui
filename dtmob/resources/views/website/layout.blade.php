<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>点推移动-广告营销平台</title>
	<meta name="keywords" content="">
	<meta name="description" content="">
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes"/>
    <link rel="stylesheet" href="{{$group->public}}css/bootstrap.min.css">
    <link rel="stylesheet" href="{{$group->public}}css/swiper.min.css">
    <link rel="stylesheet" href="{{$group->public}}css/iconfont.css">
    <link rel="stylesheet" href="{{$group->public}}css/mobliemenu.css">
    <link rel="stylesheet" href="{{$group->public}}css/jquery.fullpage.min.css" />
    <link rel="stylesheet" href="{{$group->public}}css/all.css">
    <script src="{{$group->public}}js/jquery-1.12.4.min.js"></script>
</head>
<body>
<div class="header">
    <div class="head">
        <div class="menu-button">
            <i class="iconfont icon-msnui-menu"></i>
        </div>
        <a href="/" class="mb-logo"><img src="{{$group->public}}images/mb-logo.png" alt=""></a>
        <a href="/login" class="online-ex"><i class="fa fa-desktop"></i>登录</a>
    </div>
    <ul class="header-menu">
        <li class="active"><a href="/">网站首页</a></li>
        <li><a href="/product">产品优势</a></li>
        <li><a href="/news">公告中心</a></li>
        <li><a href="/help">帮助中心</a></li>
        <li><a href="/about">关于我们</a></li>
    </ul>
</div>
<div class="mb-bg"></div>
<div class="top-area">
    <div class="container" style="max-width:1400px;">
        <a href="/" class="logo"><img src="{{$group->public}}images/logo.png" alt=""></a>
        <ul class="menu">
            <li><a href="/">网站首页</a></li>
            <li><a href="/product">产品优势</a></li>
            <li><a href="/news">公告中心</a></li>
            <li><a href="/help">帮助中心</a></li>
            <li><a href="/about">关于我们</a></li>
        </ul>
        <ul class="login-area">
            <li></li>
            <li><a href="/login" class="login">登录</a></li>
            <li><a href="/register" class="register">注册</a></li>
        </ul>
    </div>
</div>

@yield('content')


<script src="{{$group->public}}js/tether.min.js"></script>
<script src="{{$group->public}}js/bootstrap.min.js"></script>
<script src="{{$group->public}}js/jqthumb.min.js"></script>
<script src="{{$group->public}}js/swiper.jquery.min.js"></script>
<script src="{{$group->public}}js/scrolloverflow.min.js"></script>
<script src="{{$group->public}}js/jquery.fullpage.min.js"></script>
<!-- <script src="{{$group->public}}js/mobliemenu.js"></script> -->
<script src="{{$group->public}}js/all.js"></script>
</body>
</html>
