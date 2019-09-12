<?php
namespace App\Http\Controllers\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\AdsPosition;
use App\Model\WebmasterAds;
use App\Model\WebmasterWebsite;

class AdsController extends ApiController
{
    protected static $AdField = ['id', 'name', 'state', 'type', 'url', 'units', 'created_at'];

    protected static $MyadField = ['id', 'name', 'position', 'billing', 'position_id', 'position_name', 'state', 'webmaster_id', 'created_at'];

    //获取
    public function getAdtype(Request $request)
    {
        //验证登陆
        self::Webmaster();

        $adtype = AdsPosition::where('id', '<>', '0')
            ->whereIn('id', json_decode(self::$user->allow_ad_type, true))
            ->orderBy('id', 'asc')
            ->get(self::$AdField);

        return response()->json(['data'=>['adtype'=>$adtype]], 200);
    }

    //我的广告位置
    public function getMyads(Request $request)
    {
        //验证登陆
        self::Webmaster();

        //网站搜索
        $name = trim($request->input('name'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $myads = WebmasterAds::where('webmaster_id', '=', self::$user->id);
        if(!empty($name))
        {
            $myads = $myads->where('name', 'like', '%'.$name.'%');
        }

        $count = $myads->count();
        $myads = $myads->orderBy('id', 'desc')->offset($offset)->limit($limit)->get(self::$MyadField);

        $adtype = AdsPosition::orderBy('id', 'asc')
            ->get(self::$AdField);


        foreach($myads as $key=>$val)
        {
            foreach($adtype as $k=>$v)
            {
                if($val->position_id==$v->id)
                {
                    $myads[$key]->url = $v->url;
                }
            }
        }

        $data = [
            'count'=>$count,
            'myads'=>$myads,
        ];

        return response()->json(['data'=>$data], 200);
    }

    public function getMyad(Request $request, $id)
    {
        //验证登陆
        self::Webmaster();
        
        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }
        
        $myad = WebmasterAds::where('id', '=', $id)->where('webmaster_id', '=', self::$user->id)->first(self::$MyadField);

        if(empty($myad)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        return response()->json(['data'=>['myad'=>$myad]], 200);
    }

    //修改
    public function putMyad(Request $request, $id)
    {
        self::Webmaster();

        if(empty($id))
        {
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $present = $request->input();
        $myad = WebmasterAds::where('webmaster_id', '=', self::$user->id)->where('id', '=', $id)->first();
        if(empty($myad))
        {
            return response()->json(['message'=>'找不到此广告'], 300);
        }

        if(!empty($present['name'])){ $myad->name = trim($present['name']); }
        if(!empty($present['position'])){ $myad->position = trim($present['position']); }
        if(!empty($present['billing'])){ $myad->billing = trim($present['billing']); }

        if(!empty($present['position_id']))
        {
            #验证广告位
            $adsPosition = AdsPosition::where('id', '=', $present['position_id'])->where('state', '=', '1')->first();
            if(empty($adsPosition))
            {
                return response()->json(['message'=>'找不到广告位'], 300);
            }
            #验证广告位重复
            if(($present['position_id']=='11' && self::$user->is_top_bottom=='1') || ($present['position_id']=='13' && self::$user->is_left_right=='1'))
            {
                $count = WebmasterAds::where('position_id', $present['position_id'])->where('position', $present['position'])->where('webmaster_id', self::$user->id)->where('id', '<>', $id)->count();
            }
            else
            {
                $count = WebmasterAds::where('position_id', '=', $present['position_id'])->where('webmaster_id', '=', self::$user->id)->where('id', '<>', $id)->count();
            }

            if($count>0)
            {
                return response()->json(['message'=>'此广告形式已经存在。'], 300);
            }

            $myad->position_id = $adsPosition->id;
            $myad->position_name = $adsPosition->name;
        }

        if($myad->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

    //添加
    public function postMyad(Request $request)
    {
        //验证登陆
        self::Webmaster();
        
        $present = $request->input();

        //参数
        if(empty($present['name'])){
            return response()->json(['message'=>'请填写广告名称'], 300); 
        }
        if(empty($present['position_id'])){
            return response()->json(['message'=>'请选择广告类型'], 300); 
        }
        if(empty($present['position'])){
            return response()->json(['message'=>'请选择广告位置'], 300); 
        }
        if(empty($present['billing'])){
            return response()->json(['message'=>'请选择计费方式'], 300);
        }
        #查找广告位
        $adsPosition = AdsPosition::where('id', '=', $present['position_id'])->where('state', '=', '1')->first();
        if(empty($adsPosition)){
            return response()->json(['message'=>'找不到广告位'], 300);
        }
        #验证是否重复
        if(($present['position_id']=='11' && self::$user->is_top_bottom=='1') || ($present['position_id']=='13' && self::$user->is_left_right=='1'))
        {
            $count = WebmasterAds::where('position_id', '=', $present['position_id'])->where('position', $present['position'])->where('webmaster_id', '=', self::$user->id)->count();
        }
        else
        {
            $count = WebmasterAds::where('position_id', '=', $present['position_id'])->where('webmaster_id', '=', self::$user->id)->count();
        }
        if($count>0){
            return response()->json(['message'=>'此广告形式已经存在。'], 300);
        }
        #检查有没有添加网站
        $website = WebmasterWebsite::where('webmaster_id', '=', self::$user->id)->first();
        if(empty($website)){
            return response()->json(['message'=>'请选添加你的网站'], 300);
        }
        if($website->state!='1'){
            return response()->json(['message'=>'你的网站还没有通过审核'], 300);
        }

        $myad = new WebmasterAds;
        $myad->webmaster_id = self::$user->id;
        $myad->name = $present['name'];
        $myad->position_id = $adsPosition->id;
        $myad->position_name = $adsPosition->name;
        $myad->position = $present['position'];
        $myad->billing = $present['billing'];

        if($myad->save())
        {
            return response()->json(['message'=>'添加成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'添加失败'], 300);
        }
    }
}
