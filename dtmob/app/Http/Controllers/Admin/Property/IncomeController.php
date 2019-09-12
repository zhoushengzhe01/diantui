<?php
namespace App\Http\Controllers\Admin\Property;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Hash;
use DB;

class IncomeController extends ApiController
{
   public function getIncomes(Request $request)
   {
        self::Admin();

        #时间区分
        $start_date = trim($request->get('start_date'));
        $stop_date = trim($request->get('stop_date'));

        if( empty($start_date) )
            $start_date = date("Y-m"). "-01";
        if( empty($stop_date) )
            $stop_date = date("Y-m-d");
        
        #30天为一个月
        if(strtotime($start_date) < (strtotime($stop_date)-60*60*24*30)){
            return response()->json(['message'=>'时间跨度不能超过一个月'], 300);
        }
        
        $sql = "select advertiser_expend_day.date, sum(advertiser_expend_day.money) as money, sum(advertiser_expend_day.pc_number) as pc_number, sum(advertiser_expend_day.pv_number) as pv_number, sum(advertiser_expend_day.ip_number) as ip_number, advertiser.alliance_agent_id from advertiser_expend_day
            inner join advertiser_ads on advertiser_ads.id = advertiser_expend_day.advertiser_ad_id
            inner join advertiser on advertiser.id = advertiser_ads.advertiser_id
            where advertiser_expend_day.date >= '".$start_date."'
            and advertiser_expend_day.date <= '".$stop_date."'";

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $sql .= " and advertiser.alliance_agent_id = '".self::$user->alliance_agent_id."'";
        }

        $incomes = DB::select($sql.' group by advertiser_expend_day.date');
        $all_income = DB::select($sql);

        return response()->json(['data'=>['incomes'=>$incomes, 'all_income'=>$all_income[0]]], 200);
   }
}