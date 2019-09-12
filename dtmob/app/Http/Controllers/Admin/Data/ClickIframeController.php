<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Model\EarningClick;


class ClickIframeController extends ApiController
{
    //页面加载到广告点击间隔时间
    public function getIframe(Request $request)
    {
        self::Admin();

        $cache_key = $request->input('cache_key');
        
        $clicks = Cache::get($cache_key);
        $count = count($clicks);
        
        $data = ['yes'=>0, 'no'=>0];

        foreach($clicks as $key=>$val)
        {
        	if($val->ifrom=='1')
        	{
        		 $data['yes'] += 1;
        	}
        	else
        	{
        		$data['no'] += 1;
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
