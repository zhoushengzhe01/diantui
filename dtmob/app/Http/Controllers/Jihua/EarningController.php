<?php
namespace App\Http\Controllers\Jihua;

use Illuminate\Support\Facades\Redis;
use Illuminate\Routing\Controller as BaseController;

use App\Model\EarningClick;
use App\Model\EarningClickAccount;
use App\Model\EarningHour;
use App\Model\EarningDay;
use App\Model\Alliance;
use App\Model\AllianceHour;
use App\Model\AllianceFlux;
use App\Model\AllianceFluxExpendDay;
use App\Model\AllianceFluxExpendHour;
use App\Model\Webmaster;
use App\Model\WebmasterAds;
use App\Model\WebmasterMoneyLog;
use App\Model\Advertiser;
use App\Model\AdvertiserAds;
use App\Model\AdvertiserExpendHour;
use App\Model\AdvertiserExpendDay;
use App\Model\ClickPushLogs;

//计划脚本
class EarningController extends BaseController
{
    //点击统计
    public static function EarningPcHour()
    {
        $time = date("Y-m-d H:i:s");
    
        startClick:
        
        $click = EarningClickAccount::where('state', '=', '1')->where('created_at', '<=', $time)->orderBy('id', 'asc')->first();
        
        if(!empty($click))
        {
            //广告主(自家广告自家流量)
            if($click->type=='1' && !empty($click->advertiser_id))
            {
                $advertiser = Advertiser::where('id', '=', $click->advertiser_id)->first();
                $advertiserAd = AdvertiserAds::where('id', '=', $click->advertiser_ad_id)->first();
                $webmasterAd = WebmasterAds::where('id', '=', $click->myads_id)->first();

                startAdvertiser:

                //同类型
                $earningClick = EarningClickAccount::select('id')
                    ->where('advertiser_id', '=', $click->advertiser_id)
                    ->where('advertiser_ad_id', '=', $click->advertiser_ad_id)
                    ->where('myads_id', '=', $click->myads_id)
                    ->where('state', '=', '1')
                    ->where('type', '=', $click->type)
                    ->where('created_at', '>=', substr($click->created_at, 0, 13).':00:00')
                    ->where('created_at', '<=', substr($click->created_at, 0, 13).':59:59')
                    ->offset(0)->limit(1000)
                    ->get()
                    ->toArray();
            
                if(!empty($earningClick))
                {
                    $update = EarningClickAccount::where('id', '=', 0);
                    foreach($earningClick as $key=>$val)
                    {
                        $update = $update->orWhere(['id'=>$val['id']]);
                    }
                    $update->update(['state'=>2]);
                    //点击数量
                    $pc_number = $key+1;
                    //广告点击计费
                    if($advertiserAd->price_type == 1){
                        $money = round(($advertiserAd->in_price/1000) * ($webmasterAd->in_advertiser_price/100),4) * $pc_number;
                        $out_money = round(($advertiserAd->out_price/1000) * ($webmasterAd->out_advertiser_price/100),4) * $pc_number;
                    }else{
                        $money = 0;
                        $out_money = 0;
                    }

                    //更新广告主
                    $advertiser->money = $advertiser->money - $money;
                    $advertiser->save();

                    //检测账户余额小于20分钟消耗
                    $warningMoney = intval($advertiser->last_hour_consume/2);
                    if($warningMoney<150){
                        $warningMoney = 150;
                    }
                    if($advertiser->money < $warningMoney ){
                        $advertiserAd->state = '2';
                    }
                    $advertiserAd->save();
                    

                    #账户里面有钱才给站长结算
                    if($advertiser->money>0)
                    {
                        #广告主小时数据
                        $expendHour = AdvertiserExpendHour::firstOrCreate(['advertiser_ad_id'=>$click->advertiser_ad_id,'time'=>substr($click->created_at, 0, 13).':00:00']);
                        $expendHour->pc_number += $pc_number;
                        $expendHour->money += $money;
                        $expendHour->out_money += $out_money;
                        $expendHour->save();
                        
                        #站长小时数据
                        $earningHour = EarningHour::firstOrCreate(['webmaster_ad_id'=>$click->myads_id,'time'=>substr($click->created_at, 0, 13).':00:00']);
                        $earningHour->pc_number += $pc_number;
                        $earningHour->money += $out_money;
                        $earningHour->save();
                    }
                    else
                    {
                        file_put_contents(__DIR__.'/123.txt', 'money:'.$advertiser->money.'id:'.$advertiser->id);
                    }

                    //完成
                    $update->delete();

                    echo date("m-d H:i:s") . " 点击结算-广告主：". $click->created_at ."\r\n";
                    goto startAdvertiser;
                }
            }

            //联盟（联盟广告自家流量）
            if($click->type=='2' && !empty($click->alliance_id))
            {
                $alliance = Alliance::where('id', '=', $click->alliance_id)->first(['price']);
                $webmasterAd = WebmasterAds::where('id', '=', $click->myads_id)->first(['out_alliance_price', 'billing']);

                startWebmaster:
                
                $earningClick = EarningClickAccount::where('alliance_id', '=', $click->alliance_id)
                    ->where('myads_id', '=', $click->myads_id)
                    ->where('type', '=', $click->type)
                    ->where('state', '=', '1')
                    ->where('created_at', '>=', substr($click->created_at, 0, 13).':00:00')
                    ->where('created_at', '<=', substr($click->created_at, 0, 13).':59:59')
                    ->offset(0)->limit(50)
                    ->get(['id', 'alliance_id', 'myads_id'])
                    ->toArray();
            
                if(!empty($earningClick))
                {
                    $update = EarningClickAccount::where('id', '=', '0');
                    foreach($earningClick as $key=>$val)
                    {
                        $update = $update->orWhere(['id'=>$val['id']]);  
                    }
                    
                    $update->update(['state'=>'2']);
                    
                    //点击数量
                    $pc_number = $key+1;
                    if($webmasterAd->billing=='CPC'){
                        $out_money = round(($alliance->price/1000) * ($webmasterAd->out_alliance_price/100), 4) * $pc_number;
                    }else{
                        $out_money = 0;
                    }

                    //联盟小时数据
                    $allianceHour = AllianceHour::firstOrCreate(['alliance_id'=>$click->alliance_id,'time'=>substr($click->created_at, 0, 13).':00:00']);
                    $allianceHour->pc_number += $pc_number;
                    $allianceHour->out_money += $out_money;
                    $allianceHour->save();

                    //站长小时数据
                    $earningHour = EarningHour::firstOrCreate(['webmaster_ad_id'=>$click->myads_id,'time'=>substr($click->created_at, 0, 13).':00:00']);
                    $earningHour->pc_number += $pc_number;
                    $earningHour->money += $out_money;
                    $earningHour->save();

                    $update->delete();

                    echo date("m-d H:i:s") . " 点击结算-联盟：". $click->created_at ."\r\n";

                    goto startWebmaster;
                }

            }
    
            //联盟流量（自家广告联盟流量）
            if($click->type=='3' && !empty($click->advertiser_id))
            {
                $advertiser = Advertiser::where('id', '=', $click->advertiser_id)->first();
                $advertiserAd = AdvertiserAds::where('id', '=', $click->advertiser_ad_id)->first();
                $allianceFlux = AllianceFlux::where('id', '=', $click->alliance_flux_id)->first();

                startAllianceFlux:
                
                $earningClick = EarningClickAccount::where('advertiser_id', '=', $click->advertiser_id)
                    ->where('advertiser_ad_id', '=', $click->advertiser_ad_id)
                    ->where('alliance_flux_id', '=', $click->alliance_flux_id)
                    ->where('state', '=', '1')
                    ->where('type', '=', $click->type)
                    ->where('created_at', '>=', substr($click->created_at, 0, 13).':00:00')
                    ->where('created_at', '<=', substr($click->created_at, 0, 13).':59:59')
                    ->offset(0)->limit(50)
                    ->get()
                    ->toArray();
                
                if(!empty($earningClick))
                {
                    $update = EarningClickAccount::where('id', '=', 0);
                    foreach($earningClick as $key=>$val)
                    {
                        $update = $update->orWhere(['id'=>$val['id']]);
                    }
                    $update->update(['state'=>2]);

                    //点击点击数量  目前只有cpc模式
                    $pc_number = $key+1;
                    $money = round(($advertiserAd->in_price/1000) * ($allianceFlux->in_price_ratio/100), 4) * $pc_number;
                    $out_money = round(($advertiserAd->out_price/1000) * ($allianceFlux->out_price_ratio/100), 4) * $pc_number;

                    //更新广告主
                    $advertiser->money = $advertiser->money - $money;
                    $advertiser->save();

                    //检测账户余额少于5元停止广告
                    // if($advertiser->money < 50){
                    //     //$advertiserAd->state = '2';
                    // }
                    // $advertiserAd->save();

                    //广告主小时数据
                    $expendHour = AdvertiserExpendHour::firstOrCreate(['advertiser_ad_id'=>$click->advertiser_ad_id,'time'=>substr($click->created_at, 0, 13).':00:00']);
                    $expendHour->pc_number += $pc_number;
                    $expendHour->pv_number += $pc_number * 30;
                    $expendHour->money += $money;
                    $expendHour->out_money += $out_money;
                    $expendHour->save();

                    //联盟小时数据
                    $fluxExpendHour = AllianceFluxExpendHour::firstOrCreate(['alliance_flux_id'=>$click->alliance_flux_id,'time'=>substr($click->created_at, 0, 13).':00:00']);
                    $fluxExpendHour->pc_number += $pc_number;
                    $fluxExpendHour->pv_number += $pc_number * 30;
                    $fluxExpendHour->money += $money;
                    $fluxExpendHour->out_money += $out_money;
                    $fluxExpendHour->save();

                    $update->delete();
                    
                    echo date("m-d H:i:s") . " 点击结算-联盟流量：". $click->created_at ."\r\n";
                
                    goto startAllianceFlux;
                }
            }

            echo date("m-d H:i:s") . " 结算一个类型：". $click->created_at ."\n";

            goto startClick;
        }
        
    }

