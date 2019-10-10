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
use app\Config\AppConfig;
use Redis;

class AdsController extends Controller
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

        #统计没有更换域名的站长ID
        #if(self::$client['ip']=='122.55.213.160')
        {
            if($_SERVER['HTTP_HOST']!=$setting['ad_domain'])
            {   
                $path = AppConfig::get('root').'/cache/domain_webmaster_id';
                if(!file_exists($path.'/'.$webmaster['id'])){

                    @mkdir($path, 0777, true);

                    #写入文件操作
                    $file = @fopen($path.'/'.$webmaster['id'], "w");
                    @fwrite($file, '');
                    @fclose($file);
                    
                }
            }
            
        }

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
                    //同类型 和  返回广告不跑
                    if($paramet['position_id']==$ad['adstype_id'] && $ad['is_put_return_ad']=='0')
                    {
                        if($ad['put_type']=='1' || $ad['put_type']=='3')
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
                    if(!empty($val['category_id']) && !empty($webmasterAd['disabled_ad_category'])){
                        if(in_array($val['category_id'], json_decode($webmasterAd['disabled_ad_category'], true))){
                            unset($advertiserAds[$key]); continue;
                        }
                    }
                }

                #广告定区投放----------------------------------
                // if( in_array($val['id'], ['1673', '1736', '1737', '1738']) )
                // {
                //     $ipArray = explode('.', self::$client['ip']);
                
                //     if( in_array($ipArray[2], ['115', '116', '117', '118', '119', '120']) )
                //     {
                //         $advertiserAds = [$val];
                //         $sum_weight = $val['weight'];
                //         break;
                //     }
                //     else
                //     {
                //         unset($advertiserAds[$key]);
                //         continue;
                //     }                    
                // }
                #广告定区投放----------------------------------

                
            }

            #按照分池投放
            // if(self::$client['ip']=='122.55.213.160')
            // {
                $advertiser_ads = $advertiserAds;
                foreach($advertiser_ads as $key=>$val)
                {
                    $flowpool = json_decode($val['flowpool'], true);
                    if( !in_array($webmaster['flow_pool_id'] , $flowpool) )
                    {
                        unset($advertiser_ads[$key]); continue;
                    }
                }

                #没有跑默认池广告，本身就是默认池就不用了
                if(count($advertiser_ads)<=0 && $webmaster['flow_pool_id']!='1')
                {
                    foreach($advertiserAds as $key=>$val)
                    {
                        $flowpool = json_decode($val['flowpool'], true);
                        if( !in_array('1', $flowpool) )
                        {
                            unset($advertiserAds[$key]); continue;
                        }
                    }
                }
                else
                {
                    $advertiserAds = $advertiser_ads;
                }
            //}

            #遍历总权重
            foreach($advertiserAds as $key=>$val)
            {
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

            #返回跳转加密
            $rskip_str = Helper::encode(['adids'=>implode(',', $adids), 'time'=>time()]);

            //百度小图标换素材二广告
            if($webmasterAd['position_id']=='13')
            {
                if(strpos(Helper::server('http_user_agent'), 'baidu'))
                {
                    $webmasterAd['ad_size'] = '2';
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
            shuffle($matter);   //图片打乱

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


            #站长屏蔽模块
            $reader = Helper::getCity( self::$client['ip'] );
            if( !empty($reader['region_name']) )
            {
                $region = $reader['region_name'];

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
                        //重置展示率
                        $webmasterAd['zhikouling'] = -1;
                    }
                }
                //2.地区屏蔽暗层
                if( !empty($webmasterAd['hid_height_disabled_region']) )
                {
                    if(strpos($webmasterAd['hid_height_disabled_region'], $region) !== false)
                    {
                        //重置暗层高度
                        $webmasterAd['hid_height'] = -1;
                    }
                }
                //3.地区屏蔽强点
                if( !empty($webmasterAd['click_disabled_region']) )
                {
                    if(strpos($webmasterAd['click_disabled_region'], $region) !== false)
                    {
                        //重置强制点击
                        $webmasterAd['compel_click'] = -1;
                    }
                }
                //3.地区屏蔽暗层
                if( !empty($webmasterAd['skip_disabled_region']) )
                {
                    if(strpos($webmasterAd['skip_disabled_region'], $region) !== false)
                    {
                        //重置强制跳转
                        $webmasterAd['compel_skip'] = -1;
                    }
                }
                //4.JS特效屏蔽
                if( !empty($webmasterAd['js_effects_disabled_region']) )
                {
                    if(strpos($webmasterAd['js_effects_disabled_region'], $region) !== false)
                    {
                        //重置强制JS效果
                        $webmasterAd['js_effects'] = -1;
                    }
                }
                //5.一反屏蔽
                $setting['is_one_return'] = 1;
                if( !empty($setting['one_return_disabled_region']) )
                {
                    if(strpos($setting['one_return_disabled_region'], $region) !== false)
                    { 
                        //重置强制跳转
                        $setting['one_return_url'] = '';
                    }
                }

                //4.二反屏蔽
                if( !empty($setting['two_return_disabled_region']) )
                {
                    if(strpos($setting['two_return_disabled_region'], $region) !== false)
                    { 
                        //屏蔽了则二反链接设置空
                        $setting['two_return_url'] = '';
                    }
                }
            }
            else
            {
                $webmasterAd['zhikouling'] = 0;
                $webmasterAd['hid_height'] = 0;
                $webmasterAd['compel_skip'] = 0;
                $webmasterAd['js_effects'] = 0;
                $setting['one_return_url'] = '';
                $setting['two_return_url'] = '';
            }

            #广告域名
            $purl_domain = 'https://' . $setting['pv_domain'];
            $curl_domain = 'https://' . $setting['click_domain'];
            $matter_domain = 'https://image.sxybjjz.cn';
            
            #广告高度
            if(in_array($webmasterAd['webmaster_id'], [1089])){
                $index = 1;
            }else if(in_array($webmasterAd['webmaster_id'], [1051,1035,1137,1473,2931])){
                $index = 100;
            }else if(in_array($webmasterAd['webmaster_id'], [1066,1102])){
                $index = 1989101;
            }else{
                $index = 2147483647;
            }

            #距离凌晨时间
            $distance_time = strtotime(date("Y-m-d",strtotime("+1 day"))) - time() + mt_rand(0,60);
            
            header('Content-Type: application/x-javascript; charset=UTF-8');
            if(self::$client['ip']=='180.191.154.55')
            {
                require '../script/advertiser-'.$webmasterAd['position_id'].'.js';
            }
            else
            {
                require '../script/advertiser-'.$webmasterAd['position_id'].'-min.js';
            }
            self::$redis->close();
        }
        else
        {
            ##联盟广告
            $alliances = $this->getCachedData('alliance_'.$webmasterAd['position_id'].'_'.$webmasterAd['position'], 5, ['position_id'=>$webmasterAd['position_id'], 'position'=>$webmasterAd['position']], function($paramet)
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

            #距离凌晨时间
            $distance_time = strtotime(date("Y-m-d",strtotime("+1 day"))) - time() + mt_rand(0,60);

            #数据加密计费用
            $string = Helper::encode($data);
            if(self::$client['isWechat']==true) {
                $purl_domain = 'https://' . $setting['wechat_pv_domain'];
            }
            if(self::$client['isWechat']==false) {
                $purl_domain = 'https://' . $setting['pv_domain'];
            }

            header('Content-Type: application/x-javascript; charset=UTF-8');
            if(self::$client['ip']=='122.55.213.160'){
                require '../script/alliance.js';
            }else{
                require '../script/alliance-min.js';
            }
            self::$redis->close();

        }
    
    }


}