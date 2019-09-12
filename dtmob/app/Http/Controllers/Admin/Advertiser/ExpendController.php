<?php
namespace App\Http\Controllers\Admin\Advertiser;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\AdvertiserExpendDay;
use App\Model\AdvertiserExpendHour;
use App\Model\Advertiser;

class ExpendController extends ApiController
{
    public function getExpendsDay(Request $request)
    {
        self::Admin();

        //网站杜索
        $start_date = trim($request->input('start_date'));
        $stop_date = trim($request->input('stop_date'));
        $advertiser_id = trim($request->input('advertiser_id'));
        $advertiser_ad_id = trim($request->input('advertiser_ad_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit)){
            $limit = 10;
        }

        $expends = AdvertiserExpendDay::select('advertiser_expend_day.*', 'ad.title', 'adv.alliance_agent_id')
            ->join('advertiser_ads as ad', 'ad.id', '=', 'advertiser_expend_day.advertiser_ad_id')
            ->join('advertiser as adv', 'adv.id', '=', 'ad.advertiser_id')
            ->where('advertiser_expend_day.money', '>', '0');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $expends = $expends->where('adv.alliance_agent_id', '=', self::$user->alliance_agent_id);
        }
        #商务权限限制
        if(self::$user->department_id==4){
            $expends = $expends->where('adv.busine_id', '=', self::$user->id);
        }
        
        if(!empty($start_date))
        {
            $expends = $expends->where('advertiser_expend_day.date', '>=', $start_date);
        }
        if(!empty($stop_date))
        {
            $expends = $expends->where('advertiser_expend_day.date', '<=', $stop_date);
        }
        if(!empty($advertiser_id))
        {
            $expends = $expends->where('ad.advertiser_id', '=', $advertiser_id);
        }
        if(!empty($advertiser_ad_id))
        {
            $expends = $expends->where('ad.id', '=', $advertiser_ad_id);
        }  
         
            
        $count = $expends->count();
        $all_money = $expends->sum('advertiser_expend_day.money');
        $all_out_money = $expends->sum('advertiser_expend_day.out_money');
        $expends = $expends->orderBy('id', 'desc')->offset($offset)->limit($limit)->get(['ad.title', 'advertiser_expend_day.*']);

        $data = [
            'count'=>$count,
            'all_money'=>$all_money,
            'all_out_money'=>$all_out_money,
            'expends'=>$expends,
        ];
        
        return response()->json(['data'=>$data], 200);
    }


    public function getExpendsHour(Request $request, $advertiser_ad_id)
    {
        self::Admin();

        if(empty($advertiser_ad_id))
            return response()->json(['message'=>'缺少广告位ID'], 400);

        //网站杜索
        $date = trim($request->input('date'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $expends = AdvertiserExpendHour::where('money', '>', '0')
            ->where('advertiser_ad_id', '=', $advertiser_ad_id);

        if(!empty($date))
        {
            $expends = $expends->where('time', '>=', $date.' 00:00:00')->where('time', '<=', $date.' 23:59:59');
        }

        $count = $expends->count();
        $all_money = $expends->sum('money');
        $all_out_money = $expends->sum('out_money');
        $expends = $expends->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'all_money'=>$all_money,
            'all_out_money'=>$all_out_money,
            'expends'=>$expends,
        ];
        
        return response()->json(['data'=>$data], 200);
    }
    
}
