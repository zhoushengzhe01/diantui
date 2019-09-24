<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\AllianceAgent;

class AllianceAgentController extends ApiController
{
    public function getAllianceAgents(Request $request)
    {
        self::Admin();

        #网站搜索
        $name = trim($request->input('name'));
        $domain = trim($request->input('domain'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $alliance_agents = AllianceAgent::select('*');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $alliance_agents = $alliance_agents->where('id', self::$user->alliance_agent_id);
        }

        if(!empty($name)){
            $alliance_agents = $alliance_agents->where('name', 'like', '%'.$name.'%');
        }
        if(!empty($domain)){
            $alliance_agents = $alliance_agents->where('domain', 'like', '%'.$domain.'%');
        }

        $count = $alliance_agents->count();
        $alliance_agents = $alliance_agents->orderBy('id', 'asc')->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'alliance_agents'=>$alliance_agents,
        ];

        return response()->json(['data'=>$data], 200);
    }

    public function putAllianceAgent(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $alliance_agent = AllianceAgent::where('id', '=', $id)->first();

        if(empty($alliance_agent)){
            return response()->json(['message'=>'找不到数据'], 400);
        }

        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            if(self::$user->alliance_agent_id != $alliance_agent->id){
                return response()->json(['message'=>'对不起你无权操作'], 400);
            }
        }

        $present = $request->input();
        if(!empty($present['name'])){ $alliance_agent->name = trim($present['name']); }
        if(!empty($present['domain'])){ $alliance_agent->domain = trim($present['domain']); }
        if(!empty($present['key'])){ $alliance_agent->key = trim($present['key']); }
        if(!empty($present['pg_domain'])){ $alliance_agent->pg_domain = trim($present['pg_domain']); }
        if(!empty($present['pc_domain'])){ $alliance_agent->pc_domain = trim($present['pc_domain']); }
        if(!empty($present['pv_domain'])){ $alliance_agent->pv_domain = trim($present['pv_domain']); }
        if(!empty($present['image_domain'])){ $alliance_agent->image_domain = trim($present['image_domain']); }
        if(!empty($present['seo_title'])){ $alliance_agent->seo_title = trim($present['seo_title']); }
        if(!empty($present['seo_words'])){ $alliance_agent->seo_words = trim($present['seo_words']); }
        if(!empty($present['seo_description'])){ $alliance_agent->seo_description = trim($present['seo_description']); }

        if(!empty($present['old_pg_domain']))
        {
            if($present['old_pg_domain']!=$alliance_agent->old_pg_domain)
            {
                $alliance_agent->old_pg_domain = trim($present['old_pg_domain']);
                $alliance_agent->pg_domain_update_time = date("Y-m-d H:i:s");
            }
        }

        if($alliance_agent->save()){
            return response()->json(['message'=>'修改成功'], 200);
        }else{
            return response()->json(['message'=>'修改失败'], 300);
        }
    }
}