<?php
namespace app\Controller;

use app\Helpers\Helper;
use app\Config\AppConfig;
use app\Model\Setting;
use app\Model\Game;
use app\Model\Webmaster;
use app\Model\WebmasterAds;
use app\Model\WebmasterWebsite;
use Redis;
use Memcache;

class HudongController extends Controller
{
    //获得互动式广告
    public function getHudong($myads_id)
    {
        //获得系统设置
        $setting = $this->getCachedData('setting', 30, [], function($paramet)
        {
            $result = Setting::get(['key', 'value'])->toArray();

            $setting = [];
            foreach($result as $key=>$val){
                $setting[$val['key']] = $val['value'];
            }
            return $setting;
        });

        //获得站长广告位
        $webmasterAds = $this->getCachedData('webmaster_ads_'.$myads_id, 10, ['myads_id'=>$myads_id], function($paramet)
        {
            $webmasterAds = WebmasterAds::where('id', '=', $paramet['myads_id'])->where('state', '=', 1)->first();
            if(empty($webmasterAds))
            {
                return ['state'=>false, 'message'=>'找不到广告位'];
            }

            return $webmasterAds = $webmasterAds->toArray();    
        });

        //获得站长数据
        $webmaster = $this->getCachedData('webmaster_'.$webmasterAds['webmaster_id'], 20, ['webmaster_id'=>$webmasterAds['webmaster_id']], function($paramet)
        {
            $webmaster = Webmaster::where('id', '=', $paramet['webmaster_id'])->where('state', '=', '1')->first();

            if(!empty($webmaster))
            {
                $webmaster = $webmaster->toArray();
                //域名
                if($webmaster['is_limit_domain']=='1')
                {
                    //域名检测
                    $website = WebmasterWebsite::where('webmaster_id', '=', $webmaster['id'])
                        ->where('domain', '=', self::$client['source']['domain'])
                        ->where('state', '=', '1')
                        ->first(['category_id']);

                    if(!empty($website))
                    {
                        $website = $website->toArray();
                        $webmaster[self::$client['source']['domain']] = $website['category_id'];
                    }
                    else
                    {
                        return ['state'=>false, 'message'=>'域名没有登记']; 
                    }
                }
                return $webmaster;
            }
            else
            {
                return ['state'=>false, 'message'=>'找不到站长'];
            }
        });

        //数据
        $data = [
            'type' => 4,
            'position_id' => $webmasterAds['position_id'],
            'webmaster_id' => $webmasterAds['webmaster_id'],
            'webmaster_ad_id' => $webmasterAds['id'],
            'app_key' => $webmasterAds['app_key'],
            'time' => time(),
        ];

        $string = Helper::encode($data);

        require '../script/hudong_ad.min.js'; die;
    }
}