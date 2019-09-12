<?php
namespace app\Controller;

use app\Controller;
use app\Helpers\Helper;
use app\Model\Setting;
use app\Model\WebmasterAds;
use app\Model\Webmaster;
use app\Model\WebmasterWebsite;
use app\Model\AdvertiserAds;
use app\Model\MatterPackage;
use app\Model\Alliance;
use Redis;

class AdsReturnController extends Controller
{
    //普通广告
    public function getCoding($webmaster_ad_id)
    {
        //获得系统设置
        $setting = $this->getCachedData('setting', 10, [], function($paramet)
        {
            $setting = json_decode(self::$redis->hget('setting', 'setting'), true);
            if( count($setting) <=0 ){
                return ['state'=>false, 'message'=>'找不到配置信息'];
            }
            return $setting;
        });
        

        //获得站长广告位
        $result = $this->getCachedData('webmaster_ads_'.$webmaster_ad_id, 5, ['webmaster_ad_id'=>$webmaster_ad_id], function($paramet)
        {
            $webmasterAd = json_decode(self::$redis->hget('webmaster_ads', $paramet['webmaster_ad_id']), true);
            if( count($webmasterAd) <=0 ){
                return ['state'=>false, 'message'=>'找不到广告位'.$paramet['webmaster_ad_id'].json_encode($webmasterAd, true)];
            }

            $webmaster = json_decode(self::$redis->hget('webmasters', $webmasterAd['webmaster_id']), true);
            if( count($webmaster) <=0 ){
                return ['state'=>false, 'message'=>'找不到站长信息'. $webmasterAd['webmaster_id'].json_encode($webmaster, true)];
            }
            //域名检测
            if($webmaster['is_limit_domain']=='1')
            {
                if( !in_array(self::$client['source']['domain'], $webmaster['websites']) )
                {
                    //return ['state'=>false, 'message'=>'域名没有登记'];
                }
            }

            return ['webmaster'=>$webmaster, 'webmasterAd'=>$webmasterAd];
        });
        
        #站长
        $webmaster = $result['webmaster'];
        #站长广告
        $webmasterAd = $result['webmasterAd'];
        #获取所有广告
        $advertiserAds = $this->getCachedData('advertiser_ads_'.$webmasterAd['position_id'], 5, [], function($paramet)
        {
            //广告主广告
            $result = self::$redis->hgetall('advertiser_ads');
            $advertiserAds = [];
            foreach($result as $key=>$val)
            {
                $ad = json_decode($val, true);
              
                #是否投放返回广告
                if($ad['is_put_return_ad']=='1' && $ad['state']=='1' )
                {
                    #自己流量和全部流量
                    if($ad['put_type']=='1' || $ad['put_type']=='0')
                    {
                        $advertiserAds[] = $ad;
                    }
                }
            }
            return $advertiserAds;    
        });

        

        $sum_weight = 0;
        foreach($advertiserAds as $key=>$val)
        {
            //总预算
            if( $val['budget']>0 ){
                if($val['expend'] > $val['budget']){
                    unset($advertiserAds[$key]); continue;
                }
            }
            //天预算
            if( $val['budget_day']>0 ){
                if($val['expend_day'] > $val['budget_day']){
                    unset($advertiserAds[$key]); continue;
                }
            }
            //系统
            if( $val['client']=='1' ){
                if(self::$client['system']!='iPhone' && self::$client['system']!='iPad'){
                    unset($advertiserAds[$key]); continue;
                }
            }
            if( $val['client']=='2' ){
                if(self::$client['system']!='Android'){
                    unset($advertiserAds[$key]); continue;
                }
            }
            //是否微信
            if($val['is_wechat']=='0' && self::$client['isWechat']==true){
                unset($advertiserAds[$key]); continue;
            }
            if($val['is_wechat']=='1' && self::$client['isWechat']==false){
                unset($advertiserAds[$key]); continue;
            }
            //投放日期
            if(!empty($val['put_date_start']) && !empty($val['put_date_stop'])){
                if( date('Y-m-d')< $val['put_date_start']  || date('Y-m-d' ) > $val['put_date_stop']){
                    unset($advertiserAds[$key]); continue;
                }
            }
            //分类
            if($val['is_put_category']=='1'){
                //登记限制域名才能定位分类
                if(!empty($webmaster[self::$client['source']['domain']])){
                    $category_id = $webmaster[self::$client['source']['domain']];
                    if( !in_array($category_id, $val['categorys']) ){
                        unset($advertiserAds[$key]); continue;
                    }
                }
            }
            //投放站长
            if($val['is_put_webmaster']=='1'){
                $webmaster_id = $webmaster['id'];
                if( !in_array($webmaster_id, explode('|', $val['put_webmasters'])) ){
                    unset($advertiserAds[$key]); continue;
                }
            }
            //屏蔽站长
            if($val['is_disabled_webmaster']=='1'){
                $webmaster_id = $webmaster['id'];
                if( in_array($webmaster_id, explode('|', $val['disabled_webmasters'])) ){
                    unset($advertiserAds[$key]); continue;
                }
            }
            //投放小时
            if($val['is_put_hour']=='1'){
                if( !in_array(date('H'), json_decode($val['hours'], true)) ){
                    unset($advertiserAds[$key]); continue;
                }
            }
            //禁止的广告主
            if( $webmasterAd['is_disabled_advertiser_ad']=='1' ){
                if( in_array($val['advertiser_id'], explode('|', $webmasterAd['disabled_advertiser_ad'])) ){
                    unset($advertiserAds[$key]); continue;
                }
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
            return ['state'=>false, 'message'=>'没有广告']; 
        }

        

        //摇号
        $rand = mt_rand(1, $sum_weight);
        $weight = 0;
        $adids = [];    //能跑的广告
        foreach($advertiserAds as $key=>$val)
        { 
            $adids[] = $val['id'];

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

        #数据
        $data = [
            'type' => 1,
            'webmaster_id' => $webmasterAd['webmaster_id'],
            'webmaster_ad_id' => $webmasterAd['id'],
            'hid_chance' => $webmasterAd['hid_height_chance'],
            'position_id' => $webmasterAd['position_id'],
            'advertiser_id' => $advertiserAd['advertiser_id'],
            'is_wechat_out_skip' => $advertiserAd['is_wechat_out_skip'],
            'is_wechat_cover' => $advertiserAd['is_wechat_cover'],
            'return_chance' => $webmasterAd['return_chance'],
            'advertiser_ad_id' => $advertiserAd['id'],
            'time' => time(),
        ];
        $string = Helper::encode($data);
            

        //屏蔽地区
        $region = trim( Helper::getRegion( ip2long(self::$client['ip']) ) );
        if($region)
        {
            //广告屏蔽-屏蔽后直接不展示
            if($webmasterAd['is_ad_disabled']=='1')
            {
                if(strpos($webmasterAd['ad_disabled_region'], $region) !== false)
                {
                    die('100048');
                }
                if(empty($region))
                {
                    die('100048');
                }
            }
            //1.地区屏蔽支口令
            if( !empty($webmasterAd['zhikouling_disabled_region']) )
            {
                if(strpos($webmasterAd['zhikouling_disabled_region'], $region) !== false)
                {
                    $webmasterAd['zhikouling'] = -1;
                }
            }
            //2.地区屏蔽暗层
            if( !empty($webmasterAd['hid_height_disabled_region']) )
            {
                if(strpos($webmasterAd['hid_height_disabled_region'], $region) !== false)
                {
                    $webmasterAd['hid_height'] = -1;
                }
            }
            //3.地区屏蔽强点
            if( !empty($webmasterAd['click_disabled_region']) )
            {
                if(strpos($webmasterAd['click_disabled_region'], $region) !== false)
                {
                    $webmasterAd['compel_click'] = -1;
                }
            }
            //3.地区屏蔽强跳
            if( !empty($webmasterAd['skip_disabled_region']) )
            {
                if(strpos($webmasterAd['skip_disabled_region'], $region) !== false)
                {
                    $webmasterAd['compel_skip'] = -1;
                }
            }
        }
        else
        {
            $webmasterAd['zhikouling'] = 0;
            $webmasterAd['hid_height'] = 0;
            $webmasterAd['compel_skip'] = 0;
        }
        

        #广告域名
        $purl_domain = 'https://' . $setting['pv_domain'];
        $curl_domain = 'https://' . $setting['click_domain'];
        $matter_domain = 'https://' . $setting['matter_domain'];

        #距离时间
        $distance_time = strtotime(date("Y-m-d",strtotime("+1 day"))) - time() + mt_rand(0,60);

        header('Content-Type: application/x-javascript; charset=UTF-8');
        if(self::$client['ip']=='112.10.243.86'){
            require '../script/advertiser-'.$webmasterAd['position_id'].'.js';
        }else{
            require '../script/advertiser-'.$webmasterAd['position_id'].'-min.js';
        }
        self::$redis->close();
    }
}