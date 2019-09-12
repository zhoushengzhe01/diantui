<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\StatInterval;
use Hash;

class StatIntervalController extends ApiController
{

    public function getIntervals(Request $request, $webmaster_ad_id)
    {
        self::Admin();
        
        //网站搜索
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $intervals = StatInterval::where('webmaster_ad_id', '=', $webmaster_ad_id);

        $count = $intervals->count();
        $pc = $intervals->sum('pc');
        $intervals = $intervals->orderBy('pc', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'pc'=>$pc,
            'count'=>$count,
            'intervals'=>$intervals,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
