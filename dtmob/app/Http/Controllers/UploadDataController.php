<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Model\WebmasterAds;
use App\Model\Webmaster;
use App\Model\Advertiser;
use App\Model\AdvertiserAds;
use App\Model\Alliance;
use App\Model\AllianceHour;
use App\Model\EarningClick;
use App\Model\EarningClickAccount;
use App\Model\AdvertiserExpendHour;
use App\Model\EarningHour;
use App\Model\ClickPushLogs;

class UploadDataController extends Controller
{
    //PV数据
    public function uploadPV(Request $request)
    {
        $data = $request->input('data');
        
        DB::beginTransaction();

        try{
            foreach($data as $key=>$val)
            {
                if(!empty($val['type']) && $val['type']=='1')
                {
                    $advertiser = Advertiser::where('id', '=', $val['advertiser_id'])->first();
                    $advertiserAd = AdvertiserAds::where('id', '=', $val['advertiser_ad_id'])->first();
                    $webmasterAd = WebmasterAds::where('id', '=', $val['webmaster_ad_id'])->first(['in_advertiser_price', 'out_advertiser_price']);

                    if(!empty($advertiser) && !empty($advertiserAd) && !empty($webmasterAd))
                    {
                        $pv_number = intval($val['pv']);
                        $ip_number = intval($val['ip']);
                        $ifrom_number = intval($val['ifrom']);
                        #价格
                        if($advertiserAd->price_type == 2){
                            $money = round(($advertiserAd->in_price/1000) * ($webmasterAd->in_advertiser_price/100),4) * $pv_number;
                            $out_money = round(($advertiserAd->out_price/1000) * ($webmasterAd->out_advertiser_price/100),4) * $pv_number;
                        }else{
                            $money = 0;
                            $out_money = 0;
                        }
                        
                        //更新广告主
                        $advertiser->money = $advertiser->money - $money;
                        $advertiser->save();

                        //检测账户余额少于5元停止广告
                        if($advertiser->money < 50){
                            $advertiserAd->state = '2';
                        }
                        $advertiserAd->save();

                        //广告主小时数据
                        $expendHour = AdvertiserExpendHour::firstOrCreate(['advertiser_ad_id'=>$val['advertiser_ad_id'],'time'=>$val['time'].':00:00']);
                        $expendHour->pv_number += $pv_number;
                        $expendHour->ip_number += $ip_number;
                        $expendHour->money += $money;
                        $expendHour->out_money += $out_money;
                        $expendHour->save();

                        //站长小时数据
                        $earningHour = EarningHour::firstOrCreate(['webmaster_ad_id'=>$val['webmaster_ad_id'],'time'=>$val['time'].':00:00']);
                        $earningHour->pv_number += $pv_number;
                        $earningHour->ip_number += $ip_number;
                        $earningHour->ifrom_number += $ifrom_number;
                        $earningHour->money += $out_money;
                        $earningHour->save();
                    }
                    
                }
                
                #站长流量联盟广告
                if(!empty($val['type']) && $val['type']=='2')
                {
                    $webmasterAd = WebmasterAds::where('id', '=', $val['webmaster_ad_id'])->first(['out_alliance_price', 'billing', 'alliance_billing']);

                    if(!empty($webmasterAd))
                    {
                        $pv_number = intval($val['pv']);
                        $ip_number = intval($val['ip']);

                        if($webmasterAd->alliance_billing=='CPM'){
                            $alliance = Alliance::where('id', '=', $val['alliance_id'])->first(['price_cpm']);
                            $out_money = ($alliance->price_cpm/1000) * ($webmasterAd->out_alliance_price/100) * $pv_number;
                        }else{
                            $out_money = 0;
                        }

                        //联盟小时数据
                        $allianceHour = AllianceHour::firstOrCreate(['alliance_id'=>$val['alliance_id'],'time'=>$val['time'].':00:00']);
                        $allianceHour->pv_number += $pv_number;
                        $allianceHour->ip_number += $ip_number;
                        $allianceHour->out_money += $out_money;
                        $allianceHour->save();

                        //站长小时数据
                        $earningHour = EarningHour::firstOrCreate(['webmaster_ad_id'=>$val['webmaster_ad_id'],'time'=>$val['time'].':00:00']);
                        $earningHour->pv_number += $pv_number;
                        $earningHour->ip_number += $ip_number;
                        $earningHour->money += $out_money;
                        $earningHour->alliance_money += $out_money;
                        $earningHour->save();
                    }
                }
            }
        
            DB::commit();
        
        } catch (\Exception $e){

            DB::rollback();
            return response()->json(['messge'=>$e->getMessage()], 300); 
        }

        return response()->json(['messge'=>'ok'], 200);
    }

