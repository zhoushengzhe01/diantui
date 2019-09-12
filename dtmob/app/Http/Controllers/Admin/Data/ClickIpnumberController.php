<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Model\EarningClick;


class ClickIpnumberController extends ApiController
{
    //页面加载到广告点击间隔时间
    public function getIpnumber(Request $request)
    {
        self::Admin();
        
        $cache_key = $request->input('cache_key');
        
        $clicks = Cache::get($cache_key);
        $count = count($clicks);

        $data = [];
        foreach($clicks as $key=>$val)
        {
            if($val->ipnumber<=10)
            {
                $ipnumber = intval($val->ipnumber/1) * 1;
            }
            else if($val->ipnumber<=100)
            {
                $ipnumber = intval($val->ipnumber/10) * 10;
            }
            else
            {
                $ipnumber = 1000;
            }
            
            if(empty($data[$ipnumber]))
            {
                $data[$ipnumber] = 1;
            }
            else
            {
                $data[$ipnumber] += 1;
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
