<?php
namespace App\Http\Controllers\Webmaster;

use Illuminate\Http\Request;
use App\Http\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Model\WebmasterLoginLog;
use App\Model\Webmaster;
use App\Model\Users;
use App\Model\AllianceAgent;
use Cache;
use Hash;
use Session;

class AuthController extends Controller
{
    //登录
    public function postLogin(Request $request)
    {
        #验证码
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
        #检测用户名是否被锁
        $islock = intval(Cache::get($present['username']))+1;
        if($islock>5){
            return response()->json(['message'=>'账号已被锁定，请在1小时后等了'], 300);
        }
        
        $webmaster = Webmaster::where('username', '=', $present['username'])->first();
        if(empty($webmaster)){
            Cache::put($present['username'], $islock, 60);
            return response()->json(['message'=>'用户名或密码错误，你还有'.(4-$islock).'次机会。'], 300);
        }
        if (!Hash::check($present['password'], $webmaster->password)){
            Cache::put($present['username'], $islock, 60);
            return response()->json(['message'=>'用户名或密码错误，你还有'.(4-$islock).'次机会。'], 300);
        }

        #联盟安全验证
        $alliance_agent = AllianceAgent::where('id', $webmaster->alliance_agent_id)->first();
        if(empty($alliance_agent)){
            return response()->json(['message'=>'不存在的用户'], 300);
        }
        if(!strstr($alliance_agent->domain, $_SERVER['HTTP_HOST'])){
            return response()->json(['message'=>'不存在的用户'], 300);
        }

        Cache::put($present['username'], 0, 60);
        
        $clientIp = Helper::getClientIp();
        $loginLog = new WebmasterLoginLog();
        $loginLog->webmaster_id = $webmaster->id;
        $loginLog->webmaster_username = $webmaster->username;
        $loginLog->ip = $clientIp;
        $loginLog->region = '';
        $loginLog->city = '';
        $loginLog->isp = '';
        $loginLog->save();
        
        //储层session
        Session::put('webmaster_id', $webmaster->id);
        Session::put('webmaster_name', $webmaster->username);

        return response()->json(['message'=>'登陆成功', 'url'=>'/webmaster'], 200);
    }
    
    ##注册
    public function postRegister(Request $request)
    {
        $present = $request->input();

        $rules = ['captcha' => 'required|captcha'];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return response()->json(['message'=>'验证码失败'], 300);
        }

        if(empty($present['username']))
        {
            return response()->json(['message'=>'请输入用户名'], 300);
        }else{
            if(!preg_match("/^[a-zA-Z0-9_-]{4,16}$/", $present['username'])){
                return response()->json(['message'=>'请输入4到16位字母或数字组成'], 300);
            }
        }
        if(empty($present['password'])){
            return response()->json(['message'=>'请填写密码'], 300);
        }else{
            if(strlen($present['password'])<6 || strlen($present['password'])>18){
                return response()->json(['message'=>'密码必须大于6位 并 小于18位'], 300);
            }
        }
        if(empty($present['setpassword'])){
            return response()->json(['message'=>'请输入确认密码'], 300);
        }else{
            if($present['password'] != $present['setpassword']){
                return response()->json(['message'=>'输入的两次密码不一样'], 300);
            }
        }
        if(empty($present['nickname'])){
            return response()->json(['message'=>'请输入联系人'], 300);
        }
        if(empty($present['mobile'])){
            return response()->json(['message'=>'请输入手机号码'], 300);
        }
        if(empty($present['qq'])){
            return response()->json(['message'=>'请输入QQ号码'], 300);
        }
        
        #联盟
        $alliance_agent = AllianceAgent::where('domain', 'like', '%'.$_SERVER['HTTP_HOST'].'%')->first(['id','name']);
        if(empty($alliance_agent))
        {
            return response()->json(['message'=>'非法入口，禁止注册'], 300);
        }

        //单IP每天注册5个
        $count = Webmaster::where('login_ip', '=', Helper::getClientIp())->where('created_at', '>', date('Y-m-d').' 00:00:00')->count();
        if($count>=5){
            return response()->json(['message'=>'今天你已经注册了多个账号，不能在注册了'], 300);
        }
        //用户名
        $count = Webmaster::where('username', '=', $present['username'])->count();
        if($count > 0){
            return response()->json(['message'=>'用户名已经存在'], 300);
        }

        //插入数据
        $webmaster = new Webmaster();
        $webmaster->alliance_agent_id = $alliance_agent->id;
        $webmaster->username = $present['username'];
        $webmaster->nickname = $present['nickname'];
        $webmaster->password = bcrypt($present['password']);
        $webmaster->mobile = $present['mobile'];
        $webmaster->qq = $present['qq'];
        $webmaster->login_ip = $request->getClientIp();
        $webmaster->state = 1;

        #客服选择
        if(!empty($present['service_id'])){
            $user = Users::where('id', $present['service_id'])->where('department_id', '3')->where('state', '1')->where('alliance_agent_id', $alliance_agent->id)->first();
            if(!empty($user)){
                $webmaster->service_id = $present['service_id'];
            }
        }
        if(!empty($present['webmaster_id'])){
            $web = Webmaster::where('id', $present['webmaster_id'])->where('state', '1')->where('alliance_agent_id', $alliance_agent->id)->first();
            if(!empty($web)){
                $webmaster->pid = $web->id;
                $webmaster->service_id = $web->service_id;
            }
        }
        #随机客服
        if(empty($webmaster->service_id))
        {
            $users = Users::where('department_id', '3')->where('state', '1')->where('alliance_agent_id', $alliance_agent->id)->get(['id'])->toArray();
            shuffle($users);
            $webmaster->service_id = $users[0]['id'];
        }
 
        if($webmaster->save()){
            return response()->json(['message'=>'注册成功，请登陆', 'url'=>'/login/ad'], 200);
        }else{
            return response()->json(['message'=>'注册失败'], 300);
        }
     }
 
     //推出
     public function putLogout(Request $request)
     {
         Session::put('webmaster_id', '');
         Session::put('webmaster_name', '');
 
         return response()->json(['message'=>'推出成功', 'url'=>'/login/ad'], 200);
     }
}
