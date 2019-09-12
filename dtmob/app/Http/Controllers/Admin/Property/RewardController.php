<?php
namespace App\Http\Controllers\Admin\Property;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\WebmasterReward;
use App\Model\Webmaster;
use App\Model\WebmasterMoneyLog;

class RewardController extends ApiController
{

    public function getRewards(Request $request)
    {
        self::Admin();

        //网站搜索
        $start_date = trim($request->get('start_date'));
        $stop_date = trim($request->get('stop_date'));
        $webmaster_id = trim($request->input('webmaster_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $rewards = WebmasterReward::select('webmaster_reward.*', 'webmaster.alliance_agent_id')
            ->join('webmaster', 'webmaster.id', '=', 'webmaster_reward.webmaster_id');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $rewards = $rewards->where('webmaster.alliance_agent_id', self::$user->alliance_agent_id);
        }
        if(!empty($start_date)){
            $rewards = $rewards->where('webmaster_reward.created_at', '>=', $start_date.' 00:00:00');
        }
        if(!empty($stop_date)){
            $rewards = $rewards->where('webmaster_reward.created_at', '<=', $stop_date.' 23:59:59');
        }
        if(!empty($webmaster_id)){
            $rewards = $rewards->where('webmaster_reward.webmaster_id', '=', $webmaster_id);
        }

        $count = $rewards->count();
        $all_money = $rewards->sum('webmaster_reward.money');
        $rewards = $rewards->orderBy('webmaster_reward.id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'all_money'=>$all_money,
            'count'=>$count,
            'rewards'=>$rewards,
        ];
        
        return response()->json(['data'=>$data], 200);

    }

    public function getReward(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $reward = WebmasterReward::where('id', '=', $id)->first();

        if(empty($reward))
            return response()->json(['message'=>'未找到数据'], 300);

        return response()->json(['data'=>['message'=>$reward]], 200);
    }

    public function putReward(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $reward = WebmasterReward::where('id', '=', $id)->first();
        if(empty($reward)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        $present = $request->input();

        if(!empty($present['note'])){ $reward->note = $present['note']; }
        
        if($reward->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

    public function postReward(Request $request)
    {
        self::Admin();

        $present = $request->input();

        if(empty($present['webmaster_id'])){
            return response()->json(['message'=>'请输入站长'], 300);
        }
        if(empty($present['money'])){
            return response()->json(['message'=>'请输入金额'], 300);
        }
        if(empty($present['note'])){
            return response()->json(['message'=>'请输入说明'], 300);
        }

        $webmaster = Webmaster::where('id', '=', $present['webmaster_id'])->first();
        if(empty($webmaster)){
            return response()->json(['message'=>'站长ID有误，找不到站长'], 300);
        }

        $reward = new WebmasterReward;
        $reward->webmaster_id = trim($present['webmaster_id']);
        $reward->money = trim($present['money']);
        $reward->note = trim($present['note']);
        
        if($reward->save())
        {
            $webmaster->money += $present['money'];
            $webmaster->save();
            
            //余额变动
            $moneyLog = new WebmasterMoneyLog;
            $moneyLog->webmaster_id = $reward->webmaster_id;
            $moneyLog->money = $reward->money;
            $moneyLog->message = $reward->note;
            $moneyLog->save();

            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }
}
