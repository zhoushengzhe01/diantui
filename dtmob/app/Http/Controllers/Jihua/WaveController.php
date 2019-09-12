<?php
namespace App\Http\Controllers\Jihua;

use Illuminate\Support\Facades\Redis;
use Illuminate\Routing\Controller as BaseController;

use App\Model\Webmaster;
use App\Model\WebmasterAds;
use App\Model\EarningDay;
use App\Model\EarningHour;
use DB;

//统计流量波动脚本
class WaveController extends BaseController
{
    //设置流量波动
    public static function SetWebmasterWave()
    {
        ##凌晨12点处理
        if(date("H")=='0')
        {
            // $date1 = date("Y-m-d",strtotime("-1 day"));
            // $date2 = date("Y-m-d",strtotime("-2 day"));
            // $earningday1 = EarningDay::where('date', $date1)->where('pv_number', '>', 500)->get();
            // $earningday2 = EarningDay::where('date', $date2)->where('pv_number', '>', 500)->get();
            // foreach($earningday1 as $key=>$val)
            // {
            //     pv_number
            //     foreach($earningday2 as $k=>$v)
            //     {
                    
            //     }
            // }
        }
        else
        {
            $webmasterads = WebmasterAds::where('state', '1')->where('earning_day', '>', 0)->get();
            WebmasterAds::where('state', '1')->update(['wave'=>0]);
            foreach($webmasterads as $key=>$val)
            {
                #昨天
                $pv_number1 = EarningHour::where('webmaster_ad_id', $val->id)->where('time', '<', date("Y-m-d H", strtotime("-1 day")).':00:00')->where('time', '>=', date("Y-m-d", strtotime("-1 day")).' 00:00:00')->sum('pv_number');

                #今天
                $pv_number2 = EarningHour::where('webmaster_ad_id', $val->id)->where('time', '<', date("Y-m-d H").':00:00')->where('time', '>=', date("Y-m-d").' 00:00:00')->sum('pv_number');
                
                #基础PV
                $base_pv = intval(date("H"))*10;

                if($pv_number1<=$base_pv)
                {
                    $wave = 500;    //增量
                }
                if($pv_number2<=$base_pv)
                {
                    $wave = -500;   //扯量
                }

                if($pv_number1<=$base_pv && $pv_number2<=$base_pv)
                {
                    $wave = 0;      //没合作
                }
                
                if($pv_number1>$base_pv && $pv_number2>$base_pv)
                {
                    $wave = ($pv_number2 / $pv_number1 * 100) - 100;
                }  
                
                if($wave<-500)
                {
                    $wave = -500;
                }
                if($wave>500)
                {
                    $wave = 500;
                }

                WebmasterAds::where('id', $val->id)->update(['wave'=>$wave]);
            }
        }
    }
}