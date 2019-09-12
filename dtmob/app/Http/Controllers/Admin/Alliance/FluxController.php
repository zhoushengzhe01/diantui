<?php
namespace App\Http\Controllers\Admin\Alliance;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\AllianceFlux;
use App\Model\AllianceFluxExpend;
use Hash;

class FluxController extends ApiController
{
    public function getAllianceFluxs(Request $request)
    {
        self::Admin();

        //网站杜索
        $adstype_id = trim($request->input('adstype_id'));
        $name = trim($request->input('name'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit)){
            $limit = 10;
        }

        $fluxs = AllianceFlux::select('*');
        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $fluxs = $fluxs->where('alliance_agent_id', '=', self::$user->alliance_agent_id);
        }

        if(!empty($adstype_id)){
            $fluxs = $fluxs->where('adstype_id', '=', $adstype_id);
        }
        if(!empty($title)){
            $fluxs = $fluxs->where('name', 'like', '%'.$name.'%');
        }

        $count = $fluxs->count();
        $fluxs = $fluxs->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'fluxs'=>$fluxs,
        ];
        
        return response()->json(['data'=>$data], 200);
    }

    public function getAllianceFlux(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);        
        }

        $flux = AllianceFluxExpend::where('id', '=', $id)->first();
        
        if(empty($flux)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        return response()->json(['data'=>['flux'=>$flux]], 200);    
    }

    public function putAllianceFlux(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
        {
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $present = $request->input();

        $flux = AllianceFlux::where('id', '=', $id)->first();

        
        if(!empty($present['name'])){ $flux->name = trim($present['name']); }
        if(!empty($present['alliance_agent_id'])){ $flux->alliance_agent_id = trim($present['alliance_agent_id']); }
        if(!empty($present['adstype_id'])){ $flux->adstype_id = trim($present['adstype_id']); }
        if(!empty($present['advertiser_ad_id'])){ $flux->advertiser_ad_id = trim($present['advertiser_ad_id']); }
        if(!empty($present['account'])){ $flux->account = trim($present['account']); }
        if(!empty($present['password'])){ $flux->password = trim($present['password']); }
        if(!empty($present['url'])){ $flux->url = trim($present['url']); }
        if(!empty($present['record_num'])){ $flux->record_num = trim($present['record_num']); }
        if(!empty($present['sign'])){ $flux->sign = trim($present['sign']); }
        if(!empty($present['in_price_ratio']) || $present['in_price_ratio']=='0'){ $flux->in_price_ratio = trim($present['in_price_ratio']); }
        if(!empty($present['out_price_ratio']) || $present['out_price_ratio']=='0'){ $flux->out_price_ratio = trim($present['out_price_ratio']); }
        if(!empty($present['state'])){ $flux->state = trim($present['state']); }
        
        if($flux->save())
        {
            return response()->json(['message'=>'添加成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'添加失败'], 300);
        }
    }

    public function postAllianceFlux(Request $request)
    {
        self::Admin();

        $present = $request->input();
 
        if(empty($present['name']))
            return response()->json(['message'=>'请输入联盟名字'], 300);

        if(empty($present['adstype_id']))
            return response()->json(['message'=>'请输入选择广告类型'], 300);

        if(empty($present['advertiser_ad_id']))
            return response()->json(['message'=>'请输入关联计划ID'], 300);
         
        if(empty($present['account']))
            return response()->json(['message'=>'请输入登陆账号'], 300);
         
        if(empty($present['password']))
            return response()->json(['message'=>'请输入登陆密码'], 300);
         
        if(empty($present['url']))
            return response()->json(['message'=>'请输入联盟网址'], 300);
         
        if(empty($present['record_num']))
            return response()->json(['message'=>'请输入计费次数'], 300);
         
        if(empty($present['sign']))
            return response()->json(['message'=>'请输入密钥'], 300);
         
        if(empty($present['out_price_ratio']))
            return response()->json(['message'=>'消耗价格'], 300);

        if(empty($present['in_price_ratio']))
            return response()->json(['message'=>'消耗价格'], 300);

        if(empty($present['state']))
            return response()->json(['message'=>'请输入状态'], 300);

        $flux = new AllianceFlux;
        $flux->name = trim($present['name']);
        $flux->adstype_id = trim($present['adstype_id']);
        $flux->advertiser_ad_id = trim($present['advertiser_ad_id']);
        $flux->account = trim($present['account']);
        $flux->password = trim($present['password']);
        $flux->url = trim($present['url']);
        $flux->record_num = trim($present['record_num']);
        $flux->sign = trim($present['sign']);
        $flux->state = trim($present['state']);
        $flux->out_price_ratio = trim($present['out_price_ratio']);
        $flux->in_price_ratio = trim($present['in_price_ratio']);

        if($flux->save()){
            return response()->json(['message'=>'添加成功'], 200);
        }else{
            return response()->json(['message'=>'添加失败'], 300);
        }
    }
}