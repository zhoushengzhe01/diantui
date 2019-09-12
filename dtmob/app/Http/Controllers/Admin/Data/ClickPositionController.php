<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Model\EarningClick;

#点击位置分布
class ClickPositionController extends ApiController
{
    public function getClickposition(Request $request)
    {
        self::Admin();
        
        $cache_key = $request->input('cache_key');
        
        $clicks = Cache::get($cache_key);

        $data = [];
        foreach($clicks as $key=>$val)
        {
        $data[] = self::clickposition($val);
        }

        return response()->json(['data'=>$data], 200);
    }

    public static function clickposition($user_agent)
    {
     
        $screen = explode('*', $user_agent->screen);
        $clickp = explode('*', $user_agent->clickp);

        if($screen[0] < $screen[1])
        {
            $position['width'] = intval($clickp[0] * 360 / $screen[0]);
            $position['height'] = intval($clickp[1] * 640 / $screen[1]);
        }
        else
        {
            $position['width'] = intval($clickp[1] * 640 / $screen[1]);
            $position['height'] = intval($clickp[0] * 360 / $screen[0]);
        }

        return $position;
    }
}
