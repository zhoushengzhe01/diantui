<?php
namespace App\Http\Controllers\Admin\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\WebmasterWebsite;

class WebsiteController extends ApiController
{

    public function getWebsites(Request $request)
    {
        self::Admin();

        //网站搜索
        $webmaster_id = trim($request->input('webmaster_id'));
        $domain = trim($request->input('domain'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $websites = WebmasterWebsite::select('webmaster_website.*', 'webmaster.alliance_agent_id')
            ->join('webmaster', 'webmaster.id','=','webmaster_website.webmaster_id');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $websites = $websites->where('webmaster.alliance_agent_id', '=', self::$user->alliance_agent_id);
        }
        ##客服限制权限
        if(self::$user->department_id==3){
            $websites = $websites->where('webmaster.service_id', '=', self::$user->id);
        }

        if(!empty($webmaster_id)){
            $websites = $websites->where('webmaster_website.webmaster_id', '=', $webmaster_id);
        }
        if(!empty($domain)){
            $websites = $websites->where('webmaster_website.domain', 'like', '%'.$domain.'%');
        }

        $count = $websites->count();
        $websites = $websites->orderBy('webmaster_website.id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'websites'=>$websites,
        ];

        return response()->json(['data'=>$data], 200);
    }

    public function getWebsite(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);

        $website = WebmasterWebsite::where('id', '=', $id)->first();

        if(empty($website))
            return response()->json(['message'=>'未找到数据'], 300);

        return response()->json(['data'=>['website'=>$website]], 200);
    }

    public function putWebsite(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $website = WebmasterWebsite::where('id', '=', $id)->first();

        if(empty($website))
            return response()->json(['message'=>'未找到数据'], 300);

        $present = $request->input();

        if(!empty($present['webmaster_id'])){ $website->webmaster_id = $present['webmaster_id']; }

        if(!empty($present['name'])){ $website->name = $present['name']; }

        if(!empty($present['domain'])){ $website->domain = $present['domain']; }

        if(!empty($present['category_id'])){ $website->category_id = $present['category_id']; }

        if(!empty($present['icp'])){ $website->icp = $present['icp']; }

        if(!empty($present['state']) || $present['state']==0){ $website->state = $present['state']; }
        
        if($website->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }

    }

}