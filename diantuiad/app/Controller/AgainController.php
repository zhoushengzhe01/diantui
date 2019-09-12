<?php
namespace app\Controller;

use app\Controller;
use app\Helpers\Helper;
use app\Config\AppConfig;
use app\Model\EarningClick;
use app\Model\AgainClick;
use app\Model\EarningPv;
use app\Model\Alliance;
use app\Model\AdvertiserAds;
use app\Model\AllianceFlux;

use Memcache;
use Redis;

class AgainController extends Controller
{
    //二次点击的代码
    public function coding($advertiser_ad_id)
    {
        $data = [
            'advertiser_ad_id' => $advertiser_ad_id,
            'ip' => Helper::getClientIp(),
            'time' => time(),
            'rand' => mt_rand(100000, 999999),
        ];
        $string = Helper::encode($data);

        $purl_domain = '//again.homemark.cn';
        $curl_domain = '//again.homemark.cn';
        
        require '../script/again.js';
        die;
    }

    //PC统计
    public function againPc()
    {
        if( !($data = Helper::decode(Helper::request('se'))) )
        {
            die('验证不通过');
        }

        $refso = Helper::request('refso');
        $source = Helper::request('source');
        $url = Helper::request('url');
        $host = Helper::request('host');
        $screen = Helper::request('screen');
        $clickp = Helper::request('clickp');
        $link = Helper::request('link');
        $type = Helper::request('type');

        //没有前来源不记录
        if( !empty($source) )
        {
            $url_arr = parse_url($source);
            if( !empty($url_arr['host']) )
            {
                $host = trim($url_arr['host']);
                
                //选获取是否有
                $count = (new AgainClick)->where('advertiser_ad_id', '=', $data['advertiser_ad_id'])
                    ->where('host', '=', $host)
                    ->where('ip', '=', Helper::getClientIp())
                    ->where('created_at', '>', date('Y-m-d')." 00:00:00" )
                    ->count();
                
                if( $count < 5 )
                {
                    $again_data = [
                        'advertiser_ad_id' => $data['advertiser_ad_id'],
                        'host' => $host,
                        'system' => Helper::getSystem(),
                        'ip' => Helper::getClientIp(),
                        'url' => substr($url , 0 , 200),
                        'source' => $source,
                        'refso' => substr($refso , 0 , 200),
                        'screen' => $screen,
                        'clickp' => $clickp,
                        'user_agent' => substr(Helper::server('http_user_agent'), 0 , 250),
                        'time' => time() - intval($data['time']),
                        'updated_at' => date("Y-m-d H:i:s"),
                        'created_at' => date("Y-m-d H:i:s"),
                    ];
                    $id = (new AgainClick)->data($again_data)->save();
                }     
            }
        }
    }

    //PC统计
    public function againPv()
    {
        if( !($data = Helper::decode(Helper::request('se'))) )
        {
            die('验证不通过');
        }

        $refso = Helper::request('refso');
        $source = Helper::request('source');
        $url = Helper::request('url');
        $host = Helper::request('host');
        $screen = Helper::request('screen');
        $clickp = Helper::request('clickp');
        $link = Helper::request('link');
        $type = Helper::request('type');

        //没有前来源不记录
        if( !empty($source) )
        {
            $url_arr = parse_url($source);

            if( !empty($url_arr['host']) )
            {
                $host = trim($url_arr['host']);

                //选获取是否有
                $redis = new Redis;
                $redis->connect('127.0.0.1', 6379);

                //自己广告-自己流量
                $pointer = "advertiser_ad_id:" . $data['advertiser_ad_id'] . "-host:" . $host . "_" . date('Ymd');
                $pvLibrary = json_decode($redis->hget("pv_again_library", $pointer), true);
                if(empty($pvLibrary))
                {
                    $pvLibrary = [
                        'advertiser_ad_id' => $data['advertiser_ad_id'],
                        'host' => $host,
                        'date' => date('Y-m-d'),
                        'pv' => 1,
                    ];
                    $redis->lpush("pv_again_pointer", $pointer);
                }
                else
                {
                    $pvLibrary['pv'] += 1;
                }
                $redis->hset("pv_again_library", $pointer, json_encode($pvLibrary, true));
            }
        }
    }
}