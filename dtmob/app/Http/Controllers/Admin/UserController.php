<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Users;
use Hash;

class UserController extends ApiController
{

    public function getUsers(Request $request)
    {
        self::Admin();

        #网站搜索
        $department_id = trim($request->input('department_id'));
        $username = trim($request->input('username'));
        $qq = trim($request->input('qq'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit)){
            $limit = 10;
        }

        $users = Users::select('*');
        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $users = $users->where('alliance_agent_id', self::$user->alliance_agent_id);
        }
        if(!empty($department_id)){
            $users = $users->where('department_id', '=', $department_id);
        }
        if(!empty($username)){
            $users = $users->where('username', '=', $username);
        }
        if(!empty($qq)){
            $users = $users->where('qq', '=', $qq);
        }

        $count = $users->count();
        $users = $users->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'users'=>$users,
        ];
        
        return response()->json(['data'=>$data], 200);
    }

    public function getUser(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);

        $user = Users::where('id', '=', $id)->first();

        if(empty($user))
            return response()->json(['message'=>'未找到数据'], 300);

        return response()->json(['data'=>['user'=>$user]], 200);
    }

    public function putUser(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $user = Users::where('id', '=', $id)->first();

        if(empty($user)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        $present = $request->input();

        if(!empty($present['department_id'])){ $user->department_id = $present['department_id']; }
        if(!empty($present['alliance_agent_id'])){ $user->alliance_agent_id = $present['alliance_agent_id']; }
        if(!empty($present['username'])){ $user->username = $present['username']; }
        if(!empty($present['nickname'])){ $user->nickname = $present['nickname']; }
        if(!empty($present['stagename'])){ $user->stagename = $present['stagename']; }
        if(!empty($present['password'])){ $user->password = bcrypt($present['password']); }
        if(!empty($present['mobile'])){ $user->mobile = $present['mobile']; }
        if(!empty($present['email'])){ $user->email = $present['email']; }
        if(!empty($present['qq'])){ $user->qq = $present['qq']; }
        if(!empty($present['state'])){ $user->state = $present['state']; }

        if($user->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

    public function postUser(Request $request)
    {
        self::Admin();

        $present = $request->input();

        if(empty($present['department_id'])){
            return response()->json(['message'=>'请选择部门'], 300);
        }
        if(empty($present['alliance_agent_id'])){
            return response()->json(['message'=>'请选择部门'], 300);
        }
        if(empty($present['username'])){
            return response()->json(['message'=>'输入登录名称'], 300);
        }
        if(empty($present['nickname'])){
            return response()->json(['message'=>'输入真是姓名'], 300);
        }
        if(empty($present['stagename'])){
            return response()->json(['message'=>'输入艺名'], 300);
        }
        if(empty($present['password'])){
            return response()->json(['message'=>'输入登录密码'], 300);
        }
        if(empty($present['mobile'])){
            return response()->json(['message'=>'输入电话号码'], 300);
        }
        if(empty($present['qq'])){
            return response()->json(['message'=>'输入手机号码'], 300);
        }
        if(empty($present['state'])){
            return response()->json(['message'=>'输入状态'], 300);
        }

        $user = new Users;
        $user->department_id = trim($present['department_id']);
        $user->alliance_agent_id = trim($present['alliance_agent_id']);
        $user->username = trim($present['username']);
        $user->nickname = trim($present['nickname']);
        $user->stagename = trim($present['stagename']);
        $user->password = bcrypt($present['password']);
        $user->mobile = trim($present['mobile']);
        $user->qq = trim($present['qq']);
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

    public function getMyUser(Request $request)
    {
        self::Admin();

        $user = Users::where('id', '=', self::$user->id)->first();

        if(empty($user))
            return response()->json(['message'=>'未找到数据'], 300);

        return response()->json(['data'=>['user'=>$user]], 200);
    }

    public function putMyUser(Request $request)
    {
        self::Admin();

        $user = Users::where('id', '=', self::$user->id)->first();

        if(empty($user)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        $present = $request->input();

        if(!empty($present['department_id'])){ $user->department_id = $present['department_id']; }
        if(!empty($present['alliance_agent_id'])){ $user->alliance_agent_id = $present['alliance_agent_id']; }
        if(!empty($present['username'])){ $user->username = $present['username']; }
        if(!empty($present['nickname'])){ $user->nickname = $present['nickname']; }
        if(!empty($present['stagename'])){ $user->stagename = $present['stagename']; }
        if(!empty($present['password'])){ $user->password = $present['password']; }
        if(!empty($present['mobile'])){ $user->mobile = $present['mobile']; }
        if(!empty($present['email'])){ $user->email = $present['email']; }
        if(!empty($present['qq'])){ $user->qq = $present['qq']; }
        if(!empty($present['state'])){ $user->state = $present['state']; }

        if($user->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }
}
