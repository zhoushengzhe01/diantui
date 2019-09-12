<?php
namespace App\Http\Controllers\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\WebmasterMoney;
use App\Model\WebmasterReward;
use App\Model\EarningDay;
use App\Model\EarningHour;

class MoneyController extends ApiController
{
    //佣金
    public function getEarnings(Request $request, $type)
    {
        self::Webmaster();

        //网站搜索
        $date = trim($request->input('date'));
        $position_id = trim($request->input('position_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        if($type=='day')
        {
            $earnings = EarningDay::where('webmaster_ads.webmaster_id', '=', self::$user->id)
                ->where('earning_day.created_at', '>=', self::$save_time)
                ->leftJoin('webmaster_ads', 'webmaster_ads.id', '=', 'earning_day.webmaster_ad_id')
                ->select('earning_day.*');

            $count = $earnings->count();
            $earnings = $earnings->orderBy('earning_day.id', 'desc')->offset($offset)->limit($limit)->get();
        }
        if($type=='hour')
        {
            $earnings = EarningHour::where('webmaster_ads.webmaster_id', '=', self::$user->id)
                ->where('earning_hour.created_at', '>=', self::$save_time)
                ->leftJoin('webmaster_ads', 'webmaster_ads.id', '=', 'earning_hour.webmaster_ad_id')
                ->select('earning_hour.*');

            $count = $earnings->count();
            $earnings = $earnings->orderBy('earning_hour.id', 'desc')->offset($offset)->limit($limit)->get();
        }

        $data = [
            'count'=>$count,
            'earnings'=>$earnings,
        ];

        return response()->json(['data'=>$data], 200);
    }

    //奖励报表
    public function getRewards(Request $request)
    {
        self::Webmaster();

        //网站搜索
        $date = trim($request->input('date'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $rewards = WebmasterReward::where('webmaster_id', '=', self::$user->id);

        if(!empty($date))
        {
            $rewards = $rewards->where('created_at', '>=', $date.' 00:00:00')
                ->where('created_at', '<=', $date.' 23:59:59')
                ->where('webmaster_reward.created_at', '>=', self::$save_time);
        }

        $count = $rewards->count();
        $rewards = $rewards->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'rewards'=>$rewards,
        ];

        return response()->json(['data'=>$data], 200);
    }

    //财务
    public function getMoneys(Request $request)
    {
        self::Webmaster();
        
        //网站搜索
        $date = trim($request->input('date'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $moneys = WebmasterMoney::where('webmaster_id', '=', self::$user->id)
            ->where('webmaster_money.created_at', '>=', self::$save_time);

        if(!empty($date))
        {
            $moneys = $moneys->where('created_at', '>=', $date.' 00:00:00')->where('created_at', '<=', $date.' 23:59:59');
        }

        $count = $moneys->count();
        $moneys = $moneys->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'moneys'=>$moneys,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
