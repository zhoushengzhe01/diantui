<?php
namespace App\Service;

use App\Model\AdvertiserExpendDay;
use App\Model\AdvertiserExpendHour;

class AdvertiserExpendService
{

    public function getEarning($advertiser_id="", $advertiser_ad_id="", $position_id="", $busine_id="", $user)
    {

        //前天数据
        $earningDay1 = AdvertiserExpendDay::select('advertiser_expend_day.pv_number', 'advertiser_expend_day.pc_number', 'advertiser_expend_day.ip_number', 'advertiser_expend_day.money', 'advertiser_expend_day.date')
            ->join('advertiser_ads as ad', 'ad.id', '=', 'advertiser_expend_day.advertiser_ad_id')
            ->join('advertiser as adv', 'adv.id', '=', 'ad.advertiser_id');

        //昨天数据
        $earningDay2 = AdvertiserExpendDay::select('advertiser_expend_day.pv_number', 'advertiser_expend_day.pc_number', 'advertiser_expend_day.ip_number', 'advertiser_expend_day.money', 'advertiser_expend_day.date')
            ->join('advertiser_ads as ad', 'ad.id', '=', 'advertiser_expend_day.advertiser_ad_id')
            ->join('advertiser as adv', 'adv.id', '=', 'ad.advertiser_id');

        //今天数据
        $earningHour = AdvertiserExpendHour::select('advertiser_expend_hour.pv_number', 'advertiser_expend_hour.pc_number', 'advertiser_expend_hour.ip_number', 'advertiser_expend_hour.money', 'advertiser_expend_hour.time')
            ->join('advertiser_ads as ad', 'ad.id', '=', 'advertiser_expend_hour.advertiser_ad_id')
            ->join('advertiser as adv', 'adv.id', '=', 'ad.advertiser_id');


        if($advertiser_id!='')
        {
            $earningDay1 = $earningDay1->where('adv.id', '=', $advertiser_id);
            $earningDay2 = $earningDay2->where('adv.id', '=', $advertiser_id);
            $earningHour = $earningHour->where('adv.id', '=', $advertiser_id);
        }
        if($advertiser_ad_id!='')
        {
            $earningDay1 = $earningDay1->where('ad.id', '=', $advertiser_ad_id);
            $earningDay2 = $earningDay2->where('ad.id', '=', $advertiser_ad_id);
            $earningHour = $earningHour->where('ad.id', '=', $advertiser_ad_id);
        }
        if($position_id!='')
        {
            $earningDay1 = $earningDay1->where('ad.adstype_id', '=', $position_id);
            $earningDay2 = $earningDay2->where('ad.adstype_id', '=', $position_id);
            $earningHour = $earningHour->where('ad.adstype_id', '=', $position_id);
        }
        if($busine_id!='')
        {
            $earningDay1 = $earningDay1->where('adv.busine_id', '=', $busine_id);
            $earningDay2 = $earningDay2->where('adv.busine_id', '=', $busine_id);
            $earningHour = $earningHour->where('adv.busine_id', '=', $busine_id);
        }
        #联盟权限限制
        if($user->alliance_agent_id!=config('other.alliance_agent_id')){
            $earningDay1 = $earningDay1->where('adv.alliance_agent_id', '=', $user->alliance_agent_id);
            $earningDay2 = $earningDay2->where('adv.alliance_agent_id', '=', $user->alliance_agent_id);
            $earningHour = $earningHour->where('adv.alliance_agent_id', '=', $user->alliance_agent_id);
        }
        ##商务限制权限
        if($user->department_id==4){
            $earningDay1 = $earningDay1->where('adv.busine_id', '=', $user->id);
            $earningDay2 = $earningDay2->where('adv.busine_id', '=', $user->id);
            $earningHour = $earningHour->where('adv.busine_id', '=', $user->id);
        }

        $array = ['pv_number'=>0, 'pc_number'=>0, 'ip_number'=>0, 'money'=>0];
        $data = [array_merge(['name'=>'前天'],$array), array_merge(['name'=>'昨天'],$array), array_merge(['name'=>'今天'],$array)];

        //两天前数据
        $result = $earningDay1->where('advertiser_expend_day.date', '=', date('Y-m-d', strtotime("-2 day")))->get()->toArray();
        foreach($result as $key=>$val)
        {
            $data[0]['pv_number'] += $val['pv_number'];
            $data[0]['pc_number'] += $val['pc_number'];
            $data[0]['ip_number'] += $val['ip_number'];
            $data[0]['money'] += $val['money'];
        }

        //昨天数据
        $result = $earningDay2->where('advertiser_expend_day.date', '=', date('Y-m-d', strtotime("-1 day")))->get()->toArray();
        foreach($result as $key=>$val)
        {
            $data[1]['pv_number'] += $val['pv_number'];
            $data[1]['pc_number'] += $val['pc_number'];
            $data[1]['ip_number'] += $val['ip_number'];
            $data[1]['money'] += $val['money'];
        }


        //今天数据
        $result = $earningHour->where('advertiser_expend_hour.time', 'like', date('Y-m-d', time()).'%')->get()->toArray();
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