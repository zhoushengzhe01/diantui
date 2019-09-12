<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Model\EarningClick;


class ClickSourceController extends ApiController
{
    //页面点击来源
    public function getClickSource(Request $request)
    {
        self::Admin();

        $cache_key = $request->input('cache_key');
        
        $clicks = Cache::get($cache_key);
        $count = count($clicks);

        $data = [];
        foreach($clicks as $key=>$val)
        {   
            $source = $val->ctype;

            if(empty($data[$source]))
            {
                $data[$source] = 1;
            }
            else
            {
                $data[$source] += 1;
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
}
