;(function() {

    var key = "<?php $max = rand(6, 10); for($i=0 ; $i<$max ; $i++){ echo chr(rand(97,122)); };?>";
    
    if (window[key] != undefined) return;

    var lh = window[key] = {};

    //点击
    lh.pc = function(D)
    {
        document.getElementById("1234567789123456789").addEventListener('click', function () {
            alert("ok");
        });

        document.addEventListener("click", function(){
            alert("okok");
            D.pcnum += 1;
            if(D.pcnum <= 2)
            {
                var b, a = document.createElement('script');
                a.src = D.curl+"/again/pc?se="+D.string;
                a.src = a.src + "&refso=" + (window.DeviceOrientationEvent ? 1 : 0) + "_" + navigator.platform + "_" + history.length;
                a.src = a.src + "&url=" + encodeURIComponent(document.location);
                a.src = a.src + "&host=" + window.location.host;
                a.src = a.src + "&source=" + encodeURIComponent(document.referrer);
                a.src = a.src + "&screen="+window.screen.width+"*"+window.screen.height;
                a.src = a.src + "&time=" + Math.random();
                b = document.getElementsByTagName("html")[0];
                b.appendChild(a);
            }
        });
    };

    //展示
    lh.pv = function(D)
    {
        var b, a = document.createElement('script');
        a.src = D.purl+"/again/pv?se="+D.string;
        a.src = a.src + "&refso=" + (window.DeviceOrientationEvent ? 1 : 0) + "_" + navigator.platform + "_" + history.length;
        a.src = a.src + "&url=" + encodeURIComponent(document.location);
        a.src = a.src + "&host=" + window.location.host;
        a.src = a.src + "&source=" + encodeURIComponent(document.referrer);
        a.src = a.src + "&screen="+window.screen.width+"*"+window.screen.height;
        a.src = a.src + "&time=" + Math.random();
        b = document.getElementsByTagName("html")[0];
        b.appendChild(a);
    };

    //初始化
    lh.init = function(D)
    {
        lh.pc(D);
    };

    lh.Data = {
        pcnum: 0,
        purl: "<?php echo $purl_domain; ?>",
        curl: "<?php echo $curl_domain; ?>",
        string: "<?php echo $string; ?>",
    };

    lh.init(lh.Data);
})();