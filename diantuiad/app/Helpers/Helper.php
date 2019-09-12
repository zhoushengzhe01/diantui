<?php
namespace app\Helpers;

use app\Helpers\Helper;
use app\Helpers\CityReader;
use app\Config\AppConfig;

class Helper
{
    //系统服务参数
    public static function server($key)
    {
        if(!empty($_SERVER[strtoupper($key)]))
        {
            return trim($_SERVER[strtoupper($key)]);
        }
        else
        {
            return false;
        }
    }

    //获取主域名
    public static function getPrimaryDomain($host, $suffix){
        
        if(trim($host) == '')
        {
            return FALSE;
        }
        
        $pattern = '/(?:[a-z0-9-]){1,63}\.(?:'.$suffix.')(?:\:\d+)?$/';

        if(preg_match($pattern, $host, $matches))
        {
            if(empty($matches[0]))
            {
                return false;
            }
            else
            {
                return trim($matches[0]);
            }
            
        }
    }
    //请求参数
    public static function request($name=null)
    {
        if($name)
        {
            if(empty($_REQUEST[$name]))
            {
                return false;
            }
            else
            {
                return trim($_REQUEST[$name]);
            }
        }
        else
        {
            return self::arrayToObject($_REQUEST);
        }
        
    }

    //数组 转 对象
    public static function arrayToObject($arr) {

        if (gettype($arr) != 'array') {
        
            return;
        
        }
        
        foreach ($arr as $k => $v) {
        
            if (gettype($v) == 'array' || getType($v) == 'object') {
        
                $arr[$k] = (object)array_to_object($v);
        
            }
        
        }

        return (object)$arr;
    }

    //对象 转 数组
    public static function objectToArray($obj)
    {
        $obj = (array)$obj;

        foreach ($obj as $k => $v) {
        
            if (gettype($v) == 'resource') {
                return;
            }
        
            if (gettype($v) == 'object' || gettype($v) == 'array') {

                $obj[$k] = (array)object_to_array($v);
            }
        }
        return $obj;
    }

    public static function array_index($t)
	{
		$f = 0;
		$config = [];
		$key = "ZtKfUjbwiR90dm8BgsX4cQ7T5W6kDCHNYG1oyEzqpxrLlFSJPn3uMevhOaVI2A_-";
        while($f < strlen($key))
        {
			$config[] = substr($key, $f++, 1);
		}
		return array_search($t, $config);
    }
    
    public static function decode64($str)
    {
        $str = str_replace("!","",$str);
		$slen = strlen($str);
		$mod = $slen%4;
		$num = floor($slen/4);
		$desc = [];
		for( $i=0; $i<$num; $i++ ){
			$arr = array_map("self::array_index", str_split(substr($str,$i*4,4)));
			$desc_0 = ($arr[0]<<2)|(($arr[1]&48)>>4);
			$desc_1 = (($arr[1]&15)<<4)|(($arr[2]&60)>>2);
			$desc_2 = (($arr[2]&3)<<6)|$arr[3];
			$desc = array_merge($desc,[$desc_0,$desc_1,$desc_2]);
		}
		if($mod == 0){
			return implode('', array_map("chr",$desc));
		}
		
		$arr = array_map("self::array_index", str_split(substr($str,$num*4,4)));
		
		if(count($arr) == 1)
		{
			$desc_0 = $arr[0]<<2;
			if($desc_0 != 0){
				$desc = array_merge($desc,[$desc_0]);
			}
		}
		else if(count($arr) == 2)
		{
			$desc_0 = ($arr[0]<<2)|(($arr[1]&48)>>4);
			$desc = array_merge($desc,[$desc_0]);
		}
		else if(count($arr) == 3)
		{
			$desc_0 = ($arr[0]<<2)|(($arr[1]&48)>>4);
			$desc_1 = ($arr[1]<<4)|(($arr[2]&60)>>2);
			$desc = array_merge($desc,[$desc_0,$desc_1]);
		}
		return implode('', array_map("chr",$desc));
    }
    
    //获取客户端系统
    public static function getSystem()
    {
        $userAgent = self::server('http_user_agent');

        if (strpos($userAgent, 'Android') !== false) {
            $system = 'Android';

        } elseif (strpos($userAgent, 'iPhone') !== false) {
            $system = 'iPhone';
        
        } elseif (strpos($userAgent, 'iPad') !== false) {
            $system = 'iPad';
        
        } elseif (strpos($userAgent, 'Windows') !== false) {
            $system = 'Windows';
        
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $system = 'Linux';
        
        } elseif (strpos($userAgent, 'unix') !== false) {
            $system = 'unix';
        
        } elseif (strpos($userAgent, 'sun') !== false) {
            $system = 'sun';
        
        } elseif (strpos($userAgent, 'ibm') !== false) {
            $system = 'ibm';

        } elseif (strpos($userAgent, 'Mac') !== false) {
            $system = 'Mac';

        } elseif (strpos($userAgent, 'PowerPC') !== false) {
            $system = 'PowerPC';

        } elseif (strpos($userAgent, 'AIX') !== false) {
            $system = 'AIX';

        } elseif (strpos($userAgent, 'HPUX') !== false) {
            $system = 'HPUX';
        
        } elseif (strpos($userAgent, 'NetBSD') !== false) {
            $system = 'NetBSD';
        
        } elseif (strpos($userAgent, 'BSD') !== false) {
            $system = 'BSD';
        
        } elseif (strpos($userAgent, 'OSF1') !== false) {
            $system = 'OSF1';
        
        } elseif (strpos($userAgent, 'IRIX') !== false) {
            $system = 'IRIX';
        
        } elseif (strpos($userAgent, 'FreeBSD') !== false) {
            $system = 'FreeBSD';
        
        } else{
            $system = 'Unknown';
        }

        return $system;
    }

