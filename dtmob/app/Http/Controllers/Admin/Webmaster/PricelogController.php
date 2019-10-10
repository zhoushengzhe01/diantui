<?php
namespace App\Http\Controllers\Admin\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\WebmasterAdPriceLogs;

class PricelogController extends ApiController
{

    public function getLogs(Request $request, $ad_id)
    {
        self::Admin();

        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        if(empty($ad_id))
            return response()->json(['message'=>'缺少参数'], 400);

            $logs = WebmasterAdPriceLogs::where('ad_id', '=', $ad_id);

        $count = $logs->count();
        $logs = $logs->orderBy('created_at', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'data'=>$logs,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
