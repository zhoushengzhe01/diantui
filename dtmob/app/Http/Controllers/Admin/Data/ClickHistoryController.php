<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Model\EarningClick;


class ClickHistoryController extends ApiController
{
    //页面加载到广告点击间隔时间
    public function getHistory(Request $request)
    {
        self::Admin();
        
        $cache_key = $request->input('cache_key');
        
        $clicks = Cache::get($cache_key);
        $count = count($clicks);

        $data = [];
        foreach($clicks as $key=>$val)
        {
            if($val->history<=5)
            {
                $history = intval($val->history/1) * 1;
            }
            else if($val->history<=20)
            {
                $history = intval($val->history/5) * 5;
            }
            else if($val->history<=100)
            {
                $history = intval($val->history/20) * 20;
            }
            else
            {
                $history = 1000;
            }
            
            if(empty($data[$history]))
            {
                $data[$history] = 1;
            }
            else
            {
                $data[$history] += 1;
            }
        }
        arsort($data);

        //转换
        $new_data = [];
        foreach ($data as $key => $value) {
            $new_data[] = [
                'number'=>$key,
                'value'=>$value,
                'percent'=>intval($value/$count*1000)/10 . '%',
            ];
        }
        return response()->json(['data'=>$new_data], 200);
    }
}
