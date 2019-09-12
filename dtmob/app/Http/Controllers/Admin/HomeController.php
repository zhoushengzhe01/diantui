<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\WebmasterWebsiteCategory;
use App\Model\Alliance;
use App\Model\Users;
use App\Model\UsersMenu;
use App\Model\UsersMenusRule;
use App\Model\UsersDepartment;
use App\Model\Setting;
use App\Model\AdvertiserAdCategorys;
use App\Model\AdsPosition;
use Session;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        //公共变量
        $group = [];

        //验证是否登录
        $user_id = Session::get('user_id');
        $user_name = Session::get('user_name');
        
        if(empty($user_id) || empty($user_name))
        {
            header("Location: /admin/login"); 
            die;
        }

        //用户
        $user = Users::where('id', '=', $user_id)->where('username', '=', $user_name)->first();
        if(empty($user))
        {
            header("Location: /admin/login"); 
            die;
        }
        
        if($user->state=='2')
        {
            die('账号已经关闭');
        }

        //获取授权
        $rule = UsersMenusRule::where('department_id', '=', $user->department_id)->first(['rules']);
        if( empty($rule) )
        {
            die('找不到授权信息');
        }
        $rules = json_decode($rule->rules, true);

        //查找菜单
        $menus = UsersMenu::where('type', '=', 'menu')
            ->where('status', '=', '1')
            ->where('pid', '=', '0')
            ->whereIn('id', $rules)
            ->orderBy('sort', 'asc')
            ->get(['id', 'name', 'url', 'icon']);
        foreach($menus as $key=>$val)
        {
            $menus[$key]->list = UsersMenu::where('type', '=', 'menu')
                ->where('status', '=', '1')
                ->where('pid', '=', $val->id)
                ->whereIn('id', $rules)
                ->orderBy('sort', 'asc')
                ->get(['id', 'name', 'url', 'pid', 'icon']);
        }
        
        $group['user'] = $user;
        $group['menus'] = $menus;
        //$group['adsType'] = config('other.adsType');
        $group['adtype'] = AdsPosition::where('state', '1')->orderBy('id', 'asc')->get(['id', 'name', 'is_group']);
        $group['hours'] = config('other.hours');
        $group['page'] = 'home';


        //获得系统设置信息
        $setting = Setting::get();
        $group['setting'] = [];
        foreach($setting as $key=>$val)
        {
            $group['setting'][$val->key] = $val->value;
        }

        //查找网站分类
        $group['categorys'] = WebmasterWebsiteCategory::where('state', '=', '1')->get(['id','name'])->toArray();

        $group['alliances'] = Alliance::where('state', '=', '1')->get(['id','name'])->toArray();

        $group['departments'] = UsersDepartment::where('state', '=', '1')->get(['id','name'])->toArray();

        //广告分类
        $group['adcategorys'] = AdvertiserAdCategorys::where('state', '=', '1')->get(['id','name'])->toArray();

        return view('admin', ['group'=>$group]);
    }
}
