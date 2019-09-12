<?php
namespace App\Http\Controllers\Admin\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\WebmasterWebsiteCategory;

class WebsiteCategoryController extends ApiController
{

    public function getWebsiteCategorys(Request $request)
    {
        self::Admin();

        //网站搜索
        $name = trim($request->input('name'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $categorys = WebmasterWebsiteCategory::where('id', '<>', '0');

        if(!empty($name))
            $categorys = $categorys->where('name', 'like', '%'.$name.'%');

        $count = $categorys->count();
        $categorys = $categorys->orderBy('sort', 'asc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'categorys'=>$categorys,
        ];
        
        return response()->json(['data'=>$data], 200);
    }

    public function getWebsiteCategory(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);

        $category = WebmasterWebsiteCategory::where('id', '=', $id)->first();

        if(empty($category))
            return response()->json(['message'=>'未找到数据'], 300);

        return response()->json(['data'=>['category'=>$category]], 200);
    }

    public function putWebsiteCategory(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $category = WebmasterWebsiteCategory::where('id', '=', $id)->first();

        if(empty($category))
            return response()->json(['message'=>'未找到数据'], 300);

        $present = $request->input();

        if(!empty($present['name'])){ $category->name = $present['name']; }

        if(!empty($present['sort'])){ $category->sort = $present['sort']; }

        if(!empty($present['state'])){ $category->state = $present['state']; }

        if($category->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

    public function postWebsiteCategory(Request $request)
    {
        self::Admin();

        $present = $request->input();

        if(empty($present['name']))
            return response()->json(['message'=>'输入分类名称'], 300);

        if(empty($present['sort']))
            return response()->json(['message'=>'输入排序'], 300);

        if(empty($present['state']))
            return response()->json(['message'=>'选择状态'], 300);

        $category = new WebmasterWebsiteCategory;

        $user->name = trim($present['name']);
        $user->sort = trim($present['sort']);
        $user->state = trim($present['state']);

        if($user->save())
        {
            return response()->json(['message'=>'添加成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'添加失败'], 300);
        }
    }
}
