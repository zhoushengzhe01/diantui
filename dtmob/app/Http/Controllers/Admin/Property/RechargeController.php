<?php
namespace App\Http\Controllers\Admin\Property;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\Advertiser;
use App\Model\Agents;
use App\Model\AdvertiserRecharge;
use App\Model\AdvertiserMoneyLog;
use DB;

class RechargeController extends ApiController
{
    public function getRecharges(Request $request)
    {
        self::Admin();

        //网站搜索
        $start_date = trim($request->get('start_date'));
        $stop_date = trim($request->get('stop_date'));
        $advertiser_id = trim($request->input('advertiser_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit)){
            $limit = 10;
        }

        $recharges = AdvertiserRecharge::select('advertiser_recharge.*', 'advertiser.alliance_agent_id')
            ->join('advertiser', 'advertiser.id', '=', 'advertiser_recharge.advertiser_id');
        
        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $recharges = $recharges->where('advertiser.alliance_agent_id', self::$user->alliance_agent_id);
        }
        
        if(!empty($start_date)){
            $recharges = $recharges->where('advertiser_recharge.created_at', '>=', $start_date.' 00:00:00');
        }
        if(!empty($stop_date)){
            $recharges = $recharges->where('advertiser_recharge.created_at', '<=', $stop_date.' 23:59:59');
        }
        if(!empty($advertiser_id)){
            $recharges = $recharges->where('advertiser_recharge.advertiser_id', '=', $advertiser_id);
        }

        $count = $recharges->count();
        $all_money = $recharges->sum('advertiser_recharge.money');
        $recharges = $recharges->orderBy('advertiser_recharge.id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'all_money'=>$all_money,
            'count'=>$count,
            'recharges'=>$recharges,
        ];

        return response()->json(['data'=>$data], 200);
    }

    public function getRecharge(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }
        
        $recharge = AdvertiserRecharge::where('id', '=', $id)->first();

        if(empty($recharge)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        return response()->json(['data'=>['message'=>$recharge]], 200);
    }

    public function postRecharge(Request $request)
    {
        self::Admin();

        $present = $request->input();
        if( empty($present['advertiser_id']) ) {
            return response()->json(['message'=>'请输入广告主ID'], 300);
        }
        if( empty($present['money']) ) {
            return response()->json(['message'=>'请输入充值金额'], 300);
        }

        $advertiser = Advertiser::where('id', '=', $present['advertiser_id'])->first();
        if(empty($advertiser)){
            return response()->json(['message'=>'找不到广告主'], 300);
        }
        if($present['outname']!=$advertiser->nickname){
            return response()->json(['message'=>'出款人和账户信息不符'], 300);
        }

        $recharge = new AdvertiserRecharge;
        $recharge->advertiser_id = $present['advertiser_id'];
        $recharge->message = $present['message'];
        $recharge->money = $present['money'];
        $recharge->operator = self::$user->username;
        $recharge->outname = $advertiser->nickname;
        $recharge->state = 1;

        if($recharge->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

    public function putRecharge(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $recharge = AdvertiserRecharge::where('id', '=', $id)->first();
        if(empty($recharge)){
            return response()->json(['message'=>'未找到数据'], 300);
        }
        if($recharge->state=='4'){
            return response()->json(['message'=>'充值已经完成无需在操作'], 300);
        }

        $advertiser = Advertiser::where('id', '=', $recharge->advertiser_id)->first();
        if(empty($advertiser)){
            return response()->json(['message'=>'找不到广告主'], 300);
        }

        $present = $request->input();
        if(!empty($present['money'])){ $recharge->money = $present['money']; }
        if(!empty($present['outname'])){ $recharge->outname = $present['outname']; }
        if(!empty($present['message'])){ $recharge->message = $present['message']; }
        if(!empty($present['state'])){ $recharge->state = $present['state']; }

        if($recharge->save())
        {
            if($present['state']=='4')
            {
                //查找代理有进行返点
                if( !empty($advertiser->agent_id) )
                {
                    $agent = Agents::where('id', $advertiser->agent_id)->first();
                    if( !empty($agent) )
                    {
                        $return_money = intval($recharge->money * $agent->return_point)/100;

                        $agent->money += $return_money;
                        $agent->save();

                        AdvertiserRecharge::where('id', '=', $id)->update(['return_point'=>$agent->return_point, 'return_money'=>$return_money]);

                        ##反点详情
                        DB::table('agents_earnings')->insert([
                            'agent_id' => $agent->id,
                            'advertiser_id' => $advertiser->id,
                            'advertiser_ad_id' => 1,
                            'return_point' => $agent->return_point,
                            'flowing_money'=> $recharge->money,
                            'date'=>'2018-06-01',
                            'money'=>$return_money,
                            'created_at'=>date('Y-m-d H:i:s'),
                        ]);

                        #余额变动
                        DB::table('agents_money_log')->insert([
                            'agent_id' => $agent->id,
                            'money' => $return_money,
                            'message' => '充值返现',
                            'balance' => $agent->money,
                            'state' => '1',
                            'created_at'=>date('Y-m-d H:i:s'),
                        ]);
                    }
                }

                //修改余额
                $advertiser->money += $recharge->money;
                $advertiser->save();

                //变动日志
                $moneyLog = new AdvertiserMoneyLog;
                $moneyLog->advertiser_id = $recharge->advertiser_id;
                $moneyLog->money = $recharge->money;
                $moneyLog->message = "账户充值";
                $moneyLog->save();

            }
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }
}
