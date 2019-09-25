<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Model\EarningClick;
use App\Http\Helpers\CityReader;


class ClickCityController extends ApiController
{
    #城市分布
    public function getCity(Request $request) {
        self::Admin();

        $cache_key = $request->input('cache_key');

        $clicks = Cache::get($cache_key);
        $count = count($clicks);

        $data = [];
        $reader = new CityReader;
        foreach($clicks as $key=>$val) {
            if( $val->ip && $val->ip!='unknown' ) {
                $ipData = $reader->findMap($val->ip, 'CN');
            }
            $city = empty($ipData['city_name']) ? '未知' : $ipData['city_name'];
            if(empty($data[$city])) {
                $data[$city] = 1;
            }else {
                $data[$city] += 1;
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
