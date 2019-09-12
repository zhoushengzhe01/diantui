<?php
namespace app\Controller;

use app\Helpers\Helper;
use app\Config\AppConfig;
use app\Model\EarningClick;
use app\Model\Setting;
use app\Model\Game;
use Redis;
use Memcache;

class HudongStatController extends Controller
{
    //互动广告点击
    public function setHudongPc()
    {
        if( !($data = Helper::decode(Helper::request('se'))) )
        {
            die('验证不通过');
        }
        
        //数据
        $refso = Helper::request('refso');
        $source = Helper::request('source');
        $url = Helper::request('url');
        $screen = Helper::request('screen');
        $clickp = Helper::request('clickp');
        $link = Helper::request('link');

        //互动式广告
        $ip = explode('.', Helper::getClientIp());
        $ipKey = trim($ip[0].'.'.$ip[1].'.'.$ip[2]);

        //获取互动式游戏
        $game = Game::where('state', '=', '1')->get()->toArray();

        $sum_weight = 0;

        foreach($game as $key=>$val)
        {
            $sum_weight += $val['weight'];
        }
        
        //摇号
        $rand = mt_rand(1, $sum_weight);

        $weight = 0;

        foreach($game as $key=>$val)
        {
            $weight += intval($val['weight']);

            if($rand <= $weight)
            {
                $game = $val;
                break;
            }
        }

        $earningClick = new EarningClick;
        $earningClick->position_id = $data['position_id'];
        $earningClick->webmaster_id = $data['webmaster_id'];
        $earningClick->myads_id = $data['webmaster_ad_id'];
        $earningClick->type = '4';
        $earningClick->system = Helper::getSystem();
        $earningClick->ip = Helper::getClientIp();
        $earningClick->url = substr($url , 0 , 200);
        $earningClick->source = $source;
        $earningClick->refso = substr($refso , 0 , 200);
        $earningClick->screen = $screen;
        $earningClick->clickp = $clickp;
        $earningClick->user_agent = Helper::server('http_user_agent');
        $earningClick->time = time() - intval($data['time']);
        $earningClick->save();
        
        //加密密码
        $app_passwd = 'lhmob@999';

        $data = [
            'sign'=>$game['sign'],      
            'app_key'=>$data['app_key'],    //app_key
            'time_stamp'=>$data['time'],     //时间戳
            'nonce'=>md5(microtime()), //随机数
            'app_passwd'=>$app_passwd,
        ];

        ksort($data);

        $data['signature'] = md5(http_build_query($data));
        
        unset($data['app_passwd']);
        unset($data['sign']);

        //获取
        header('Location: http://hudong.cxmyq.com/dist/hudong/'.$game['view'].'-'.$game['sign'].'?sign='.$game['sign'].'&'.http_build_query($data));
        die;
    }
 
    //互动广告展示
    public function setHudongPm()
    {
        if( !($data = Helper::decode(Helper::request('se'))) )
        {
            die('验证不通过');
        }

        //数据
        $refso = Helper::request('refso');
        $source = Helper::request('source');
        $url = Helper::request('url');
        $screen = Helper::request('screen');

        //互动式广告
        $ip = explode('.', Helper::getClientIp());
        $ipKey = trim($ip[0].'.'.$ip[1].'.'.$ip[2]);

        //获取互动式游戏
        $game = Game::where('state', '=', '1')->get()->toArray();

        $sum_weight = 0;

        foreach($game as $key=>$val)
        {
            $sum_weight += $val['weight'];
        }
        
        //摇号
        $rand = mt_rand(1, $sum_weight);

        $weight = 0;

        foreach($game as $key=>$val)
        {
            $weight += intval($val['weight']);

            if($rand <= $weight)
            {
                $game = $val;
                break;
            }
        }
        
        //数据储层
        $redis = new Redis;
        $redis->connect('127.0.0.1', 6379);
        
        $pointer = 'webmaster_ad_id:' . $data['webmaster_ad_id'] . '_position_id:' . $data['position_id'] . '_' . date('YmdH');

        $pvLibrary = json_decode($redis->hget("pv_library", $pointer), true);

        if(empty($pvLibrary))
        {
            $pvLibrary = [
                'type' => '4',
                'position_id' => $data['position_id'],
                'webmaster_id' => $data['webmaster_id'],
                'webmaster_ad_id' => $data['webmaster_ad_id'],
                'game_id' => $game['id'],
                'time' => date('Y-m-d H'),
                'pc' => 1,
                'ip' => 1,
            ];

            $redis->lpush("pv_pointer", $pointer);
        }
        else
        {
            $pvLibrary['pc'] += 1;
            $pvLibrary['ip'] += 1;
        }

        $redis->hset("pv_library", $pointer, json_encode($pvLibrary, true));

        //互动式广告展示
        //die('互动式广告展示');
    }
}