    //展示统计
    public static function EarningPvHour()
    {
        
        // $llen = Redis::llen("pv_pointer");

        // $n = 1;

        // while ( $pointer = Redis::rpop("pv_pointer") ) 
        // {
        //     //获取
        //     $library = json_decode(Redis::hget("pv_library", $pointer), true);

        //     //删除
        //     Redis::hdel("pv_library", $pointer);

        //     //站长流量自家广告
        //     if($library['type']=='1')
        //     {
        //         $advertiser = Advertiser::where('id', '=', $library['advertiser_id'])->first();
        //         $advertiserAd = AdvertiserAds::where('id', '=', $library['advertiser_ad_id'])->first();
        //         $webmasterAd = WebmasterAds::where('id', '=', $library['webmaster_ad_id'])->first(['in_advertiser_price', 'out_advertiser_price']);
                
        //         $pv_number = intval($library['pv']);
        //         $ip_number = intval($library['ip']);

        //         if($advertiserAd->price_type == 2){
        //             $money = round(($advertiserAd->in_price/1000) * ($webmasterAd->in_advertiser_price/100),4) * $pv_number;
        //             $out_money = round(($advertiserAd->out_price/1000) * ($webmasterAd->out_advertiser_price/100),4) * $pv_number;
        //         }else{
        //             $money = 0;
        //             $out_money = 0;
        //         }
                
        //         //更新广告主
        //         $advertiser->money = $advertiser->money - $money;
        //         $advertiser->save();

        //         //检测账户余额少于5元停止广告
        //         if($advertiser->money < 50){
        //             $advertiserAd->state = '2';
        //         }
        //         $advertiserAd->save();

        //         //广告主小时数据
        //         $expendHour = AdvertiserExpendHour::firstOrCreate(['advertiser_ad_id'=>$library['advertiser_ad_id'],'time'=>$library['time'].':00:00']);
        //         $expendHour->pv_number += $pv_number;
        //         $expendHour->ip_number += $ip_number;
        //         $expendHour->money += $money;
        //         $expendHour->out_money += $out_money;
        //         $expendHour->save();

        //         //站长小时数据
        //         $earningHour = EarningHour::firstOrCreate(['webmaster_ad_id'=>$library['webmaster_ad_id'],'time'=>$library['time'].':00:00']);
        //         $earningHour->pv_number += $pv_number;
        //         $earningHour->ip_number += $ip_number;
        //         $earningHour->money += $out_money;
        //         $earningHour->save();
        //     }

            
        //     //站长流量联盟广告
        //     if($library['type']=='2')
        //     {
        //         $webmasterAd = WebmasterAds::where('id', '=', $library['webmaster_ad_id'])->first(['out_alliance_price', 'billing', 'alliance_billing']);

        //         if(!empty($webmasterAd))
        //         {
        //             $pv_number = intval($library['pv']) * 2;
        //             $ip_number = intval($library['ip']);

        //             if($webmasterAd->alliance_billing=='CPM'){
        //                 $alliance = Alliance::where('id', '=', $library['alliance_id'])->first(['price_cpm']);
        //                 $out_money = ($alliance->price_cpm/1000) * ($webmasterAd->out_alliance_price/100) * $pv_number;
        //             }else{
        //                 $out_money = 0;
        //             }

        //             //联盟小时数据
        //             $allianceHour = AllianceHour::firstOrCreate(['alliance_id'=>$library['alliance_id'],'time'=>$library['time'].':00:00']);
        //             $allianceHour->pv_number += $pv_number;
        //             $allianceHour->ip_number += $ip_number;
        //             $allianceHour->out_money += $out_money;
        //             $allianceHour->save();

        //             //站长小时数据
        //             $earningHour = EarningHour::firstOrCreate(['webmaster_ad_id'=>$library['webmaster_ad_id'],'time'=>$library['time'].':00:00']);
        //             $earningHour->pv_number += $pv_number;
        //             $earningHour->ip_number += $ip_number;
        //             $earningHour->money += $out_money;
        //             $earningHour->save();
        //         }
        //     }

        //     //互动式广告
        //     if($library['type']=='4')
        //     {
        //         $earningHour = EarningHour::firstOrCreate(['webmaster_ad_id'=>$library['webmaster_ad_id'],'time'=>$library['time'].':00:00']);
        //         $earningHour->pv_number += $pv_number;
        //         $earningHour->save();
        //     }

        //     echo date("m-d H:i:s") . " PV结算长度：".$llen."，目前：".$n."，时间：".$library['time']."\n";

        //     usleep(5000); //0.005秒


        //     if($n>=$llen)
        //     {
        //         break;
        //     }
        //     $n++;
        // }
    }

