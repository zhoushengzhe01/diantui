<?php
namespace App\Http\Controllers\Admin\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Webmaster;
use App\Model\WebmasterLowerEarnings;
use DB;

class LowerEarningController extends ApiController
{

    public function getLowerEarnings(Request $request)
    {
        self::Admin();

        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $webmaster_id = intval($request->get('webmaster_id'));
        $lower_webmaster_id = intval($request->get('lower_webmaster_id'));
        $start_date = trim($request->get('start_date'));
        $stop_date = trim($request->get('stop_date'));

        if(empty($start_date)) $start_date = date("Y-m"). "-01";
        if(empty($stop_date)) $stop_date = date("Y-m-d");
        
        $lowerearnings = WebmasterLowerEarnings::select('webmaster_lower_earnings.*', 'webmaster.alliance_agent_id')
            ->join('webmaster', 'webmaster_lower_earnings.webmaster_id', '=', 'webmaster.id');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $lowerearnings = $lowerearnings->where('webmaster.alliance_agent_id', '=', self::$user->alliance_agent_id);
        }
        ##客服限制权限
        if(self::$user->department_id==3){
            $lowerearnings = $lowerearnings->where('webmaster.service_id', '=', self::$user->id);
        }
        
        if(!empty($webmaster_id)){
            $lowerearnings = $lowerearnings->where('webmaster_lower_earnings.webmaster_id', '=', $webmaster_id);
        }
        if(!empty($lower_webmaster_id)){
            $lowerearnings = $lowerearnings->where('webmaster_lower_earnings.lower_webmaster_id', '=', $lower_webmaster_id);
        }
        if(!empty($start_date)){
            $lowerearnings = $lowerearnings->where('webmaster_lower_earnings.date', '>=', $start_date);
        }
        if(!empty($stop_date)){
            $lowerearnings = $lowerearnings->where('webmaster_lower_earnings.date', '<=', $stop_date);
        }

        $count = $lowerearnings->count();
        $lowerearnings = $lowerearnings->orderBy('webmaster_lower_earnings.id', 'desc')->offset($offset)->limit($limit)->get(['webmaster_ads.*','webmaster.username']);
        
        $data = [
            'count'=>$count,
            'lowerearnings'=>$lowerearnings,
        ];
        
        return response()->json(['data'=>$data], 200);
    }
}
