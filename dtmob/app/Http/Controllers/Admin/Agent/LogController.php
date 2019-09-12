<?php
namespace App\Http\Controllers\Admin\Agent;

use Illuminate\Http\Request;

use App\Http\Controllers\ApiController;
use App\Model\AgentsEarnings;
use App\Model\AgentsLogs;
use App\Model\AgentsBank;
use App\Model\Agents;


class LogController extends ApiController
{
    //列表
    public function getLogs(Request $request)
    {
        self::Admin();
        
        //网站杜索
        $agent_id = trim($request->input('agent_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $logs = AgentsLogs::select('agents_logs.*', 'agents.alliance_agent_id')->join('agents', 'agents.id', '=', 'agents_logs.agent_id');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $logs = $logs->where('agents.alliance_agent_id', self::$user->alliance_agent_id);
        }

        if(!empty($agent_id)){
           $logs = $logs->where('agents_logs.agent_id', '=', $agent_id);
        }

        $count = $logs->count();
        $logs = $logs->orderBy('agents_logs.id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'logs'=>$logs,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
