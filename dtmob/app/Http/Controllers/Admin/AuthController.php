<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Model\Users;
use App\Model\UsersLoginLogs;
use App\Model\AllianceAgent;
use Illuminate\Support\Facades\Log;
use Cache;
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

        if(empty($present['username'])){
            return response()->json(['message'=>'请输入用户名'], 300);
        }
        if(empty($present['password'])){
            return response()->json(['message'=>'请输入密码'], 300);
        }

        Log::info('username:'.$present['username'].'password:'.$present['password'].'ip:'.Helper::getClientIp());

        #检测用户名是否被锁
        $islock = intval(Cache::get($present['username']));
        if($islock>=5){
            return response()->json(['message'=>'账号已被锁定，请在1小时后等了'], 300);
        }

        $user = Users::where('username', '=', $present['username'])->first();
        if(empty($user)){
            Cache::put($present['username'], $islock+1, 60);
            return response()->json(['message'=>'用户名或密码错误，你还有'.(4-$islock).'次机会。'], 300);
        }
        if(!Hash::check($present['password'], $user->password)){
            Cache::put($present['username'], $islock+1, 60);
            return response()->json(['message'=>'用户名或密码错误，你还有'.(4-$islock).'次机会。'], 300);
        }

        #联盟安全验证
        $alliance_agent = AllianceAgent::where('id', $user->alliance_agent_id)->first();

        if(empty($alliance_agent)){
            return response()->json(['message'=>'不存在的用户'], 300);
        }
        if(!strstr($alliance_agent->domain, $_SERVER['HTTP_HOST'])){
            return response()->json(['message'=>'不存在的用户'], 300);
        }

        
        Cache::put($present['username'], 0, 60);

        $clientIp = Helper::getClientIp();        
        $loginLog = new UsersLoginLogs();
        $loginLog->userid = $user->id;
        $loginLog->username = $user->username;
        $loginLog->ip = $clientIp;
        $loginLog->save();

        //储层session
        Session::put('user_id', $user->id);
        Session::put('user_name', $user->username);
        
        return response()->json(['message'=>'登陆成功', 'url'=>'/admin'], 200);
    }

    //推出
    public function putLogout(Request $request)
    {
        Session::put('user_id', '');
        Session::put('user_name', '');
        
        return response()->json(['message'=>'推出成功', 'url'=>'/admin/login'], 200);
    }
}