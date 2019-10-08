<?php
namespace App\Http\Controllers\Admin\Advertiser;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\AdsPosition;
use App\Model\AdvertiserAds;
use App\Service\AdvertiserExpendService;
use App\Model\Flowpool;

use Hash;
use Excel;

class AdController extends ApiController
{

    public function getAds(Request $request)
    {
        self::Admin();

        //网站杜索
        $advertiser_id = trim($request->input('advertiser_id'));
        $adstype_id = trim($request->input('adstype_id'));
        $busine_id = trim($request->input('busine_id'));
        $position_id = trim($request->input('position_id'));
        $is_wechat = trim($request->input('is_wechat'));
        $client = trim($request->input('client'));
        $state = trim($request->input('state'));
        $id = trim($request->input('id'));
        $username = trim($request->input('username'));
        $flowpool = trim($request->input('flowpool'));
        

        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }
        
        $ads = AdvertiserAds::select('advertiser_ads.*', 'adv.username', 'adv.alliance_agent_id', 'adv.busine_id')
            ->join('advertiser as adv', 'adv.id','=','advertiser_ads.advertiser_id');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $ads = $ads->where('adv.alliance_agent_id', '=', self::$user->alliance_agent_id);
        }
        #商务权限限制
        if(self::$user->department_id==4)
        {
            $ads = $ads->where('adv.busine_id', '=', self::$user->id);
        }
        if(!empty($advertiser_id))
        {
            $ads = $ads->where('advertiser_ads.advertiser_id', '=', $advertiser_id);
        }
        if(!empty($adstype_id))
        {
            $ads = $ads->where('advertiser_ads.adstype_id', '=', $adstype_id);
        }
        if(!empty($busine_id))
        {
            $ads = $ads->where('adv.busine_id', '=', $busine_id);
        }
        if(!empty($is_wechat) || $is_wechat=='0')
        {
            $ads = $ads->where('advertiser_ads.is_wechat', '=', $is_wechat);
        }
        if(!empty($client))
        {
            $ads = $ads->whereIn('advertiser_ads.client', ['0', $client]);
        }
        
        if(!empty($state))
        {
            $ads = $ads->where('advertiser_ads.state', '=', $state);
        }
        if(!empty($id))
        {
            $ads = $ads->where('advertiser_ads.id', '=', $id);
        }
        if(!empty($username)) {
            $ads = $ads->where('adv.username', 'like', '%'.$username.'%');
        }
        if(!empty($flowpool)) {
            $ads = $ads->where('advertiser_ads.flowpool', 'like', '%'.$flowpool.'%');
        }

        $count = $ads->count();
        $ads = $ads->orderBy('advertiser_ads.expend_day', 'desc')->offset($offset)->limit($limit)->get();

        #查找收益
        foreach($ads as $key=>$val)
        {
            $ads[$key]['day'] = (new AdvertiserExpendService)->getEarning(0, $val->id, 0, 0, self::$user, $username, $flowpool);
            
            $ads[$key]['flowpool'] = json_decode($val->flowpool, true);
            if(gettype($ads[$key]['flowpool'])!='array'){
                $ads[$key]['flowpool'] = [];
            }
        }

        #查找总收益
        $all_earning = (new AdvertiserExpendService)->getEarning($advertiser_id, $id, $adstype_id, 0, self::$user, $username, $flowpool);

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

        $ad = AdvertiserAds::where('id', '=', $id)->first();
        
        if(empty($ad)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        $ad->flowpool = json_decode($ad->flowpool, true);
        if(gettype($ad->flowpool)!='array'){
            $ad->flowpool = [];
        }

        if(!empty($ad->hour_weight)){
            $ad->hour_weight = json_decode($ad->hour_weight, true);
        }else{
            $ad->hour_weight = [];
        }

        if(!empty($ad->webmasters))
            $ad->webmasters = json_decode($ad->webmasters, true);
        else
            $ad->webmasters = [];

        if(!empty($ad->categorys))
            $ad->categorys = json_decode($ad->categorys, true);
        else
            $ad->categorys = [];
        
        if(!empty($ad->hours))
            $ad->hours = json_decode($ad->hours, true);
        else
            $ad->hours = [];

        
        $path = 'images/'.$id.'.png';
        if(file_exists($path))
        {
            $ad->image = '/'.$path;
        }
        else
        {
            $ad->image = '';
        }

        return response()->json(['data'=>['ad'=>$ad]], 200);
    }

    public function putAd(Request $request, $id)
    {
        self::Admin();
        
        if(empty($id))
        {
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $present = $request->input();

        $ad = AdvertiserAds::where('id', '=', $id)->first();
        
        if(!empty($present['title'])){ $ad->title = trim($present['title']); }
        if(!empty($present['link'])){ $ad->link = trim($present['link']); }
        if(!empty($present['advertiser_id'])){ $ad->advertiser_id = trim($present['advertiser_id']); }
        if(!empty($present['adstype_id'])){ $ad->adstype_id = trim($present['adstype_id']); }
        if(!empty($present['package_id'])){ $ad->package_id = trim($present['package_id']); }
        if(!empty($present['price_type'])){ $ad->price_type = trim($present['price_type']); }
        if(!empty($present['show_price'])){ $ad->show_price = trim($present['show_price']); }
        if(!empty($present['out_price'])){ $ad->out_price = trim($present['out_price']); }
        if(!empty($present['in_price'])){ $ad->in_price = trim($present['in_price']); }
        if(!empty($present['weight'])){ $ad->weight = trim($present['weight']); }
        if(!empty($present['client']) || $present['client']=='0'){ $ad->client = trim($present['client']); }
        if(!empty($present['flowpool'])){ $ad->flowpool = json_encode($present['flowpool'], true); }

        $ad->put_type = $present['put_type'];
        $ad->is_put_webmaster = $present['is_put_webmaster'];
        $ad->put_webmasters = empty($present['put_webmasters']) ? '' : trim($present['put_webmasters']);
        $ad->is_disabled_webmaster = $present['is_disabled_webmaster'];
        $ad->disabled_webmasters = empty($present['disabled_webmasters']) ? '' : trim($present['disabled_webmasters']);
        $ad->category_id = $present['category_id'];
        
        if(!empty($present['is_wechat_out_skip']) || $present['is_wechat_out_skip']=='0'){ $ad->is_wechat_out_skip = trim($present['is_wechat_out_skip']); }
        if(!empty($present['is_wechat_cover']) || $present['is_wechat_cover']=='0'){ $ad->is_wechat_cover = trim($present['is_wechat_cover']); }
        if(!empty($present['is_wechat']) || $present['is_wechat']=='0'){ $ad->is_wechat = trim($present['is_wechat']); }
        if(!empty($present['is_hour_weight']) || $present['is_hour_weight']=='0'){ $ad->is_hour_weight = trim($present['is_hour_weight']); }
        if(!empty($present['is_put_hour']) || $present['is_put_hour']=='0'){ $ad->is_put_hour = trim($present['is_put_hour']); }
        if(!empty($present['is_put_category']) || $present['is_put_category']=='0'){ $ad->is_put_category = trim($present['is_put_category']); }
        if(!empty($present['is_put_return_ad']) || $present['is_put_return_ad']=='0'){ $ad->is_put_return_ad = $present['is_put_return_ad']; }
        if(!empty($present['state']) || $present['state']=='0'){ $ad->state = trim($present['state']); }
        
        
        if($present['is_hour_weight']=='1')
        {
            if(count($present['hour_weight'])<12){
                return response()->json(['message'=>'至少设置12个小时的权重'], 300);
            }else{
                $ad->hour_weight = json_encode($present['hour_weight'], true);
            }
        }

        if($present['is_put_category']=='1')
        {
            if(count($present['categorys'])<3)
            {
                return response()->json(['message'=>'至少选择三个投放分类'], 300);
            }else
            {
                $ad->categorys = json_encode($present['categorys'], true);
            }
        }

        if($present['is_put_hour']=='1')
        {
            if(count($present['hours'])<12)
            {
                return response()->json(['message'=>'至少选择投12个小时时段'], 300);
            }else
            {
                $ad->hours = json_encode($present['hours'], true);
            }
        }

        if(!empty($present['date']) && count($present['date'])==2)
        {
            $ad->put_date_start = $present['date'][0];
            $ad->put_date_stop = $present['date'][1];
        }
        else
        {
            $ad->put_date_start = '';
            $ad->put_date_stop = '';
        }
        
        if($ad->save())
        {
            return response()->json(['message'=>'添加成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'添加失败'], 300);
        }
    }

    public function postAd(Request $request)
    {
        self::Admin();


        $present = $request->input();

        if(empty($present['title']))
            return response()->json(['message'=>'请输入广告标题'], 300);
        
        if(empty($present['link']))
            return response()->json(['message'=>'请输入推广地址'], 300);
        
        if(empty($present['advertiser_id']) && $present['advertiser_id']!='0')
            return response()->json(['message'=>'请输入广告主ID'], 300);
        
        if(empty($present['adstype_id']))
            return response()->json(['message'=>'请选择广告类型'], 300);

        if(empty($present['category_id']))
            return response()->json(['message'=>'请选择广告分类'], 300);
        
        if(empty($present['package_id']))
            return response()->json(['message'=>'请选择广告素材包'], 300);
        
        if(empty($present['price_type']))
            return response()->json(['message'=>'请选择计费方式'], 300);
        
        if(empty($present['out_price']))
            return response()->json(['message'=>'请输入站长价格'], 300);

        if(empty($present['show_price']))
            return response()->json(['message'=>'请输入展示价格'], 300);

        if(empty($present['in_price']))
            return response()->json(['message'=>'请输入点击价格'], 300);
        
        if(empty($present['weight']))
            return response()->json(['message'=>'请输入广告权重'], 300);

        if(empty($present['client']) && $present['client']!='0')
            return response()->json(['message'=>'选择投放系统'], 300);
        
        if(empty($present['is_wechat']) && $present['is_wechat']!='0')
            return response()->json(['message'=>'选择是否微信量'], 300);
        
        if($present['is_hour_weight']=='1')
        {
            if(count($present['hour_weight'])<12)
            {
                return response()->json(['message'=>'至少设置12个小时的权重'], 300);
            }else
            {
                $present['hour_weight'] = json_encode($present['hour_weight'], true);
            }
        }

        if($present['is_put_category']=='1')
        {
            if(count($present['categorys'])<3)
            {
                return response()->json(['message'=>'至少选择三个投放分类'], 300);
            }else
            {
                $present['categorys'] = json_encode($present['categorys'], true);
            }
        }

        if($present['is_put_hour']=='1')
        {
            if(count($present['hours'])<12)
            {
                return response()->json(['message'=>'至少选择投12个小时时段'], 300);
            }
            else
            {
                $present['hours'] = json_encode($present['hours'], true);
            }
        }
        
        if(!empty($present['date']) && count($present['date'])==2)
        {
            $put_date_start = $present['date'][0];
            $put_date_stop = $present['date'][1];
        }
        else
        {
            $put_date_start = '';
            $put_date_stop = '';
        }

        $ad = new AdvertiserAds;
        $ad->advertiser_id = trim($present['advertiser_id']);
        $ad->adstype_id = intval($present['adstype_id']);
        $ad->package_id = intval($present['package_id']);
        $ad->title = trim($present['title']);
        $ad->link = trim($present['link']);
        $ad->out_price = $present['out_price'];
        $ad->in_price = $present['in_price'];
        $ad->show_price = $present['show_price'];
        $ad->price_type = trim($present['price_type']);
        $ad->budget = trim($present['budget']);
        $ad->budget_day = trim($present['budget_day']);
        $ad->client = trim($present['client']);
        $ad->is_wechat = trim($present['is_wechat']);
        $ad->weight = trim($present['weight']);
        $ad->put_date_start = trim($put_date_start);
        $ad->put_date_stop = trim($put_date_stop);
        $ad->category_id = $present['category_id'];
        $ad->state = $present['state'];

        $ad->is_hour_weight = trim($present['is_hour_weight']);
        if($present['is_hour_weight']=='1'){
            $ad->categorys = $present['categorys'];
        }

        $ad->is_put_category = trim($present['is_put_category']);
        if($present['is_put_category']=='1'){
            $ad->categorys = $present['categorys'];
        }
        
        $ad->is_put_hour = $present['is_put_hour'];
        if($present['is_put_hour']=='1'){
            $ad->hours = $present['hours'];
        }
        
        if($ad->save())
        {
            return response()->json(['message'=>'添加成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'添加失败'], 300);
        }
        
    }

    //获得广告数量
    public function getAdnumber(Request $request)
    {
        self::Admin();

        #分池数据
        $data = Flowpool::where('state', '=', '1')->orderBy('sort', 'asc')->get(['id', 'name']);

        foreach($data as $k=>$v)
        {
            $type = AdsPosition::where('state', '=', '1')->get(['id', 'name']);

            if(count($type)>0)
            {
                foreach($type as $key=>$vel)
                {
                    $adnumber = [];

                    #这里是返回
                    if($vel['id']=='14')
                    {
                        //wap-android
                        $type[$key]->wapandroid = AdvertiserAds::where('state', '=', '1')->where('flowpool', 'like', '%'.$v->id.'%')->where('is_put_return_ad', '=', '1')->where('is_wechat', '0')->whereIn('client',['2','0'])->where('is_put_webmaster', '0')->count();

                        //wap-ios
                        $type[$key]->wapios = AdvertiserAds::where('state', '=', '1')->where('flowpool', 'like', '%'.$v->id.'%')->where('is_put_return_ad', '=', '1')->where('is_wechat', '0')->whereIn('client',['1','0'])->where('is_put_webmaster', '0')->count();

                        //微信-android
                        $type[$key]->wechatandroid = AdvertiserAds::where('state', '=', '1')->where('flowpool', 'like', '%'.$v->id.'%')->where('is_put_return_ad', '=', '1')->where('is_wechat', '1')->whereIn('client',['2','0'])->where('is_put_webmaster', '0')->count();

                        //微信-ios
                        $type[$key]->wechatios = AdvertiserAds::where('state', '=', '1')->where('flowpool', 'like', '%'.$v->id.'%')->where('is_put_return_ad', '=', '1')->where('is_wechat', '1')->whereIn('client',['1','0'])->where('is_put_webmaster', '0')->count();
                    }
                    else
                    {
                        //wap-android
                        $type[$key]->wapandroid = AdvertiserAds::where('adstype_id', '=', $vel['id'])->where('flowpool', 'like', '%'.$v->id.'%')->where('state', '=', '1')->where('is_put_return_ad', '=', '0')->where('is_wechat', '0')->whereIn('client',['2','0'])->where('is_put_webmaster', '0')->count();

                        //wap-ios
                        $type[$key]->wapios = AdvertiserAds::where('adstype_id', '=', $vel['id'])->where('flowpool', 'like', '%'.$v->id.'%')->where('state', '=', '1')->where('is_put_return_ad', '=', '0')->where('is_wechat', '0')->whereIn('client',['1','0'])->where('is_put_webmaster', '0')->count();

                        //微信-android
                        $type[$key]->wechatandroid = AdvertiserAds::where('adstype_id', '=', $vel['id'])->where('flowpool', 'like', '%'.$v->id.'%')->where('state', '=', '1')->where('is_put_return_ad', '=', '0')->where('is_wechat', '1')->whereIn('client',['2','0'])->where('is_put_webmaster', '0')->count();

                        //微信-ios
                        $type[$key]->wechatios = AdvertiserAds::where('adstype_id', '=', $vel['id'])->where('flowpool', 'like', '%'.$v->id.'%')->where('state', '=', '1')->where('is_put_return_ad', '=', '0')->where('is_wechat', '1')->whereIn('client',['1','0'])->where('is_put_webmaster', '0')->count();
                    }

                }
            }

            $data[$k]['number'] = $type;
        }

    
        return response()->json(['data'=>$data], 200);
    }

    public function uploadImg(Request $request, $id) {      
        if (!$request->hasFile('file')) {
            return response()->json([], 500);
        }
        $file = $request->file('file');
        $data = ['status' => 200,'message' => 'success'];
        if ($file->isValid()) {
            // 获取文件相关信息
            $originalName = $file->getClientOriginalName(); // 文件原名
            $ext = $file->getClientOriginalExtension();     // 扩展名
            $realPath = $file->getRealPath();   //临时文件的绝对路径
            $type = $file->getClientMimeType();     // image/jpeg

            // 上传文件
            $filename = $id;
            // 使用我们新建的uploads本地存储空间（目录）

            $filename = $filename . '.' . $ext;
            // 使用我们新建的upload_company_img本地存储空间（目录）
            //这里的upload_company_img是配置文件的名称
            $bool = \Storage::disk('upload_advertiser_img')->put($filename, file_get_contents($realPath));
            if(!$bool) {
                $data['status'] = 300;
                $data['message'] = 'fail';
            }
        }else {
            $data['status'] = 0;
            $data['message'] = 'isValid is null';
        }
        return response()->json($data);
    }

    public function exportAdvertiser(Request $request) {

        self::Admin();

        $data = [];
        $date = $request->input('s_date');
        $second1 = strtotime($date);
        $second2 = strtotime(date('Y-m-d'));
        $day = ($second2 - $second1)/86400;
        $dayArr = [0,1,2];
        $tempClient = ['android/ios','ios','android'];
        if(in_array($day,$dayArr)) {
            $data = (new AdvertiserExpendService)->getExportData($day, 0, 0, 0, $limitMoney = ['min'=>10,'isequal'=>false],self::$user);
            if(count($data) > 0) {
                $tempArr = [];
                foreach($data as $key => &$val) {
                    $tempArr[$key]['date'] = date( "Y/m/d",strtotime($date));
                    $tempArr[$key]['username'] = $val['username'];
                    $tempArr[$key]['advertiser_id'] = $val['advertiser_id'];
                    $tempArr[$key]['title'] = $val['title'];
                    $tempArr[$key]['money'] = $val['money'];
                    $tempArr[$key]['reg_num'] = '';
                    $tempArr[$key]['reg_price'] = '';
                    $tempArr[$key]['bind_num'] = '';
                    $tempArr[$key]['bind_price'] = '';
                    $tempArr[$key]['recharge_num'] = '';
                    $tempArr[$key]['recharge_price'] = '';
                    $tempArr[$key]['recharge_money'] = '';
                    $tempArr[$key]['client'] = $tempClient[$val['client']];
                    $tempArr[$key]['inclusion_size'] = '';
                    $tempArr[$key]['speed'] = '';
                    $tempArr[$key]['recharge channel'] = '';
                    $tempArr[$key]['link'] = $val['link'];
                }
                $data = $tempArr;
            }
        }
        $fields = [['日期', '账号', 'ID', '名称', '消耗', '注册数量', '注册单价', '绑定数量', '绑定单价', '充值数量', '充值单价', '充值金额', '终端', '包体大小','下载速度','充值通道','广告地址']];
        if(empty($data)) {
            $data = [''];
        } 
        $data = array_merge($fields, $data);
        Excel::create($date.'@导出产品',function($excel) use ($data) {
            $excel->sheet(date('Y-m-d').'@导出记录', function($sheet) use ($data) {               
                $sheet->rows($data);
                $sheet->setWidth([
                    'A' => 16,'B' => 13,'C' => 10,
                    'D' => 30,'E' => 13,'F' => 13, 
                    'G' => 13,'H' => 13,'I' => 13,
                    'J' => 13,'K' => 13,'L' => 13,
                    'M' => 13,'N' => 16,'O' => 13,
                    'P' => 23,'Q' => 50
                ])->setFontSize(12);
                $count = count($data);          
                //菜单样式
                $sheet->cells('A1:Q1', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setFontWeight('bold');
                });
                $sheet->cells('C2:C'.$count.'', function($cells) {
                    $cells->setAlignment('left');
                });
                $sheet->cells('E2:E'.$count.'', function($cells) {
                    $cells->setAlignment('left');
                });
                $sheet->setMergeColumn([ 
                    'columns' => ['A'], 
                    'rows' => [ 
                        [2, $count]
                    ], 
                ]);
                $sheet->cells('A2:A'.$count.'', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setValignment('center'); 
                    $cells->setFontWeight('bold');
                });
            });
        })->export('xlsx');
    }
}