    //判断是否微信
    public static function isWechat()
    {
        $userAgent = self::server('http_user_agent');
       
        if(strpos($userAgent, 'MicroMessenger') == true){
            
            return true;
        }else{

            return false;
        }
    }

    //判断是否微信
    public static function isQQAPP()
    {
        $userAgent = self::server('http_user_agent');
       
        if(strpos($userAgent, 'MQQBrowser') == true){
            
            return true;
        }else{

            return false;
        }
    }

    /**
     * 获得客户端
     * 类型：微信，浏览器，APP，蜘蛛
     */
    public static function getClient()
    {
        $userAgent = self::server('http_user_agent');
       
        if(strpos($userAgent, 'MicroMessenger') == true)
        {
            return 'wechat';
        }
        else if(strpos($userAgent, 'Browser') == true)
        {
            return 'browser';
        }
        else if(preg_match("/(googlebot|baiduspider|sogou|360spider|bingbot)/i", $userAgent) == true)
        {
            return 'spider';
        }
        else if( !empty($userAgent) )
        {
            return 'app';
        }
        else
        {
            return false;
        }
    }

    //获取客户端IP地址
    public static function getClientIp()
    {
        
        if(!empty($_SERVER["X-Real-IP"]))
        {
            $cip = $_SERVER["X-Real-IP"];
        }
        else if(!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"]))
        {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        else
        {
            $cip = '';
        }
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);

        return $cip;
    }

    //获得地区
    public static function getRegion($ip_number)
    {
        $ipList = include AppConfig::get('root').'/cache/ipLists/ipFileList.php';

        $ip_file = '';

        foreach($ipList as $key=>$val)
        {
            if($key<=$ip_number && $ip_number<=$val)
            {
                $ip_file = $key.'-'.$val;
                break;
            }
        }

        $region = '';

        if(file_exists(AppConfig::get('root').'/cache/ipLists/'.$ip_file))
        {
            $content = file( AppConfig::get('root').'/cache/ipLists/'.$ip_file );

            foreach($content as $val)
            {
                $ip_arr = explode("\t", $val);

                if( intval($ip_arr[0])<=$ip_number && $ip_number <= intval($ip_arr[1]) )
                {
                    $region = $ip_arr[2];
                    break;
                }
            }

            return $region;
        }
        else
        {
            return false;
        }
    }

    public static function getCity($ip)
    {
        $ip_array = explode('.', $ip);

        if(count($ip_array) == 4)
        {
            $path = AppConfig::get('root').'/cache/ipipfree/'.$ip_array[0].'/'.$ip_array[1];
            $file_path = $path.'/'.$ip_array[2];
            if(file_exists($file_path))
            {
                $file = fopen($file_path, "r");
                $file_size = filesize($file_path);
                $result = json_decode(fread($file, $file_size), true);
                fclose($file);
            }
            else
            {
                $reader = new CityReader(AppConfig::get('root').'/cache/ipipfree.ipdb');
                $result = $reader->findMap($ip, 'CN');

                if(!file_exists($file_path)){
                    @mkdir($path, 0777, true);
                }
                #写入文件操作
                $file = @fopen($file_path, "w");
                @fwrite($file, json_encode($result, true));
                @fclose($file);
            }
        }
        else
        {
            $reader = new CityReader(AppConfig::get('root').'/cache/ipipfree.ipdb');
            $result = $reader->findMap($ip, $language);
        }
        
        return $result;
    }
    
    //获取配置文件
    public static function getConfig()
    {
        if( !file_exists(AppConfig::get('config_path')) )
        {
            mkdir( iconv("UTF-8", "GBK", AppConfig::get('config_path') ), 0777, true); 
        }

        $fileName = "/config.json";
        $data =  json_decode(file_get_contents(AppConfig::get('config_path').$fileName), true);

        if(!empty($data))
        {
            return $data;
        }
        else
        {
            return false;
        }
    }


    //获取密钥
    public static function getAccessToken($data)
    {
        if(!is_array($data))
        {
            return false;
        }

        $data['token'] = md5(AppConfig::get('token'));
        
        ksort($data);

        $token = md5(http_build_query($data));

        return $token;
    }

    //加密
    public static function encode($data)
    {
        ksort($data);

        $data['key'] = md5( implode("&", $data).'&'.AppConfig::get('token') );

        $string = '';
        
        foreach($data as $key=>$val)
        {
            $string .= $key.'='.$val.'&';
        }
        
        $string = base64_encode(substr($string, 0, -1));

        return $string;
    }

    //解密
    public static function decode($string)
    {
        $data = [];

        $array = explode('&', base64_decode($string));

        foreach($array as $val)
        {
            $arr = explode('=', $val);
            $data[$arr[0]] = $arr[1];
        }

        //验证
        $key = $data['key'];
        
        unset($data['key']);

        if( $key != md5( implode("&", $data).'&'.AppConfig::get('token')) )
        {
            return false;
        }
        else
        {
            return $data;
        }
    }


    //提示
    public static function message($msg)
    {
        die('document.writeln("<font size=2>'.$msg.'")');
    }
}
