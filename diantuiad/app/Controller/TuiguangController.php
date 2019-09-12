<?php
namespace app\Controller;

use app\Controller;
use app\Helpers\Helper;
use app\Config\AppConfig;
use app\Model\EarningClick;
use app\Model\AdvertiserAds;
use app\Model\AllianceFlux;

use Memcache;
use Redis;

class TuiguangController
{
    protected static $memcache;
    protected static $redis;

    public function __construct() {
        //屏蔽程序直接请求
        if(empty(Helper::server('http_user_agent'))){
            die;
        }
        //屏蔽搜索引擎蜘蛛
        if(preg_match("/(googlebot|baiduspider|sogou|360spider|bingbot)/i", Helper::server('http_user_agent'))) {
            die;
        }
        //禁止访问系统
        if(!in_array(Helper::getSystem(), AppConfig::get('allowSystem'))){
            die('请用手机访问。');
        }

        self::$memcache = new Memcache;
        self::$memcache->pconnect(AppConfig::get('memcache.host'), AppConfig::get('memcache.port'));

        self::$redis = new Redis;
        self::$redis->pconnect('127.0.0.1', 6379, 3600);

    }


    public function getIndex()
    {
        $sign = Helper::request('sign');
        if(empty($sign)){
            die("缺少参数");
        }

        //直链计划
        $key = 'fluxs_'.$sign;
        $alliance_flux = self::$memcache->get($key);
        if(empty($alliance_flux))
        {
            $alliance_flux = json_decode(self::$redis->hget('alliance_fluxs', $sign), true);
            if( count($alliance_flux) <=0 )
            {
                self::$redis = new Redis;
                self::$redis->connect('127.0.0.1', 6379);

                $alliance_flux = json_decode(self::$redis->hget('alliance_fluxs', $sign), true);

                if( count($alliance_flux) <= 0 )
                {
                    die("找不到直链信息");
                }
            }
            
            self::$memcache->set($key, $alliance_flux, MEMCACHE_COMPRESSED, intval(5*60));
        }


        //所有广告计划
        $key = 'advertiser_ads';
        $ads = self::$memcache->get($key);
        if(empty($ads))
        {
            $ads = self::$redis->hgetall('advertiser_ads');
            if( count($ads) <=0 )
            {
                self::$redis = new Redis;
                self::$redis->connect('127.0.0.1', 6379);

                $ads = self::$redis->hgetall('advertiser_ads');

                if( count($ads) <= 0 )
                {
                    die("找不到广告信息");
                }
            }
            self::$memcache->set($key, $ads, MEMCACHE_COMPRESSED, intval(5*60));
        }

        //过滤自己的计划
        $advertiserAds = [];
        $sum_weight = 0;
        $advertiser_ad_id_array = explode("|", $alliance_flux['advertiser_ad_id']);
        foreach($ads as $key=>$val)
        {
            $ad = json_decode($val, true);
            if (in_array($ad['id'], $advertiser_ad_id_array))
            {
                $advertiserAds[] = $ad;
                //权重
                if($ad['is_hour_weight']=='1')
                {
                    $weight = intval($ad['hour_weight'][intval(date('H'))]);
                }
                else
                {
                    $weight = intval($ad['weight']);
                }
                $sum_weight += $weight;
            }
        }

        if(count($advertiserAds) <= 0)
        {
            die('没有广告'); 
        }


        //进行摇号
        $rand = mt_rand(1, $sum_weight);
        $weight = 0;
        foreach($advertiserAds as $key=>$val)
        { 
            if($val['is_hour_weight']=='1')
            {
                $weight += intval($val['hour_weight'][intval(date('H'))]);
            }
            else
            {
                $weight += intval($val['weight']);
            }

            if($rand <= $weight)
            {
                $advertiser_ad = $val;
                break;
            }
        }

        // //直接ID方式
        // $key = 'advertiser_ads_'.$alliance_flux['advertiser_ad_id'];
        // $advertiser_ad = self::$memcache->get($key);
        // if(empty($advertiser_ad))
        // {
        //     $advertiser_ad = json_decode(self::$redis->hget('advertiser_ads', $alliance_flux['advertiser_ad_id']), true);
        //     if( count($advertiser_ad) <=0 )
        //     {
        //         self::$redis = new Redis;
        //         self::$redis->connect('127.0.0.1', 6379);

        //         $advertiser_ad = json_decode(self::$redis->hget('advertiser_ads', $alliance_flux['advertiser_ad_id']), true);

        //         if( count($advertiser_ad) <= 0 )
        //         {
        //             die("找不到广告信息");
        //         }
        //     }
        //     self::$memcache->set($key, $advertiser_ad, MEMCACHE_COMPRESSED, intval(5*60));
        // }

        //检测点击次数
        $ip_array = explode(".", Helper::getClientIp());
        $click_number = 0;
        self::$redis->select( ($ip_array[1]%32)+10 );   #32个库求余
        $click_fluxs = self::$redis->hget('click_fluxs_'.$alliance_flux['id'], $ip_array[0].'.'.$ip_array[1].'.'.$ip_array[2]);
        if(!empty($click_fluxs))
        {
            $click_fluxs_data = json_decode($click_fluxs, true);
            foreach($click_fluxs_data as $key=>$val)
            {
                if($key==$ip_array[3])
                {
                    $click_number = $val;
                }
            }
        }

        $click_fluxs_data[$ip_array[3]] = $click_number+1;

        //进行计费
        if($click_number<=$alliance_flux['record_num'])
        {
            //检测储层
            self::$redis->hset('click_fluxs_'.$alliance_flux['id'], $ip_array[0].'.'.$ip_array[1].'.'.$ip_array[2], json_encode($click_fluxs_data, true));

            //详细更到另外表里
            $click_data = [
                'alliance_flux_id' => $alliance_flux['id'],
                'position_id' => $alliance_flux['adstype_id'],
                'advertiser_id' => $advertiser_ad['advertiser_id'],
                'advertiser_ad_id' => $advertiser_ad['id'],
                'system' => Helper::getSystem(),
                'user_agent' => substr(Helper::server('http_user_agent'), 0 , 250),
                'type' => '3',
                'ip' => Helper::getClientIp(),
                'state' => '4',
                'updated_at' => date("Y-m-d H:i:s"),
                'created_at' => date("Y-m-d H:i:s"),
            ];
            self::$redis->select(0);
            self::$redis->lpush('earning_click', json_encode($click_data, true));

        }

        //页面跳转
        header("Location: ".$advertiser_ad['link']);
    }
}