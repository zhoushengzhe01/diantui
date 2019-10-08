<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Model\WebmasterAds;
use App\Model\EarningClick;
use App\Model\EarningDay;

class ClickCacheController extends ApiController
{
    //列表
    public function getCache(Request $request)
    {
        self::Admin();

        #搜索
        $webmaster_ad_id = trim($request->input('webmaster_ad_id'));
        $webmaster_id = trim($request->input('webmaster_id'));
        $date_time = trim($request->input('date_time'));
        $url = trim($request->input('url'));
        $offset = trim($request->input('offset'));

        #广告
        $ads = WebmasterAds::select('webmaster_ads.*', 'webmaster.username', 'webmaster.flow_pool_id', 'webmaster.grade', 'webmaster.alliance_agent_id', 'webmaster.service_id')
            ->join('webmaster', 'webmaster.id','=','webmaster_ads.webmaster_id');

        if(!empty($webmaster_id)){
            $ads = $ads->where('webmaster_ads.webmaster_id', $webmaster_id);
        }
        if(!empty($webmaster_ad_id)){
            $ads = $ads->where('webmaster_ads.id', $webmaster_ad_id);
        }
        $count = $ads->count();
        $ads = $ads->orderBy('webmaster_ads.earning_day', 'desc')->offset($offset)->limit(1)->get()->toArray();
        if(empty($ads)){
            return response()->json(['message'=>'找不到广告位'], 300);
        }

        $ad_id = $ads[0]['id'];

        #点击
        $clicks = EarningClick::where('myads_id', $ad_id);
        if(!empty($date_time)){
            $clicks = $clicks->where('created_at', '<=', date('Y-m-d').' '.$date_time.':00');
        }
        if(!empty($url)){
            $clicks = $clicks->where('url', 'like', '%'.$url.'%');
        }
        $clicks = $clicks->limit(500)->orderBy('id', 'desc')->get();
        if(count($clicks)<=0){
            return response()->json(['message'=>'没有点击'], 300);
        }
        $cache_key = 'webmaster_ad_' . $ad_id;
        Cache::put($cache_key, $clicks, 10);

        #小时
        $hour = [];
        for($i=0 ; $i<24 ; $i++){
            $hour[] = date("d日H点", strtotime('-'.$i.' hour'));
        }
        
        #每天收益
        $earningDay = EarningDay::where('webmaster_ad_id', $ad_id)->limit(5)->orderBy('id', 'desc')->get();

        $data = [
            'cache_key'=>$cache_key,
            'click_count'=>count($clicks),
            'count' => $count,
            'ads' => $ads,
            'ads'=>$ads,
            'hour' => $hour,
        ];

        return response()->json(['data'=>$data], 200);
    }
    
}
