<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>用户注册</title>
    <meta http-equiv="Cache-Control" content="no-transform" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=yes" />
    <link rel="stylesheet" href="{{$group->public}}css/bootstrap.min.css">
    <link rel="stylesheet" href="{{$group->public}}css/all.css">
    <script src="{{$group->public}}js/jquery-1.12.4.min.js"></script>
    <script src="{{$group->public}}js/jquery.cookie.js"></script>
</head>
<body class="login-page">

<div class="logo"><a href="/"><img src="{{$group->public}}images/login-logo.png" alt=""></a></div>
<div class="con register">
    <div class="top">
        <h5>用户注册</h5>
        <div class="check">
            <div class="form_cont">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" name="type" value="3" id="customRadioInline1" class="custom-control-input web juser_type" onClick="type_select(3)">
                    <label class="custom-control-label" for="customRadioInline1">网站主</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" name="type" value="4" id="customRadioInline2" class="custom-control-input advert juser_type" onClick="type_select(4)">
                    <label class="custom-control-label" for="customRadioInline2">广告主</label>
                </div>
            </div>
            <div class="input">
                <div class="list">
                    <p>用户名称</p>
                    <input type="text" name="username" placeholder="用户名称">
                </div>
                <div class="list">
                    <p>密码</p>
                    <input type="password" name="password" placeholder="密码必须大于6位 并 小于18位">
                </div>
                <div class="list">
                    <p>确认密码</p>
                    <input type="password" name="setpassword" placeholder="密码必须大于6位 并 小于18位">
                </div>
                <div class="list">
                    <p>联系人</p>
                    <input type="text" name="nickname" placeholder="请输入联系人">
                </div>
                <div class="list advertiser" style="display: none;">
                    <p>公司名称</p>
                    <input type="text" name="company_name" placeholder="请输入公司名称">
                </div>
                <div class="list">
                    <p>手机号码</p>
                    <input type="text" name="mobile" placeholder="请输入常用手机号，方便及时与您联系">
                </div>
                <div class="list">
                    <p>QQ</p>
                    <input type="text" name="qq" placeholder="请输入常用QQ号码，找回密码需要">
                </div>
                <div class="list code">
                    <p>验证码</p>
                    <div id="v_container" class="yzm"><img src="/captcha/math.json" width="130" onclick="this.src='/captcha/math.json?'+Math.random()"></div>
                    <input type="text" name="captcha" id="captcha" placeholder="请输入验证码" />
                </div>
                <input id="f_submit" type="submit" class="log jbtn_register" value="注 册" />
            </div>
        </div>
    </div>
    <div class="bottom">
        <p>已有账号？<a href="/login">立即登录</a></p>
    </div>
</div>
<p class="copy">&copy;2018 dtmob.cn &nbsp;点推网络 &nbsp;版权所有 &nbsp;意见邮件：dtmobcn@gmail.com &nbsp;投诉QQ：2322279938</p>

<script>
//读取地址栏参数
function getQueryString(name) { 
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
    var r = window.location.search.substr(1).match(reg); 
    if (r != null) return unescape(r[2]);
    return null;
}

//网站主广告主切换
function type_select(type)
{
    $("input[name='type'][value="+type+"]").attr("checked",true);
    if(type==3){
        $(".advertiser").hide();
    }
    if(type==4){
        $(".advertiser").show();
    }
}
type_select(0);

//提示
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

$(function(){
    //客服推广
    var uid = parseInt(getQueryString('uid'));
    if(uid){
        $.cookie('uid', uid, { expires: 2 });
    }
    //站长推广
    var wid = parseInt(getQueryString('wid'));
    if(wid){
        $.cookie('wid', wid, { expires: 2 });
    }

    $("#f_submit").click(function(){

        var type = $("input[name='type']:checked").val();

        if(type=='3') {
            var url = '/webmaster/register.json';
        } else if(type=='4') {
            var url = '/advertiser/register.json';
        } else {
            message('warning', '请选择注册网站主，或广告主');
            return false;
        }

        var paramet = {
            webmaster_id: parseInt($.cookie('wid')),
            agent_id: '',
            busine_id: parseInt($.cookie('uid')),
            service_id: parseInt($.cookie('uid')),
            username: $("input[name='username']").val(),
            password: $("input[name='password']").val(),
            setpassword: $("input[name='setpassword']").val(),
            nickname: $("input[name='nickname']").val(),
            company: $("input[name='company_name']").val(),
            mobile: $("input[name='mobile']").val(),
            qq: $("input[name='qq']").val(),
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
            success: function(data){
                message('success', '注册成功，请登陆');
                window.location.href = '/login';
            },
            error: function(data) {
                var data = JSON.parse(data.responseText);
                message('warning', data.message);
            }
        });

    });

});

</script>

</body>
</html>