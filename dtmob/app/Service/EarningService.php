<?php
namespace App\Service;

use App\Model\EarningDay;
use App\Model\EarningHour;

class EarningService
{
    #加三个搜索
    public function getEarning($webmaster_id="", $webmaster_ad_id="", $position_id="", $service_id="", $user)
    {

        //前天数据
        $earningDay1 = EarningDay::select('earning_day.pv_number', 'earning_day.pc_number', 'earning_day.ip_number', 'earning_day.money', 'earning_day.date')
            ->join('webmaster_ads as ad', 'ad.id', '=', 'earning_day.webmaster_ad_id')
            ->join('webmaster as web', 'web.id', '=', 'ad.webmaster_id');

        //昨天数据
        $earningDay2 = EarningDay::select('earning_day.pv_number', 'earning_day.pc_number', 'earning_day.ip_number', 'earning_day.money', 'earning_day.date')
            ->join('webmaster_ads as ad', 'ad.id', '=', 'earning_day.webmaster_ad_id')
            ->join('webmaster as web', 'web.id', '=', 'ad.webmaster_id');

        //今天数据
        $earningHour = EarningHour::select('earning_hour.pv_number', 'earning_hour.pc_number', 'earning_hour.ip_number', 'earning_hour.money', 'earning_hour.time')
            ->join('webmaster_ads as ad', 'ad.id', '=', 'earning_hour.webmaster_ad_id')
            ->join('webmaster as web', 'web.id', '=', 'ad.webmaster_id');

        
        #广告搜索
        if(!empty($webmaster_id))
        {
            $earningDay1 = $earningDay1->where('web.id', '=', $webmaster_id);
            $earningDay2 = $earningDay2->where('web.id', '=', $webmaster_id);
            $earningHour = $earningHour->where('web.id', '=', $webmaster_id);
        }
        if(!empty($webmaster_ad_id))
        {
            $earningDay1 = $earningDay1->where('ad.id', '=', $webmaster_ad_id);
            $earningDay2 = $earningDay2->where('ad.id', '=', $webmaster_ad_id);
            $earningHour = $earningHour->where('ad.id', '=', $webmaster_ad_id);
        }
        if(!empty($position_id))
        {
            $earningDay1 = $earningDay1->where('ad.position_id', '=', $webmaster_id);
            $earningDay2 = $earningDay2->where('ad.position_id', '=', $webmaster_id);
            $earningHour = $earningHour->where('ad.position_id', '=', $webmaster_id);
        }
        if(!empty($service_id))
        {
            $earningDay1 = $earningDay1->where('web.service_id', '=', $service_id);
            $earningDay2 = $earningDay2->where('web.service_id', '=', $service_id);
            $earningHour = $earningHour->where('web.service_id', '=', $service_id);
        }

        #联盟权限限制
        if($user->alliance_agent_id!=config('other.alliance_agent_id')){
            $earningDay1 = $earningDay1->where('web.alliance_agent_id', '=', $user->alliance_agent_id);
            $earningDay2 = $earningDay2->where('web.alliance_agent_id', '=', $user->alliance_agent_id);
            $earningHour = $earningHour->where('web.alliance_agent_id', '=', $user->alliance_agent_id);
        }
        ##客服限制权限
        if($user->department_id==3){
            $earningDay1 = $earningDay1->where('web.service_id', '=', $user->id);
            $earningDay2 = $earningDay2->where('web.service_id', '=', $user->id);
            $earningHour = $earningHour->where('web.service_id', '=', $user->id);
        }


        $array = ['pv_number'=>0, 'pc_number'=>0, 'ip_number'=>0, 'money'=>0];
        $data = [array_merge(['name'=>'前天'],$array), array_merge(['name'=>'昨天'],$array), array_merge(['name'=>'今天'],$array)];

        //两天前数据
        $result = $earningDay1->where('earning_day.date', '=', date('Y-m-d', strtotime("-2 day")))->get()->toArray();
        foreach($result as $key=>$val)
        {
            $data[0]['pv_number'] += $val['pv_number'];
            $data[0]['pc_number'] += $val['pc_number'];
            $data[0]['ip_number'] += $val['ip_number'];
            $data[0]['money'] += $val['money'];
        }


        //昨天数据
        $result = $earningDay2->where('earning_day.date', '=', date('Y-m-d', strtotime("-1 day")))->get()->toArray();
        foreach($result as $key=>$val)
        {
            $data[1]['pv_number'] += $val['pv_number'];
            $data[1]['pc_number'] += $val['pc_number'];
            $data[1]['ip_number'] += $val['ip_number'];
            $data[1]['money'] += $val['money'];
        }
        
        //今天数据
        $result = $earningHour->where('earning_hour.time', 'like', date('Y-m-d', time()).'%')->get()->toArray();
        foreach($result as $key=>$val)
        {
            $data[2]['pv_number'] += $val['pv_number'];
            $data[2]['pc_number'] += $val['pc_number'];
            $data[2]['ip_number'] += $val['ip_number'];
            $data[2]['money'] += $val['money'];
        }

        return $data;
    }
}