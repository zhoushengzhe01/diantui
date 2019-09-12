<?php
namespace App\Http\Controllers\Advertiser;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\AdvertiserExpendDay;
use App\Model\AdvertiserExpendHour;


class DataController extends ApiController
{
    //获取
    public function getData(Request $request)
    {
        self::Advertiser();

        $defaultDate = $this->defaultDate(14);

        $advertiser_ad_id = trim($request->input('myad_id'));

        $defaultDay = date('Y-m-d');
   
        //两周数据
        $weekExpends = AdvertiserExpendDay::where('advertiser_expend_day.date', '>=', $defaultDate[0]['date'])
                ->where('advertiser_expend_day.date', '<=', $defaultDate[13]['date'])
                ->where('advertiser_ads.advertiser_id', '=', self::$user->id)
                ->leftJoin('advertiser_ads', 'advertiser_ads.id', '=', 'advertiser_expend_day.advertiser_ad_id')
                ->select('advertiser_expend_day.*');

        if(!empty($advertiser_ad_id))
            $weekExpends = $weekExpends->where('advertiser_expend_day.advertiser_ad_id', '=', $advertiser_ad_id);

        $weekExpends = $weekExpends->get()->toArray();

        
        //今天数据
        $dayExpends = AdvertiserExpendHour::where('advertiser_expend_hour.state', '=', '1')
                ->where('advertiser_expend_hour.time', '>=', $defaultDay.' 00:00:00')
                ->where('advertiser_expend_hour.time', '<=', $defaultDay.' 23:59:59')
                ->where('advertiser_ads.advertiser_id', '=', self::$user->id)
                ->leftJoin('advertiser_ads', 'advertiser_ads.id', '=', 'advertiser_expend_hour.advertiser_ad_id')
                ->select();

        if(!empty($advertiser_ad_id))
            $dayExpends = $dayExpends->where('advertiser_expend_hour.advertiser_ad_id', '=', $advertiser_ad_id);

        $dayExpends = $dayExpends->get()->toArray();


        //一周数据
        $weekExpends = $this->groupWeekByDay($defaultDate, $weekExpends);
        
        //今天数据 为结算的小时+已结算的天数据
        $dayExpends = $this->groupDayByDay($dayExpends, $weekExpends);

        //昨天数据
        $yesterday = $this->getYesterday($weekExpends);
        

        //本周上周
        $week = $this->groupWeekByWeek($weekExpends, $dayExpends);
        
        $data = [
            'weekexpends' => $weekExpends,
            'yesterday' => $yesterday,
            'dayexpends' => $dayExpends,
            'week' => $week,
        ];

        return response()->json(['data'=>$data], 200);
    }

    //默认date
    public function defaultDate($number)
    {
        $week = (date("w")==0) ? 6 : (date("w")-1);

        $date = [];

        for($i=0 ; $i<$number ; $i++)
        {
            $key = $number-(7-$week)-$i;

            $date[$i]['date'] = date("Y-m-d",strtotime('-'.$key.' day'));
            if($i<7)
            {
                $date[$i]['week'] = 'last';
            }
            else
            {
                $date[$i]['week'] = 'this';
            }

        }

        return $date;
    }

    
    //获取昨天数据
    public function getYesterday($data)
    {
        $yesterday = [
            'pv_number'=> 0,
            'pc_number'=> 0,
            'money'=> 0,
        ];

        foreach($data as $k=>$v)
        {
            if($v['date']==date('Y-m-d', strtotime('-1 day')))
            {
                $yesterday['pv_number'] += $v['pv_number'];
                $yesterday['pc_number'] += $v['pc_number'];
                $yesterday['money'] += $v['money'];
            }
        }

        return $yesterday;
    }


    //汇总两周数据／天
    public function groupWeekByDay($default, $data)
    {   
        foreach($default as $key=>$val)
        {
            //初始化
            $val['pv_number'] = 0;
            $val['pc_number'] = 0;
            $val['money'] = 0;

            foreach($data as $k=>$v)
            {
                if($v['date']==$val['date'])
                {
                    $val['pv_number'] += $v['pv_number'];
                    $val['pc_number'] += $v['pc_number'];
                    $val['money'] += $v['money'];
                }

            }

            $default[$key] = $val;
        }
        return $default;
    }

    //汇总上周数据／
    public function groupWeekByWeek($weekExpends, $dayExpends)
    {
        //本周
        $lastWeek = [
            'pv_number'=> 0,
            'pc_number'=> 0,
            'money'=> 0,
        ];

        $thisWeek = [
            'pv_number'=> 0,
            'pc_number'=> 0,
            'money'=> 0,
        ];

        foreach($weekExpends as $key=>$val)
        {
            //上周
            if($val['week']=='last')
            {
               $lastWeek['pv_number'] += $val['pv_number'];
               $lastWeek['pc_number'] += $val['pc_number'];
               $lastWeek['money'] += $val['money'];
            }

            //本周
            if($val['week']=='this')
            {
                //今天的只加今天的
                if($val['date']==date('Y-m-d'))
                {
                    $thisWeek['pv_number'] += $dayExpends['pv_number'];
                    $thisWeek['pc_number'] += $dayExpends['pc_number'];
                    $thisWeek['money'] += $dayExpends['money'];
                }
                else
                {
                    $thisWeek['pv_number'] += $val['pv_number'];
                    $thisWeek['pc_number'] += $val['pc_number'];
                    $thisWeek['money'] += $val['money'];
                }   
            }
        }

        return ['lastWeek'=>$lastWeek, 'thisWeek'=>$thisWeek];
    }

    //汇总当天数据／天
    public function groupDayByDay($dayExpends, $weekExpends)
    {   
        //初始化
        $default = [
            'pv_number'=> 0,
            'pc_number'=> 0,
            'money'=> 0,
        ];
        
  
        foreach($dayExpends as $key=>$val)
        {   
            if(substr($val['time'], 0, 10)==date('Y-m-d'))
            {
                $default['pv_number'] += $val['pv_number'];
                $default['pc_number'] += $val['pc_number'];
                $default['money'] += $val['money'];
            }
        }

        foreach($weekExpends as $key=>$val)
        {
            if($val['date']==date('Y-m-d'))
            {
                $default['pv_number'] += $val['pv_number'];
                $default['pc_number'] += $val['pc_number'];
                $default['money'] += $val['money'];
            }
        }

        return $default;
    }
}