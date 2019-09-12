<?php
namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\AgentsEarnings;
use App\Model\AgentsLogs;
use App\Model\AgentsBank;
use App\Model\Agents;

class EarningsController extends ApiController
{
    public function getEarnings(Request $request)
    {
        //验证登陆
        self::Agent();

        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit)){
            $limit = 10;
        }

        //我的素材
        $earnings = AgentsEarnings::where('agent_id', '=', self::$user->id);

        $count = $earnings->count();
        $earnings = $earnings->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'earnings'=>$earnings,
        ];
        
        return response()->json(['data'=>$data], 200);
    }
}