    // 同步站长
    public function uploadPC(Request $request)
    {
        $data = $request->input('data');
        $start_time = time();
        
        DB::beginTransaction();

        try{

            foreach($data as $key=>$val)
            {
                //自己流量-自己广告主
                if($val['type']=='1')
                {
                    if(count($val) > 1 && !empty($val['position_id']))
                    {
                        $click = new EarningClick;
                        $click->position_id = $val['position_id'];
                        $click->webmaster_id = $val['webmaster_id'];
                        $click->myads_id = $val['myads_id'];
                        $click->advertiser_id = $val['advertiser_id'];
                        $click->advertiser_ad_id = $val['advertiser_ad_id'];
                        $click->system = $val['system'];
                        $click->url = substr($val['url'], 0 , 250);
                        $click->source = substr($val['source'], 0 , 250);
                        $click->refso = substr($val['refso'], 0 , 250);
                        $click->interval = $val['interval'];
                        $click->history = $val['history'];
                        $click->ipnumber = $val['ipnumber'];
                        $click->jstime = $val['jstime'];
                        $click->ctype = $val['ctype'];
                        $click->screen = $val['screen'];
                        $click->clickp = $val['clickp'];
                        $click->user_agent = substr($val['user_agent'], 0 , 250);
                        $click->time = $val['time'];
                        $click->type = $val['type'];
                        $click->click_source = $val['click_source'];
                        $click->ip = $val['ip'];
                        $click->state = $val['state'];
                        $click->updated_at = $val['updated_at'];
                        $click->created_at = $val['created_at'];
                        $click->save();
                        
        
                        //插入结算表
                        $click_account = new EarningClickAccount;
                        $click_account->webmaster_id = $val['webmaster_id'];
                        $click_account->myads_id = $val['myads_id'];
                        $click_account->advertiser_id = $val['advertiser_id'];
                        $click_account->advertiser_ad_id = $val['advertiser_ad_id'];
                        $click_account->position_id = $val['position_id'];
                        $click_account->type = $val['type'];
                        $click_account->state = '1';
                        $click_account->updated_at = $val['updated_at'];
                        $click_account->created_at = $val['created_at'];
                        $click_account->save();
                    }
                }

                //联盟流量 自家广告
                if($val['type']=='3')
                {
                    $click = new EarningClick;
                    $click->position_id = $val['position_id'];
                    $click->alliance_flux_id = $val['alliance_flux_id'];
                    $click->advertiser_id = $val['advertiser_id'];
                    $click->advertiser_ad_id = $val['advertiser_ad_id'];
                    $click->system = $val['system'];
                    $click->user_agent = substr($val['user_agent'], 0 , 250);
                    $click->type = $val['type'];
                    $click->click_source = $val['click_source'];
                    $click->ip = $val['ip'];
                    $click->state = $val['state'];
                    $click->updated_at = $val['updated_at'];
                    $click->created_at = $val['created_at'];
                    $click->save();

                    //插入结算表
                    $click_account = new EarningClickAccount;
                    $click_account->position_id = $val['position_id'];
                    $click_account->alliance_flux_id = $val['alliance_flux_id'];
                    $click_account->advertiser_id = $val['advertiser_id'];
                    $click_account->advertiser_ad_id = $val['advertiser_ad_id'];
                    $click_account->type = $val['type'];
                    $click_account->state = '1';
                    $click_account->updated_at = $val['updated_at'];
                    $click_account->created_at = $val['created_at'];
                    $click_account->save();
                }
            }

            //记录日志
            $this->PushLog(count($data), (time()-$start_time), '1');
            DB::commit();

        } catch (\Exception $e){

            //记录日志
            $this->PushLog(count($data), (time()-$start_time), '2');

            DB::rollback();
            return response()->json(['messge'=>$e->getMessage()], 300);
        }


        return response()->json(['messge'=>'ok'], 200);
    }

    //推送日志记录
    public function PushLog($push_number, $push_time, $state)
    {
        $pushlogs = new ClickPushLogs();
        $pushlogs->push_number = $push_number;
        $pushlogs->push_time = $push_time;
        $pushlogs->state = $state;
        $pushlogs->ip = Helper::getClientIp();
        $pushlogs->save();
    }
}