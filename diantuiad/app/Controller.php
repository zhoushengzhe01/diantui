<?php
namespace app;

use app\Config\AppConfig;
use app\Helpers\Helper;
use app\Model\Setting;
use Memcache;
use Redis;
use RedisCluster;

class Controller 
{
    protected static $debag = false;
    protected static $client;     //客户端信息
    protected static $memcache;     //缓存信息
    protected static $redis;     //缓存信息
    
    public function __construct()
    {
        $this->init();
    }

    //初始化
    public function init()
    {
        //屏蔽程序直接请求
        if(empty(Helper::server('http_user_agent'))){
            die;
        }
        //屏蔽搜索引擎蜘蛛
        if(preg_match("/(googlebot|baiduspider|360spider|bingbot)/i", Helper::server('http_user_agent'))) {
            die;
        }
        //禁止访问系统
        if(!in_array(Helper::getSystem(), AppConfig::get('allowSystem'))){
            die("console.log('禁止访问的系统。')");
        }

        self::$client['isWechat'] = Helper::isWechat();
        self::$client['isQQAPP'] = Helper::isQQAPP();
        self::$client['ip'] = Helper::getClientIp();
        self::$client['system'] = Helper::getSystem();
        self::$client['source'] = parse_url(Helper::server('http_referer'));

        if(empty(self::$client['source'])){
            die("console.log('不能直接访问，请正确投放。')");
        }
        
        
        if(!empty(Helper::server('http_referer')))
        {
            if( count(self::$client['source'])<2 )
            {
                die("console.log('不能直接打开')");
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
                        die("console.log('不通过的域名后缀".$suffix."')");
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
        self::$redis->pconnect(AppConfig::get('redis.host'), AppConfig::get('redis.port'), AppConfig::get('redis.time'));
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
                    self::$redis->connect(AppConfig::get('redis.host'), AppConfig::get('redis.port'));
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
}
