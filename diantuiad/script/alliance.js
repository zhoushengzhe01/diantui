<?= $alliance['code'];?>;
;(function() {
    
    var key = parseInt(Math.random() * 1000000);

    if (window[key] != undefined) return;
    
    var lh = window[key] = {};

    lh.getCookie = function(name)
    {
        var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");

        if(arr=document.cookie.match(reg)){
            return unescape(arr[2]);
        }else{
            return '';
        }
    };
    lh.setCookie = function(name, value, second)
    {
        var exp = new Date();
        exp.setTime( exp.getTime() + second*1000);
        document.cookie = name + "=" + escape (value) + ";expires=" + exp.toGMTString();
    };
    lh.isCookie = function()
    {
        lh.setCookie('iscookie', '1');
        if( '1'!=lh.getCookie('iscookie')){
            return false;
        }else{
            return true;
        }
    };
    //创建
    lh.CScript = function(url)
    {
        var script = document.createElement('script');
        script.async = true;
        script.src = url;
        document.body.appendChild(script);
    };

    lh.show = function(D)
    {
        var num = 1;
        if(lh.isCookie())
        {
            var ip = lh.getCookie('is_repeat_ip_'+D.adid);
            if(ip){
                if(ip.indexOf(D.ip2long)>-1){
                    var num = 5;
                }else{
                    ip = ip+':'+D.ip2long;
                    lh.setCookie('is_repeat_ip_'+D.adid, ip, D.dis_time);
                }
            }else{
                ip = D.ip2long;
                lh.setCookie('is_repeat_ip_'+D.adid, ip, D.dis_time);
            }
        }

        if(parseInt(Math.random() * 100) < (100/num))
        {
            var url = D.purl+"/pv?se=" + D.string;
            url = url + "&refso=" + (window.DeviceOrientationEvent ? 1 : 0) + "_" + navigator.platform + "_" + history.length;
            url = url + "&url=" + encodeURIComponent(document.location);
            url = url + "&source=" + encodeURIComponent(document.referrer);
            url = url + "&screen=" + window.screen.width+"*"+window.screen.height;
            url = url + "&n=" + num;
            url = url + "&ifrom=1";
            url = url + "&time=" + Math.random();

            lh.CScript(url);
        }
    };
    //是否模拟器
    lh.IsMN = function()
    {
        if(navigator.platform.indexOf("Win") == 0 || navigator.platform.indexOf("Mac") == 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    };
    //初始化
    lh.init = function(D)
    {
        if(lh.IsMN())
        {

        }
        //else
        {
            lh.show(D);
            if(D.statis_code && parseInt(Math.random() * 100)<D.statis_code_ratio){
                lh.CScript(D.statis_code);
            }
        }
    };
    lh.Data = {
        purl: "<?php echo $purl_domain; ?>",
        string: "<?php echo $string; ?>",
        adid: "<?php echo $webmasterAd['id']; ?>",
        ip2long: "<?php echo ip2long(self::$client['ip']); ?>",
        statis_code: "<?php echo $webmasterAd['statis_code']; ?>",
        statis_code_ratio: "<?php echo $webmasterAd['statis_code_ratio']; ?>",
        dis_time: parseInt("<?php echo $distance_time; ?>"),
    };
    lh.init(lh.Data);
})();