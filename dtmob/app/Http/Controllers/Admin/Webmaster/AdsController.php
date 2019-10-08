<?php
namespace App\Http\Controllers\Admin\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\WebmasterAds;
use App\Model\EarningDay;
use App\Model\EarningHour;
use App\Service\EarningService;
use App\Model\WebmasterAdPriceLogs;

class AdsController extends ApiController
{

    public function getAds(Request $request)
    {
        self::Admin();
        
        //网站搜索
        $id = trim($request->input('id'));
        $webmaster_id = trim($request->input('webmaster_id'));
        $service_id = trim($request->input('service_id'));
        $position_id = trim($request->input('position_id'));
        $earning_day = intval($request->input('earning_day'));
        $grade = intval($request->input('grade'));
        $orderby = trim($request->input('orderby'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        $username = trim($request->input('username'));
        $flow_pool_id = trim($request->input('flow_pool_id'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $ads = WebmasterAds::select('webmaster_ads.*', 'webmaster.username', 'webmaster.flow_pool_id', 'webmaster.grade', 'webmaster.alliance_agent_id', 'webmaster.service_id')
            ->join('webmaster', 'webmaster.id','=','webmaster_ads.webmaster_id');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $ads = $ads->where('webmaster.alliance_agent_id', '=', self::$user->alliance_agent_id);
        }
        ##客服限制权限
        if(self::$user->department_id==3){
            $ads = $ads->where('webmaster.service_id', '=', self::$user->id);
        }
        if(!empty($id)){
            $ads = $ads->where('webmaster_ads.id', '=', $id);
        }
        if(!empty($webmaster_id)){
            $ads = $ads->where('webmaster_ads.webmaster_id', '=', $webmaster_id);
        }
        if(!empty($username)) {
            $ads = $ads->where('webmaster.username', 'like', '%'.$username.'%');
        }
        if(!empty($flow_pool_id)) {
            $ads = $ads->where('webmaster.flow_pool_id', $flow_pool_id);
        }
        if(!empty($service_id)){
            $ads = $ads->where('webmaster.service_id', '=', $service_id);
        }
        if(!empty($position_id)){
            $ads = $ads->where('webmaster_ads.position_id', '=', $position_id);
        }
        if(!empty($earning_day)){
            $ads = $ads->where('webmaster_ads.earning_day', '>', $earning_day);
        }
        if(!empty($grade)){
            $ads = $ads->where('webmaster.grade', '=', $grade);
        }
        if(!empty($orderby)){
            $orderby = explode(':',$orderby);
            $ads = $ads->orderBy('webmaster_ads.'.$orderby[0], $orderby[1]);
        }else{
            $ads = $ads->orderBy('webmaster_ads.earning_day', 'desc');
        }

        $count = $ads->count();
        $ads = $ads->offset($offset)->limit($limit)->get(['webmaster_ads.*','webmaster.username']);
        
        ##查找收益
        foreach($ads as $key=>$val)
        {
            $ads[$key]['day'] = (new EarningService)->getEarning('', $val->id, '', '', self::$user, $username, $flow_pool_id);
        }

        $all_earning = (new EarningService)->getEarning($webmaster_id, $id, $position_id, '', self::$user, $username, $flow_pool_id);
        
        $data = [
            'all_earning'=>$all_earning,
            'count'=>$count,
            'ads'=>$ads,
        ];
        
        return response()->json(['data'=>$data], 200);
    }

    public function getAd(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }
        $webmasterad = WebmasterAds::where('id', '=', $id)->first();
        if(empty($webmasterad)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        //他返回
        if(!empty($webmasterad->other_return)){
            $webmasterad->other_return = json_decode($webmasterad->other_return, true);
        }else{
            $webmasterad->other_return = [];
        }

        //自返回
        if(!empty($webmasterad->own_return)){
            $webmasterad->own_return = json_decode($webmasterad->own_return, true);
        }else{
            $webmasterad->own_return = [];
        }

        //直返回
        if(!empty($webmasterad->click_return)){
            $webmasterad->click_return = json_decode($webmasterad->click_return, true);
        }else{
            $webmasterad->click_return = [];
        }

        //分类
        if(!empty($webmasterad->disabled_ad_category)){
            $webmasterad->disabled_ad_category = json_decode($webmasterad->disabled_ad_category, true);
        }else{
            $webmasterad->disabled_ad_category = [];
        }

        return response()->json(['data'=>['webmasterad'=>$webmasterad]], 200);
    }

    public function putAd(Request $request, $id)
    {
        self::Admin();
        
        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $ad = WebmasterAds::where('id', '=', $id)->first();
        if(empty($ad)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        $present = $request->input();

        #判断数值是否更改
        $isEdit = false;
        if($present['is_auto_price'] != $ad->is_auto_price || $present['target_price'] != $ad->target_price || $present['in_advertiser_price'] != $ad->in_advertiser_price || $present['out_advertiser_price'] != $ad->out_advertiser_price || $present['hid_height_chance'] != $ad->hid_height_chance) {
            $isEdit = true;
        }

        #媒介权限数值范围拦截
        $false_close = intval($present['false_close']);
        $hid_height = intval($present['hid_height']);
        $hid_height_chance = intval($present['hid_height_chance']);

        if(self::$user->department_id==3)
        {
            #假关闭
            if( ($false_close < 50 || $false_close>80) && $false_close!=$ad->false_close )
            {
                return response()->json(['message'=>'假关闭值请在50-80调整'], 300);
            }
            #暗层计费
            if( ($hid_height_chance < 10 || $hid_height_chance>80) && $hid_height_chance!=$ad->hid_height_chance)
            {
                return response()->json(['message'=>'暗层计费请在10-80调整'], 300);
            }
            #暗层高度
            if($hid_height!=$ad->hid_height)
            {
                if( $ad->position=='11' && ($hid_height < 100 || $hid_height>200) )
                {
                    return response()->json(['message'=>'横幅 暗层值请在100-200之间调整'], 300);
                }
                if($ad->position=='13' && ($hid_height < 20 || $hid_height>50))
                {
                    return response()->json(['message'=>'小图标 暗层值请在20-50之间调整'], 300);
                }
                if($ad->position=='12' && ($hid_height < 10 || $hid_height>50))
                {
                    return response()->json(['message'=>'信息流 暗层值请在10-50之间调整'], 300);
                }
            }
        }


        if(!empty($present['webmaster_id'])){ $ad->webmaster_id = $present['webmaster_id']; }
        if(!empty($present['name'])){ $ad->name = $present['name']; }
        if(!empty($present['position_id'])){ $ad->position_id = $present['position_id']; }
        if(!empty($present['position_name'])){ $ad->position_name = $present['position_name']; }
        if(!empty($present['position'])){ $ad->position = $present['position']; }
        if(!empty($present['price'])){ $ad->price = $present['price']; }
        if(!empty($present['billing'])){ $ad->billing = $present['billing']; }
        if(!empty($present['in_advertiser_price'])){ $ad->in_advertiser_price = $present['in_advertiser_price']; }
        if(!empty($present['out_advertiser_price'])){ $ad->out_advertiser_price = $present['out_advertiser_price']; }
        if(!empty($present['out_alliance_price'])){ $ad->out_alliance_price = $present['out_alliance_price']; }
        if(!empty($present['ad_ratio']) || $present['ad_ratio']==0){ $ad->ad_ratio = $present['ad_ratio']; }
        if(!empty($present['is_stat']) || $present['is_stat']==0){ $ad->is_stat = $present['is_stat']; }
        if(!empty($present['ad_size'])){ $ad->ad_size = $present['ad_size']; }
        if(!empty($present['icons_top'])){ $ad->icons_top = $present['icons_top']; }

        $ad->false_close = $false_close;
        $ad->hid_height = $hid_height;
        $ad->hid_height_chance = $hid_height_chance;


        $ad->is_disabled_advertiser_ad = $present['is_disabled_advertiser_ad'];
        $ad->disabled_advertiser_ad = empty($present['disabled_advertiser_ad']) ? '' : trim($present['disabled_advertiser_ad']);
        $ad->disabled_ad_category = json_encode($present['disabled_ad_category'], true);

        $ad->bot_hid_height = $present['bot_hid_height'];
        $ad->top_hid_height = $present['top_hid_height'];
        
        $ad->hid_height_disabled_region = empty($present['hid_height_disabled_region']) ? '' : trim($present['hid_height_disabled_region']);

        $ad->zhikouling = $present['zhikouling'];
        $ad->zhikouling_disabled_region = empty($present['zhikouling_disabled_region']) ? '' : trim($present['zhikouling_disabled_region']);

        $ad->compel_click = $present['compel_click'];
        $ad->compel_chance = $present['compel_chance'];
        $ad->compel_interval = $present['compel_interval'];
        $ad->click_disabled_region = empty($present['click_disabled_region']) ? '' : trim($present['click_disabled_region']);

        $ad->compel_skip = $present['compel_skip'];
        $ad->skip_disabled_region = empty($present['skip_disabled_region']) ? '' : trim($present['skip_disabled_region']);

        $ad->js_effects = $present['js_effects'];
        $ad->js_effects_disabled_region = empty($present['js_effects_disabled_region']) ? '' : trim($present['js_effects_disabled_region']);
        
        if(!empty($present['state'])){ $ad->state = $present['state']; }
        if(!empty($present['is_top'])){ $ad->is_top = $present['is_top']; }
        $ad->is_jiexi = $present['is_jiexi'];
        $ad->statis_code = empty($present['statis_code']) ? '' : $present['statis_code'];
        $ad->statis_code_ratio = empty($present['statis_code_ratio']) ? 0 : $present['statis_code_ratio'];
        $ad->style_type = empty($present['style_type']) ? '' : $present['style_type'];
        $ad->style = empty($present['style']) ? '' : $present['style'];
        
        $ad->is_ad_disabled = empty($present['is_ad_disabled']) ? 0 : $present['is_ad_disabled'];
        $ad->ad_disabled_region = empty($present['ad_disabled_region']) ? '' : $present['ad_disabled_region'];

        //返回模块
        $ad->other_return = json_encode($present['other_return'], true);
        $ad->click_return = json_encode($present['click_return'], true);
        $ad->own_return = json_encode($present['own_return'], true);
        $ad->return_chance = $present['return_chance'];

        $ad->is_auto_price = $present['is_auto_price'];
        $ad->target_price = intval($present['target_price']);
        $ad->init_price = intval($present['init_price']);
        $ad->return_skip = intval($present['return_skip']);
        $ad->return_num = intval($present['return_num']);

        #记录日志操作
        $logBool = true;
        if($isEdit) {
            $priceLogs = new WebmasterAdPriceLogs;       
            $priceLogs->is_auto_price = $present['is_auto_price'];
            $priceLogs->target_price = $present['target_price'];
            $priceLogs->in_advertiser_price = $present['in_advertiser_price'];
            $priceLogs->out_advertiser_price = $present['out_advertiser_price'];
            $priceLogs->hid_height_chance = $present['hid_height_chance'];
            $priceLogs->ad_id = $id;
            $priceLogs->username = self::$user->username;
            $priceLogs->ip = $request->getClientIp();
            $logBool = $priceLogs->save();
        }
        if($ad->save() && $logBool)
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }
}