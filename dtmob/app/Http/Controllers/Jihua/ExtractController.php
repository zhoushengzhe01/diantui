<?php
namespace App\Http\Controllers\Jihua;

use Illuminate\Support\Facades\Redis;
use Illuminate\Routing\Controller as BaseController;
use App\Model\Webmaster;
use App\Model\EarningDay;
use App\Model\AdvertiserAds;
use App\Model\WebmasterMoneyLog;
use App\Model\WebmasterLowerEarnings;
use App\Model\Advertiser;
use App\Model\Agents;
use App\Model\AdvertiserExpendDay;
use DB;

//返点脚本
class ExtractController extends BaseController
{
    //站长返点
    public static function WebmasterExtract()
    {
        $date = date("Y-m-d",strtotime("-1 day"));

        #循环
        startWebmaster:
        $webmasters = Webmaster::select('earning_day.*', 'webmaster.pid', 'webmaster.id as lower_webmaster_id')
            ->join('webmaster_ads', 'webmaster_ads.webmaster_id', '=', 'webmaster.id')
            ->join('earning_day', 'earning_day.webmaster_ad_id', '=', 'webmaster_ads.id')
            ->where('webmaster.pid', '>', 0)
            ->where('webmaster.state', '=', '1')
            ->where('earning_day.date', '=', $date)
            ->where('earning_day.introducer_extract_state', '=', '1')
            ->where('earning_day.money', '>', 0)
            ->offset(0)
            ->limit(10)
            ->get()
            ->toArray();

        if(!empty($webmasters))
        {
            foreach($webmasters as $key=>$value) {

                ##修改状态
                DB::table('earning_day')->where('id', $value['id'])->update(['introducer_extract_state'=>'2']);

                #昨天流水
                $flowing_money = trim($value['money']);

                #查找上线
                $pwebmaster = Webmaster::where('id', '=', $value['pid'])->first();

                if(!empty($pwebmaster))
                {
                    #提成
                    $money = round(($flowing_money * $pwebmaster->return_point)/100, 4);
                    if($money>0)
                    {
                        ##事物处理
                        DB::beginTransaction();

                        try {
                            $balance = round($pwebmaster->money + $money, 4);

                            ##修改状态
                            DB::table('earning_day')
                                ->where('id', $value['id'])
                                ->update([
                                    'introducer_extract_state'=>'4',
                                    'introducer_extract_money'=>$money,
                                    'introducer_extract'=>$pwebmaster->return_point
                                ]);

                            // ##修改账户余额
                            DB::table('webmaster')->where('id', $pwebmaster->id)
                                 ->update(['money'=>$balance]);

                            ##反点详情
                            DB::table('webmaster_lower_earnings')->insert([
                                'webmaster_id'=>$pwebmaster->id,
                                'webmaster_ad_id'=>$value['webmaster_ad_id'],
                                'lower_webmaster_id'=>$value['lower_webmaster_id'],
                                'return_point'=>$pwebmaster->return_point,
                                'flowing_money'=>round($flowing_money, 4),
                                'date'=>$date,
                                'money'=>$money,
                                'state'=>'1',
                                'created_at'=>date('Y-m-d H:i:s'),
                            ]);

                            #余额变动
                            DB::table('webmaster_money_log')->insert([
                                'webmaster_id' => $pwebmaster->id,
                                'money' => $money,
                                'message' => '下线返现',
                                'balance' => $balance,
                                'state' => '1',
                                'created_at'=>date('Y-m-d H:i:s'),
                            ]);

                            DB::commit();
                        
                        } catch (\Exception $e) {

                            DB::rollBack();
                        }
                    }
                }
            }

            goto startWebmaster;
        }
        
    }

    //客服提成
    public static function ServiceExtract()
    {
        $date = date("Y-m-d",strtotime("-1 day"));
        $offset = 0;

        startService:
        $earningDay = EarningDay::where('date', $date)->where('service_extract_state', '1')->offset($offset)->limit(10)->get()->toArray();

        if(!empty($earningDay))
        {
            foreach($earningDay as $key=>$val)
            {
                //有推荐提成
                if($val['introducer_extract_state']=='4'){
                    $extract = 2;
                }else{
                    $extract = 3;
                }
                $extract_money = round($val['money']*$extract/100, 4);

                EarningDay::where('id', $val['id'])->update(['service_extract'=>$extract, 'service_extract_money'=>$extract_money, 'service_extract_state'=>'2']);
            }

            $offset += 10;
            goto startService;
        }   
    }

    //代理提成
    public static function AgentExtract()
    {
        die;
        $date = date("Y-m-d",strtotime("-1 day"));

        #循环
        startAgent:

        $advertisers = Advertiser::select('expend_day.*', 'advertiser.agent_id', 'advertiser.id as advertiser_id')
            ->join('advertiser_ads', 'advertiser_ads.advertiser_id', '=', 'advertiser.id')
            ->join('advertiser_expend_day as expend_day', 'expend_day.advertiser_ad_id', '=', 'advertiser_ads.id')
            ->where('advertiser.agent_id', '>', 0)
            ->where('advertiser.state', '=', '1')
            ->where('expend_day.date', '=', $date)
            ->where('expend_day.agent_extract_state', '=', '1')
            ->where('expend_day.money', '>', 0)
            ->offset(0)
            ->limit(10)
            ->get()
            ->toArray();


        if(!empty($advertisers))
        {

            foreach($advertisers as $key=>$value) {

                ##修改状态
                DB::table('advertiser_expend_day')->where('id', $value['id'])->update(['agent_extract_state'=>'2']);

                #昨天流水
                $flowing_money = trim($value['money']);

                #查找上线
                $agent = Agents::where('id', '=', $value['agent_id'])->first();

                if(!empty($agent))
                {
                    #提成
                    $money = round(($flowing_money * $agent->return_point)/100, 4);
                    if($money>0)
                    {
                        ##事物处理
                        DB::beginTransaction();

                        try {
                            $balance = round($agent->money + $money, 4);

                            ##修改状态
                            DB::table('advertiser_expend_day')
                                ->where('id', $value['id'])
                                ->update([
                                    'agent_extract_state'=>'4',
                                    'agent_extract_money'=>$money,
                                    'agent_extract'=>$agent->return_point
                                ]);

                            ##修改账户余额
                            DB::table('agents')->where('id', $agent->id)
                                 ->update(['money'=>$balance]);

                            ##反点详情
                            DB::table('agents_earnings')->insert([
                                'agent_id'=>$agent->id,
                                'advertiser_id'=>$value['advertiser_id'],
                                'advertiser_ad_id'=>$value['advertiser_ad_id'],
                                'return_point'=>$agent->return_point,
                                'flowing_money'=>round($flowing_money, 4),
                                'date'=>$date,
                                'money'=>$money,
                                'created_at'=>date('Y-m-d H:i:s'),
                            ]);

                            #余额变动
                            DB::table('agents_money_log')->insert([
                                'agent_id' => $agent->id,
                                'money' => $money,
                                'message' => '下线返现',
                                'balance' => $balance,
                                'state' => '1',
                                'created_at'=>date('Y-m-d H:i:s'),
                            ]);

                            DB::commit();
                        
                        } catch (\Exception $e) {

                            DB::rollBack();
                        }
                    }
                }
            }

            goto startAgent;
        }


    }





}