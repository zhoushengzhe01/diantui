<?php
namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\Advertiser;
use App\Model\AgentsEarnings;
use App\Model\AgentsLogs;
use App\Model\AgentsBank;
use App\Model\Agents;

class UserController extends ApiController
{
    public function getUser(Request $request)
    {
        self::Agent();

        $agent = Agents::where('id', '=', self::$user->id)->first();

        if(empty($agent)){
            $agent = (object)[];
        }

        $data = [
            'user'=>$agent,
        ];
        return response()->json(['data'=>$data], 200);
    }

    public function putUser(Request $request)
    {
        self::Agent();
        
        $username = trim($request->input('username'));
        $nickname = trim($request->input('nickname'));
        $email = trim($request->input('email'));
        $mobile = trim($request->input('mobile'));
        $qq = trim($request->input('qq'));
        
        if(empty($username)){
            return response()->json(['message'=>'请输入用户名'], 300);
        }
        if(empty($nickname)){
            return response()->json(['message'=>'请输入真实姓名'], 300);
        }
        if(empty($email)){
            return response()->json(['message'=>'请输入你的邮箱'], 300);
        }
        if(empty($qq)){
            return response()->json(['message'=>'请输入你的QQ'], 300);
        }

        #验证用户名是否重复
        $count = Agents::where('username', '=', $username)->where('id', '<>', self::$user->id)->count();
        if(!empty($count)){
            return response()->json(['message'=>'用户名已存在'], 300);
        }

        $agent = Agents::where('id', '=', self::$user->id)->first();
        if(empty($agent)){
            return response()->json(['message'=>'找不到用户信息'], 300);
        }
        $agent->username = $username;
        $agent->nickname = $nickname;
        $agent->email = $email;
        $agent->mobile = $mobile;
        $agent->qq = $qq;

        if($agent->save()){
            return response()->json(['message'=>'保存成功'], 200);
        }else{
            return response()->json(['message'=>'保存失败'], 300);
        }
    }

    #获取银行信息
    public function getBank(Request $request)
    {
        self::Agent();
        $bank = AgentsBank::where('agent_id', '=', self::$user->id)->first();

        if(empty($bank)){
            $bank = (object)[];
        }

        $data = [
            'bank'=>$bank,
        ];
        return response()->json(['data'=>$data], 200);
    }

    public function putBank(Request $request)
    {
        self::Agent();

        $bankname = trim($request->input('bankname'));
        $branch = trim($request->input('branch'));
        $account = trim($request->input('account'));
        $accountid = trim($request->input('accountid'));

        if(empty($bankname)){
            return response()->json(['message'=>'请输入所属银行'], 300);
        }
        if(empty($branch)){
            return response()->json(['message'=>'请输入所属支行'], 300);
        }
        if(empty($account)){
            return response()->json(['message'=>'请输入收款人信息'], 300);
        }
        if(empty($accountid)){
            return response()->json(['message'=>'请输入银行卡信息'], 300);
        }

        $bank = AgentsBank::where('agent_id','=', self::$user->id)->first();
        if(empty($bank)){
            $bank = new AgentsBank();
        }
        $bank->agent_id = self::$user->id;
        $bank->bankname = $bankname;
        $bank->branch = $branch;
        $bank->account = $account;
        $bank->accountid = $accountid;

        if($bank->save()){
            return response()->json(['message'=>'保存成功'], 200);
        }else{
            return response()->json(['message'=>'保存失败'], 300);
        }

    }
}
