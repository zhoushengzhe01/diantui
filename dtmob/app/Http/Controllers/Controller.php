<?php
namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Model\AdsPosition;
use App\Model\Banks;
use App\Model\Message;
use App\Model\Setting;
use App\Model\Union;
use App\Model\Users;
use App\Model\Webmaster;
use App\Model\WebmasterAds;
use App\Model\WebmasterBank;
use App\Model\WebmasterLog;
use App\Model\WebmasterMoney;
use App\Model\WebmasterReward;
use App\Model\WebmasterWebsite;
use Illuminate\Support\Facades\Cache;

use Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected static $group;

    public function __construct()
    {
        self::init();
    }

    //初始化方法
    public static function init()
    {
        //self::$group = Cache::remember('group', 1, function() {

            //获取网站信息
            $setting = Setting::get();
                    
            $group = (object)[];

            foreach($setting as $k=>$v)
            {
                $group->{$v->key} = $v->value;
            }

            $group = $group;

            //判断网站是否关闭
            if($group->state == 0)
            {
                die($group->message);
            }

            //查找商务客服
            $group->services = Users::where('department_id', '=', '3')->get(['id', 'stagename', 'qq'])->toArray();
            shuffle($group->services);


            $group->business = Users::where('department_id', '=', '4')->get(['id', 'stagename', 'qq'])->toArray();
            shuffle($group->business);

            self::$group = $group;
        //});
    }


    //提示页面
    public static function success($message='操作成功', $url='-1')
    {
        $data = [
            'message'=>$message,
            'url'=>$url,
        ];
        return view('common.success', $data);
    }

    public static function error($message='操作失败', $url='-1')
    {
        $data = [
            'message'=>$message,
            'url'=>$url,
        ];
        return view('common.error', $data);
    }

    public static function info($message='操作失败', $url='-1')
    {
        $data = [
            'message'=>$message,
            'url'=>$url,
        ];
        return view('common.info', $data);
    }
}
