<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Model\EarningClick;


class ClickIntervaltimeController extends ApiController
{
    //页面加载到广告点击间隔时间
    public function getIntervaltime(Request $request)
    {
        self::Admin();
        
        $cache_key = $request->input('cache_key');
        
        $clicks = Cache::get($cache_key);
        $count = count($clicks);

        $data = [];
        foreach($clicks as $key=>$val)
        {
            if($val->interval<=10)
            {
                $intervaltime = intval($val->interval/2) * 2;
            }
            else if($val->interval<=100)
            {
                $intervaltime = intval($val->interval/10) * 10;
            }
            else if($val->interval<=200)
            {
                $intervaltime = intval($val->interval/100) * 100;
            }
            else if($val->interval<=2000)
            {
                $intervaltime = intval($val->interval/200) * 200;
            }
            else if($val->interval<=20000)
            {
                $intervaltime = 20000;
            }
           
            
            if(empty($data[$intervaltime]))
            {
                $data[$intervaltime] = 1;
            }
            else
            {
                $data[$intervaltime] += 1;
            }
        }

        arsort($data);

        //转换
        $new_data = [];
        foreach ($data as $key => $value) {
            $new_data[] = [
                'second'=>$key,
                'value'=>$value,
                'percent'=>intval($value/$count*1000)/10 . '%',
            ];
        }

        return response()->json(['data'=>$new_data], 200);
    }
}
