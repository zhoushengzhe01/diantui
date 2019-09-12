<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\StatRegion;
use Hash;

class StatRegionController extends ApiController
{

    public function getRegions(Request $request, $webmaster_ad_id)
    {
        self::Admin();
        
        //网站杜索
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $regions = StatRegion::where('webmaster_ad_id', '=', $webmaster_ad_id);

        $count = $regions->count();
        $pc = $regions->sum('pc');
        $regions = $regions->orderBy('pc', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'pc'=>$pc,
            'count'=>$count,
            'regions'=>$regions,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
