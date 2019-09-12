<?php
namespace App\Http\Controllers\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\AdsPosition;
use App\Model\Webmaster;
use App\Model\WebmasterLowerEarnings;
use App\Model\WebmasterWebsite;

//下限提成
class LowerEarningController extends ApiController
{
    //获得下线
    public function getLowers(Request $request)
    {
        self::Webmaster();

        //网站搜索
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $lowers = Webmaster::select('id', 'username', 'created_at')->where('pid', '=', self::$user->id);
        $count = $lowers->count();
        $lowers = $lowers->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'lowers'=>$lowers,
        ];
        
        return response()->json(['data'=>$data], 200);
    }

    //获得下线收益
    public function getLowerEarnings(Request $request)
    {
        self::Webmaster();
        
        //获得下线数量
        $lower_count = Webmaster::where('pid', '=', self::$user->id)->count();

        //网站搜索
        $date = trim($request->input('date'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $lower_earnings = WebmasterLowerEarnings::select('webmaster_lower_earnings.*', 'webmaster.username')
            ->join('webmaster', 'webmaster.id', '=', 'webmaster_lower_earnings.lower_webmaster_id')
            ->where('webmaster.pid', '=', self::$user->id)
            ->where('webmaster_lower_earnings.created_at', '>=', self::$save_time);

        if(!empty($date))
        {
            $lower_earnings = $lower_earnings->where('webmaster_lower_earnings.date', '=', $date);
        }

        $count = $lower_earnings->count();
        $lower_earnings = $lower_earnings->orderBy('webmaster_lower_earnings.id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'lower_count'=>$lower_count,
            'count'=>$count,
            'lower_earnings'=>$lower_earnings,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
