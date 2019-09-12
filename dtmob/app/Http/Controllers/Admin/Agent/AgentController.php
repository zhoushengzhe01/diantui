<?php
namespace App\Http\Controllers\Admin\Agent;

use Illuminate\Http\Request;

use App\Http\Controllers\ApiController;
use App\Model\AgentsEarnings;
use App\Model\AgentsLogs;
use App\Model\AgentsBank;
use App\Model\Agents;


class AgentController extends ApiController
{
    //列表
    public function getAgents(Request $request)
    {
        self::Admin();
        
        //网站杜索
        $id = trim($request->input('id'));
        $username = trim($request->input('username'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit)){
            $limit = 10;
        }

        $agents = Agents::select('*');
        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $agents = $agents->where('alliance_agent_id', self::$user->alliance_agent_id);
        }
        if(!empty($id)){
           $agents = $agents->where('id', '=', $id);
        }
        if(!empty($username)){
           $agents = $agents->where('username', 'like', '%'.$username.'%');
        }

        $count = $agents->count();
        $agents = $agents->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'agents'=>$agents,
        ];

        return response()->json(['data'=>$data], 200);
    }

    //获取单个
    public function getAgent(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        $agent = Agents::where('id', '=', $id)->get();
        if(empty($agent)){
            return response()->json(['message'=>'找不到信息'], 300);
        }

        $data = [
            'agent'=>$agent,
        ];

        return response()->json(['data'=>$data], 200);
    }

    //添加
    public function postAgent(Request $request)
    {
        self::Admin();

        $present = $request->input();

        if(empty($present['username'])){
            return response()->json(['message'=>'输入用户名'], 300);
        }
        if(empty($present['nickname'])){
            return response()->json(['message'=>'输入真实姓名'], 300);
        }
        if(empty($present['password']))
        {
            return response()->json(['message'=>'请填写密码'], 300);
        }
        else{
            if(strlen($present['password'])<6 || strlen($present['password'])>18){
                return response()->json(['message'=>'密码必须大于6位 并 小于18位'], 300);
            }
        }
        if(empty($present['return_point']) && $present['return_point']!='0'){
            return response()->json(['message'=>'请输入利润点'], 300);
        }
        if(empty($present['email'])){
            return response()->json(['message'=>'输入邮箱地址'], 300);
        }
        if(empty($present['mobile'])){
            return response()->json(['message'=>'输入电话号码'], 300);
        }
        if(empty($present['qq'])){
            return response()->json(['message'=>'输入QQ号码'], 300);
        }
        if(empty($present['busine_id'])){
            return response()->json(['message'=>'选择对接商务'], 300);
        }
        if(empty($present['alliance_agent_id'])){
            return response()->json(['message'=>'选择联盟'], 300);
        }
        
        //验证用户名
        $agent = Agents::where('username', '=', $present['username'])->count();
        if(!empty($agent)){
            return response()->json(['message'=>'用户名已经存在'], 300);
        }

        $agent = new Agents();
        $agent->alliance_agent_id = intval($present['alliance_agent_id']);
        $agent->busine_id = intval($present['busine_id']);
        $agent->username = trim($present['username']);
        $agent->nickname = trim($present['nickname']);
        $agent->password = bcrypt($present['password']);
        $agent->return_point = trim($present['return_point']);
        $agent->email = trim($present['email']);
        $agent->mobile = trim($present['mobile']);
        $agent->qq = trim($present['qq']);
        $agent->state = trim($present['state']);

        if($agent->save())
        {
            return response()->json(['message'=>'添加成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'添加失败'], 300);
        }
    }

    //修改
    public function putAgent(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'非法入口，缺少ID'], 300);
        }
        $agent = Agents::where('id', '=', $id)->get();
        if(empty($agent))
        {
            return response()->json(['message'=>'找不到信息'], 300);
        }

        //参数
        $present = $request->input();

        if(empty($present['busine_id'])){
            return response()->json(['message'=>'选择对接商务'], 300);
        }
        if(empty($present['username'])){
            return response()->json(['message'=>'输入用户名'], 300);
        }
        if(empty($present['nickname'])){
            return response()->json(['message'=>'输入真实姓名'], 300);
        }
        if(empty($present['return_point']) && $present['return_point']!='0'){
            return response()->json(['message'=>'输入返点比例'], 300);
        }
        if(!empty($present['password']))
        {
            if(strlen($present['password'])<6 || strlen($present['password'])>18){
                return response()->json(['message'=>'密码必须大于6位 并 小于18位'], 300);
            }
        }
        if(empty($present['email'])){
            return response()->json(['message'=>'输入邮箱地址'], 300);
        }
        if(empty($present['mobile'])){
            return response()->json(['message'=>'输入电话号码'], 300);
        }
        if(empty($present['qq'])){
            return response()->json(['message'=>'输入QQ号码'], 300);
        }

        //验证用户名
        $agent = Agents::where('id', '<>', $id)->where('username', '=', $present['username'])->count();
        if(!empty($agent)){
            return response()->json(['message'=>'用户名已经存在'], 300);
        }

        $agent = Agents::where('id', '=', $id)->first();
        $agent->busine_id = intval($present['busine_id']);
        $agent->username = trim($present['username']);
        $agent->nickname = trim($present['nickname']);
        $agent->return_point = trim($present['return_point']);
        if(!empty($present['password']))
        {
            $agent->password = bcrypt($present['password']);
        }
        $agent->email = trim($present['email']);
        $agent->mobile = trim($present['mobile']);
        $agent->qq = trim($present['qq']);
        $agent->state = trim($present['state']);

        if($agent->save()){
            return response()->json(['message'=>'添加成功'], 200);
        }else{
            return response()->json(['message'=>'添加失败'], 300);
        }
    }
}
