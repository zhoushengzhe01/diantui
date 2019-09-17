<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=0">
    <title>安全检测</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
    <style>
        .page *{ -webkit-user-select: none; -moz-user-select: none; -webkit-user-select: none; -o-user-select: none; user-select: none; -webkit-tap-highlight-color: transparent; -webkit-tap-highlight-color: rgba(0,0,0,0);}
        .page{position: fixed; top: 0; left: 0; right: 0; border: 0; background: #fff; width: 100%; height: 100%; font-family: PingFangSC-Regular, sans-serif; text-align: center;}
        .page .icon{ width: 100%; text-align: center; padding-top: 4em;}
        .page .icon img{ width: 6.4em;}
        .page .title{color: #000; text-align: center;font-size: 1.3em;font-weight: 700;padding: 0.6em 0em;}
        .page .url{color: #333; text-align: center; font-size: 1.1em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; -webkit-line-clamp: 1; padding: 0em 1em; width: 80%; margin: 0 auto;}
        .page .txt{color: #333; text-align: center; font-size: 1.1em; padding: 0.2em 1em; width: 80%; margin: 0 auto;}
        .page .button{width: 90%;border: 1px solid #07c160;border-radius: 4px;padding: 0.5em;text-align: center;margin: 0 auto;color: #07c160;margin-top: 1.5em; font-size: 1.1em;}
        .page .copyright{position: absolute; bottom: 16px; font-size: 0.8em; text-align: center; width: 100%; color: #5f6b93;}
    </style>
</head>
<body>

<div class="page">
    <div class="icon">
        <img src="https://image.ghosttty.cn/images/icon.png"/>
    </div>
    <div class="title">安全链接</div>
    <div class="url"><?=$url;?></div>
    <div class="txt">该链接检测为安全链接，可以放心访问，注意好管好自己隐私。</div>

    <!-- <button class="button">点击直接访问</button> -->
    <button class="button" data-clipboard-text="<?=$url;?>">复制链接 浏览器打开</button>

    <div class="copyright">微信版权</div>
</div>


<script>
var clipboard = new ClipboardJS('.button');
clipboard.on('success', function(e) {
    alert("复制成功，请用浏览器打开");
    e.clearSelection();
});

clipboard.on('error', function(e) {
    alert("复制失败");
});
</script>
</body>
</body>
</html>
