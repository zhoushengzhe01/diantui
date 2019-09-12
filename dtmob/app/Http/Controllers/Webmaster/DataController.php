<?php
namespace App\Http\Controllers\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\WebmasterAds;
use App\Model\EarningHour;
use App\Model\EarningDay;
use App\Model\Webmaster;
use App\Model\WebmasterLowerEarnings;
use DB;


class DataController extends ApiController
{
    //首页数据
    public function getData(Request $request)
    {
        self::Webmaster();

        //今日收益
        $todayEarning = EarningHour::where('earning_hour.time', '>=', date('Y-m-d').' 00:00:00')
            ->where('earning_hour.time', '<=', date('Y-m-d').' 23:59:59')
            ->where('webmaster_ads.webmaster_id', '=', self::$user->id)
            ->leftJoin('webmaster_ads', 'webmaster_ads.id', '=', 'earning_hour.webmaster_ad_id')
            ->sum('earning_hour.money');

        //昨日收益
        $yesterdayEarning = EarningDay::where('earning_day.date', '=', date("Y-m-d", strtotime("-1 day")))
            ->where('webmaster_ads.webmaster_id', '=', self::$user->id)
            ->leftJoin('webmaster_ads', 'webmaster_ads.id', '=', 'earning_day.webmaster_ad_id')
            ->sum('earning_day.money');

        //下线数量
        $lowerCount = Webmaster::where('pid', '=', self::$user->id)->count();

        //下线收益
        $lowerEarning = WebmasterLowerEarnings::where('date', '=', date("Y-m-d", strtotime("-1 day")))
            ->where('webmaster_id', '=', self::$user->id)
            ->sum('money');


        //7天广告收益
        $earnings = DB::select("select sum(earning_day.money) as money, earning_day.date from earning_day 
            inner join webmaster_ads on webmaster_ads.id = earning_day.webmaster_ad_id
            where webmaster_ads.webmaster_id = '".self::$user->id."'
            and earning_day.date > '".date("Y-m-d", strtotime("-7 day"))."'
            and earning_day.date <= '".date("Y-m-d")."'
            group by earning_day.date
            order by earning_day.date asc");

        //7天下线收益
        $lowerEarnings = DB::select("select sum(money) as money, date from webmaster_lower_earnings 
            where webmaster_id = '".self::$user->id."'
            and date > '".date("Y-m-d", strtotime("-7 day"))."'
            and date <= '".date("Y-m-d")."'
            group by date
            order by date asc");

        $data = [
            'todayEarning' => round($todayEarning, 3),
            'yesterdayEarning' => round($yesterdayEarning, 3),
            'earnings' => $this->defaultDate($earnings),
            'lowerCount' => $lowerCount,
            'lowerEarning' => round($lowerEarning, 3),
            'lowerEarnings' => $this->defaultDate($lowerEarnings),
        ];

        return response()->json(['data'=>$data], 200);
    }

    public function defaultDate($earnings)
    {

        for($i=6 ; $i>=0 ; $i--)
        {
            $data = [
                'date' => date("Y-m-d", strtotime("-".$i." day")),
                'money' => 0,
            ];

            $is_exist = false;

            foreach($earnings as $key=>$val)
            {
                if( $data['date']==$val->date )
                {
                    $data['money'] = round($val->money, 3);
                }
            }

            $result[] = $data;
        }


        return $result;
    }
}