<?php
namespace app\Controller;

use app\Controller;
use app\Helpers\Helper;
use Redis;

class WeixinController 
{
    //普通广告
    public function getWeixin()
    {
        if( !empty($_GET['string']) )
        {
            $date = Helper::decode($_GET['string']);

            if(count($date)<2)
            {
                die;
            }

            #参数获取
            $url = urldecode($date[0]);
            $is_cover = $date[1];
            $time = $date[2];
            $secretkey = $date[3];
            $advertiser_ad_id = $date[4];

            #限时访问
            // if(time() > ($time+120))
            // {
            //     if($time!=123)
            //     {
            //         die;
            //     }   
            // }

            #加密验证
            if(md5(md5($time.'&dtmob@123')) != $secretkey )
            {
                if($time!=123)
                {
                    die;
                }
            }

            #无来源屏蔽
            // if($_SERVER["HTTP_REFERER"])
            // {
            //     die;
            // }

            #无user_agent屏蔽
            if( empty($_SERVER['HTTP_USER_AGENT']) )
            {
                die;
            }

            #屏蔽蜘蛛
            if(preg_match("/(googlebot|baiduspider|sogou|360spider|bingbot)/i", $_SERVER['HTTP_USER_AGENT'])) {
                die;
            }

            #屏蔽PC访问
            if( !$this->is_mobile() )
            {
                header("Location: http://www.baidu.com/");
                die;
            }
            else
            {
                #是微信
                if($this->get_client()=='wechat' || $this->get_client()=='app')
                {
                    #安卓做跳转 直接跳转
                    if($this->is_ios())
                    {
                        if($is_cover=='0')
                        {
                            header("Location: ".$url);
                            die;
                        }
                    }
                    else
                    {
                        if(!empty($_GET['open']) && $_GET['open']==1){
                            if($this->get_client()=='wechat'){
                                header("HTTP/1.0 200 Partial Content");
                            }else{
                                header("HTTP/1.0 206 Partial Content");
                            }
                            header("Content-Disposition: attachment; filename=\"load.doc\"");
                            header("Content-Type: application/vnd.ms-word;charset=utf-8");
                        }
                        require '../script/weixin-zhezhao-android.php';
                        die;

                        // if(Helper::getClientIp()=='103.5.62.134' || Helper::getClientIp()=='122.55.213.160')
                        // {
                            
                        //}

                        // #别人家跳转地址注意保存
                        // #http://jt.zhnqiauto.com/jt.php?jt=http://www.baodu.com
                        // header("HTTP/1.0 206 Partial Content");
                        // header("Accept-Ranges: bytes 0-1/1");
                        // header("Connection: keep-alive");
                        // header("Content-Disposition: attachment;filename=1579.apk");
                        // header("Content-Type: text/plain;charset=UTF-8");
                        // header("X-Daa-Tunnel: hop_count=3");
                        // die;
                    }
                }
                else
                {
                    if($url) {
                        header("Location: ".$url);
                    }
                    die;
                }
            }
            
            require '../script/weixin-zhezhao.php';
        }
    }


    /**
     * 获得客户端
     * 类型：微信，浏览器，APP，蜘蛛
     */
    public function get_client()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        
        if(strpos($userAgent, 'MicroMessenger') == true)
        {
            return 'wechat';
        }
        else if(strpos($userAgent, 'Browser') == true)
        {
            return 'browser';
        }
        else if(preg_match("/(googlebot|baiduspider|spider|360spider|bingbot)/i", $userAgent) == true)
        {
            return 'spider';
        }
        else if( preg_match("/(App@Client)/i", $userAgent) == true )
        {
            return 'app';
        }
        else
        {
            return false;
        }
    }

    public function is_mobile()
    {
        # 如果监测到是指定的浏览器之一则返回true
        $regex_match="/(nokia|iphone|android|ipad|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
        $regex_match .="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
        $regex_match .="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
        $regex_match .="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
        $regex_match .="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
        $regex_match .=")/i";

        # preg_match()方法功能为匹配字符，既第二个参数所含字符是否包含第一个参数所含字符，包含则返回1既true
        return preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
    }

    public function is_ios()
    {
        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){

            return true;

        } else {
        
            return false;
        }
    }
}