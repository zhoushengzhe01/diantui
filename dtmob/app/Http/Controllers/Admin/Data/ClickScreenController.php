<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Model\EarningClick;



class ClickScreenController extends ApiController
{
    //页面加载到广告点击间隔时间
    public function getScreen(Request $request)
    {
        self::Admin();

        $cache_key = $request->input('cache_key');

        $clicks = Cache::get($cache_key);
        $count = count($clicks);

        $data = [];
        foreach($clicks as $key=>$val)
        {   
            $screen = explode("*", $val->screen);
            
            if(count($screen)>0)
            {
                if($screen[0]<=$screen[1])
                {
                    $screen = $screen[0].'*'.$screen[1];
                }
                else
                {
                    $screen = $screen[1].'*'.$screen[0];
                }
            }

            if(empty($data[$screen]))
            {
                $data[$screen] = 1;
            }
            else
            {
                $data[$screen] += 1;
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
