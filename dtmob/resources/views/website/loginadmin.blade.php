<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>点推移动-后台管理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="{{$group->public}}js/jquery-3.4.0.min.js" type="text/javascript" charset="utf-8"></script>    
</head>

<body style="background: #000">
<div class="login">
	<div class="content">
		<h1>管理员后台</h1>
		<div class="form">
			<input type="text" name="username" class="username" placeholder="Username">
            <input type="password" name="password" class="password" placeholder="Password">
            <img src="/captcha/math.json" width="130" onclick="this.src='/captcha/math.json?'+Math.random()">
            <input type="text" name="captcha" class="captcha" placeholder="Captcha" style="width:150px;">
			<button onclick="submit()">登陆</button>
        </div>
	</div>
</div>

<script>
    //提示
	function message(type, message)
	{
        var div = document.createElement('div');
		div.className = 'message '+type;
		div.innerHTML = '<div class="'+type+'">'+message+'</div>';
		document.body.appendChild(div);
		setTimeout(function(){
			$(div).remove();
		}, 3000);
	}

	function submit()
	{   
		var paramet = {
			username: $("input[name='username']").val(),
            password: $("input[name='password']").val(),
            captcha: $("input[name='captcha']").val(),
			_token: '{{csrf_token()}}',
		};
		var ajax = $.ajax({
			type: 'POST',
			url: '/admin/login.json',
			dataType: "json",
			contentType: 'application/json',
			withCredentials: true,
			data: JSON.stringify(paramet),
			success: function(data)
			{
                message('success', '登陆成功');
                
                window.location.href = '/admin';
			},
			error: function(data) {

				var data = JSON.parse(data.responseText);

				message('warning', data.message);
			}
		});
	}
</script>


<style>
*{
    -moz-box-sizing: border-box;
    -webkit-box-sizing: border-box;
    -o-box-sizing: border-box;
    -ms-box-sizing: border-box;
    box-sizing: border-box;
    margin:0px;
    padding:0px;
    font-size: 13px;
}
.login{
    display: table;
    width: 100%;
    height: 100%;
    background: #334054;
    position: absolute;
    bottom: 0px;
    top: 0px;
}
.login .content{
    display: table-cell;
    vertical-align: middle;
    text-align: center;
}
.login .content .form{
    width: 300px;
    margin: 0 auto;
}
.login .content h1{
    font-size: 20px;
    color: #53d192;
}
img{
    vertical-align: middle;
    margin-right: 10px;
    border: 1px solid #3d3d3d;
    border-radius: 6px;
}
.login .content input{
    width: 300px;
    height: 42px;
    margin-top: 25px;
    padding: 0 15px;
    background: #2d2d2d;
    background: rgba(45,45,45,.15);
    -moz-border-radius: 6px;
    -webkit-border-radius: 6px;
    border-radius: 6px;
    border: 1px solid #3d3d3d;
    border: 1px solid rgba(255,255,255,.15);
    -moz-box-shadow: 0 2px 3px 0 rgba(0,0,0,.1) inset;
    -webkit-box-shadow: 0 2px 3px 0 rgba(0,0,0,.1) inset;
    box-shadow: 0 2px 3px 0 rgba(0,0,0,.1) inset;
    font-family: 'PT Sans', Helvetica, Arial, sans-serif;
    font-size: 14px;
    color: #fff;
    text-shadow: 0 1px 2px rgba(0,0,0,.1);
    -o-transition: all .2s;
    -moz-transition: all .2s;
    -webkit-transition: all .2s;
    -ms-transition: all .2s;
}
.login .content button{
    cursor: pointer;
    width: 300px;
    height: 44px;
    margin-top: 25px;
    padding: 0;
    background: #53d192;
    -moz-border-radius: 6px;
    -webkit-border-radius: 6px;
    border-radius: 6px;
    border: 1px solid #53d192;
    -moz-box-shadow: 0 15px 30px 0 rgba(255,255,255,.25) inset, 0 2px 7px 0 rgba(0,0,0,.2);
    -webkit-box-shadow: 0 15px 30px 0 rgba(255,255,255,.25) inset, 0 2px 7px 0 rgba(0,0,0,.2);
    box-shadow: 0 15px 30px 0 rgba(255,255,255,.25) inset, 0 2px 7px 0 rgba(0,0,0,.2);
    font-family: 'PT Sans', Helvetica, Arial, sans-serif;
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    text-shadow: 0 1px 2px rgba(0,0,0,.1);
    -o-transition: all .2s;
    -moz-transition: all .2s;
    -webkit-transition: all .2s;
    -ms-transition: all .2s;
}
.message{
    z-index: 90000;
    top: 10px;
    left: 0px;
    right: 0px;
    width: 300px;
    margin: 0 auto;
    box-sizing: border-box;
    border-radius: 4px;
    position: fixed;
    background-color: #fff;
    overflow: hidden;
    opacity: 1;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-align: center;
    align-items: center;
    transition: opacity .2s;    
}
.message .success{
	background-color: #f0f9eb;
	color: #67c23a;
	padding: 8px 16px;
    width: 100%;
}
.message .info{
	background-color: #f4f4f5;
	color: #909399;
	padding: 8px 16px;
	width: 100%;
}
.message .warning{
	background-color: #fdf6ec;
	color: #e6a23c;
	padding: 8px 16px;
	width: 100%;
}
.message .error{
	background-color: #fef0f0;
	color: #f56c6c;
	padding: 8px 16px;
	width: 100%;
}
</style>
</body>
</html>