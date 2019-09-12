<?php
namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\Advertiser;
use App\Model\AgentsEarnings;
use App\Model\AgentsLogs;
use App\Model\AgentsBank;
use App\Model\Agents;
use App\Model\AgentsMoney;
use App\Model\AgentsMoneyLog;


class MoneyController extends ApiController
{
    //获得
    public function getMoneys(Request $request)
    {
        self::Agent();

        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit)) {
            $limit = 10;
        }

        //我的素材
        $moneys = AgentsMoney::where('agent_id', '=', self::$user->id);
        $count  = $moneys->count();
        $moneys = $moneys->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();
        
        $data = [
            'count'=>$count,
            'moneys'=>$moneys,
        ];

        return response()->json(['data'=>$data], 200);
    }

    //获得余额变动
    public function getMoneyLogs(Request $request)
    {
        self::Agent();

        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit)) {
            $limit = 10;
        }

        //我的素材
        $moneylogs = AgentsMoneyLog::where('agent_id', '=', self::$user->id);
        $count  = $moneylogs->count();
        $moneylogs = $moneylogs->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'moneylogs'=>$moneylogs,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
