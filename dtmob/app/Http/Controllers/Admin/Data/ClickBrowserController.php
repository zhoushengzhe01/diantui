<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\ApiController;
use App\Model\EarningClick;


class ClickBrowserController extends ApiController
{
    #终端浏览器分布
    public function getBrowser(Request $request)
    {
        self::Admin();

        $cache_key = $request->input('cache_key');
        
        $clicks = Cache::get($cache_key);
        $count = count($clicks);

        $data = [];
        foreach($clicks as $key=>$val)
        {
            $browser = self::browser($val->user_agent);
            if(empty($data[$browser]))
            {
                $data[$browser] = 1;
            }
            else
            {
                $data[$browser] += 1;
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


    public static function browser($user_agent)
    {
        $name = 'other';

        if(preg_match('/micromessenger\/[0-9]+/', strtolower($user_agent))>0)
        {
            $name = 'micromessenger';
        }
        else
        {
            preg_match('/[a-z0-9]+browser/', strtolower($user_agent), $matches);

            if( count($matches)>0 )
            {
                $name = $matches[0];
            }
            else
            {
                if(preg_match('/baiduboxapp\/[0-9]+/', strtolower($user_agent))>0)
                {
                    $name = 'baiduboxapp';
                }
                else if(preg_match('/quark\/[0-9]+/', strtolower($user_agent))>0)
                {
                    $name = 'quark';
                }
                else if(preg_match('/eui\ browser\/[0-9]+/', strtolower($user_agent))>0)
                {
                    $name = 'euibrowser';
                }
                else if(preg_match('/mqbhd/', strtolower($user_agent))>0)
                {
                    $name = 'mqbhd';
                }
                else if(preg_match('/opr\/[0-9]+/', strtolower($user_agent))>0)
                {
                    $name = 'opr';
                }
                else if(preg_match('/liebao/', strtolower($user_agent))>0)
                {
                    $name = 'liebao';
                }
                else if(preg_match('/firefox\/[0-9]+/', strtolower($user_agent))>0)
                {
                    $name = 'firefox';
                }
                else if(preg_match('/maxthon\/[0-9]+/', strtolower($user_agent))>0)
                {
                    $name = 'maxthon';
                }
                else if(preg_match('/crios\/[0-9]+/', strtolower($user_agent))>0)
                {
                    $name = 'google';
                }
                else if(preg_match('/mxios\/[0-9]+/', strtolower($user_agent))>0)
                {
                    $name = 'mxios';
                }
                else if(preg_match('/sogousearch/', strtolower($user_agent))>0)
                {
                    $name = 'sogousearch';
                }
                else if(preg_match('/qq\/[0-9]/', strtolower($user_agent))>0)
                {
                    $name = 'qqapp';
                }
                else if(preg_match('/searchcraft\/[0-9]/', strtolower($user_agent))>0)
                {
                    $name = 'searchcraft';
                }
                else if(preg_match('/safari/', strtolower($user_agent))>0 && preg_match('/chrome/', strtolower($user_agent))<=0)
                {
                    if(preg_match('/iphone/', strtolower($user_agent))>0 || preg_match('/ipad/', strtolower($user_agent))>0)
                    {
                        $name = 'safari';
                    }
                }
            }
        }

        preg_match('/[a-z]+/', $name, $matches);

        return $matches[0];
    }
}
