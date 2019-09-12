<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use App\Http\Controllers\ApiController;
use App\Model\EarningClick;


class WebmasterClickController extends ApiController
{

    //列表
    public function getWebmasterClicks(Request $request)
    {
        self::Admin();

        ##网站搜索
        $webmaster_ad_id = trim($request->input('webmaster_ad_id'));
        $source_key = trim($request->input('source_key'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 20;
        }

        $earning_clicks = EarningClick::select('*');

        if(!empty($webmaster_ad_id))
        {
            $earning_clicks = $earning_clicks->where('myads_id', $webmaster_ad_id);
        }
        if(!empty($source_key))
        {
            $earning_clicks = $earning_clicks->where('source', 'like', '%'.$source_key.'%');
        }

        $count = $earning_clicks->count();
        $earning_clicks = $earning_clicks->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'earning_clicks'=>$earning_clicks,
        ];
        
        return response()->json(['data'=>$data], 200);

    }
}
