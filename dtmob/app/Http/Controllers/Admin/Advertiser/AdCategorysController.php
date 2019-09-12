<?php
namespace App\Http\Controllers\Admin\Advertiser;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\AdvertiserAds;
use App\Model\AgainDay;
use App\Model\AdvertiserAdCategorys;

use Hash;

class AdCategorysController extends ApiController
{

    public function getCategorys(Request $request)
    {
        self::Admin();

        //网站杜索
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit)){
            $limit = 10;
        }

        $categorys = AdvertiserAdCategorys::select('*');
        $count = $categorys->count();
        $categorys = $categorys->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'categorys'=>$categorys,
        ];
        
        return response()->json(['data'=>$data], 200);
    }

    public function getCategory(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }
        $category = AdvertiserAdCategorys::where('id', $id)->first();

        return response()->json(['data'=>['ad'=>$ad]], 200);
    }

    public function putCategory(Request $request, $id){

        self::Admin();
        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $name = trim($request->input('name'));
        $sort = intval($request->input('sort'));
        $state = trim($request->input('state'));
        if(empty($name)){
            return response()->json(['message'=>'请输入分类名称'], 300);
        }

        $category = AdvertiserAdCategorys::where('id', $id)->first();
        if(empty($category)){
            return response()->json(['message'=>'找不到分类'], 300);
        }
        $category->name = $name;
        $category->sort = $sort;
        $category->state = $state;

        if($category->save()){
            return response()->json(['message'=>'修改成功'], 200);
        }else{
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

    public function postCategory(Request $request)
    {
        self::Admin();

        $name = trim($request->input('name'));
        $sort = intval($request->input('sort'));
        $state = trim($request->input('state'));
        if(empty($name)){
            return response()->json(['message'=>'请输入分类名称'], 300);
        }

        $category = new AdvertiserAdCategorys;
        $category->name = $name;
        $category->sort = $sort;
        $category->state = $state;

        if($category->save()){
            return response()->json(['message'=>'添加成功'], 200);
        }else{
            return response()->json(['message'=>'添加失败'], 300);
        }
    }
}