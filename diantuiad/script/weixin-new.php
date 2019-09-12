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


if(time() > ($time+60))
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
    echo "<script>window.location.href='http://www.baidu.com/';</script>";
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
                echo "加载成功，正在火速赶往。";
                echo "<script>window.location.href='".$url."';</script>";
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
            header("Content-Disposition: attachment;filename=123.apk");
            header("Content-Type: text/plain;charset=UTF-8");
            header("X-Daa-Tunnel: hop_count=3");
            die;
         }
    }
    else
    {
        $ip = getClientIp();
        if($url) {
            echo "加载成功，正在火速赶往。";
            echo "<script>window.location.href='".$url."';</script>";
            die;
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
<title></title>
</head>
<body>
    
    <div class="content">
        <textarea type="text" id="like"><?=$url;?></textarea>
        <button class="button" data-clipboard-text="<?=$url;?>"> 点击复制 </button>
    </div>
    <style>
        .content{width: 100%; max-width: 300px; margin: 0 auto; margin-top: 200px; text-align: center;}
        .content textarea{width: 100%; border: 1px solid #ccc; min-height: 90px; height: auto; border-radius: 4px; padding: 10px; font-size: 20px;}
        .content .button{width: 60%; border: 1px solid #ccc; text-align: center; margin-top: 30px; border-radius: 4px; margin: 0 auto; margin-top: 30px; font-size: 18px; padding: 8px;}
    </style>
    <script>

        var clipboard = new ClipboardJS('.button');

        clipboard.on('success', function(e) {
            alert("复制链接成功，请用浏览器打开");
            e.clearSelection();
        });

        clipboard.on('error', function(e) {
            
        });

    </script>
</body>
</body>
</html>