<?php
namespace App\Http\Controllers\Admin\Property;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use DB;

class ExpenditureController extends ApiController
{
    #站长支出
    public function getExpenditures(Request $request)
    {
        self::Admin();

        $start_date = trim($request->get('start_date'));
        $stop_date = trim($request->get('stop_date'));

        if( empty($start_date) ){
            $start_date = date("Y-m"). "-01";
        }
        if( empty($stop_date) ){
            $stop_date = date("Y-m-d");
        }

        #30天为一个月
        if(strtotime($start_date) < (strtotime($stop_date)-60*60*24*30)){
            return response()->json(['message'=>'时间跨度不能超过一个月'], 300);
        }

        $sql = "select earning_day.date, sum(earning_day.money) as money, sum(earning_day.pc_number) as pc_number, sum(earning_day.pv_number) as pv_number, sum(earning_day.ip_number) as ip_number, webmaster.alliance_agent_id from earning_day
            inner join webmaster_ads on webmaster_ads.id = earning_day.webmaster_ad_id
            inner join webmaster on webmaster.id = webmaster_ads.webmaster_id
            where earning_day.date >= '".$start_date."'
            and earning_day.date <= '".$stop_date."'";
        
        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $sql .= " and webmaster.alliance_agent_id = '".self::$user->alliance_agent_id."'";
        }

        $expenditures = DB::select($sql.' group by earning_day.date');
        $all_expenditure = DB::select($sql);

        return response()->json(['data'=>['expenditures'=>$expenditures, 'all_expenditure'=>$all_expenditure[0]]], 200);
    }
}