<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Customers;
use App\Model\CustomersNote;
use App\Model\CustomersSource;

class CustomerSourceController extends ApiController
{

    public function getSources(Request $request)
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

        $sources = CustomersSource::where('id', '<>', '0');

        if(!empty($name))
            $sources = $sources->where('name', 'like', '%'.$name.'%');

        $count = $sources->count();
        $sources = $sources->orderBy('id', 'desc')->get();

        $data = [
            'count'=>$count,
            'sources'=>$sources,
        ];

        return response()->json(['data'=>$data], 200);
    }

    /*
    public function getSource(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $source = CustomersSource::where('id', '=', $id)->first();

        if(empty($source))
            return response()->json(['message'=>'未找到数据'], 300);

        return response()->json(['data'=>['source'=>$source]], 200);
    }
    */


    public function putSource(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $source = CustomersSource::where('id', '=', $id)->first();

        if(empty($source))
            return response()->json(['message'=>'未找到数据'], 300);

        $present = $request->input();

        if(!empty($present['name'])){ $source->name = $present['name']; }

        if(!empty($present['sort'])){ $source->sort = $present['sort']; }
        
        if(!empty($present['state'])){ $source->state = $present['state']; }

        if($source->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }


    public function postSource(Request $request)
    {
        self::Admin();

        $present = $request->input();

        if(empty($present['name']))
            return response()->json(['message'=>'输入名称'], 300);

        if(empty($present['sort']))
            return response()->json(['message'=>'输入排序'], 300);

        if(empty($present['state']))
            return response()->json(['message'=>'选择状态'], 300);

        $source = new CustomersSource;
        $source->name = $present['name'];
        $source->sort = $present['sort'];
        $source->state = $present['state'];

        if($source->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }
}