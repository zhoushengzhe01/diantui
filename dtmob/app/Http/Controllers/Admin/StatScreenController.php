<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\StatScreen;
use Hash;

class StatScreenController extends ApiController
{

    public function getScreens(Request $request, $webmaster_ad_id)
    {
        self::Admin();
        
        //网站杜索
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $screens = StatScreen::where('webmaster_ad_id', '=', $webmaster_ad_id);

        $count = $screens->count();
        $pc = $screens->sum('pc');
        $screens = $screens->orderBy('pc', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'pc'=>$pc,
            'count'=>$count,
            'screens'=>$screens,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
