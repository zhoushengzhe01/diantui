<?php
namespace app\Controller;

use app\Controller;
use app\Config\AppConfig;
use app\Helpers\Helper;
use app\Model\Setting;
use app\Model\AdvertiserAds;
use Memcache;
use Redis;

class SkipController
{
    protected static $debag = false;
    protected static $client;     //客户端信息
    protected static $memcache;   //缓存信息
    protected static $redis;      //缓存信息

    public function __construct()
    {
        $this->init();
    }

    //初始化
    public function init()
    {
        //屏蔽程序直接请求
        if(empty(Helper::server('http_user_agent'))){
            die('15001');
        }
        //屏蔽搜索引擎蜘蛛
        if(preg_match("/(googlebot|baiduspider|360spider|bingbot)/i", Helper::server('http_user_agent'))) {
            die('15002');
        }
        //禁止访问系统
        if(!in_array(Helper::getSystem(), AppConfig::get('allowSystem'))){
            die('15003');
        }

        self::$client['isWechat'] = Helper::isWechat();
        self::$client['isQQAPP'] = Helper::isQQAPP();
        self::$client['ip'] = Helper::getClientIp();
        self::$client['system'] = Helper::getSystem();
        self::$client['source'] = parse_url(Helper::server('http_referer'));
        
        if(!empty(Helper::server('http_referer')))
        {
            if( count(self::$client['source'])<2 )
            {
                die('15004');
            }

            //后缀
            $domainArray = explode(".", self::$client['source']['host']);
            $count = count($domainArray);
            $suffix = $domainArray[$count-2] . '.' . $domainArray[$count-1];    //两个后缀
            if(!in_array($suffix, AppConfig::get('twosuffix')))
            {
                $suffix = $domainArray[$count-1];   //一个后缀
                
                if($suffix)
                {
                    if(!in_array($suffix, AppConfig::get('suffix')))
                    {
                        die('15005');
                    }
                    else
                    {
                        self::$client['source']['domain'] = $domainArray[$count-2] . '.' . $suffix;
                    }
                }
            }
            else
            {
                self::$client['source']['domain'] = $domainArray[$count-3] . '.' . $suffix;
            }
        }
        
        self::$memcache = new Memcache;
        self::$memcache->pconnect(AppConfig::get('memcache.host'), AppConfig::get('memcache.port'));

        self::$redis = new Redis;
        self::$redis->pconnect('127.0.0.1', 6379, 3600);
    }

    //Memcache 储层
    public function getCachedData($key, $minutes, $paramet, $callback)
    {
        if(true)
        {
            $results = self::$memcache->get($key);

            if(empty($results))
            {
                $results = $callback($paramet);
                //有异常用短连接获取第二次
                if(@$results['state']===false)
                {
                    self::$redis = new Redis;
                    self::$redis->connect('127.0.0.1', 6379);
                    $results = $callback($paramet);
                }

                if( isset($results['state']) && $results['state']==false )
                {
                    self::$memcache->set($key, $results, MEMCACHE_COMPRESSED, 2);
                }
                else
                {
                    self::$memcache->set($key, $results, MEMCACHE_COMPRESSED, intval($minutes * 60));   //设置缓存20分钟
                }
            }
            //有异常从源头切除
            if(@$results['state']===false)
            { 
                die("console.log('".$results['message']."')");
            }
            return $results;
        }
    }

    //普通广告
    public function returnSkip()
    {
        if( !($data = Helper::decode(Helper::request('se'))) )
        {
            die;
        }

        $advertiser_ads = explode(',', $data['adids']);

        $advertiserAds = $this->getCachedData('advertiser_ads', 5, [], function($paramet)
        {
            //广告主广告
            $result = self::$redis->hgetall('advertiser_ads');
            $advertiserAds = [];
            foreach($result as $key=>$val)
            {
                $ad = json_decode($val, true);
                if($ad['put_type']=='1' || $ad['put_type']=='3')
                {
                    $advertiserAds[] = $ad;
                }
            }
            return $advertiserAds;
        });

        #排除
        $sum_weight = 0;
        foreach($advertiserAds as $key=>$val)
        {
            if( !in_array($val['id'], $advertiser_ads) )
            {
                unset($advertiserAds[$key]); continue;
            }

            //权重
            if($val['is_hour_weight']=='1'){
                $weight = intval($val['hour_weight'][intval(date('H'))]);
            }else{
                $weight = intval($val['weight']);
            }
            $sum_weight += $weight;
        }

        if(count($advertiserAds) <= 0)
        {
            die('15006');
        }

        //摇号
        $rand = mt_rand(1, $sum_weight);
        $weight = 0;

        foreach($advertiserAds as $key=>$val)
        { 
            if($val['is_hour_weight']=='1'){

                $weight += intval($val['hour_weight'][intval(date('H'))]);
            }else{

                $weight += intval($val['weight']);
            }

            if($rand <= $weight){

                $advertiserAd = $val;
                break;
            }
        }
        self::$redis->close();

        //微信遮罩
        if(self::$client['isWechat']=='1' && $advertiserAd['is_wechat_out_skip']=='1')
        {
            require '../script/returnSkip.php';
            die;
        }
        
        //直接跳转
        header("Location: ".$advertiserAd['link']);
        die;
    }
}