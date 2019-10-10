<?php
namespace app\Controller;

use app\Controller;
use app\Helpers\Helper;
use app\Config\AppConfig;
use app\Model\EarningClick;
use app\Model\EarningClickAccount;
use app\Model\EarningPv;
use app\Model\Alliance;
use app\Model\AdvertiserAds;
use app\Model\AllianceFlux;

use Memcache;
use Redis;

class StatisController extends Controller
{
    //跳转
    public function skipUrl($url, $id)
    {
        header('Content-Type: application/x-javascript; charset=UTF-8');
        echo "if(top.location!=self.location){ top.location='".$url."'; }else{ window.location.href='".$url."'; }";
        die;
    }

    //IP统计
    public function statis_ip()
    {   
        $is_exist = false;
        $ip_array = explode('.', Helper::getClientIp());
        $key = $ip_array[0].'.'.$ip_array[1].'.'.$ip_array[2];

        self::$redis->select(0);
        $result = self::$redis->hget('ip_library', $key);
        if(!empty($result))
        {
            $is_exist = true;
        }
        else
        {
            //没有去分库查找
            self::$redis->select(($ip_array[1]%8) + 2);
            $result = self::$redis->hget('ip_library_'.$ip_array[2], $key);
            
            if(!empty($result))
            {
                $is_exist = true;
            }
            else
            {
                //进行插入
                self::$redis->select(0);
                self::$redis->hset('ip_library', $key, '1');
            }
        }

        return $is_exist;
    }
    
    //今日IP是否重复
    public function isNewip($data)
    {
        $ip_array = explode('.', Helper::getClientIp());
        $is_new_ip = true;

        self::$redis->select( ($ip_array[1]%128)+10 );
        $result = self::$redis->hget('webmaster_'.$data['webmaster_ad_id'], $ip_array[0].'.'.$ip_array[1].'.'.$ip_array[2]);

        if(!empty($result))
        {
            $ip_data = explode(",", $result);
            if(in_array($ip_array[3], $ip_data)) {
                $is_new_ip = false;
            } else {
                $ip_data[] = $ip_array[3];
            }
        }
        else
        {
            $ip_data = [$ip_array[3]];
        }
        //是新IP存入数据库中
        if($is_new_ip===true){
            self::$redis->hset('webmaster_'.$data['webmaster_ad_id'], $ip_array[0].'.'.$ip_array[1].'.'.$ip_array[2], implode(",", $ip_data));
        }
        return $is_new_ip;
    }

    //PV统计
    public function postPv()
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
        $n = intval(Helper::request('n'));
        $ifrom = intval(Helper::request('ifrom')) * $n;
        
        //是否新IP
        if($n=='1')
        {
            $is_new_ip = $this->isNewip($data);
        }
        else
        {
            $is_new_ip = false;
        }

        //PV  IP统计指针
        $minute = intval(date('i'));
        if($minute%4==0 || $minute%4==1)
        {
            $pv_pointer = 'pv_library_single';
        }
        else
        {
            $pv_pointer = 'pv_library_double';
        }

        self::$redis->select(0);
        //自己广告-自己流量
        if($data['type']=='1')
        {
            #开始事务
            $pointer = 'webmaster_ad_id:' . $data['webmaster_ad_id'] . '-advertiser_ad_id:'.$data['advertiser_ad_id'] . '_' . date('YmdH');
            $pvLibrary = json_decode(self::$redis->hget($pv_pointer, $pointer), true);
            if(empty($pvLibrary))
            {
                $pvLibrary = [
                    'type' => '1',
                    'position_id' => $data['position_id'],
                    'advertiser_id' => $data['advertiser_id'],
                    'advertiser_ad_id' => $data['advertiser_ad_id'],
                    'webmaster_id' => $data['webmaster_id'],
                    'webmaster_ad_id' => $data['webmaster_ad_id'],
                    'time' => date('Y-m-d H'),
                    'ifrom' => $ifrom,
                    'pv' => $n,
                    'ip' => $is_new_ip ? 1 : 0,
                ];
            }
            else
            {
                $pvLibrary['ifrom'] += $ifrom;
                $pvLibrary['pv'] += $n;
                $pvLibrary['ip'] += $is_new_ip ? 1 : 0;
            }
            self::$redis->hset($pv_pointer, $pointer, json_encode($pvLibrary, true));
        }

