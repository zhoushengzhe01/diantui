<?php
namespace App\Http\Helpers;
use Illuminate\Support\Facades\Redis;

class Helper
{
    //IP定位城市库
    public static function getCityByip($ip)
    {
        $ipArr = explode('.', $ip);
        $data = Redis::hget('lhmob_iplibrary', $ipArr[0].'.'.$ipArr[1].'.'.$ipArr[2]);
        if(empty($data))
        {
            usleep(50000);
            $data = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip);
            if(!empty($data))
            {
                $data = json_decode($data, true);
                if(!empty($data['data']['country']))
                {
                    if($data['data']['country']=='中国')
                    {
                        $data = [
                            'ip'=>$ipArr[0].'.'.$ipArr[1].'.'.$ipArr[2],
                            'country'=>$data['data']['country'],
                            'region'=>$data['data']['region'],
                            'city'=>$data['data']['city'],
                            'isp'=>$data['data']['isp'],
                            'region_id'=>$data['data']['region_id'],
                            'city_id'=>$data['data']['city_id'],
                            'isp_id'=>$data['data']['isp_id'],
                        ];
                        Redis::hset('lhmob_iplibrary', $ipArr[0].'.'.$ipArr[1].'.'.$ipArr[2], json_encode($data, true));
                        
                        echo "远程协助\n";
                        return $data;
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return json_decode($data, true);
        }   
    }


    public static function getCityip($ip)
    {
        $data = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.$ip);

        $data = json_decode($data, true);

        return $data['data'];
    }

    public static function getClientIp()
    {
        if(!empty($_SERVER["X-Real-IP"]))
        {
            $cip = $_SERVER["X-Real-IP"];
        }
        else if(!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"]))
        {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        else
        {
            $cip = '';
        }
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);

        return $cip;
    }
    

    //获取手机型号
    public static function getMobileBrand($user_agent)
    {
        if(stripos($user_agent, "iPhone")!==false)
        {
            //return 'iPhone '.substr($user_agent, 20, -15);
            return 'iPhone';
        }
        else
        {
            if(stripos($user_agent, "SAMSUNG")!==false || stripos($user_agent, "Galaxy")!==false || strpos($user_agent, "GT-")!==false || strpos($user_agent, "SCH-")!==false || strpos($user_agent, "SM-")!==false)
                return 'SAMSUNG';
            elseif(stripos($user_agent, "Huawei")!==false || stripos($user_agent, "Honor")!==false || stripos($user_agent, "H60-")!==false || stripos($user_agent, "H30-")!==false || stripos($user_agent, "STF-")!==false)
                return 'Huawei';
            elseif(stripos($user_agent, "Lenovo")!==false) 
                return 'Lenovo';
            elseif(strpos($user_agent, "MI")!==false)
                return 'MI';
            elseif(strpos($user_agent, "HM NOTE")!==false || strpos($user_agent, "HM201")!==false)
                return 'HM';
            elseif(stripos($user_agent, "Coolpad")!==false || strpos($user_agent, "8190Q")!==false || strpos($user_agent, "5910")!==false) 
                return 'Coolpad';
            elseif(stripos($user_agent, "ZTE")!==false || stripos($user_agent, "X9180")!==false || stripos($user_agent, "N9180")!==false || stripos($user_agent, "U9180")!==false) 
                return 'ZTE';
            elseif(stripos($user_agent, "OPPO")!==false || strpos($user_agent, "X9007")!==false || strpos($user_agent, "X907")!==false || strpos($user_agent, "X909")!==false || strpos($user_agent, "R831S")!==false || strpos($user_agent, "R827T")!==false || strpos($user_agent, "R821T")!==false || strpos($user_agent, "R811")!==false || strpos($user_agent, "R2017")!==false) 
                return 'OPPO';
            elseif(strpos($user_agent, "HTC")!==false || stripos($user_agent, "Desire")!==false || stripos($user_agent, "P308L")!==false || stripos($user_agent, "g17")!==false) 
                return 'HTC';
            elseif(stripos($user_agent, "vivo")!==false) 
                return 'vivo';
            elseif(stripos($user_agent, "K-Touch")!==false) 
                return 'K-Touch';
            elseif(stripos($user_agent, "Nubia")!==false || stripos($user_agent, "NX50")!==false || stripos($user_agent, "NX40")!==false || stripos($user_agent, "NX569J")!==false) 
                return 'Nubia';
            elseif(strpos($user_agent, "M045")!==false || strpos($user_agent, "M032")!==false || strpos($user_agent, "M355")!==false || strpos($user_agent, "MZ-MX6")!==false || strpos($user_agent, "MZ-M5")!==false  || strpos($user_agent, "metal")!==false)
                return 'MZ';
            elseif(stripos($user_agent, "DOOV")!==false) 
                return 'DOOV';
            elseif(stripos($user_agent, "GFIVE")!==false) 
                return 'GFIVE';
            elseif(stripos($user_agent, "Gionee")!==false || strpos($user_agent, "GN")!==false || strpos($user_agent, "F103")!==false) 
                return 'Gionee';
            elseif(stripos($user_agent, "HS-U")!==false || stripos($user_agent, "HS-E")!==false) 
                return 'Hisense';
            elseif(stripos($user_agent, "Nokia")!==false) 
                return 'Nokia';
            elseif(stripos($user_agent, "Letv")!==false) 
                return 'Letv';
            else{
                return 'Unknown';
            }
        }
    }

    //获取浏览器
    public static function getInterval($second)
    {   
        if($second<50)
        {
            $second = ceil($second/5) * 5;
            return $second-5 .'-'. $second;
        }
        elseif($second<100)
        {   
            $second = ceil($second/10) * 10;
            return $second-10 .'-'. $second;
        }
        elseif($second<500)
        {   
            $second = ceil($second/20) * 20;
            return $second-20 .'-'. $second;
        }
        elseif($second<1000)
        {   
            $second = ceil($second/50) * 50;
            return $second-50 .'-'. $second;
        }
        else
        {
            return 1000 .'-'. 20000;
        }
    }

    //点击点击方位计算
    public static function getLocation($screen, $location)
    {
        $screenArr = explode("*", $screen);
        $locationArr = explode("*", $location);
        if(count($locationArr)==2 && count($locationArr)==2)
        {
            $screenWidth = intval($screenArr[0]);
            $locationWidth = intval($locationArr[0]);
            $screenHeight = intval($screenArr[1]);
            $locationHeight = intval($locationArr[1]);
            
            $width = ceil($locationWidth/($screenWidth/10));
            $height = ceil($locationHeight/($screenHeight/20));

            return $width.','.$height;
        }
        else
        {
            return '0,0';
        }
    }
}