<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Http\Request;
use App\Model\Advertiser;
use App\Model\Webmaster;
use App\Model\Agents;
use App\Model\Users;
use App\Model\EditLogs;
use App\Model\UsersMenu;
use App\Model\UsersMenusRule;

use Session;

class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected static $user;
    protected static $save_time;

    //广告主登录
    public static function Advertiser()
    {
        $advertiser_id = Session::get('advertiser_id');
        $advertiser_name = Session::get('advertiser_name');

        if(empty($advertiser_id) || empty($advertiser_name))
        {
            self::response_json(['message'=>'没有登陆，请登陆。', 'url'=>'/'], 300);
        }
        
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if($method=='put')
        {
            self::response_json(['message'=>'没有操作权限，请联系客服'], 300);
        }

        $advertiser = Advertiser::where('id', '=', $advertiser_id)->where('username', '=', $advertiser_name)->first();

        if(empty($advertiser))
        {
            self::response_json(['message'=>'用户已经失效，请从新登陆。', 'url'=>'/'], 300);
        }

        if($advertiser->state=='2')
        {
            self::response_json(['message'=>'账号被封，请联系你的专属客服。', 'url'=>'/'], 300);
        }

        self::$save_time = date("Y-m-d", strtotime("-14 day")). '00:00:00';

        self::$user = $advertiser;
    }

    //广告主代理
    public static function Agent()
    {
        $agent_id = Session::get('agent_id');
        $agent_name = Session::get('agent_name');

        if(empty($agent_id) || empty($agent_name))
        {
            self::response_json(['message'=>'没有登陆，请登陆。', 'url'=>'/'], 300);
        }

        $agent = Agents::where('id', '=', $agent_id)->where('username', '=', $agent_name)->first();

        if(empty($agent)){
            self::response_json(['message'=>'用户已经失效，请从新登陆。', 'url'=>'/'], 300);
        }
        if($agent->state=='2'){
            self::response_json(['message'=>'账号被封，请联系你的专属客服。', 'url'=>'/'], 300);
        }
        
        self::$user = $agent;
    }

    //网站主登录
    public static function Webmaster()
    {
        $webmaster_id = Session::get('webmaster_id');
        $webmaster_name = Session::get('webmaster_name');

        if(empty($webmaster_id) || empty($webmaster_name))
        {
            self::response_json(['message'=>'没有登陆，请登陆。', 'url'=>'/'], 300);
        }

        $webmaster = Webmaster::where('id', '=', $webmaster_id)->where('username', '=', $webmaster_name)->first();

        if(empty($webmaster))
        {
            self::response_json(['message'=>'用户已经失效，请从新登陆。', 'url'=>'/'], 300);
        }

        if($webmaster->state=='2')
        {
            self::response_json(['message'=>'账号被封，请联系你的专属客服。', 'url'=>'/'], 300);
        }

        self::$save_time = date("Y-m-d", strtotime("-14 day")). '00:00:00';

        self::$user = $webmaster;
    }


    //后台管理员登录
    public static function Admin()
    {
        $user_id = Session::get('user_id');
        $user_name = Session::get('user_name');
        $user_id = 10;
        $user_name = 'dtmob';

        if(empty($user_id) || empty($user_name))
        {
            self::response_json(['message'=>'没有登陆，请登陆。', 'url'=>'/admin/login'], 300);
        }

        $user = Users::where('id', '=', $user_id)->where('username', '=', $user_name)->first();

        if(empty($user))
        {
            self::response_json(['message'=>'用户已经失效，请从新登陆。', 'url'=>'/admin/login'], 300);
        }

        if($user->state=='2')
        {
            self::response_json(['message'=>'账号被封，请联系你的专属客服。', 'url'=>'/admin/login'], 300);
        }
        
        #权限验证
        //if($user->department_id!=1)
        {
            #查找次接口权限ID
            $method = strtolower($_SERVER['REQUEST_METHOD']);
            $parse = parse_url($_SERVER['REQUEST_URI']);

            if( empty($parse['path']) ){
                $url = '/'; 
            }else{
                $url = preg_replace("/[0-9]+/","{id}",trim($parse['path']));
            }

            $menu = UsersMenu::where('url', '=', $url)->where('method', '=', $method)->first();
            if( empty($menu) )
            {
                self::response_json(['message'=>'API接口地址未登记'.$url], 300);
            }
            
            $rule = UsersMenusRule::where('department_id', '=', $user->department_id)->first();
            if( empty($rule) )
            {
                self::response_json(['message'=>'本部门没有设置权限请设置'], 300);
            }

            $rules = json_decode($rule->rules, true);

            if(!in_array($menu->id, $rules))
            {
                self::response_json(['message'=>'你没有权限访问'], 300);
            }
        }

        self::$user = $user;

        $method = strtolower($_SERVER['REQUEST_METHOD']);

        if($method=='post' || $method=='put')
        {
            $request = file_get_contents('php://input');
            
            $url = trim($_SERVER['REQUEST_URI']);

            $editlog = new EditLogs;
            $editlog->userid = self::$user->id;
            $editlog->username = self::$user->username;
            $editlog->method = $method;
            $editlog->url = $url;
            $editlog->data = $request;
            $editlog->save();

            //self::response_json(['message'=>'没有权限修改'], 300);
        }
    }


    //json格式输出
    public static function response_json($data, $status)
    {
        header('Content-Type:application/json; charset=utf-8');

        header("HTTP/1.0 ".$status." OK");

        die(json_encode($data, true));
    }
}
