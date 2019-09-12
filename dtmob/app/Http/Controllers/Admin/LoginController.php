<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\Webmaster;
use App\Model\Advertiser;
use App\Model\Agents;
use Session;

class LoginController extends Controller
{
    //站长登录
    public function WebmasterLogin(Request $request)
    {
        $webmaster_id = $request->input('webmaster_id');
        if(empty($webmaster_id)){
            return response()->json(['message'=>'缺少站长ID'], 300);
        }

        $webmaster = Webmaster::where('id', '=', $webmaster_id)->first();
        if(empty($webmaster)){
            return response()->json(['message'=>'用户名不存在'], 300);
        }
        
        //储层session
        Session::put('webmaster_id', $webmaster->id);
        Session::put('webmaster_name', $webmaster->username);

        return response()->json(['data'=>['message'=>'登陆成功', 'url'=>'/webmaster']], 200);
    }

    //广告主登录
    public function AdvertiserLogin(Request $request)
    {
        $advertiser_id = $request->input('advertiser_id');
        if(empty($advertiser_id)){
            return response()->json(['message'=>'缺少站长ID'], 300);
        }

        $advertiser = Advertiser::where('id', '=', $advertiser_id)->first();
        if(empty($advertiser)){
            return response()->json(['message'=>'用户名不存在'], 300);
        }
        
        //储层session
        Session::put('advertiser_id', $advertiser->id);
        Session::put('advertiser_name', $advertiser->username);

        return response()->json(['data'=>['message'=>'登陆成功', 'url'=>'/advertiser']], 200);
    }

    //代理登录
    public function AgentLogin(Request $request)
    {
        $agent_id = $request->input('agent_id');
        if(empty($agent_id)){
            return response()->json(['message'=>'缺少站长ID'], 300);
        }

        $agent = Agents::where('id', '=', $agent_id)->first();
        if(empty($Agent)){
            return response()->json(['message'=>'用户名不存在'], 300);
        }
        
        //储层session
        Session::put('agent_id', $agent->id);
        Session::put('agent_name', $agent->username);

        return response()->json(['data'=>['message'=>'登陆成功', 'url'=>'/agent']], 200);
    }
}
