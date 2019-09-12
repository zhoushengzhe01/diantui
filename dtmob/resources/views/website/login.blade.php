<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>点推移动-移动广告营销平台</title>
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes" />
    <link rel="stylesheet" href="{{$group->public}}css/bootstrap.min.css">
    <link rel="stylesheet" href="{{$group->public}}css/iconfont.css">
    <link rel="stylesheet" href="{{$group->public}}css/mobliemenu.css">
    <link rel="stylesheet" href="{{$group->public}}css/all.css">
	<script src="{{$group->public}}js/jquery-1.12.4.min.js"></script>
	<script src="{{$group->public}}js/tether.min.js"></script>
	<script src="{{$group->public}}js/bootstrap.min.js"></script>
	<script src="{{$group->public}}js/jqthumb.min.js"></script>
	<script src="{{$group->public}}js/mobliemenu.js"></script>
	<script src="{{$group->public}}js/all.js"></script>
</head>
<body class="login-page">
    
<div class="logo"><a href="/"><img src="{{$group->public}}images/login-logo.png" alt=""></a></div>
<!--logo-->
<div class="con">
    <div class="top">
        <h5>用户登录</h5>
        <div class="check">
            <div class="form_cont">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="customRadioInline1" name="type" value="1" checked="checked" class="custom-control-input" />
                    <label class="custom-control-label" for="customRadioInline1">媒介主</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="customRadioInline2" name="type" value="2" class="custom-control-input" />
                    <label class="custom-control-label" for="customRadioInline2">广告主</label>
                </div>
            </div>
            <div class="input">
                <div class="list">
                    <p>用户名</p>
                    <input type="text" name="username" placeholder="请输入用户名" />
                </div>
                <div class="list">
                    <p>密码</p>
                    <input type="password" name="password" placeholder="请输入密码" />
                </div>
                <div class="list code">
                    <p>验证码</p>
                    <div id="v_container" class="yzm"><img src="/captcha/math.json" width="130" onclick="this.src='/captcha/math.json?'+Math.random()"></div>
                    <input type="text" name="captcha" id="captcha" placeholder="请输入验证码" />
                </div>
                <input type="submit" class="log jbtn_login" value="登录" onclick="submit()"/>
                <p class="tip">如有账户疑问请联系客服专员</p>
            </div>
        </div>
    </div>
    <div class="bottom">
        <p>还没注册帐号？<a href="/register">立即注册</a></p>
    </div>
</div>
<!--con-->

<p class="copy">&copy;2018 dtmob.cn &nbsp;点推网络 &nbsp;版权所有 &nbsp;意见邮件：dtmobcn@gmail.com &nbsp;投诉QQ：2322279938</p>

<script>
	function submit()
	{
        var type = $("input[name='type']:checked").val();
		if(type=='1')
			var url = '/webmaster/login.json';
		if(type=='2')
			var url = '/advertiser/login.json';

		var paramet = {
			username: $("input[name='username']").val(),
            password: $("input[name='password']").val(),
            captcha: $("input[name='captcha']").val(),
			_token: '{{csrf_token()}}',
		};
        
		var ajax = $.ajax({
			type: 'POST',
			url: url,
			dataType: "json",
			contentType: 'application/json',
			withCredentials: true,
			data: JSON.stringify(paramet),
			success: function(data)
			{
				message('success', '登陆成功');
				
				if(type=='1')
				{
					window.location.href = '/webmaster';	
				}
				if(type=='2')
				{
					window.location.href = '/advertiser';	
				}
				
			},
			error: function(data) {
				
                var data = JSON.parse(data.responseText);
                
				message('warning', data.message);
			}
		});
    }
    
    function message(type, message)
    {
        var div = document.createElement('div');
            
        div.className = 'message '+type;

        div.innerHTML = '<div class="'+type+'">'+message+'</div>';

        document.body.appendChild(div);

        setTimeout(function()
        {
            $(div).remove();

        }, 3000);
    }
</script>
</body>
</html>