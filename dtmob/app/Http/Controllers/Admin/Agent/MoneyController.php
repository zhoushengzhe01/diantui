<?php
namespace App\Http\Controllers\Admin\Agent;

use Illuminate\Http\Request;

use App\Http\Controllers\ApiController;
use App\Model\AgentsEarnings;
use App\Model\AgentsLogs;
use App\Model\AgentsMoneyLog;
use App\Model\AgentsMoney;
use App\Model\AgentsBank;
use App\Model\Agents;


class MoneyController extends ApiController
{
    //列表
    public function getMoneys(Request $request)
    {
        self::Admin();
        
        //网站杜索
        $agent_id = trim($request->input('agent_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $moneys = AgentsMoney::select('agents_money.*', 'agents.alliance_agent_id')->join('agents', 'agents.id', '=', 'agents_money.agent_id');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $moneys = $moneys->where('agents.alliance_agent_id', self::$user->alliance_agent_id);
        }

        if(!empty($agent_id)){
           $moneys = $moneys->where('agents_money.agent_id', '=', $agent_id);
        }

        $count = $moneys->count();
        $moneys = $moneys->orderBy('agents_money.id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'moneys'=>$moneys,
        ];
        
        return response()->json(['data'=>$data], 200);
    }

    //获取单个
    public function getMoney(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        $money = AgentsMoney::where('id', '=', $id)->get();
        if(empty($money)){
            return response()->json(['message'=>'找不到信息'], 300);
        }

        $data = [
            'money'=>$money,
        ];

        return response()->json(['data'=>$data], 200);
    }

    //手动提款
    public function postMoney(Request $request)
    {
        self::Admin();

        $agent_id = $request->input('agent_id');
        $money = $request->input('money');

        //查找日结站长
        $agent = Agents::where('id', $agent_id)->first();
        if(empty($agent)){
            return response()->json(['message'=>'找不到代理信息'], 300);
        }
        if($agent->state!='1'){
            return response()->json(['message'=>'账号已经被封，不能提现'], 300);
        }
        if($money>$agent->money){
            return response()->json(['message'=>'提现金额超出，最多只能提现'. $agent->money], 300);
        }
        if($money<50){
            return response()->json(['message'=>'提现金额少于50元，拒绝提现'], 300);
        }
        //查找银行账户
        $bank = AgentsBank::where('agent_id', $agent->id)
            ->where('bankname', '<>', '')
            ->where('branch', '<>', '')
            ->where('account', '<>', '')
            ->where('accountid', '<>', '')
            ->first();
        
        if(empty($bank)){
            return response()->json(['message'=>'银行信息未填写，提现失败'], 300);
        }
        
        $agentsMoney = new AgentsMoney;
        $agentsMoney->agent_id = $agent->id;
        $agentsMoney->type = '1';
        $agentsMoney->money = $money;
        $agentsMoney->state = '1';
        $agentsMoney->bank_branch = $bank->branch;
        $agentsMoney->bank_name = $bank->bankname;
        $agentsMoney->bank_card = $bank->accountid;
        $agentsMoney->bank_account = $bank->account;
    
        if($agentsMoney->save())
        {
            Agents::where('id', '=', $agent->id)->update(['money'=> $agent->money-$money ]);

            //插入余额变动记录
            $agentsMoneyLog = new AgentsMoneyLog;
            $agentsMoneyLog->agent_id = $agent->id;
            $agentsMoneyLog->money = '-'.$money;
            $agentsMoneyLog->message = "提现";
            $agentsMoneyLog->save();

            return response()->json(['message'=>'提现成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'提现失败'], 300);
        }
    }

    //修改
    public function putMoney(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $money = AgentsMoney::where('id', '=', $id)->first();
        if(empty($money)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        //数据
        $present = $request->input();

        if(!empty($present['state'])){ $money->state = $present['state']; }
        if(!empty($present['message'])){ $money->message = $present['message']; }
        if(!empty($present['bank_branch'])){ $money->bank_branch = $present['bank_branch']; }
        if(!empty($present['bank_name'])){ $money->bank_name = $present['bank_name']; }
        if(!empty($present['bank_card'])){ $money->bank_card = $present['bank_card']; }
        if(!empty($present['bank_account'])){ $money->bank_account = $present['bank_account']; }

        if($money->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }
}
