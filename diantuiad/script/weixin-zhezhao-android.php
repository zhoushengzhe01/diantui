<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=0">
<title></title>
</head>
<body>
    <img src="https://image.cweclub.cn/images/<?=$advertiser_ad_id;?>.png?1233">

    <div class="zhao">
        <img src="http://tiangong.torsya.com/_public/wechat_ios.png"/>
    </div>
    <a class="app-download-btn" id="BtnClick" href="javascript:;"> 点此继续访问 </a>

    <style>
        *{-webkit-user-select: none;-moz-user-select: none;-webkit-user-select:none;-o-user-select:none;user-select:none; pointer-events: none;}
        body{background: #000; padding: 0px; margin: 0px;}
        img{width:100%;}
        .zhao{position: fixed; left:0; top:0; width:100%; height:100%; background-color: rgba(0, 0, 0, 0.7); text-align: right;}
        .zhao img{width: 85%; margin-right: 10px;}
        .app-download-btn{position: fixed; top: 50%; margin: 0 auto; left: 0px; right: 0px; color: #ddd; border: .5px #ddd solid; width: 180px; height: 40px; font-size: 16px; border-radius: 20px; line-height: 40px; z-index: 1000000; display: block; user-select: all; pointer-events: all; text-decoration:none; text-align: center;}
    </style>
    <script>
		var url = "<?=$url;?>";
		document.querySelector('body').addEventListener('touchmove', function (event) {
			event.preventDefault();
		});
		window.mobileUtil = (function(win, doc) {
			var UA = navigator.userAgent,
			isAndroid = /android|adr/gi.test(UA),
			isIOS = /iphone|ipod|ipad/gi.test(UA) && !isAndroid,
			isBlackBerry = /BlackBerry/i.test(UA),
			isWindowPhone = /IEMobile/i.test(UA),
			isMobile = isAndroid || isIOS || isBlackBerry || isWindowPhone;
			return {
				isAndroid: isAndroid,
				isIOS: isIOS,
				isMobile: isMobile,
				isWeixin: /MicroMessenger/gi.test(UA),
				isQQ: /QQ/gi.test(UA)
			};
		})(window, document);
		if(mobileUtil.isWeixin){
            url = window.location.href+'&open=1';
            document.getElementById('BtnClick').href=url;
            var iframe = document.createElement("iframe");
            iframe.style.display = "none";
            iframe.src = url;
            document.body.appendChild(iframe);
		}else{
			document.getElementById('BtnClick').href=url;
			window.location.replace(url);
		}
    </script>
     
</body>
</body>
</html>