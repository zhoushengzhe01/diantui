<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Model\EarningClick;


class ClickSystemversionController extends ApiController
{
    #系统版本
    public function getSystemversion(Request $request)
    {
        self::Admin();

        $cache_key = $request->input('cache_key');
        
        $clicks = Cache::get($cache_key);
        $count = count($clicks);
            
        $data = [];
        foreach($clicks as $key=>$val)
        {
            $system = self::systemversion($val->user_agent);
            if(empty($data[$system]))
            {
                $data[$system] = 1;
            }
            else
            {
                $data[$system] += 1;
            }
        }
        arsort($data);

        //转换
        $new_data = [];
        foreach ($data as $key => $value) {
            $new_data[] = [
                'version'=>$key,
                'value'=>$value,
                'percent'=>intval($value/$count*1000)/10 . '%',
            ];
        }

        return response()->json(['data'=>$new_data], 200);
    }

    public static function systemversion($user_agent)
    {
        $system = 'other';
        
        if(preg_match("/Android\ [0-9]+\.[0-9]+|Android\ [0-9]+/", $user_agent, $result))
        {
            $system = $result[0];
        }
        else if(preg_match("/iPhone\ OS\ [0-9]+\_[0-9]+/", $user_agent, $result))
        {
            $system = $result[0];
        }
        else if(preg_match("/iPad\;\ U\;\ CPU\ OS\ [0-9]+\_[0-9]+|iPad\;\ CPU\ OS\ [0-9]+\_[0-9]+/", $user_agent, $result))
        {
            $system = $result[0];
        }
        
        $system = preg_replace("/\ |\;|CPU/", "_", $system);
        $system = preg_replace("/\____|___|__/", "_", $system);
        return $system;
    }   
}