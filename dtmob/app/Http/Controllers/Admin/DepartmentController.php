<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\UsersDepartment;
use App\Model\UsersMenusRule;
use DB;

class DepartmentController extends ApiController
{
    //部门
    public function getDepartments(Request $request)
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

        $departments = UsersDepartment::where('id', '<>', '0');

        if(!empty($name))
            $departments = $departments->where('name', 'like', '%'.$name.'%');

        $count = $departments->count();
        $departments = $departments->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        foreach($departments as $key=>$val)
        {
            $rule = UsersMenusRule::where('department_id', '=', $val->id)->first();

            if(!empty($rule))
            {
                $departments[$key]->rule = json_decode( $rule->rules, true);
            }
            else
            {
                $departments[$key]->rule = '';
            }
        }

        $data = [
            'count'=>$count,
            'departments'=>$departments,
        ];
        
        return response()->json(['data'=>$data], 200);

    }

    public function getDepartment(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $department = UsersDepartment::where('id', '=', $id)->first();

        $rule = UsersMenusRule::where('department_id', '=', $id)->first();

        $department->rule = $rule->rules;

        if(empty($department))
            return response()->json(['message'=>'未找到数据'], 300);

        return response()->json(['data'=>['department'=>$department]], 200);

    }

    public function putDepartment(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $present = $request->input();

        if( empty($present['menus']) )
            return response()->json(['message'=>'请选择权重'], 300);

        //开始事务
        DB::beginTransaction(); 

        $department = UsersDepartment::where('id', '=', $id)->first();

        if(empty($department))
            return response()->json(['message'=>'未找到数据'], 300);

        if(!empty($present['name'])){ $department->name = $present['name']; }
        
        if(!empty($present['state'])){ $department->state = $present['state']; }

        if( empty($department->save()) )
        {
            DB::rollback();
            return response()->json(['message'=>'修改失败'], 300);
        }

        $rules = UsersMenusRule::firstOrCreate(['department_id'=>$id]);

        $rules->rules = json_encode($present['menus'], true);

        if( empty($rules->save()) )
        {
            DB::rollback();
            return response()->json(['message'=>'修改失败'], 300);
        }

        //提交事务
        DB::commit();

        return response()->json(['message'=>'修改成功'], 200);
    }

    public function postDepartment(Request $request)
    {
        self::Admin();

        $present = $request->input();

        if(empty($present['name']))
            return response()->json(['message'=>'请输入部门名称'], 300);
        
        if(empty($present['state']))
            return response()->json(['message'=>'请选择状态'], 300);
        
        $department = new UsersDepartment;
        $department->name = trim($present['name']);
        $department->state = trim($present['state']);
        
        if($department->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }
}