    //每天数据存入账户余额-站长
    public static function EarningDaySaveBalance()
    {
        startWebmasterSaveBalance:

        $earningDay = EarningDay::where('state', '=', '1')->where('date', '<', date('Y-m-d'))->orderBy('id', 'asc')->offset(0)->limit(10)->get()->toArray();
   
        if(!empty($earningDay))
        {
            foreach($earningDay as $key=>$val)
            {
                EarningDay::where('id', '=', $val['id'])->update(['state'=>2]);

                $webmaster = Webmaster::where('webmaster_ads.id', '=', $val['webmaster_ad_id'])
                    ->leftJoin('webmaster_ads', 'webmaster_ads.webmaster_id', '=', 'webmaster.id')
                    ->select('webmaster.*')
                    ->first();

                if(!empty($webmaster))
                {
                    $webmaster->money += $val['money'];
                    $webmaster->save();

                    //插入余额变动记录
                    $moneylog = new WebmasterMoneyLog;
                    $moneylog->webmaster_id = $webmaster->id;
                    $moneylog->money = $val['money'];
                    $moneylog->message = '收益结算';
                    $moneylog->save();

                    EarningDay::where('id', '=', $val['id'])->update(['state'=>4]);
                }
            }

            echo date("m-d H:i:s") . " 每天账户收益结算到账户余额：".$val['date']."\n";

            sleep(1);

            goto startWebmasterSaveBalance;
        }

    }

    //数据清除
    public static function clearDate()
    {
        //点击数据保留三天
        EarningClick::where('created_at', '<', date("Y-m-d",strtotime("-3 day")).' 00:00:00')->delete();

        //保留30天的
        EarningHour::where('created_at', '<', date("Y-m-d",strtotime("-30 day")).' 00:00:00')->delete();
        
        //保留30天的
        AllianceHour::where('created_at', '<', date("Y-m-d",strtotime("-30 day")).' 00:00:00')->delete();

        //保留30天的
        AllianceFluxExpendHour::where('created_at', '<', date("Y-m-d",strtotime("-30 day")).' 00:00:00')->delete();

        //保留30天的
        AdvertiserExpendHour::where('created_at', '<', date("Y-m-d",strtotime("-30 day")).' 00:00:00')->delete();

        //推送日志
        ClickPushLogs::where('created_at', '<', date("Y-m-d",strtotime("-5 day")).' 00:00:00')->delete();

        //清空今天消耗，今天收益
        WebmasterAds::where('id', '<>', 0)->update(['earning_day'=>0]);
        AdvertiserAds::where('id', '<>', 0)->update(['expend_day'=>0]);
    }
}