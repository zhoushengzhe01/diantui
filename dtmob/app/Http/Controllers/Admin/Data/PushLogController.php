<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use App\Http\Controllers\ApiController;
use App\Model\ClickPushLogs;


class PushLogController extends ApiController
{

    //列表
    public function getPushLogs(Request $request)
    {
        self::Admin();

        //网站搜索
        $state = trim($request->input('state'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        
        if(empty($limit)){
            $limit = 30;
        }

        $pushlogs = ClickPushLogs::where('id', '<>', '0');
        if(!empty($state))
        {
            $pushlogs = $pushlogs->where('state', $state);
        }

        $count = $pushlogs->count();
        $pushlogs = $pushlogs->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'pushlogs'=>$pushlogs,
        ];

        return response()->json(['data'=>$data], 200);

    }
}
