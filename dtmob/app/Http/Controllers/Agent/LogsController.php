<?php
namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\AgentsEarnings;
use App\Model\AgentsLogs;
use App\Model\AgentsBank;
use App\Model\Agents;

class LogsController extends ApiController
{
    public function getLogs(Request $request)
    {
        self::Agent();

        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        //我的素材
        $logs = AgentsLogs::select("*");
        $count = $logs->count();
        $logs = $logs->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'logs'=>$logs,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
