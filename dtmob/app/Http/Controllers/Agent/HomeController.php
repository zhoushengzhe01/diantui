<?php
namespace App\Http\Controllers\Agent;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\AgentsEarnings;
use App\Model\AgentsLogs;
use App\Model\AgentsBank;
use App\Model\Agents;
use App\Model\Users;
use App\Model\Setting;
use App\Model\Banks;
use Hash;
use Session;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        //公共变量
        $group = [];

        //验证是否登录
        $agent_id = Session::get('agent_id');
        $agent_name = Session::get('agent_name');

        if(empty($agent_id) || empty($agent_name)){
            header("Location: /agent/login");
            die;
        }
        
        //查找广告主
        $agent = Agents::where('id', '=', $agent_id)->where('username', '=', $agent_name)->first();
        if(empty($agent)){
            header("Location: /agent/login");
            die;
        }
        
        if($agent->state=='2'){
            die('账户已关闭，请联系你的对接商务。');
        }

        //查找商务
        $agent->busine = Users::where('id', '=', $agent->busine_id)->first();
        $group['agent'] = $agent;
        $group['adsType'] = config('other.adsType');
        $group['hours'] = config('other.hours');
        $group['page'] = 'home';
        $group['banks'] = Banks::get(['name', 'id']);

        //获得系统设置信息
        $setting = Setting::get();
        $group['setting'] = [];
        foreach($setting as $key=>$val){
            $group['setting'][$val->key] = $val->value;
        }

        return view('agent', ['group'=>$group]);
    }
}
