<?php
namespace App\Http\Controllers\Jihua;

use Illuminate\Support\Facades\Redis;
use Illuminate\Routing\Controller as BaseController;

use App\Model\Advertiser;
use App\Model\AdvertiserAds;
use App\Model\EarningClick;
use App\Model\WebmasterAds;
use App\Model\AdvertiserExpendHour;
use App\Model\EarningHour;
use App\Model\AdvertiserWholePointMoney;
use DB;

//统计流量波动脚本
class OtherController extends BaseController
{
    //计算广告主上一个小时的消耗
    public static function lastHourConsume()
    {
        //清除原来的
        Advertiser::where('last_hour_consume', '>' , 0)->update(['last_hour_consume'=>0.00]);

        $hourtime = date("Y-m-d H", strtotime("-1 hour")).':00:00';

        $offset = 0;

        startHourConsume:

        $hour_consume = AdvertiserExpendHour::select('advertiser_expend_hour.advertiser_ad_id', 'advertiser_expend_hour.money', 'advertiser_ads.advertiser_id')
            ->join('advertiser_ads', 'advertiser_ads.id','=','advertiser_expend_hour.advertiser_ad_id')
            ->where('advertiser_expend_hour.money', '>', 0)
            ->where('advertiser_expend_hour.time', '=', $hourtime)
            ->offset($offset)->limit(50)
            ->get()
            ->toArray();

        if(!empty($hour_consume))
        {
            foreach($hour_consume as $key=>$val)
            {
                DB::select('update advertiser set last_hour_consume=last_hour_consume+' . $val['money'] . ' where id=' . $val['advertiser_id']);
            }
            
            $offset += 50;

            goto startHourConsume;
        }
    }

    //广告主整点余额
    public static function AdvertiserWholePointMoney()
    {
        $offset = 0;
        startPointMoney:

        $advertiser = Advertiser::select('id', 'money')
            ->offset($offset)->limit(50)
            ->get()
            ->toArray();


        if(!empty($advertiser))
        {
            foreach($advertiser as $key=>$val)
            {
                $pointMoney = new AdvertiserWholePointMoney;
                $pointMoney->advertiser_id = $val['id'];
                $pointMoney->money = $val['money'];
                $pointMoney->date = date("Y-m-d H:i:s");
                $pointMoney->save();
            }
            
            $offset += 50;

            goto startPointMoney;
        }
       
    }

    public static function test()
    {
        //查找昨天晚上账户余额，减掉现在账户余额，插入今天小时消耗里面
        $pointMoney = AdvertiserWholePointMoney::where('created_at', 'like', '2019-04-07 12%')->get();
        foreach($pointMoney as $key=>$val)
        {
            $advertiser = Advertiser::where('id', $val->advertiser_id)->first();
            $advertiserAd = AdvertiserAds::where('advertiser_id', $val->advertiser_id)->where('state', 1)->first();

            if(!empty($advertiserAd))
            {
                $money = $val->money - $advertiser->money;
                if($money>0)
                {
                    #广告主小时数据
                    $expendHour = AdvertiserExpendHour::firstOrCreate(['advertiser_ad_id'=>$advertiserAd->id,'time'=>'2019-04-07 12:30:00']);
                    $expendHour->pc_number = $money/0.01;
                    $expendHour->pv_number = $money/0.008;
                    $expendHour->ip_number = $money/0.04;
                    $expendHour->money = $money;
                    $expendHour->out_money = $money/1.7;
                    $expendHour->save();
                    echo $advertiserAd->id;
                    echo $money;
                }
            }
        }
    }
    
    //通过IP京东接口给IP评分
    public static function ip_point()
    {
        Start:

        $date_time = date("Y-m-d", strtotime("-0 day"));

        $webmaster_ads = WebmasterAds::where('state', '1')->where('ip_point_time', '<', $date_time)->where('ad_ratio', '>', 50)->where('earning_day', '>', '20')->orderBy('earning_day', 'desc')->offset(0)->limit(5)->get();
        #$webmaster_ads = WebmasterAds::where('ip_point_time', '<', $date_time)->where('ad_ratio', '>', 50)->where('id', '3828')->orderBy('earning_day', 'desc')->offset(0)->limit(5)->get();

        if( count($webmaster_ads) > 0 )
        {
            foreach($webmaster_ads as $key=>$val)
            {

                echo $val->id."-------------------------------->start\r\n";

                $clicks = EarningClick::select('id', 'ip')->where('myads_id', $val->id)->orderBy('id', 'desc')->offset(0)->limit(100)->get();

                $number = 0;
                $point = 0;
                foreach($clicks as $k=>$v)
                {
                    $res = json_decode(self::get_ip_point_by_ip($v->ip));
                    
                    if( !empty($res->result) )
                    {
                        $result = $res->result;
                        if( !empty($result->data) )
                        {
                            echo $v->ip . '-->' . $result->data->score;
                            echo "\r\n";

                            $point += $result->data->score;
                            $number += 1;
                        }
                    }
                }

                $point = intval($point/$number);

                WebmasterAds::where('id', $val->id)->update(['ip_point'=>$point, 'ip_point_time'=>$date_time]);

                echo $val->id.'-------------------------------->'.$point."\r\n";

            }

            goto Start;
        }
    }

    public static function get_ip_point_by_ip($ip)
    {
        usleep(500);
        $url = "https://way.jd.com/RTBAsia/nht?ip=".$ip."&appkey=909bff6f39b7bca81f38274a1c4c65c3";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        curl_close($ch);

        if($code=='200')
        {
            return $result;
        }
        else
        {
            return false;
        }
    }
}