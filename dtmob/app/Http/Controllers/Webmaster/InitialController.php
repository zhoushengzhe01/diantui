<?php
namespace App\Http\Controllers\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Model\WebmasterWebsiteCategory;
use App\Model\Webmaster;
use App\Model\Users;
use App\Model\Banks;
use App\Model\Setting;
use App\Model\AllianceAgent;
use Hash;
use Session;

class InitialController extends Controller
{
    public function getInitial(Request $request)
    {
        //公共变量
        $group = [];

        //验证是否登录
        $webmaster_id = Session::get('webmaster_id');
        $webmaster_name = Session::get('webmaster_name');

        if(empty($webmaster_id) || empty($webmaster_name))
        {
            die("window.location.href='/'");
        }

        //查找广告主
        $webmaster = Webmaster::where('id', '=', $webmaster_id)->where('username', '=', $webmaster_name)->first();
        if(empty($webmaster))
        {
            die("window.location.href='/'");
        }
        
        if($webmaster->state=='2')
        {
            die("document.write('账户已关闭，请联系你的客服。')");
        }

        if( !empty($webmaster->service_id) )
        {
            $webmaster->service = Users::where('id', '=', $webmaster->service_id)->first();
        }
        else
        {
            $webmaster->service = Cache::remember('webmaster_'.$webmaster->id.'service_id', (3600*24*30), function() {
                $services = Users::where('department_id', '3')->where('state', '1')->get()->toArray();
                shuffle($services);
                return $services[0];
            });
        }

        $group['webmaster'] = $webmaster;
        $group['adsType'] = config('other.adsType');
        $group['hours'] = config('other.hours');
        $group['page'] = 'home';
        $group['banks'] = Banks::get(['name', 'id']);
        $group['domain'] = $_SERVER['HTTP_HOST'];

        #联盟安全验证
        $group['setting'] = AllianceAgent::where('id', $webmaster->alliance_agent_id)->first()->toArray();
        if(empty($group['setting'])){
            die("window.location.href='/'");
        }
        if(!strstr($group['setting']['domain'], $_SERVER['HTTP_HOST'])){
            die("window.location.href='/'");
        }
        
        //查找流量分类
        $group['categorys'] = WebmasterWebsiteCategory::where('state', '=', '1')->get(['id','name'])->toArray();

        echo "var Group = ".json_encode($group, true);

        echo "\r\nvar Token = '';";
        
    }
}
