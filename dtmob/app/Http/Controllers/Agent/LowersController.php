<?php
namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\Advertiser;
use App\Model\AgentsEarnings;
use App\Model\AgentsLogs;
use App\Model\AgentsBank;
use App\Model\Agents;

class LowersController extends ApiController
{
    public function getLowers(Request $request)
    {
        self::Agent();

        $username = trim($request->input('username'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit)) {
            $limit = 10;
        }

        //我的素材
        $lowers = Advertiser::where('agent_id', '=', self::$user->id);
        if(!empty($username))
        {
            $lowers = $lowers->where('username', 'like', '%'.$username.'%');
        }
        $count = $lowers->count();
        $lowers = $lowers->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'lowers'=>$lowers,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
