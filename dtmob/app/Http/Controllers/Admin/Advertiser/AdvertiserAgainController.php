<?php
namespace App\Http\Controllers\Admin\Advertiser;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\AdvertiserAds;
use App\Model\AgainDay;

use Hash;

class AdvertiserAgainController extends ApiController
{
    #二次点击
    public function getAgains(Request $request)
    {
        self::Admin();

        $advertiser_ad_id = trim($request->input('advertiser_ad_id'));
        $date = trim($request->input('date'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $agains = AgainDay::select('*')->where('pv_number', '>', 200);

       
        if(!empty($advertiser_ad_id))
        {
            $agains = $agains->where('advertiser_ad_id', '=', $advertiser_ad_id);
        }
        if(!empty($date))
        {
            $agains = $agains->where('date', '=', $date);
        }
        
        $count = $agains->count();
        $agains = $agains->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'agains'=>$agains,
        ];
        
        return response()->json(['data'=>$data], 200);
    }

}