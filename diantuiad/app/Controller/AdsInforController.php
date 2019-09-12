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


class AdsInforController extends Controller
{
    //普通广告
    public function getCoding($webmaster_ad_id)
    {
        $id = empty($_GET['time']) ? '' : $_GET['time'];
        #获得系统设置
        $setting = $this->getCachedData('setting', 10, [], function($paramet)
        {
            $setting = json_decode(self::$redis->hget('setting', 'setting'), true);
            if( count($setting) <=0 ){
                return ['state'=>false, 'message'=>'找不到配置信息'];
            }
            return $setting;
        });

        #获得站长广告位
        $result = $this->getCachedData('webmaster_ads_'.$webmaster_ad_id, 5, ['webmaster_ad_id'=>$webmaster_ad_id], function($paramet)
        {
            $webmasterAd = json_decode(self::$redis->hget('webmaster_ads', $paramet['webmaster_ad_id']), true);
            if( count($webmasterAd) <=0 ){
                return ['state'=>false, 'message'=>'找不到广告位'.$paramet['webmaster_ad_id']];
            }

            $webmaster = json_decode(self::$redis->hget('webmasters', $webmasterAd['webmaster_id']), true);
            if( count($webmaster) <=0 ){
                return ['state'=>false, 'message'=>'找不到站长信息'];
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

        #自家广告和联盟广告几率
        $ad_ratio = empty($webmasterAd['ad_ratio']) ? $setting['ad_ratio'] : $webmasterAd['ad_ratio'];
        if( $ad_ratio >= mt_rand(1, 100) )
        {
            $advertiserAds = $this->getCachedData('advertiser_ads_'.$webmasterAd['position_id'], 5, ['position_id'=>$webmasterAd['position_id']], function($paramet)
            {
                //广告主广告
                $result = self::$redis->hgetall('advertiser_ads');
                $advertiserAds = [];
                foreach($result as $key=>$val)
                {
                    $ad = json_decode($val, true);
                    if($paramet['position_id']==$ad['adstype_id'])
                    {
                        $advertiserAds[] = $ad;
                    }
                }
                return $advertiserAds;    
            });
            
            //总权重
            $sum_weight = 0;
            foreach($advertiserAds as $key=>$val)
            {
                //预算总
                if( $val['budget']>0 )
                {
                    if($val['expend'] > $val['budget'])
                    {
                        unset($advertiserAds[$key]); continue;
                    }
                }
                //预算天
                if( $val['budget_day']>0 )
                {
                    if($val['expend_day'] > $val['budget_day'])
                    {
                        unset($advertiserAds[$key]); continue;
                    }
                }
                //系统
                if( $val['client']=='1' )
                {
                    if(self::$client['system']!='iPhone' && self::$client['system']!='iPad')
                    {
                        unset($advertiserAds[$key]); continue;
                    }
                }
                if( $val['client']=='2' )
                {
                    if(self::$client['system']!='Android')
                    {
                        unset($advertiserAds[$key]); continue;
                    }
                }
                //是否微信
                if($val['is_wechat']=='0' && self::$client['isWechat']==true)
                {
                    unset($advertiserAds[$key]); continue;
                }
                if($val['is_wechat']=='1' && self::$client['isWechat']==false)
                {
                    unset($advertiserAds[$key]); continue;
                }
                //投放日期
                if(!empty($val['put_date_start']) && !empty($val['put_date_stop']))
                {
                    if( date('Y-m-d')< $val['put_date_start']  || date('Y-m-d' ) > $val['put_date_stop'])
                    {
                        unset($advertiserAds[$key]); continue;
                    }
                }
                //分类
                if($val['is_put_category']=='1')
                {   
                    //登记限制域名才能定位分类
                    if(!empty($webmaster[self::$client['source']['domain']]))
                    {
                        $category_id = $webmaster[self::$client['source']['domain']];

                        if( !in_array($category_id, $val['categorys']) )
                        {
                            unset($advertiserAds[$key]); continue;
                        }
                    }
                }

                if($val['is_put_webmaster']=='1')
                {
                    $webmaster_id = $webmaster['id'];

                    if( !in_array($webmaster_id, explode('|', $val['put_webmasters'])) )
                    {
                        unset($advertiserAds[$key]); continue;
                    }
                }
                //屏蔽站长
                if($val['is_disabled_webmaster']=='1')
                {
                    $webmaster_id = $webmaster['id'];

                    if( in_array($webmaster_id, explode('|', $val['disabled_webmasters'])) )
                    {
                        unset($advertiserAds[$key]); continue;
                    }
                }
                //投放小时
                if($val['is_put_hour']=='1')
                {
                    if( !in_array(date('H'), json_decode($val['hours'], true)) )
                    {
                        unset($advertiserAds[$key]); continue;
                    }
                }
                //禁止的广告主
                if( $webmasterAd['is_disabled_advertiser_ad']=='1' )
                {
                    if( in_array($val['advertiser_id'], explode('|', $webmasterAd['disabled_advertiser_ad'])) ){
                        unset($advertiserAds[$key]); continue;
                    }
                    if(!empty($val['category_id']) && !empty($webmasterAd['disabled_ad_category'])){
                        if(in_array($val['category_id'], json_decode($webmasterAd['disabled_ad_category'], true))){
                            unset($advertiserAds[$key]); continue;
                        }
                    }
                }
                //权重
                if($val['is_hour_weight']=='1')
                {
                    $weight = intval($val['hour_weight'][intval(date('H'))]);
                }
                else
                {
                    $weight = intval($val['weight']);
                }
                $sum_weight += $weight;
            }

            if($sum_weight <= 0)
            {
                return ['state'=>false, 'message'=>'没有广告']; 
            }

            //摇号
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
                    $advertiserAd = $val;
                    break;
                }
            }

            //素材管理
            $matter = $this->getCachedData('package_'.$webmasterAd['ad_size'].'_'.$advertiserAd['package_id'], 5, ['ad_size'=>$webmasterAd['ad_size'], 'package_id'=>$advertiserAd['package_id']], function($paramet)
            {
                $package = json_decode(self::$redis->hget('matter_packages', $paramet['package_id']), true);
                if( count($package) <=0 ){
                    return ['state'=>false, 'message'=>'找不到素材'];
                }
                if($paramet['ad_size']=='1'){
                    $matter = $package['picture1'];
                }
                if($paramet['ad_size']=='2'){
                    $matter = $package['picture2'];
                }
                if($paramet['ad_size']=='3'){
                    $matter = $package['picture3'];
                }
                return $matter;
            });
            shuffle($matter);

            //数据
            $data = [
                'type' => 1,
                'webmaster_id' => $webmasterAd['webmaster_id'],
                'webmaster_ad_id' => $webmasterAd['id'],
                'hid_chance' => $webmasterAd['hid_height_chance'],
                'position_id' => $webmasterAd['position_id'],
                'advertiser_id' => $advertiserAd['advertiser_id'],
                'is_wechat_out_skip' => $advertiserAd['is_wechat_out_skip'],
                'is_wechat_cover' => $advertiserAd['is_wechat_cover'],
                'advertiser_ad_id' => $advertiserAd['id'],
                'time' => time(),
            ];
            $string = Helper::encode($data);

            //地区屏蔽重置参数-----地区--------
            $region = trim( Helper::getRegion( ip2long(self::$client['ip']) ) );
            if($region)
            {
                //1.JS特效屏蔽
                if( !empty($webmasterAd['js_effects_disabled_region']) )
                {
                    if(strpos($webmasterAd['js_effects_disabled_region'], $region) !== false)
                    {
                        //重置强制JS效果
                        $webmasterAd['js_effects'] = -1;
                    }
                }
                //2.地区屏蔽暗层
                if( !empty($webmasterAd['hid_height_disabled_region']) )
                {
                    if(strpos($webmasterAd['hid_height_disabled_region'], $region) !== false)
                    {
                        //重置暗层高度
                        $webmasterAd['top_hid_height'] = -1;
                        $webmasterAd['bot_hid_height'] = -1;
                    }
                }
            }
            else
            {
                $webmasterAd['js_effects'] = 0;
                $webmasterAd['top_hid_height'] = 0;
                $webmasterAd['bot_hid_height'] = 0;
            }

            #判断https
            if(Helper::server('server_port')=='443')
            {
                $is_https = true;
            }
            else
            {
                $is_https = false;
            }
            
            #广告域名
            $purl_domain = 'https://' . $setting['pv_domain'];
            $curl_domain = 'https://' . $setting['click_domain'];
            $matter_domain = 'https://' . $setting['matter_domain'];

            #广告高度特殊调整
            if(in_array($webmasterAd['webmaster_id'], [1051,1035,1137])){
                $index = 100;
            }else if(in_array($webmasterAd['webmaster_id'], [1066])){
                $index = 1989101;
            }else{
                $index = 2147483647;
            }
            
            header('Content-Type: application/x-javascript; charset=UTF-8');
            if(self::$client['ip']=='112.10.243.86')
            {
                require '../script/advertiser-12.js';
            }
            else
            {
                require '../script/advertiser-12-min.js';
            }
            die;
        }
        else
        {
            //------------------------------------------------联盟广告代码------------------------------------------------
            $alliances = $this->getCachedData('alliance_'.$webmasterAd['position_id'], 5, ['position_id'=>$webmasterAd['position_id'], 'position'=>$webmasterAd['position']], function($paramet)
            {
                #广告主广告
                $result = self::$redis->hgetall('alliances');
                $alliances = [];
                foreach($result as $key=>$val)
                {
                    $alliance = json_decode($val, true);
                    if($paramet['position_id']==$alliance['position_id'] && $paramet['position']==$alliance['position'] )
                    {
                        $alliances[] = $alliance;
                    }
                }
                return $alliances;    
            });

            #过滤
            foreach($alliances as $key=>$val)
            {
                #允许
                if(!empty($webmaster['allow_alliance']))
                {
                    $allow_alliance =  json_decode($webmaster['allow_alliance'], true);
                    
                    if(gettype($allow_alliance)!=='array')
                    {
                        $allow_alliance =  explode(',', $webmaster['allow_alliance']);
                    }

                    if(!in_array($val['id'], $allow_alliance))
                    {
                        unset($alliances[$key]); continue;
                    }
                }

                #禁止
                if(!empty($webmaster['disabled_alliance']))
                {
                    $disabled_alliance =  json_decode($webmaster['disabled_alliance'], true);

                    if(gettype($disabled_alliance)!=='array')
                    {
                        $disabled_alliance =  explode(',', $webmaster['disabled_alliance']);
                    }

                    if(in_array($val['id'], $disabled_alliance))
                    {
                        unset($alliances[$key]); continue;
                    }
                }
            }

            if(empty($alliances))
            {   
                die('L没有广告');
            }

            $allianceArr = [];
            foreach($alliances as $val)
            {
                $allianceArr[] = $val;
            }

            $rand = mt_rand(0, count($allianceArr)-1);

            $alliance = $allianceArr[$rand];

            #计费需要的数据
            $data = [
                'type' => 2,
                'webmaster_id' => $webmasterAd['webmaster_id'],
                'webmaster_ad_id' => $webmasterAd['id'],
                'position_id' => $webmasterAd['position_id'],
                'alliance_id' => $alliance['id'],
                'time' => time(),
            ];
            #数据加密计费用
            $string = Helper::encode($data);
            
            $setting['pv_domain'] = '//'.$setting['pv_domain'];
            $setting['click_domain'] = '//'.$setting['click_domain'];

            require '../script/alliance_hf.js'; die;
            //------------------------------------------------联盟广告代码------------------------------------------------
        }
    
    }
}