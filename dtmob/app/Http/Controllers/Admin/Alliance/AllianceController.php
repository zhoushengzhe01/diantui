<?php
namespace App\Http\Controllers\Admin\Alliance;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\Alliance;
use App\Model\AllianceDay;
use App\Model\AllianceHour;

class AllianceController extends ApiController
{

    public function getAlliances(Request $request)
    {
        self::Admin();

        //网站搜索
        $name = trim($request->input('name'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $alliances = Alliance::select('*');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $alliances = $alliances->where('alliance_agent_id', '=', self::$user->alliance_agent_id);
        }

        if(!empty($name)){
            $alliances = $alliances->where('name', 'like', '%'.$name.'%');
        }

        $count = $alliances->count();
        $alliances = $alliances->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'alliances'=>$alliances,
        ];
        
        return response()->json(['data'=>$data], 200);
    }

    public function getAlliance(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $alliance = Alliance::where('id', '=', $id)->first();

        if(empty($alliance))
            return response()->json(['message'=>'未找到数据'], 300);

        return response()->json(['data'=>['alliance'=>$alliance]], 200);
        
    }

    public function putAlliance(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $alliance = Alliance::where('id', '=', $id)->first();

        if(empty($alliance))
            return response()->json(['message'=>'未找到数据'], 300);
        
        
        $present = $request->input();

        if(!empty($present['name'])){ $alliance->name = $present['name']; }
        if(!empty($present['alliance_agent_id'])){ $alliance->alliance_agent_id = $present['alliance_agent_id']; }
        if(!empty($present['position_id'])){ $alliance->position_id = $present['position_id']; }
        if(!empty($present['position'])){ $alliance->position = $present['position']; }
        if(!empty($present['account'])){ $alliance->account = $present['account']; }
        if(!empty($present['password'])){ $alliance->password = $present['password']; }
        if(!empty($present['login_url'])){ $alliance->login_url = $present['login_url']; }
        if(!empty($present['record_num'])){ $alliance->record_num = $present['record_num']; }
        if(!empty($present['code'])){ $alliance->code = $present['code']; }
        if(!empty($present['stat_code'])){ $alliance->stat_code = $present['stat_code']; }
        if(!empty($present['state'])){ $alliance->state = $present['state']; }
        if(!empty($present['price'])){ $alliance->price = $present['price']; }
        if(!empty($present['price_cpm'])){ $alliance->price_cpm = $present['price_cpm']; }

        if($alliance->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

    public function postAlliance(Request $request)
    {
        self::Admin();

        $present = $request->input();

        if(empty($present['name'])){
            return response()->json(['message'=>'请输入联盟名称'], 300);
        }
        if(empty($present['position_id'])){
            return response()->json(['message'=>'请选择广告类型'], 300);
        }
        if(empty($present['position'])){
            return response()->json(['message'=>'请选择广告位置'], 300);
        }
        if(empty($present['account'])){
            return response()->json(['message'=>'请选输入联盟账号'], 300);
        }
        if(empty($present['password'])){
            return response()->json(['message'=>'请选输入联盟密码'], 300);
        }
        if(empty($present['login_url'])){
            return response()->json(['message'=>'请选输入联盟地址'], 300);
        }
        if(empty($present['record_num'])){
            return response()->json(['message'=>'请选输入计费次数'], 300);
        }
        if(empty($present['code'])){
            return response()->json(['message'=>'请选输入广告代码'], 300);
        }
        if(empty($present['stat_code'])){
            return response()->json(['message'=>'请选输入统计代码'], 300);
        }
        if(empty($present['state'])){
            return response()->json(['message'=>'请选择状态'], 300);
        }
        if(empty($present['price'])){
            return response()->json(['message'=>'请输入广告点击价格'], 300);
        }
        if(empty($present['price_cpm'])){
            return response()->json(['message'=>'请输入广告展示价格'], 300);
        }
        if(empty($present['alliance_agent_id'])){
            return response()->json(['message'=>'选择联盟'], 300);
        }
        

        $alliance = new Alliance;
        $alliance->name = trim($present['name']);
        $alliance->alliance_agent_id = trim($present['alliance_agent_id']);
        $alliance->position_id = trim($present['position_id']);
        $alliance->position = trim($present['position']);
        $alliance->account = trim($present['account']);
        $alliance->password = trim($present['password']);
        $alliance->login_url = trim($present['login_url']);
        $alliance->record_num = trim($present['record_num']);
        $alliance->code = trim($present['code']);
        $alliance->stat_code = trim($present['stat_code']);
        $alliance->state = trim($present['state']);
        $alliance->price = trim($present['price']);
        $alliance->price_cpm = trim($present['price_cpm']);
        
        if($alliance->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

    public function getSpendings(Request $request)
    {
        
        self::Admin();

        //网站搜索
        $date = trim($request->input('date'));
        $alliance_id = trim($request->input('alliance_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $spendings = AllianceDay::select('alliance_day.*', 'alliance.record_num', 'alliance.price', 'alliance.name', 'alliance.alliance_agent_id')
            ->join('alliance', 'alliance.id', '=', 'alliance_day.alliance_id');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $spendings = $spendings->where('alliance.alliance_agent_id', '=', self::$user->alliance_agent_id);
        }

        if(!empty($date)){
            $spendings = $spendings->where('alliance_day.created_at', '>=', $date.' 00:00:00')->where('alliance_day.created_at', '<=', $date.' 23:59:59');
        }
        
        if(!empty($alliance_id)){
            $spendings = $spendings->where('alliance_day.alliance_id', '=', $alliance_id);
        }

        $count = $spendings->count();
        $spendings = $spendings->orderBy('id', 'desc')->offset($offset)->limit($limit)->get(['alliance.name', 'alliance.record_num', 'alliance.price', 'alliance_day.*']);

        $data = [
            'count'=>$count,
            'spendings'=>$spendings,
        ];
        
        return response()->json(['data'=>$data], 200);

    }
}
