<?php
namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\AgentsEarnings;
use App\Model\AgentsLogs;
use App\Model\AgentsBank;
use App\Model\Agents;
use App\Model\Users;
use App\Model\Setting;
use App\Model\Banks;
use App\Model\Advertiser;
use Hash;
use Session;

class DataController extends ApiController
{
    //获取
    public function getEarningsByday(Request $request)
    {
        //验证登陆
        self::Agent();


        $defaultDate = $this->defaultDate(14);

        //获取下线数量
        $lower_count = Advertiser::where('agent_id', '=', self::$user->id)->count();

        $add_lower_count = Advertiser::where('agent_id', '=', self::$user->id)
            ->where('created_at', '>=', date('Y-m-d').' 00:00:00')
            ->count();

        //昨天收益
        $yesterdayEarnings = AgentsEarnings::where('agent_id', '=', self::$user->id)
            ->where('date', '=', date("Y-m-d", strtotime("-1 day")))
            ->sum('money');

        //本周收益
        $thisWeekEarnings = AgentsEarnings::where('agent_id', '=', self::$user->id)
            ->where('date', '>=', $defaultDate[7]['date'])
            ->where('date', '<=', $defaultDate[13]['date'])
            ->sum('money');

        //最近14天收益
        $earnings = AgentsEarnings::where('agent_id', '=', self::$user->id)
            ->where('date', '=', date("Y-m-d", strtotime("-14 day")))
            ->get();


        $agents_earnings = [];
        for($i=13 ; $i>=0 ; $i--){
            $money = 0;
            $date = date('Y-m-d', strtotime('-'.$i.' day'));
            foreach($earnings as $k=>$v)
            {
                if($date == $v->date)
                {
                    $money = $v->money;
                }
            }
            $agents_earnings[] = [
                'money'=>$money,
                'date'=>$date,
            ];
        }
        
        $data = [
            'lower_count'=>$lower_count,
            'add_lower_count'=>$add_lower_count,
            'yesterdayEarnings'=>round($yesterdayEarnings, 2),
            'thisWeekEarnings'=>round($thisWeekEarnings, 2),
            'earnings'=>$agents_earnings,
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
}
