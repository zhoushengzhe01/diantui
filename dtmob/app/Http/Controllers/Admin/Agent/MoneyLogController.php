<?php
namespace App\Http\Controllers\Admin\Agent;

use Illuminate\Http\Request;

use App\Http\Controllers\ApiController;
use App\Model\AgentsEarnings;
use App\Model\AgentsLogs;
use App\Model\AgentsMoneyLog;
use App\Model\AgentsMoney;
use App\Model\AgentsBank;
use App\Model\Agents;


class MoneyLogController extends ApiController
{
    //列表
    public function getMoneyLogs(Request $request)
    {
        self::Admin();
        
        //网站杜索
        $agent_id = trim($request->input('agent_id'));
        $start_date = trim($request->input('start_date'));
        $stop_date = trim($request->input('stop_date'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $moneylogs = AgentsMoneyLog::select('*');
        if(!empty($agent_id)){
           $moneylogs = $moneylogs->where('agent_id', '=', $agent_id);
        }
        if(!empty($start_date))
        {
            $moneylogs = $moneylogs->where('date', '>=', $start_date);
        }
        if(!empty($stop_date))
        {
            $moneylogs = $moneylogs->where('date', '<=', $stop_date);
        }

        $count = $moneylogs->count();
        $moneylogs = $moneylogs->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'moneylogs'=>$moneylogs,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
