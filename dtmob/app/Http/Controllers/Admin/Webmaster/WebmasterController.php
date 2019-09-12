<?php
namespace App\Http\Controllers\Admin\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Http\Helpers\CityReader;

use App\Model\Webmaster;
use App\Model\WebmasterLog;
use App\Model\WebmasterLoginLog;
use Hash;

class WebmasterController extends ApiController
{
    public function getWebmasters(Request $request)
    {
        self::Admin();
        
        //网站搜索
        $webmaster_id = trim($request->input('webmaster_id'));
        $username = trim($request->input('username'));
        $nickname = trim($request->input('nickname'));
        $mobile = trim($request->input('mobile'));
        $pid = trim($request->input('pid'));
        $qq = trim($request->input('qq'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $webmasters = Webmaster::select('*');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $webmasters = $webmasters->where('alliance_agent_id', '=', self::$user->alliance_agent_id);
        }
        ##客服限制权限
        if(self::$user->department_id==3){
            $webmasters = $webmasters->whereIn('service_id', [self::$user->id,0]);
        }

        if(!empty($webmaster_id)){
            $webmasters = $webmasters->where('id', '=', $webmaster_id);
        }
        if(!empty($username)){
            $webmasters = $webmasters->where('username', 'like', '%'.$username.'%');
        }
        if(!empty($nickname)){
            $webmasters = $webmasters->where('nickname', 'like', '%'.$nickname.'%');
        }
        if(!empty($mobile)){
            $webmasters = $webmasters->where('mobile', 'like', '%'.$mobile.'%');
        }
        if(!empty($pid)){
            $webmasters = $webmasters->where('pid', '=', $pid);
        }
        if(!empty($qq)){
            $webmasters = $webmasters->where('qq', 'like', '%'.$qq.'%');
        }
        
        $count = $webmasters->count();
        $webmasters = $webmasters->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'webmasters'=>$webmasters,
        ];

        return response()->json(['data'=>$data], 200);
    }

    public function getWebmaster(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }
        $webmaster = Webmaster::where('id', '=', $id)->first();
        if(empty($webmaster)){
            return response()->json(['message'=>'未找到数据'], 300);
        }
        $webmaster->allow_alliance = json_decode($webmaster->allow_alliance, true);
        if(gettype($webmaster->allow_alliance)!='array'){
            $webmaster->allow_alliance = [];
        }
        $webmaster->disabled_alliance = json_decode($webmaster->disabled_alliance, true);
        if(gettype($webmaster->disabled_alliance)!='array'){
            $webmaster->disabled_alliance = [];
        }

        $webmaster->pid_name = '';
        if($webmaster->pid){
            $result = Webmaster::where('id', '=', $webmaster->pid)->first();
            if(!empty($result)){
                $webmaster->pid_name = $result->username;
            }
        }

        if(!empty($webmaster->allow_ad_type)){
            $webmaster->allow_ad_type = json_decode($webmaster->allow_ad_type, true);    
        }
        else
        {
            $webmaster->allow_ad_type = [];
        }
        
        return response()->json(['data'=>['webmaster'=>$webmaster]], 200);
    }

    public function putWebmaster(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }
        $webmaster = Webmaster::where('id', '=', $id)->first();
        if(empty($webmaster)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        $present = $request->input();
        if(!empty($present['service_id'])){ $webmaster->service_id = $present['service_id']; }
        if(!empty($present['alliance_agent_id'])){ $webmaster->alliance_agent_id = $present['alliance_agent_id']; }
        if(!empty($present['username'])){ $webmaster->username = $present['username']; }
        if(!empty($present['nickname'])){ $webmaster->nickname = $present['nickname']; }
        if(!empty($present['password'])){ $webmaster->password = bcrypt($present['password']);; }
        if(!empty($present['allow_alliance'])){ $webmaster->allow_alliance = json_encode($present['allow_alliance'], true); }
        if(!empty($present['disabled_alliance'])){ $webmaster->disabled_alliance = json_encode($present['disabled_alliance'], true); }
        if(!empty($present['withdrawals_state'])){ $webmaster->withdrawals_state = $present['withdrawals_state']; }
        if(!empty($present['withdrawals_type'])){ $webmaster->withdrawals_type = $present['withdrawals_type']; }
        if(!empty($present['money'])){ $webmaster->money = $present['money']; }
        if(!empty($present['email'])){ $webmaster->email = $present['email']; }
        if(!empty($present['mobile'])){ $webmaster->mobile = $present['mobile']; }
        if(!empty($present['qq'])){ $webmaster->qq = $present['qq']; }
        if(!empty($present['is_top_bottom']) || $present['is_top_bottom']=='0'){ $webmaster->is_top_bottom = $present['is_top_bottom']; }
        if(!empty($present['is_left_right']) || $present['is_left_right']=='0'){ $webmaster->is_left_right = $present['is_left_right']; }
        if(!empty($present['state'])){ $webmaster->state = $present['state']; }
        if(!empty($present['is_limit_domain'])){ $webmaster->is_limit_domain = $present['is_limit_domain']; }
        if(!empty($present['website'])){ $webmaster->website = $present['website']; }
        if(!empty($present['pid']) || $present['pid']=='0'){ $webmaster->pid = empty($present['pid'])?0:$present['pid']; }
        if(!empty($present['return_point'])){ $webmaster->return_point = $present['return_point']; }

        $webmaster->allow_ad_type = json_encode($present['allow_ad_type'], true);
        
        if($webmaster->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }

    }

    public function getWebmasterLoginlogs(Request $request)
    {
        self::Admin();

        //网站搜索
        $webmaster_id = trim($request->input('webmaster_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $logs = WebmasterLoginLog::select('webmaster_login_log.*', 'webmaster.alliance_agent_id')
            ->join('webmaster', 'webmaster.id','=','webmaster_login_log.webmaster_id');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $logs = $logs->where('webmaster.alliance_agent_id', '=', self::$user->alliance_agent_id);
        }
        ##客服限制权限
        if(self::$user->department_id==3){
            $logs = $logs->where('webmaster.service_id', '=', self::$user->id);
        }
        
        if(!empty($webmaster_id)){
            $logs = $logs->where('webmaster_login_log.webmaster_id', '=', $webmaster_id);
        }

        $count = $logs->count();
        $logs = $logs->orderBy('webmaster_login_log.id', 'desc')->offset($offset)->limit($limit)->get();

     
        $reader = new CityReader;

        foreach($logs as $k=>$v)
        {
            if( $v->ip && $v->ip!='unknown' )
            {
                $res = $reader->findMap($v->ip, 'CN');
                $logs[$k]['region'] = $res['region_name'];
                $logs[$k]['city'] = $res['city_name'];
            }
        }

        $data = [
            'count'=>$count,
            'logs'=>$logs,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
