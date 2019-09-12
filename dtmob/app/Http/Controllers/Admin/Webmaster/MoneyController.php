<?php
namespace App\Http\Controllers\Admin\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\WebmasterMoneyLog;

class MoneyController extends ApiController
{

    public function getMoneys(Request $request)
    {
        self::Admin();

        //网站搜索
        $webmaster_id = trim($request->input('webmaster_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $moneylogs = WebmasterMoneyLog::select('webmaster_money_log.*', 'webmaster.alliance_agent_id')
            ->join('webmaster', 'webmaster.id','=','webmaster_money_log.webmaster_id');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $moneylogs = $moneylogs->where('webmaster.alliance_agent_id', '=', self::$user->alliance_agent_id);
        }
        ##客服限制权限
        if(self::$user->department_id==3){
            $moneylogs = $moneylogs->where('webmaster.service_id', '=', self::$user->id);
        }

        if(!empty($webmaster_id))
        {
            $moneylogs = $moneylogs->where('webmaster_money_log.webmaster_id', '=', $webmaster_id);
        }
        
        $count = $moneylogs->count();
        $moneylogs = $moneylogs->orderBy('webmaster_money_log.id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'moneylogs'=>$moneylogs,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
