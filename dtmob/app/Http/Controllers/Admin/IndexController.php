<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\AdvertiserLoginLog;
use App\Model\Advertiser;
use App\Model\Users;
use App\Model\EarningHour;
use Hash;

class IndexController extends ApiController
{

    public function getWebmasterEarningHour(Request $request)
    {
        self::Admin();
        
        $present = $request->input();

        if(empty($present['date']))
        {
            $date = date('Y-m-d');
        }
        
        $default = ['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'];
        
        $result = EarningHour::select('earning_hour.*')
            ->join('webmaster_ads', 'webmaster_ads.id','=','earning_hour.webmaster_ad_id')
            ->join('webmaster', 'webmaster.id','=','webmaster_ads.webmaster_id')
            ->where('earning_hour.time', '>=', $date.' 00:00:00')
            ->where('earning_hour.time', '<=', $date.' 23:59:59');
            
        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $result = $result->where('webmaster.alliance_agent_id', '=', self::$user->alliance_agent_id);
        }
        ##客服限制权限
        if(self::$user->department_id==3){
            $result = $result->where('webmaster.service_id', '=', self::$user->id);
        }
        $result = $result->orderBy('earning_hour.id', 'asc')->get();


        $today = [];

        foreach($default as $key=>$val)
        {
            $data = ['money'=>0, 'pc_number'=>0, 'pv_number'=>0, 'ip_number'=>0];

            foreach($result as $k=>$v)
            {
                if(substr($v->time , 11, 2)==$val)
                {
                    $data['money'] += $v->money;
                    $data['pc_number'] += $v->pc_number;
                    $data['pv_number'] += $v->pv_number;
                    $data['ip_number'] += $v->ip_number;
                    $data['time'] = $v->time;
                }
            }

            $today[] = $data;
        }

        
        $date = date("Y-m-d", strtotime("-1 day", strtotime($date)));

        $result = EarningHour::select('earning_hour.*')
            ->join('webmaster_ads', 'webmaster_ads.id','=','earning_hour.webmaster_ad_id')
            ->join('webmaster', 'webmaster.id','=','webmaster_ads.webmaster_id')
            ->where('earning_hour.time', '>=', $date.' 00:00:00')
            ->where('earning_hour.time', '<=', $date.' 23:59:59');
            
        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $result = $result->where('webmaster.alliance_agent_id', '=', self::$user->alliance_agent_id);
        }
        ##客服限制权限
        if(self::$user->department_id==3){
            $result = $result->where('webmaster.service_id', '=', self::$user->id);
        }
        $result = $result->orderBy('earning_hour.id', 'asc')->get();
        
        $yesterday = [];

        foreach($default as $key=>$val)
        {
            $data = ['money'=>0, 'pc_number'=>0, 'pv_number'=>0, 'ip_number'=>0];

            foreach($result as $k=>$v)
            {
                if(substr($v->time , 11, 2)==$val)
                {
                    $data['money'] += $v->money;
                    $data['pc_number'] += $v->pc_number;
                    $data['pv_number'] += $v->pv_number;
                    $data['ip_number'] += $v->ip_number;
                    $data['time'] = $v->time;
                }
            }

            $yesterday[] = $data;
        }

        $data = [
            'default'=>$default,
            'today'=>$today,
            'yesterday'=>$yesterday,
        ];

        return response()->json(['data'=>$data], 200);
    }
}
