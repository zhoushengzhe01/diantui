<?php
namespace app\Controller;

class AdController
{
    //普通广告
    public function getCoding($webmaster_ad_id)
    {
        echo ";(function() { var body = document.getElementsByTagName('body')[0]; var script = document.createElement('script'); script.type = 'text/javascript'; script.src = 'https://dg.xcxzxc.cn/coding/ads/".$webmaster_ad_id."?time=' + Math.random(); body.appendChild(script); })();";
    }
}