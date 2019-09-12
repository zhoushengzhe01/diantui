<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\StatLocation;
use Hash;

class StatLocationController extends ApiController
{

    public function getLocations(Request $request, $webmaster_ad_id)
    {
        self::Admin();
        
        //网站杜索
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $locations = StatLocation::where('webmaster_ad_id', '=', $webmaster_ad_id);

        $count = $locations->count();
        $pc = $locations->sum('pc');
        $locations = $locations->orderBy('pc', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'pc'=>$pc,
            'count'=>$count,
            'locations'=>$locations,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
