<?php
namespace App\Http\Controllers\Admin\Alliance;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Flowpool;

class FlowpoolController extends ApiController
{

    public function getFlowpools(Request $request)
    {
        self::Admin();

        //搜索
        $name = trim($request->input('name'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $flowpools = Flowpool::where('id', '<>', '0');

        if(!empty($name))
            $flowpools = $cateflowpoolsgorys->where('name', 'like', '%'.$name.'%');

        $count = $flowpools->count();
        $flowpools = $flowpools->orderBy('sort', 'asc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'flowpools'=>$flowpools,
        ];
        
        return response()->json(['data'=>$data], 200);
    }

    public function getFlowpool(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);

        $flowpool = Flowpool::where('id', '=', $id)->first();

        if(empty($flowpool)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        return response()->json(['data'=>['flowpool'=>$flowpool]], 200);
    }

    public function putFlowpool(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $flowpool = Flowpool::where('id', '=', $id)->first();
        if(empty($flowpool)){
            return response()->json(['message'=>'未找到数据'], 300);
        }
            

        $present = $request->input();
        if(!empty($present['name'])){ $flowpool->name = $present['name']; }
        if(!empty($present['sort'])){ $flowpool->sort = $present['sort']; }
        if(!empty($present['state'])){ $flowpool->state = $present['state']; }

        if($flowpool->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

    public function postFlowpool(Request $request)
    {
        self::Admin();

        $present = $request->input();

        if(empty($present['name']))
            return response()->json(['message'=>'输入分类名称'], 300);

        if(empty($present['sort']))
            return response()->json(['message'=>'输入排序'], 300);

        if(empty($present['state']))
            return response()->json(['message'=>'选择状态'], 300);

        $flowpool = new Flowpool;

        $flowpool->name = trim($present['name']);
        $flowpool->sort = trim($present['sort']);
        $flowpool->state = trim($present['state']);

        if($flowpool->save())
        {
            return response()->json(['message'=>'操作成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'操作失败'], 300);
        }
    }
}
