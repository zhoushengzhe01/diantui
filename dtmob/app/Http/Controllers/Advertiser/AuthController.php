<?php
namespace App\Http\Controllers\Advertiser;

use Illuminate\Http\Request;
use App\Http\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Model\AdvertiserLoginLog;
use App\Model\Advertiser;
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
            return response()->json(['message'=>'账号已被锁定，请在1小时后登录'], 300);
        }
        $advertiser = Advertiser::where('username', '=', $present['username'])->first();
        if(empty($advertiser)){
            Cache::put($present['username'], $islock, 60);
            return response()->json(['message'=>'用户名或密码错误，你还有'.(4-$islock).'次机会。'], 300);
        }
        if (!Hash::check($present['password'], $advertiser->password)){
            Cache::put($present['username'], $islock, 60);
            return response()->json(['message'=>'用户名或密码错误，你还有'.(4-$islock).'次机会。'], 300);
        }

        #联盟安全验证
        $alliance_agent = AllianceAgent::where('id', $advertiser->alliance_agent_id)->first();
        if(empty($alliance_agent)){
            return response()->json(['message'=>'不存在的用户'], 300);
        }
        if(!strstr($alliance_agent->domain, $_SERVER['HTTP_HOST'])){
            return response()->json(['message'=>'不存在的用户'], 300);
        }

        Cache::put($present['username'], 0, 60);


        $clientIp = Helper::getClientIp();

        #日志
        $loginLog = new AdvertiserLoginLog();
        $loginLog->advertiser_id = $advertiser->id;
        $loginLog->advertiser_username = $advertiser->username;
        $loginLog->ip = $clientIp;
        $loginLog->region = '';
        $loginLog->city = '';
        $loginLog->isp = '';
        $loginLog->save();

        #储层
        Session::put('advertiser_id', $advertiser->id);
        Session::put('advertiser_name', $advertiser->username);

        return response()->json(['message'=>'登陆成功', 'url'=>'/advertiser'], 200);
    }

    //注册
    public function postRegister(Request $request)
    {
        $present = $request->input();

        $rules = ['captcha' => 'required|captcha'];
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return response()->json(['message'=>'验证码失败'], 300);
        }

        if(empty($present['username'])){
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
        if(empty($present['company'])){
            return response()->json(['message'=>'请输入公司名称'], 300);
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

        #同一个IP每天注册5次
        $count = Advertiser::where('login_ip', '=', Helper::getClientIp())->where('created_at', '>', date('Y-m-d').' 00:00:00')->count();
        if($count>=5){
            return response()->json(['message'=>'今天你已经注册了多个账号，不能在注册了'], 300);
        }
        #用户名
        $count = Advertiser::where('username', '=', $present['username'])->count();
        if($count > 0){
            return response()->json(['message'=>'用户名已经存在。'], 300);
        }

        #联盟
        $alliance_agent = AllianceAgent::where('domain', 'like', '%'.$_SERVER['HTTP_HOST'].'%')->first(['id','name']);
        if(empty($alliance_agent))
        {
            return response()->json(['message'=>'非法入口，禁止注册'], 300);
        }

        #数据
        $advertiser = new Advertiser();
        $advertiser->alliance_agent_id = $alliance_agent->id;
        $advertiser->agent_id = intval($present['agent_id']);
        $advertiser->username = $present['username'];
        $advertiser->nickname = $present['nickname'];
        $advertiser->password = bcrypt($present['password']);
        $advertiser->company = $present['company'];
        $advertiser->mobile = $present['mobile'];
        $advertiser->qq = $present['qq'];
        $advertiser->login_ip = $request->getClientIp();
        $advertiser->state = 1;

        if(!empty($present['busine_id']))
        {
            $user = Users::where('id', $present['busine_id'])->where('department_id', '4')->where('state', '1')->where('alliance_agent_id', $alliance_agent->id)->count();
            if(!empty($user))
            {
                $advertiser->busine_id = $present['busine_id'];
            }
        }
        #随机商务
        if(empty($advertiser->busine_id))
        {
            $users = Users::where('department_id', '4')->where('state', '1')->where('alliance_agent_id', $alliance_agent->id)->get(['id'])->toArray();
            shuffle($users);
            $advertiser->busine_id = $users[0]['id'];
        }

        if($advertiser->save()){
            return response()->json(['message'=>'注册成功，请登陆', 'url'=>'/login/ad'], 200);
        }else{
            return response()->json(['message'=>'注册失败'], 300);
        }
    }

    //推出
    public function putLogout(Request $request)
    {
        Session::put('advertiser_id', '');
        Session::put('advertiser_name', '');

        return response()->json(['message'=>'推出成功', 'url'=>'/login/ad'], 200);
    }
}