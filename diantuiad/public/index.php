<?php
date_default_timezone_set('PRC');

ini_set("display_errors", "On");

error_reporting(E_ALL | E_STRICT);

header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');

header('Access-Control-Allow-Origin:*');


/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/

require '../vendor/autoload.php';
define('__ROOT__', __DIR__);

/*
|--------------------------------------------------------------------------
| According to the routing controller
|--------------------------------------------------------------------------
*/
$route = [
	'get'=>[
		'/coding/[0-9]+'=>'AdsController@getCoding',
		'/coding/info/[0-9]+'=>'AdsInforController@getCoding',
		'/coding/return/[0-9]+'=>'AdsReturnController@getCoding',
		'/weixin'=>'WeixinController@getWeixin',
		'/pv'=>'StatisController@postPv',
		'/pc'=>'StatisController@postPc',
		
		//二次点击
		'/again/coding/[0-9]+'=>'AgainController@coding',
		'/again/pc'=>'AgainController@againPc',
		'/again/pv'=>'AgainController@againPv',

		//互动式广告
		'/hudong/statpc'=>'HudongStatController@setHudongPc', 	//互动式广告点击
		'/hudong/statpm'=>'HudongStatController@setHudongPm', 	//互动式广告展示
		'/hudong/[0-9]+'=>'HudongController@getHudong', 		//互动式广告请求

		//直连推广
		'/tuiguang'=>'TuiguangController@getIndex',
	]
];

$request = explode("?", trim($_SERVER['REQUEST_URI']));
$request_url = empty($request[0]) ? '/' : $request[0];

$method = strtolower( trim( $_SERVER['REQUEST_METHOD'] ) );

if(!is_array($route[$method]))
{
	die("没有路由");
}

//匹配URl
foreach($route[$method] as $k=>$v)
{
    if (preg_match("#^".$k."#", $request_url))
    {
    	$value = $v;
    }
}
if(empty($value))
{
	die("404 找不到你要的信息");
}
preg_match_all('/[0-9]+/i', $request_url, $paramet);
implode(", ", $paramet[0]);

$array = explode('@', $value);
$file = trim($array[0]);
$function = trim($array[1]);

if($file && $function)
{
	eval('$obj = new \app\\Controller\\'.$file.'();$obj->'.$function.'('.implode(", ", $paramet[0]).');');
}