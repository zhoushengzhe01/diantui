<?php
namespace App\Http\Controllers\Advertiser;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\AdvertiserExpendDay;
use App\Model\AdvertiserExpendHour;


class ExpendController extends ApiController
{
    //获坖
    public function getExpends(Request $request, $type)
    {
        self::Advertiser();
        
        $date = trim($request->input('date'));
        $advertiser_ad_id = trim($request->input('advertiser_ad_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit)){
            $limit = 10;
        }
        //每天数据
        if($type=='day'){

            $expends = AdvertiserExpendDay::select(
                'advertiser_expend_day.pv_number',
                'advertiser_expend_day.money',
                'advertiser_expend_day.date',
                'advertiser_ads.id as ad_id',
                'advertiser_ads.title',
                'advertiser_ads.price_type',
                'advertiser_ads.client',
                'advertiser_ads.is_wechat',
                'advertiser_ads.adstype_id', 
                'advertiser_ads.show_price', 
                'ads_position.cpc_price', 
                'ads_position.cpm_price')
                ->Join('advertiser_ads', 'advertiser_ads.id', '=', 'advertiser_expend_day.advertiser_ad_id')
                ->join('ads_position', 'ads_position.id','=','advertiser_ads.adstype_id')
                ->where('advertiser_ads.advertiser_id', '=', self::$user->id);

            if(!empty($date)){
                $expends = $expends->where('advertiser_expend_day.date', '=', $date);
            }
            if(!empty($advertiser_ad_id)){
                $expends = $expends->where('advertiser_expend_day.advertiser_ad_id', '=', $advertiser_ad_id);
            }

            $count = $expends->count();
            $expends = $expends->orderBy('advertiser_expend_day.id', 'desc')->offset($offset)->limit($limit)->get();
        }
        if($type=='hour')
        {
            $expends = AdvertiserExpendHour::select(
                'advertiser_expend_hour.pv_number',
                'advertiser_expend_hour.money',
                'advertiser_expend_hour.time',
                'advertiser_ads.id as ad_id',
                'advertiser_ads.title',
                'advertiser_ads.price_type',
                'advertiser_ads.client',
                'advertiser_ads.is_wechat',
                'advertiser_ads.adstype_id', 
                'advertiser_ads.show_price', 
                'ads_position.cpc_price', 
                'ads_position.cpm_price')
                ->Join('advertiser_ads', 'advertiser_ads.id', '=', 'advertiser_expend_hour.advertiser_ad_id')
                ->join('ads_position', 'ads_position.id','=','advertiser_ads.adstype_id')
                ->where('advertiser_ads.advertiser_id', '=', self::$user->id);

            if(!empty($date)){
                $expends = $expends->where('advertiser_expend_hour.time', '>=', $date.' 00:00:00')->where('advertiser_expend_hour.time', '<=', $date.' 23:59:59');
            }
            if(!empty($advertiser_ad_id)){
                $expends = $expends->where('advertiser_expend_hour.advertiser_ad_id', '=', $advertiser_ad_id);
            }

            $count = $expends->count();
            $expends = $expends->orderBy('advertiser_expend_hour.id', 'desc')->offset($offset)->limit($limit)->get();
        }

        $data = [
            'count'=>$count,
            'expends'=>$expends,
        ];

        return response()->json(['data'=>$data], 200);
    }
}








