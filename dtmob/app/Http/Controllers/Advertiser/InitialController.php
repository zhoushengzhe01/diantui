<?php
namespace App\Http\Controllers\Advertiser;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Model\WebmasterWebsiteCategory;
use App\Model\Advertiser;
use App\Model\Users;
use App\Model\Setting;
use App\Model\AdsPosition;
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
        $advertiser_id = Session::get('advertiser_id');
        $advertiser_name = Session::get('advertiser_name');

        if(empty($advertiser_id) || empty($advertiser_name)){
            die("window.location.href='/'");
        }

        //查找广告主
        $advertiser = Advertiser::where('id', '=', $advertiser_id)->where('username', '=', $advertiser_name)->first();
        if(empty($advertiser)){
            die("window.location.href='/'");
        }

        if($advertiser->state=='2'){
            die("document.write('账户已关闭，请联系你的对接商务。')");
        }

        if( !empty($advertiser->busine_id) )
        {
            $advertiser->busine = Users::where('id', '=', $advertiser->busine_id)->first();
        }
        else
        {
            $advertiser->busine = Cache::remember('advertiser_'.$advertiser->id.'busine_id', (3600*24*30), function() {
                $busines = Users::where('department_id', '4')->where('state', '1')->get()->toArray();
                shuffle($busines);
                return $busines[0];
            });
        }

        $group['advertiser'] = $advertiser;
        $group['adtype'] = AdsPosition::where('state', '1')->where('is_group', '1')->orderBy('id', 'asc')->get(['id', 'name', 'is_group']);
        $group['hours'] = config('other.hours');
        $group['page'] = 'home';
        $group['domain'] = $_SERVER['HTTP_HOST'];

        //获得系统设置信息
        $setting = Setting::get();
        $group['setting'] = [];
        foreach($setting as $key=>$val){
            $group['setting'][$val->key] = $val->value;
        }

        //查找流量分类
        $group['categorys'] = WebmasterWebsiteCategory::where('state', '=', '1')->get(['id','name'])->toArray();

        echo "var Group = ".json_encode($group, true);

        echo "\r\nvar Token = '';";
        
    }
}
