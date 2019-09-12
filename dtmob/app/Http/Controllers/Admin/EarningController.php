<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Users;
use App\Model\Webmaster;
use App\Model\WebmasterAds;
use App\Model\Message;
use DB;


class EarningController extends ApiController
{

    public function getServices(Request $request)
    {
        self::Admin();

        //时间区分
        $start_date = trim($request->get('start_date'));
        $stop_date = trim($request->get('stop_date'));

        if( empty($start_date) ){
            $start_date = date("Y-m"). "-01";
        }
        if( empty($stop_date) ){
            $stop_date = date("Y-m-d");
        }

        $sql = "select sum(earning_day.money) as earning_money, sum(earning_day.money*earning_day.service_extract/100) as service_extract_money, users.*, webmaster.alliance_agent_id from ((users inner join webmaster on webmaster.service_id=users.id)
        inner join webmaster_ads on webmaster.id = webmaster_ads.webmaster_id)
        inner join earning_day on webmaster_ads.id = earning_day.webmaster_ad_id
        where users.department_id = '3'
        and earning_day.is_extract >= '1'
        and earning_day.date >= '".$start_date."'
        and earning_day.date <= '".$stop_date."'";
        
        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $sql .= " and webmaster.alliance_agent_id = '".self::$user->alliance_agent_id."'";
        }

        $services = DB::select($sql.' group by users.id order by earning_money desc');
        $max_money = 10000;
        foreach($services as $k=>$v)
        {
            $services[$k]->earning_money = round($v->earning_money,2);

            if($v->earning_money > $max_money)
            {
                $max_money = intval($v->earning_money * 1.2);
            }
        }

        return response()->json(['data'=>['services'=>$services, 'max_money'=>$max_money]], 200);
    }

    public function getService(Request $request, $service_id)
    {
        self::Admin();

        if(empty($service_id)){
            return response()->json(['message'=>'缺少媒介ID'], 300);
        }

        //时间区分
        $start_date = trim($request->get('start_date'));
        $stop_date = trim($request->get('stop_date'));

        if( empty($start_date) )
            $start_date = date("Y-m"). "-01";
        if( empty($stop_date) )
            $stop_date = date("Y-m-d");

        $service = DB::select("select sum(earning_day.money) as earning_money, earning_day.date from (webmaster inner join webmaster_ads on webmaster.id = webmaster_ads.webmaster_id) 
            inner join earning_day on webmaster_ads.id = earning_day.webmaster_ad_id
            where webmaster.service_id = '".$service_id."'
            and earning_day.date >= '".$start_date."'
            and earning_day.date <= '".$stop_date."'
            group by earning_day.date");

        $max_money = 10000;
        foreach($service as $k=>$v)
        {
            $service[$k]->earning_money = round($v->earning_money,2);
            if($v->earning_money > $max_money)
            {
                $max_money = intval($v->earning_money * 1.2);
            }
        }

        return response()->json(['data'=>['service'=>$service, 'max_money'=>$max_money]], 200);
    }

    public function getBusines(Request $request)
    {
        self::Admin();

        #时间区分
        $start_date = trim($request->get('start_date'));
        $stop_date = trim($request->get('stop_date'));

        if( empty($start_date) ){
            $start_date = date("Y-m"). "-01";
        }
        if( empty($stop_date) ){
            $stop_date = date("Y-m-d");
        }

        $sql = "select sum(advertiser_expend_day.money) as earning_money, users.*, advertiser.alliance_agent_id from ((users inner join advertiser on advertiser.busine_id=users.id) 
            inner join advertiser_ads on advertiser.id = advertiser_ads.advertiser_id)
            inner join advertiser_expend_day on advertiser_ads.id = advertiser_expend_day.advertiser_ad_id
            where users.department_id = '4'
            and advertiser_expend_day.date >= '".$start_date."'
            and advertiser_expend_day.date <= '".$stop_date."'";

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $sql .= " and advertiser.alliance_agent_id = '".self::$user->alliance_agent_id."'";
        }

        $busines = DB::select($sql." group by users.id order by earning_money desc");
        
        $max_money = 10000;
        foreach($busines as $k=>$v)
        {
            $busines[$k]->earning_money = intval($v->earning_money);

            if($v->earning_money >= 1000000){
                $busines[$k]->money = intval($v->earning_money*0.03);
            }else if($v->earning_money >= 500000){
                $busines[$k]->money = intval($v->earning_money*0.02);
            }else{
                $busines[$k]->money = intval($v->earning_money*0.015);
            }

            if($v->earning_money > $max_money){
                $max_money = intval($v->earning_money * 1.2);
            }
        }

        return response()->json(['data'=>['busines'=>$busines, 'max_money'=>$max_money]], 200);
    }

    public function getBusine(Request $request, $busine_id)
    {
        self::Admin();

        if(empty($busine_id)){
            return response()->json(['message'=>'缺少媒介ID'], 300);
        }

        //时间区分
        $start_date = trim($request->get('start_date'));
        $stop_date = trim($request->get('stop_date'));

        if( empty($start_date) )
            $start_date = date("Y-m"). "-01";
        if( empty($stop_date) )
            $stop_date = date("Y-m-d");

        $busine = DB::select("select sum(advertiser_expend_day.money) as earning_money, advertiser_expend_day.date from (advertiser inner join advertiser_ads on advertiser.id=advertiser_ads.advertiser_id) 
            inner join advertiser_expend_day on advertiser_ads.id=advertiser_expend_day.advertiser_ad_id
            where advertiser.busine_id = '".$busine_id."'
            and advertiser_expend_day.date >= '".$start_date."'
            and advertiser_expend_day.date <= '".$stop_date."'
            group by advertiser_expend_day.date");

        $max_money = 10000;
        foreach($busine as $k=>$v)
        {
            $busine[$k]->earning_money = round($v->earning_money,2);

            if($v->earning_money > $max_money)
            {
                $max_money = intval($v->earning_money * 1.2);
            }
        }

        return response()->json(['data'=>['busine'=>$busine, 'max_money'=>$max_money], 'max_money'=>$max_money], 200);
    }
}
