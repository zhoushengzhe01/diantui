<?php
namespace App\Http\Controllers\Admin\Agent;

use Illuminate\Http\Request;

use App\Http\Controllers\ApiController;
use App\Model\AgentsEarnings;
use App\Model\AgentsLogs;
use App\Model\AgentsBank;
use App\Model\Agents;


class EarningController extends ApiController
{
    //列表
    public function getEarnings(Request $request)
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

        $earnings = AgentsEarnings::select('agents_earnings.*', 'agents.alliance_agent_id')
            ->join('agents', 'agents.id', '=', 'agents_earnings.agent_id');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $earnings = $earnings->where('agents.alliance_agent_id', self::$user->alliance_agent_id);
        }
        if(!empty($agent_id)){
           $earnings = $earnings->where('agents_earnings.agent_id', '=', $agent_id);
        }
        if(!empty($start_date)){
            $earnings = $earnings->where('agents_earnings.date', '>=', $start_date);
        }
        if(!empty($stop_date)){
            $earnings = $earnings->where('agents_earnings.date', '<=', $stop_date);
        }
        
        $count = $earnings->count();
        $earnings = $earnings->orderBy('agents_earnings.id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'earnings'=>$earnings,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
