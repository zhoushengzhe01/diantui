<?php
if(self::$client['system']=='Android')
{
    #别人家跳转地址注意保存
    #http://jt.zhnqiauto.com/jt.php?jt=http://www.baodu.com
    header("Accept-Ranges: bytes 0-1/1");
    header("Connection: keep-alive");
    header("Content-Disposition: attachment;filename=1579.apk");
    header("Content-Type: text/plain;charset=UTF-8");
    header("X-Daa-Tunnel: hop_count=3");
    die;
}
?>
<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=0">
<meta name="renderer" content="webkit">
<title>请点击右上角，用浏览器打开</title>
</head>
<body>
    <style>
    * {
        margin: 0;
        padding: 0;
    }
    html, body{
        height: 100%;
        width: 100%;
    }
    .module{
        overflow: auto;
        -webkit-overflow-scrolling:touch;
        width:100%;
        height:100%;
    }
    iframe{
        width: 1px;
        min-width: 100%;
        height: 100%;
        min-height: 100%;
    }
    </style>
    <div class="module" >
        <img src="https://image.xcxzxc.cn/images/<?= $advertiserAd['id']; ?>.jpg" style="width:100%; height:100%"/>
    </div>

    <!-- 我们的遮罩 -->
    <?php
    if($advertiserAd['is_wechat_cover']=='1')
    {
    ?>
        <style>
        #weixin-tip{position: fixed; left:0; top:0; background: rgba(0,0,0,0.7); filter:alpha(opacity=80); width: 100%; height:100%; z-index: 100;} 
        #weixin-tip p{text-align: center; margin-top: 10%; padding:0 5%;}
        </style>
        <div id="weixin-tip">
            <p><img src="https://image.xcxzxc.cn/images/live_weixin.png" alt="微信打开" style="max-width: 100%; height: auto;"></p>
        </div>
    <?php
    }
    ?>
</body>
</html>