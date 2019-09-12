<?php
namespace App\Http\Controllers\Admin\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Http\Helpers\CityReader;

use App\Model\WebmasterAds;
use App\Model\EarningDay;
use App\Model\EarningHour;
use App\Model\EarningClick;

class EarningController extends ApiController
{

    public function getEarningDay(Request $request, $webmaster_ad_id)
    {
        self::Admin();

        if(empty($webmaster_ad_id)){
            return response()->json(['message'=>'缺少广告位ID'], 400);
        }

        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $earnings = EarningDay::where('webmaster_ad_id', '=', $webmaster_ad_id);

        $count = $earnings->count();
        $earnings = $earnings->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'earnings'=>$earnings,
        ];

        return response()->json(['data'=>$data], 200);

    }

    public function putEarningDay(Request $request, $id)
    {
        self::Admin();
        if(empty($id)){
            return response()->json(['message'=>'缺少ID'], 400);
        }

        $present = $request->input();

        $earningDay = EarningDay::where('id', '=', $id)->first();
        if(empty($earningDay)){
            return response()->json(['message'=>'找不到数据'], 400);
        }
        //修改数据
        $earningDay->is_extract = $present['is_extract'];

        //受益变动做处理
        $money = intval( $present['money'] - $earningDay->money );  //增加多少钱的误差
        if($money!=0)
        {
            if( $earningDay->state != '1' ){
                return response()->json(['message'=>'结果已经处理，不能操作'], 400);
            }

            //小时数据
            $earningHour = EarningHour::where('webmaster_ad_id', $earningDay->webmaster_ad_id)->where('state', '4')->where('time', 'like', $earningDay->date.'%')->orderBy('id', 'desc')->first();
            if(empty($earningHour))
            {
                return response()->json(['message'=>'没有小时数据，不能修改价格'], 400);
            }

            $earningHour->money +=  $money;
            $earningHour->save();

            $earningDay->money += $money;
        }
        
        if($earningDay->save())
        {
            return response()->json(['message'=>'保存成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'保存失败'], 300);
        }
    }

    public function getEarningHour(Request $request, $webmaster_ad_id)
    {
        self::Admin();

        if(empty($webmaster_ad_id)){
            return response()->json(['message'=>'缺少站长ID'], 400);
        }

        $date = trim($request->input('date'));
        $type = trim($request->input('type'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $earnings = EarningHour::where('webmaster_ad_id', '=', $webmaster_ad_id);

        if(!empty($date))
        {
            $earnings = $earnings->where('time', '>=', $date.' 00:00:00')->where('time', '<=', $date.' 23:59:59');
        }

        $count = $earnings->count();
        $earnings = $earnings->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'earnings'=>$earnings,
        ];

        return response()->json(['data'=>$data], 200);
    }

    public function putEarningHour(Request $request, $id)
    {
        self::Admin();
        if(empty($id)){
            return response()->json(['message'=>'缺少ID'], 400);
        }

        $present = $request->input();

        $earningHour = EarningHour::where('id', '=', $id)->first();
        if(empty($earningHour)){
            return response()->json(['message'=>'找不到数据'], 400);
        }

        if( $earningHour->state != '1' ){
            return response()->json(['message'=>'结果已经处理，不能操作'], 400);
        }

        $earningHour->money = $present['money'];

        if($earningHour->save())
        {
            return response()->json(['message'=>'保存成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'保存失败'], 300);
        }
    }


    public function getEarningHourChart(Request $request, $webmaster_ad_id)
    {
        self::Admin();

   
        if(empty($webmaster_ad_id))
            return response()->json(['message'=>'缺少站长ID'], 400);

        $date = trim($request->input('date'));
        
        $default = ['00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23'];
        
        $result = EarningHour::where('webmaster_ad_id', '=', $webmaster_ad_id)
            ->where('time', '>=', $date.' 00:00:00')
            ->where('time', '<=', $date.' 23:59:59')
            ->orderBy('id', 'asc')->get();

        $today = [];

        foreach($default as $key=>$val)
        {
            $data = ['money'=>0, 'pc_number'=>0, 'pv_number'=>0];

            foreach($result as $k=>$v)
            {
                if(substr($v->time , 11, 2)==$val)
                {
                    $data['money'] += $v->money;
                    $data['pc_number'] += $v->pc_number;
                    $data['pv_number'] += $v->pv_number;
                    $data['created_at'] = $v->created_at;
                }
            }
            
            $today[] = $data;
        }

        
        $date = date("Y-m-d", strtotime("-1 day", strtotime($date)));

        $result = EarningHour::where('webmaster_ad_id', '=', $webmaster_ad_id)
            ->where('time', '>=', $date.' 00:00:00')
            ->where('time', '<=', $date.' 23:59:59')
            ->orderBy('id', 'asc')->get();

        $yesterday = [];

        foreach($default as $key=>$val)
        {
            $data = ['money'=>0, 'pc_number'=>0, 'pv_number'=>0];

            foreach($result as $k=>$v)
            {
                if(substr($v->time , 11, 2)==$val)
                {
                    $data['money'] += $v->money;
                    $data['pc_number'] += $v->pc_number;
                    $data['pv_number'] += $v->pv_number;
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


    public function getEarningClick(Request $request, $webmaster_ad_id)
    {
        self::Admin();

        if(empty($webmaster_ad_id))
            return response()->json(['message'=>'缺少站长ID'], 400);

        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $clicks = EarningClick::where('myads_id', '=', $webmaster_ad_id);

        $count = $clicks->count();
        $clicks = $clicks->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $reader = new CityReader;

        foreach($clicks as $k=>$v)
        {
            if( $v->ip && $v->ip!='unknown' )
            {
                $res = $reader->findMap($v->ip, 'CN');
                $clicks[$k]['region'] = $res['region_name'];
                $clicks[$k]['region'] = $res['region_name'];
                $clicks[$k]['city'] = $res['city_name'];
            }
        }

        $data = [
            'count'=>$count,
            'clicks'=>$clicks,
        ];

        return response()->json(['data'=>$data], 200);
    }
}