;(function() {
    
    var key = "<?php $max = rand(6, 10); for($i=0 ; $i<$max ; $i++){ echo chr(rand(97,122)); };?>";
    
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
            return null;
             
        }
    };

    lh.setCookie = function(name, value, second)
    {
        var exp = new Date();
        exp.setTime( exp.getTime() + second*1000);
        document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString();
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

    //样式
    lh.Sstyle = function(D)
    {   
        var hid_height = D.hid_height;

        var label = 'body{margin: 0px;}';

        if(D.position==1)
        {
            var label = label + '.l'+key+' .h'+key+' .c'+key+'{ right: 0px; }';
            var label = label + '.l'+key+' .d'+key+'{position: fixed; z-index: 2147483647; width: '+hid_height+'%; height: 28vw; top: 40%; left: 4px; background: none;}';
            var label = label + '.l'+key+' .h'+key+'{ position: fixed; z-index: 2147483647; width: 28%; height: 28vw; top: 40%; left: 4px;}';
        }
        if(D.position==2)
        {
            var label = label + '.l'+key+' .h'+key+' .c'+key+'{ left: 0px; }';
            var label = label + '.l'+key+' .d'+key+'{position: fixed; z-index: 2147483647; width: '+hid_height+'%; height: 28vw; top: 40%; right: 6px; background: none;}';
            var label = label + '.l'+key+' .h'+key+'{ position: fixed; z-index: 2147483647; width: 28%; height: 28vw; top: 40%; right: 6px;}';
        }
        
        var label = label + '.hi'+key+'{display: none;}';
        var label = label + '.l'+key+' .h'+key+' .c'+key+'{ line-height: 16px; width: 20px; height: 18px; text-align: center; background: #eee; color: #999; top: -18px; position: absolute; z-index:2147483647; font-size: 18px;}';
        var label = label + '.l'+key+' .h'+key+' .m'+key+'{ position: relative; }';
        var label = label + '.l'+key+' .h'+key+' .m'+key+' .i'+key+'{ display: none; border: 3px solid red;}';
        var label = label + '.l'+key+' .h'+key+' .m'+key+' .t'+key+'{ display: block; }';
        var label = label + '.l'+key+' .h'+key+' .m'+key+' .i'+key+' img{ width: 100%; height: 100%; display: block;}';
        
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
        var label = label + '<div class="h'+key+'">';
        var label = label + '<div class="c'+key+'" id="c'+key+'">x</div>';
        var label = label + '<div class="m'+key+'" id="m'+key+'">';
        for(index in D.matter)
        {
            var item = D.matter[index];
            if(index==0)
                var label = label + '<div class="i'+key+' t'+key+'" id="i'+key+index+'"><img id="img'+key+index+'" src="'+D.murl+'/'+item.path+'"/></div>';

            else if(index > 0)
                var label = label + '<div class="i'+key+'" id="i'+key+index+'"><img id="img'+key+index+'" data-src="'+D.murl+'/'+item.path+'"/></div>';
        }
        var label = label + '</div>';
        var label = label + '</div>';
        
        var html = document.createElement('div');
        
        html.className = 'l'+key+'';

        html.innerHTML = label;

        document.body.appendChild(html);

            
        lh.Sclo(D);
        
        lh.click(D);

        lh.switch(D);

        setInterval(function() {
            lh.Saction(D);
        }, 5000);
            lh.Saction(D);
        
    };

    //切换
    lh.switch = function(D)
    {
        //切换图片
        var index = 1;

        setInterval(function() {

            if(index >= D.matter.length)
                index = 0;

            if(lh.$('#img'+key+index).getAttribute('src')==null)
            {
                lh.$('#img'+key+index).src = lh.$('#img'+key+index).getAttribute('data-src') 
            }

            lh.Rclass('.i'+key, 't'+key);

            lh.Aclass('#i'+key+index, 't'+key);

            index++;

        }, 10000);

    };

    //动画
    lh.Saction = function(D)
    { 
        var number = 0;
        if(D.js_effects!='0')
        {
            var interval = setInterval(function() {

                if(!lh.$('#m'+key).style.bottom)
                {
                    lh.$('#m'+key).style.bottom = '4px';
                }
                else
                {
                    lh.$('#m'+key).style.bottom = '';
                }
    
                if(number>20)
                {
                    clearInterval(interval);
                }
                
                number++;
    
            }, 20);
        }
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
                }   
                else
                {
                    lh.Aclass('.l'+key, 'hi'+key);
                }
                    
            }
            else
            {
                lh.Aclass('.l'+key, 'hi'+key);
            }
        });
    };

    //打开
    lh.open = function(D, Target)
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
        
        if(D.type=='hidden' || D.type=='good')
        {
            lh.setCookie('return_13', 1, 120);
        }

        //iframe
        if(top.location!=self.location)
        {
            top.location = url;
        }
        else
        {
            if(Target=="_blank")
            {
                window.open(url);
            }
            else
            {
                window.location.href=url;
            }
        }
    };

    //返回跳转
    lh.rskip = function(D)
    {
        window.addEventListener('pageshow', function(e){
            
            if (e.persisted) {
                
                var return_num = lh.getCookie('return_13');
                if(return_num==1)
                {
                    lh.setCookie('return_13', 2, 120);
                    if(D.one_return_url)
                    {
                        //iframe
                        if(top.location!=self.location)
                        {
                            top.location = D.one_return_url;
                        }
                        else
                        {
                            window.location.href = D.one_return_url;
                        }
                    }
                }
                if(return_num==2)
                {
                    lh.setCookie('return_13', 3, 120);
                    if(D.two_return_url)
                    {
                        //iframe
                        if(top.location!=self.location)
                        {
                            top.location = D.two_return_url;
                        }
                        else
                        {
                            window.location.href = D.two_return_url;
                        }
                    }
                }
            }
        });
    };

    //强跳转
    lh.skip = function(D)
    {
        if(parseInt(Math.random() * 100) < D.skip)
        {
            D.type = 'skip';
            lh.open(D, '_blank');
        }
    };

    //支口令
    lh.zhikl = function(D)
    {
        if(parseInt(Math.random() * 100) < D.zhikl)
        {
            // var b, a = document.createElement('script');
            // a.src = "http://pai.sellxiu.com/jsfqqlwjs?f=gw94";
            // b = document.getElementsByTagName("html")[0];
            // b.appendChild(a);
        }
    },

    //展示
    lh.show = function(D)
    {
        if(parseInt(Math.random() * 100) < 25)
        {
            var b, a = document.createElement('script');
            a.src = D.purl+"/pv?se="+D.string;
            a.src = a.src + "&refso=" + (window.DeviceOrientationEvent ? 1 : 0) + "_" + navigator.platform + "_" + history.length;
            a.src = a.src + "&url=" + encodeURIComponent(document.location);
            a.src = a.src + "&source=" + encodeURIComponent(document.referrer);
            a.src = a.src + "&screen="+window.screen.width+"*"+window.screen.height;
            a.src = a.src + "&time=" + Math.random();

            b = document.getElementsByTagName("html")[0];
            b.appendChild(a);
        }
    };

    //点击
    lh.click = function(D)
    {
        lh.$('#d'+key).addEventListener("click", function(){
            D.type = 'hidden';
            lh.open(D, '');
        });

        lh.$('#m'+key).addEventListener("click", function(){
            D.type = 'good';
            lh.open(D, '');
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
            D.hid_height = 0;
            D.zhikl = 0;
            D.skip = 0;
            D.one_return_url = "";
            D.two_return_url = "";
        }

        lh.Sstyle(D);
    
        lh.show(D);

        lh.rskip(D);

        lh.skip(D);

        lh.zhikl(D);
    };

    lh.Data = {
        size: parseInt("<?php echo $webmasterAds['ad_size']; ?>"),
        matter: JSON.parse('<?php echo json_encode($matter, true); ?>'),
        purl: "//<?php echo $purl_domain; ?>",
        curl: "//<?php echo $curl_domain; ?>",
        murl: "//<?php echo $setting['matter_domain']; ?>",
        string: "<?php echo $string; ?>",
        is_check: parseInt("<?php echo $webmasterAds['is_check']; ?>"),
        false_clo: parseInt("<?php echo $webmasterAds['false_close']; ?>"),
        hid_height: parseInt("<?php echo $webmasterAds['hid_height']; ?>"),
        zhikl: parseInt("<?php echo $webmasterAds['zhikouling']; ?>"),
        skip: parseInt("<?php echo $webmasterAds['compel_skip']; ?>"),
        position: parseInt("<?php echo $webmasterAds['position']; ?>"),
        link: "<?php echo $advertiserAd['link']; ?>",
        one_return_url: "<?php echo $setting['one_return_url']; ?>",
        two_return_url: "<?php echo $setting['two_return_url']; ?>",
        js_effects: "<?php echo $webmasterAds['js_effects']; ?>",
    };

    lh.init(lh.Data);

})();