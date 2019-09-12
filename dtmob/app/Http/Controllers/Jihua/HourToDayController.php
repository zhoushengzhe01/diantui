<?php
namespace App\Http\Controllers\Jihua;

use Illuminate\Support\Facades\Redis;
use Illuminate\Routing\Controller as BaseController;

use App\Model\WebmasterAds;
use App\Model\EarningHour;
use App\Model\EarningDay;
use App\Model\AdvertiserAds;
use App\Model\AdvertiserExpendHour;
use App\Model\AdvertiserExpendDay;
use App\Model\AllianceHour;
use App\Model\AllianceDay;
use App\Model\AllianceFluxExpendDay;
use App\Model\AllianceFluxExpendHour;
use App\Model\GameHour;
use App\Model\GameDay;

//小时数据往每天里面统计
class HourToDayController extends BaseController
{
    //站长
    public static function WebmasterDay()
    {
        startWebmaster:
        
        $earningHour = EarningHour::where('state', '=', '1')->where('time', '<', date('Y-m-d H').':00:00')->orderBy('id', 'asc')->offset(0)->limit(5)->get()->toArray();

        if(!empty($earningHour))
        {
            foreach($earningHour as $key=>$val)
            {
                EarningHour::where('id', '=', $val['id'])->update(['state'=>2]);
                $earningDay = EarningDay::firstOrCreate(['webmaster_ad_id'=>$val['webmaster_ad_id'], 'date'=>substr($val['time'], 0, 10)]);
                $earningDay->pc_number += $val['pc_number'];
                $earningDay->pv_number += $val['pv_number'];
                $earningDay->ip_number += $val['ip_number'];
                $earningDay->money += $val['money'];
                $earningDay->alliance_money += $val['alliance_money'];
                $earningDay->save();

                #更新到广告表里的今天收益
                $webmasterAds = WebmasterAds::where('id', '=', $val['webmaster_ad_id'])->first();
                $webmasterAds->earning_day += $val['money'];
                $webmasterAds->save();
                EarningHour::where('id', '=', $val['id'])->update(['state'=>4]);
            }

            echo date("m-d H:i:s") . " 站长hour统计到day数据：".$val['time']."\r\n";

            goto startWebmaster;
        }
    }

    //广告主
    public static function AdvertiserDay()
    {
        startAdvertiser:
        
        $expendHour = AdvertiserExpendHour::where('state', '=', '1')->where('time', '<', date('Y-m-d H').':00:00')->orderBy('id', 'asc')->offset(0)->limit(5)->get()->toArray();
        
        if(!empty($expendHour))
        {
            foreach($expendHour as $key=>$val)
            {
                AdvertiserExpendHour::where('id', '=', $val['id'])->update(['state'=>2]);

                $expendDay = AdvertiserExpendDay::firstOrCreate(['advertiser_ad_id'=>$val['advertiser_ad_id'], 'date'=>substr($val['time'], 0, 10)]);
                $expendDay->pv_number += $val['pv_number'];
                $expendDay->pc_number += $val['pc_number'];
                $expendDay->ip_number += $val['ip_number'];
                $expendDay->money += $val['money'];
                $expendDay->out_money += $val['out_money'];
                $expendDay->save();
                
                //更新到每日消耗里面
                $advertiserAd = AdvertiserAds::where('id', '=', $val['advertiser_ad_id'])->first();
                $advertiserAd->expend += $val['money'];
                $advertiserAd->expend_day += $val['money'];
                $advertiserAd->save();

                AdvertiserExpendHour::where('id', '=', $val['id'])->update(['state'=>4]);
            }

            echo date("m-d H:i:s") . " 广告主hour统计到day数据：".$val['time']."\r\n";
            //sleep(1);
            
            goto startAdvertiser;
        }
    }

    //联盟广告
    public static function AllianceDay()
    {
        startAlliance:

        $expendHour = AllianceHour::where('state', '=', '1')->where('time', '<', date('Y-m-d H').':00:00')->orderBy('id', 'asc')->offset(0)->limit(5)->get()->toArray();

        if(!empty($expendHour))
        {
            foreach($expendHour as $key=>$val)
            {
                AllianceHour::where('id', '=', $val['id'])->update(['state'=>2]);

                $allianceDay = AllianceDay::firstOrCreate(['alliance_id'=>$val['alliance_id'], 'date'=>substr($val['time'], 0, 10)]);
                $allianceDay->out_money += $val['out_money'];
                $allianceDay->pv_number += $val['pv_number'];
                $allianceDay->pc_number += $val['pc_number'];
                $allianceDay->ip_number += $val['ip_number'];
                $allianceDay->save();

                AllianceHour::where('id', '=', $val['id'])->update(['state'=>4]);
            }

            echo date("m-d H:i:s") . " 联盟广告hour统计到day数据：".$val['time']."\r\n";
            //sleep(1);
            goto startAlliance;
        }
        
    }

    //联盟流量
    public static function AllianceFluxDay()
    {
        startAllianceFlux:

        $fluxHour = AllianceFluxExpendHour::where('state', '=', '1')->where('time', '<', date('Y-m-d H').':00:00')->orderBy('id', 'asc')->offset(0)->limit(5)->get()->toArray();

        if(!empty($fluxHour))
        {
            foreach($fluxHour as $key=>$val)
            {
                AllianceFluxExpendHour::where('id', '=', $val['id'])->update(['state'=>2]);

                $fluxDay = AllianceFluxExpendDay::firstOrCreate(['alliance_flux_id'=>$val['alliance_flux_id'], 'date'=>substr($val['time'], 0, 10)]);
                $fluxDay->money += $val['money'];
                $fluxDay->out_money += $val['out_money'];
                $fluxDay->pv_number += $val['pv_number'];
                $fluxDay->pc_number += $val['pc_number'];
                $fluxDay->save();

                AllianceFluxExpendHour::where('id', '=', $val['id'])->update(['state'=>4]);
            }

            echo date("m-d H:i:s") . " 联盟流量hour统计到day数据：".$val['time']."\r\n";
            //sleep(1);
            goto startAllianceFlux;
        }
    }

    
    //互动式游戏
    public static function GameDay()
    {
        startGameHour:

        $gameHour = GameHour::where('state', '=', '1')->where('time', '<', date('Y-m-d H').':00:00')->orderBy('id', 'asc')->offset(0)->limit(5)->get()->toArray();

        if(!empty($gameHour))
        {
            foreach($gameHour as $key=>$val)
            {
                GameHour::where('id', '=', $val['id'])->update(['state'=>2]);

                $gameDay = GameDay::firstOrCreate(['game_sign'=>$val['game_sign'], 'date'=>substr($val['time'], 0, 10)]);
                $gameDay->money += $val['money'];
                $gameDay->out_money += $val['out_money'];
                $gameDay->pv_number += $val['pv_number'];
                $gameDay->pc_number += $val['pc_number'];
                $gameDay->save();

                GameHour::where('id', '=', $val['id'])->update(['state'=>4]);
            }

            echo date("m-d H:i:s") . " 互动式游戏hour统计到day数据：".$val['time']."\r\n";
            //sleep(1);
            goto startGameHour;
        }
    }
}