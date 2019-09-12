<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Model\EarningClick;


class ClickIosandroidController extends ApiController
{
    #终端IOS和安卓的分布
    public function getIosandroid(Request $request)
    {   
        self::Admin();

        $cache_key = $request->input('cache_key');

        $clicks = Cache::get($cache_key);
        $count = count($clicks);

        $data = [];
        foreach($clicks as $key=>$val)
        {
            $iosandroid = self::iosandroid($val->user_agent);
            if(empty($data[$iosandroid]))
            {
                $data[$iosandroid] = 1;
            }
            else
            {
                $data[$iosandroid] += 1;
            }
        }
        arsort($data);

        //转换
        $new_data = [];
        foreach ($data as $key => $value) {

            $new_data[] = [
                'name'=>$key,
                'value'=>$value,
                'percent'=>intval($value/$count*1000)/10 . '%',
            ];
        }
        return response()->json(['data'=>$new_data], 200);
    }

    public static function iosandroid($user_agent)
    {
        $type = 'other';

        if(strpos($user_agent, 'iPhone') || strpos($user_agent, 'iPad'))
        {
            $type = 'ios';
        }
        if(strpos($user_agent, 'Android'))
        {
            $type = 'android';
        }
        
        return $type;
    }
}
