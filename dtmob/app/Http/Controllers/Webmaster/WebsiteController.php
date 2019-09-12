<?php
namespace App\Http\Controllers\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\WebmasterWebsite;


class WebsiteController extends ApiController
{
    //获取
    public function getWebsites(Request $request)
    {
        //验证登陆
        self::Webmaster();

        //网站搜索
        $domain = trim($request->input('domain'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $websites = WebmasterWebsite::where('webmaster_id', '=', self::$user->id);

        if(!empty($domain))
        {
            $websites = $websites->where('domain', 'like', '%'.$domain.'%');
        }

        $count = $websites->count();
        $websites = $websites->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'websites'=>$websites,
        ];

        return response()->json(['data'=>$data], 200);
    }

    //获取单个
    public function getWebsite(Request $request, $id)
    {
        self::Webmaster();
        
        if(empty($id))
        {
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $website = WebmasterWebsite::where('id', '=', $id)->where('webmaster_id', '=', self::$user->id)->first();

        if(empty($website))
        {
            return response()->json(['message'=>'未找到数据'], 300);
        }

        return response()->json(['data'=>['website'=>$website]], 200);
    }

    //添加
    public function postWebsite(Request $request)
    {
        //验证登陆
        self::Webmaster();
        
        $present = $request->input();
        
        if(empty($present['name']))
        {
            return response()->json(['message'=>'请填写网站名称'], 300);
        }
        if(empty($present['domain']))
        {
            return response()->json(['message'=>'请填写域名'], 300);
        }
        if(empty($present['icp']))
        {
            return response()->json(['message'=>'请填写备案号'], 300);
        }
        if(empty($present['category_id']))
        {
            return response()->json(['message'=>'请选择分类'], 300);
        }

        //检测
        $count = WebmasterWebsite::where('domain', '=', $present['domain'])->count();
        if($count>0)
        {
            return response()->json(['message'=>'域名已经存在了'], 300);
        }
        
        $website = new WebmasterWebsite;
        $website->webmaster_id = self::$user->id;
        $website->name = trim($present['name']);
        $website->domain = trim($present['domain']);
        $website->category_id = trim($present['category_id']);
        $website->icp = trim($present['icp']);
        $website->state = '0';

        if($website->save())
        {
            return response()->json(['message'=>'添加成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'添加失败'], 300);
        }   
    }

    //修改
    public function putWebsite(Request $request, $id)
    {
        self::Webmaster();

        if(empty($id))
        {
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $present = $request->input();

        if(empty($present['name']))
        {
            return response()->json(['message'=>'请填写网站名称'], 300);
        }
        if(empty($present['domain']))
        {
            return response()->json(['message'=>'请填写域名'], 300);
        }
        if(empty($present['icp']))
        {
            return response()->json(['message'=>'请填写备案号'], 300);
        }
        if(empty($present['category_id']))
        {
            return response()->json(['message'=>'请选择分类'], 300);
        }

        //检测
        $count = WebmasterWebsite::where('domain', '=', $present['domain'])->where('id','<>',$id)->count();
        if($count>0)
        {
            return response()->json(['message'=>'域名已经存在了'], 300);
        }

        $website = WebmasterWebsite::where('webmaster_id', '=', self::$user->id)->where('id', '=', $id)->first();
        if(empty($website))
        {
            return response()->json(['message'=>'找不到数据'], 300);
        }

        $website->name = trim($present['name']);
        $website->domain = trim($present['domain']);
        $website->category_id = trim($present['category_id']);
        $website->icp = trim($present['icp']);
        $website->state = '0';
        
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
