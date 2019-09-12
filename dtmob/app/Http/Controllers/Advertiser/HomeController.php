<?php
namespace App\Http\Controllers\Advertiser;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\WebmasterWebsiteCategory;
use App\Model\Advertiser;
use App\Model\Users;
use App\Model\Setting;
use App\Model\AdsPosition;

use Hash;
use Session;

class HomeController extends Controller
{
    //访问入口函数
    public function index(Request $request)
    {
        //公共变量
        $group = [];
        
        //验证是否登录
        $advertiser_id = Session::get('advertiser_id');
        $advertiser_name = Session::get('advertiser_name');

        if(empty($advertiser_id) || empty($advertiser_name)){
            header("Location: /");
            die;
        }

        //查找广告主
        $advertiser = Advertiser::where('id', '=', $advertiser_id)->where('username', '=', $advertiser_name)->first();
        if(empty($advertiser)){
            header("Location: /");
            die;
        }

        if($advertiser->state=='2'){
            die('账户已关闭，请联系你的对接商务。');
        }

        //查找商务
        $advertiser->busine = Users::where('id', '=', $advertiser->busine_id)->first();
        $group['advertiser'] = $advertiser;
        $group['adtype'] = AdsPosition::where('state', '1')->where('is_group', '1')->orderBy('id', 'asc')->get(['id', 'name', 'is_group']);
        $group['hours'] = config('other.hours');
        $group['page'] = 'home';

        //获得系统设置信息
        $setting = Setting::get();
        $group['setting'] = [];
        foreach($setting as $key=>$val){
            $group['setting'][$val->key] = $val->value;
        }

        //查找流量分类
        $group['categorys'] = WebmasterWebsiteCategory::where('state', '=', '1')->get(['id','name'])->toArray();

        return view('advertiser', ['group'=>$group]);
    }
}