        //联盟广告-自己流量
        if($data['type']=='2')
        {
            $pointer = 'webmaster_ad_id:' . $data['webmaster_ad_id'] . '-alliance_id:'.$data['alliance_id'] . '_' . date('YmdH');
            $pvLibrary = json_decode(self::$redis->hget($pv_pointer, $pointer), true);
            if(empty($pvLibrary))
            {
                $pvLibrary = [
                    'type' => '2',
                    'position_id' => $data['position_id'],
                    'webmaster_id' => $data['webmaster_id'],
                    'webmaster_ad_id' => $data['webmaster_ad_id'],
                    'alliance_id' => $data['alliance_id'],
                    'time' => date('Y-m-d H'),
                    'ifrom' => $ifrom,
                    'pv' => $n,
                    'ip' => intval($is_new_ip ? 1 : 0),
                ];
            }
            else
            {
                $pvLibrary['ifrom'] += $ifrom;
                $pvLibrary['pv'] += $n;
                $pvLibrary['ip'] += intval($is_new_ip ? 1 : 0);
            }
            self::$redis->hset($pv_pointer, $pointer, json_encode($pvLibrary, true));
        }

        header('Content-Type: application/x-javascript; charset=UTF-8');
        #按刷数据
        if( ($is_new_ip==true && !empty($data['webmaster_id'])) || $data['webmaster_id']=='1001' )
        {
            if(!in_array($data['webmaster_id'], [1051,1035,1177,1111,1031,1098,1097,1099,1106,1422,1658,1209,1089,1371,1205])){
                require '../script/koulin.js';
            }
        }
    }

    //PC统计
    public function postPc()
    {   
        if( !($data = Helper::decode(Helper::request('se'))) )
        {
            die('验证不通过');
        }

        if(!empty(Helper::request('sd')))
        {
            $sd = Helper::decode64(Helper::request('sd'));
            parse_str($sd, $client);
            if(empty($client['refso']) || empty($client['url']))
            {
                die('失败');
            }

        }
        else
        {
            $client = [
                "refso" => Helper::request('refso'),
                "source" => Helper::request('source'),
                "url" => Helper::request('url'),
                "screen" => Helper::request('screen'),
                "ifrom" => Helper::request('ifrom'),
                "interval" => Helper::request('interval'),
                "history" => Helper::request('history'),
                "ipnumber" => Helper::request('ipnumber'),
                "jstime" => Helper::request('jstime'),
                "clickp" => Helper::request('clickp'),
                //"clickp" => "0*0",
                "link" => Helper::request('link'),
                "ctype" => Helper::request('ctype'),
            ];
        }

        $link = $client['link'];

        $advertiser_ad_id = $data['advertiser_ad_id'];

        #微信外跳
        if( (Helper::getClient()=='wechat') && $data['is_wechat_out_skip']=='1')
        {
            #处理安全跳转
            $time = date('Ymd');
            $secretkey = md5(md5($time.'&dtmob@123'));
            
            $string =  Helper::encode([urlencode($link), $data['is_wechat_cover'], $time, $secretkey, $advertiser_ad_id]);
            if(self::$client['system']=='Android')
            {
                $domain = "http://".date("md").".361yb.cn:8090";
                $link = $domain."/weixin?string=" . $string;
            }
            else
            {
                $domain = "http://".date("md").".ihuaya.cn:8090";
                $link = $domain."/weixin?string=" . $string;
            }
        }

        #强跳
        if($client['ctype'] == 'skip'){
            $this->skipUrl($link, $advertiser_ad_id);
        }
        #暗点
        if($client['ctype'] == 'hidden'){
            if( mt_rand(1, 100) >= intval($data['hid_chance'])){
                $this->skipUrl($link, $advertiser_ad_id);
            }
        }
        #强点
        if($client['ctype'] == 'compel_click'){
            if( mt_rand(1, 100) >= 5){
                $this->skipUrl($link, $advertiser_ad_id);
            }
        }
        #返跳
        if(in_array($client['ctype'], ['click_return', 'own_return', 'other_return'])){
            if( mt_rand(1, 100) >= $data['return_chance']){
                $this->skipUrl($link, $advertiser_ad_id);
            }
        }
        #匹配
        if(!in_array($client['ctype'], ['compel_click', 'close', 'skip', 'good', 'hidden', 'return', 'click_return', 'own_return', 'other_return'])){
            $click_source = 'other';
        }else{
            $click_source = $client['ctype'];
        }

        #广告主
        if( $data['type']=='1' )
        {
            /**
             * 分库规则
             * 库：IP第二段分
             * 表：站长ID分
             * 建：存三段IP
             * 使用数据库是10-42个库：共32个库
             */
            $ip_array = explode(".", Helper::getClientIp());
            $is_click = false;  #是否点击
            
            self::$redis->select( ($ip_array[1]%128)+10 );   #32个库求余
            $click = self::$redis->hget('click_'.$data['webmaster_ad_id'], $ip_array[0].'.'.$ip_array[1].'.'.$ip_array[2]);
            if(!empty($click))
            {
                $click_data = json_decode($click, true);
                if( !empty($click_data[$ip_array[3]]) )
                {
                    //存在则点击
                    if(in_array($data['advertiser_ad_id'], $click_data[$ip_array[3]]))
                    {
                        $is_click = true;
                    }
                    else
                    {
                        $click_data[$ip_array[3]][] = $data['advertiser_ad_id'];    #结果数据
                    }

                    //点击次数超过3此不计算
                    if( count($click_data[$ip_array[3]]) >= 4 )
                    {
                        $is_click = true;
                    }
                }
                else
                {
                    $click_data[$ip_array[3]] = [$data['advertiser_ad_id']];    #结果数据
                }
            }
            else
            {
                $click_data = [$ip_array[3]=>[$data['advertiser_ad_id']]];  #结果数据
            }

            //$is_click = true;
            
            //没有点击时
            if($is_click==false)
            {
                self::$redis->hset('click_'.$data['webmaster_ad_id'], $ip_array[0].'.'.$ip_array[1].'.'.$ip_array[2], json_encode($click_data, true));

                //详细更到另外表里
                $click_data = [
                    'position_id' => $data['position_id'],
                    'webmaster_id' => $data['webmaster_id'],
                    'myads_id' => $data['webmaster_ad_id'],
                    'advertiser_id' => $data['advertiser_id'],
                    'advertiser_ad_id' => $data['advertiser_ad_id'],
                    'system' => Helper::getSystem(),
                    "url" => substr($client['url'] , 0 , 200),
                    'source' => $client['source'],
                    'refso' => substr($client['refso'] , 0 , 200),
                    'screen' => $client['screen'],
                    'clickp' => $client['clickp'],
                    'ifrom' => intval($client['ifrom']),
                    'interval' => intval($client['interval']),
                    'history' => intval($client['history']),
                    'ipnumber' => intval($client['ipnumber']),
                    'jstime' => intval($client['jstime']),
                    'ctype' => $client['ctype'],
                    'user_agent' => substr(Helper::server('http_user_agent'), 0 , 250),
                    'time' => time()-intval($data['time']),
                    'type' => '1',
                    'click_source' => $click_source,
                    'ip' => Helper::getClientIp(),
                    'state' => '4',
                    'updated_at' => date("Y-m-d H:i:s"),
                    'created_at' => date("Y-m-d H:i:s"),
                ];
                self::$redis->select(0);
                self::$redis->lpush('earning_click', json_encode($click_data, true));
            }

            $this->skipUrl($link, $advertiser_ad_id);
        }
    }
}