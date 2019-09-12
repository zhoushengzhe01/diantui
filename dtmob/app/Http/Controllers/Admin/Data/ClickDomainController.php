<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Model\EarningClick;


class ClickDomainController extends ApiController
{
    //页面加载到广告点击间隔时间
    public function getDomain(Request $request)
    {
        self::Admin();
        
        $cache_key = $request->input('cache_key');
        
        $clicks = Cache::get($cache_key);
        $count = count($clicks);

        $data = [];
        foreach($clicks as $key=>$val)
        {
            preg_match('/http(s)?:\/\/[a-zA-Z0-9\.]+/', $val->url, $res);
            $domain = preg_replace("/http(s)?:\/\//", "", $res[0]);

            //正则匹配域名
            if(empty($data[$domain]))
            {
                $data[$domain] = 1;
            }
            else
            {
                $data[$domain] += 1;
            }
        }
        arsort($data);
        
        //转换
        $new_data = [];
        foreach ($data as $key => $value) {
            $new_data[] = [
                'domain'=>$key,
                'number'=>$value,
                'percent'=>intval($value/$count*1000)/10 . '%',
            ];
        }
        return response()->json(['data'=>$new_data], 200);
    }
}
