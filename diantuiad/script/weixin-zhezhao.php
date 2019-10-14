<!DOCTYPE html>
<html>
<head lang="en">
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=0">
<title></title>
</head>
<body onload="on_load()">
    <img src="https://image.cweclub.cn/images/<?=$advertiser_ad_id;?>.png?1233">

    <div class="zhao">
        <img src="http://tiangong.torsya.com/_public/wechat_ios.png"/>
    </div>

    <style>
        *{-webkit-user-select: none;-moz-user-select: none;-webkit-user-select:none;-o-user-select:none;user-select:none; pointer-events: none;}
        body{background: #000; padding: 0px; margin: 0px;}
        img{width:100%;}
        .zhao{position: fixed; left:0; top:0; width:100%; height:100%; background-color: rgba(0, 0, 0, 0.7); text-align: right;}
        .zhao img{width: 85%; margin-right: 10px;}
    </style> 

    <script>
        function on_load()
		{
			var script = document.createElement('script');
			script.src = "https://v1.cnzz.com/z_stat.php?id=1278102391&web_id=1278102391";
			script.async = true;
			document.body.appendChild(script);

            window.addEventListener("popstate", function(e) {
                if(confirm("超级好玩，超级赚钱的游戏，真打算离开？"))
                {
                    window.history.back(-1);  
                }
                else
                {
                    window.history.go(0) 
                }
            }, false);
            window.history.pushState(null, null, null);
        }
    </script>
</body>
</body>
</html>