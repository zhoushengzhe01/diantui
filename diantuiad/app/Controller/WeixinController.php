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

            $url = urldecode($date[0]);
            $is_cover = $date[1];
            $time = $date[2];
            $secretkey = $date[3];

            require '../script/weixin-anquan.php';

            // if(Helper::getClientIp()=='122.55.213.160')
            // {
            //     require '../script/weixin-anquan.php';
            // }
            // else
            // {
            //     require '../script/weixin-anquan.php';
            //     // require '../script/weixin-new.php';
            // }
            
            #
        }
    }
}