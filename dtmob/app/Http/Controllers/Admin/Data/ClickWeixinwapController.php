<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Model\EarningClick;


class ClickWeixinwapController extends ApiController
{
    #终端IOS和安卓的分布
    public function getWeixinwap(Request $request)
    {
        self::Admin();

        $cache_key = $request->input('cache_key');

        $clicks = Cache::get($cache_key);
        $count = count($clicks);

        $data = [];
        foreach($clicks as $key=>$val)
        {
            $weixinwap = self::weixinwap($val->user_agent);
            if(empty($data[$weixinwap]))
            {
                $data[$weixinwap] = 1;
            }
            else
            {
                $data[$weixinwap] += 1;
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

    public static function weixinwap($user_agent)
    {
        if(preg_match('/micromessenger\/[0-9]+/', strtolower($user_agent))>0)
        {
            return 'weixin';
        }
        else
        {
            return 'wap';
        }
    }
}
