<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Articles;
use App\Model\ArticlesDetail;
use App\Model\ArticlesCategory;

class ArticleCategoryController extends ApiController
{

    public function getCategorys(Request $request)
    {
        self::Admin();

        //网站搜索
        $title = trim($request->input('title'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $categorys = ArticlesCategory::where('id', '<>', '0');

        if(!empty($title))
            $categorys = $categorys->where('title', 'like', '%'.$title.'%');

        $count = $categorys->count();
        $categorys = $categorys->orderBy('sort', 'asc')->orderBy('id', 'desc')->get();

        $data = [
            'count'=>$count,
            'categorys'=>$categorys,
        ];

        return response()->json(['data'=>$data], 200);
    }

    public function getCategory(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $category = ArticlesCategory::where('id', '=', $id)->first();

        if(empty($category))
            return response()->json(['message'=>'未找到数据'], 300);

        return response()->json(['data'=>['category'=>$category]], 200);

    }

    public function putCategory(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $category = ArticlesCategory::where('id', '=', $id)->first();

        if(empty($category))
            return response()->json(['message'=>'未找到数据'], 300);

        $present = $request->input();

        if(!empty($present['title'])){ $category->title = $present['title']; }

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

    public function postCategory(Request $request)
    {
        self::Admin();

        $present = $request->input();

        if(empty($present['title']))
            return response()->json(['message'=>'请输入标题'], 300);

        if(empty($present['sort']))
            return response()->json(['message'=>'请输入排序'], 300);
        
        if(empty($present['state']))
            return response()->json(['message'=>'请选择状态'], 300);

        $category = new ArticlesCategory;
        $category->title = trim($present['title']);
        $category->sort = trim($present['sort']);
        $category->state = trim($present['state']);

        if($category->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }
}