<?php
namespace App\Http\Controllers\Admin\Advertiser;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\AdvertiserLoginLog;
use App\Model\Advertiser;
use App\Model\Users;
use App\Model\Agents;
use Cache;
use Hash;

class AdvertiserController extends ApiController
{

    public function getAdvertisers(Request $request)
    {
        self::Admin();

        //网站杜索
        $id = trim($request->input('id'));
        $qq = trim($request->input('qq'));
        $username = trim($request->input('username'));
        $agent_id = trim($request->input('agent_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $advertisers = Advertiser::select('*');
        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $advertisers = $advertisers->where('alliance_agent_id', '=', self::$user->alliance_agent_id);
        }
        #商务权限限制
        if(self::$user->department_id==4){
            $advertisers = $advertisers->whereIn('busine_id', [self::$user->id,0]);
        }
        if(!empty($username)){
            $advertisers = $advertisers->where('username', 'like', '%'.$username.'%');
        }
        if(!empty($id)){
            $advertisers = $advertisers->where('id', '=', $id);
        }
        if(!empty($qq)){
            $advertisers = $advertisers->where('qq', '=', $qq);
        }
        if(!empty($agent_id)){
            $advertisers = $advertisers->where('agent_id', '=', $agent_id);
        }

        $count = $advertisers->count();
        $advertisers = $advertisers->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'advertisers'=>$advertisers,
        ];
        
        return response()->json(['data'=>$data], 200);
    }

    public function getAdvertiser(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $advertiser = Advertiser::where('id', '=', $id)->first();
        if(empty($advertiser)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        $advertiser->agent_name = '';
        if(!empty($advertiser->agent_id))
        {
            $agent = Agents::where('id', '=', $advertiser->agent_id)->first();
            $advertiser->agent_name = $agent->username;
        }

        return response()->json(['data'=>['advertiser'=>$advertiser]], 200);
    
    }

    public function putAdvertiser(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }
        
        $present = $request->input();

        $advertiser = Advertiser::where('id', '=', $id)->first();
        if(empty($advertiser)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        # 修改密码并且解封账号
        if(!empty($present['password'])){
            $advertiser->password = bcrypt($present['password']);
            Cache::put($advertiser->username, 0, 60);
        }

        if(!empty($present['alliance_agent_id'])){ $advertiser->alliance_agent_id = trim($present['alliance_agent_id']); }
        if(!empty($present['nickname'])){ $advertiser->nickname = trim($present['nickname']); }
        if(!empty($present['company'])){ $advertiser->company = trim($present['company']); }        
        if(!empty($present['email'])){ $advertiser->email = trim($present['email']); }        
        if(!empty($present['mobile'])){ $advertiser->mobile = trim($present['mobile']); }        
        if(!empty($present['qq'])){ $advertiser->qq = trim($present['qq']); }        
        if(!empty($present['state'])){ $advertiser->state = trim($present['state']); }
        if(!empty($present['agent_id']) || $present['agent_id']=='0'){ $advertiser->agent_id = $present['agent_id']; }
        
        if(!empty($present['busine_id']))
        {
            $user = Users::where('id', '=', trim($present['busine_id']))->first();

            if(empty($user)){
                return response()->json(['message'=>'找不到次商务'], 300);
            }else{
                $advertiser->busine_id = trim($present['busine_id']);
            }
        }
        
        if($advertiser->save())
        {
            return response()->json(['message'=>'操作成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'添加失败'], 300);
        }
    }

    public function getLoginlogs(Request $request)
    {
        self::Admin();

        //网站杜索
        $date = trim($request->input('date'));
        $webmaster_id = trim($request->input('webmaster_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $loginlogs = AdvertiserLoginLog::select('advertiser_login_log.*', 'advertiser.alliance_agent_id')
            ->join('advertiser', 'advertiser.id','=','advertiser_login_log.advertiser_id');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $loginlogs = $loginlogs->where('advertiser.alliance_agent_id', '=', self::$user->alliance_agent_id);
        }
        #商务权限限制
        if(self::$user->department_id==4){
            $loginlogs = $loginlogs->where('advertiser.busine_id', '=', self::$user->id);
        }
        if(!empty($date)){
            $loginlogs = $loginlogs->where('advertiser.created_at', '>=', $date.' 00:00:00')->where('advertiser.created_at', '<=', $date.' 23:59:59');
        }
        if(!empty($webmaster_id)){
            $loginlogs = $loginlogs->where('advertiser.webmaster_id', '=', $webmaster_id);
        }

        $count = $loginlogs->count();
        $loginlogs = $loginlogs->orderBy('advertiser_login_log.id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'loginlogs'=>$loginlogs,
        ];
        
        return response()->json(['data'=>$data], 200);
    }
}
