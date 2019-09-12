<?php
namespace App\Http\Controllers\Jihua;

use Illuminate\Support\Facades\Redis;
use Illuminate\Routing\Controller as BaseController;

use App\Model\EarningHour;
use App\Model\EarningDay;
use App\Model\WebmasterAds;

//计划脚本
class AutoPriceController extends BaseController
{
    //点击统计
    public static function AutoPriceByIp()
    {
        //12点  1点不调整
        $hour = date('H');

        //初始化计费率
        if($hour=='00') {
            $webmasterAds = WebmasterAds::where('is_auto_price', '=', '1')->orderBy('earning_day', 'desc')->get();

            foreach($webmasterAds as $key=>$val)
            {
                #数据
                $date = [
                    'in_advertiser_price' => $val->init_price,
                    'out_advertiser_price' => $val->init_price,
                ];
                WebmasterAds::where('id', '=', $val->id)->update($date);            
            }
        }
        
        if($hour!='00')
        {
           
            $webmasterAds = WebmasterAds::where('is_auto_price', '=', '1')->orderBy('earning_day', 'desc')->get();
           
            foreach($webmasterAds as $key=>$val)
            {
                //上一个小时的单价
                $earningHour = EarningHour::where('webmaster_ad_id', '=', $val->id)->where('time', '=', date("Y-m-d H", strtotime("-1 hour")).':00:00')->first();
                
                //每天单价
                $earningDay = EarningDay::where('webmaster_ad_id', '=', $val->id)->where('date', '=', date("Y-m-d"))->orderBy('money', 'desc')->first();

                if( !empty($earningHour) && $earningHour->money>2 )
                {
                    if( !empty($earningDay) && $earningDay->money>10 )
                    {
                        //上一个小时单价
                        $price_hour = intval(($earningHour->money/$earningHour->ip_number) * 10000);
                        
                        //今天的单价
                        $price_day = intval(($earningDay->money/$earningDay->ip_number) * 10000);
                        
                        //设置单价
                        /**
                         * 问题：什么时候加，什么时候减的问题
                         * 解决加：当小时单价低于设置设置水平时候，并且每天单价低于设置水平
                         * 解决减：当小时单价高于设置水平的时候，并且每天单价高于设置水平
                         */
                        #容差
                        if(intval($hour)<10) #10点之前融差5
                        {
                            $target_price = intval($val->target_price * 0.8);
                            $number = 5;
                        }
                        else if(intval($hour)<17)   #18点之前融差10
                        {
                            $target_price = intval($val->target_price * 0.9);
                            $number = 10;
                        }
                        else
                        {
                            $target_price = intval($val->target_price * 1);
                            $number = 15;
                        }

                        $disparity = intval($val->in_advertiser_price);
                        #增加
                        if(($price_hour-$number)<$target_price && $price_day<$target_price) {
                            ##$disparity = ceil(($target_price-$price_day)/10)*5;
                            $disparity = intval($disparity * $target_price/$price_hour)+$number;
                            ##$disparity = intval($disparity * ($target_price/$price_day));
                        }
                        #降低 直接回到上一个小时价格
                        if(($price_hour+$number)>$target_price && $price_day>$target_price) {
                            ##$disparity = floor(($target_price-$price_day)/5)*5;
                            $disparity = intval($disparity * $target_price/$price_hour)-$number;
                            ##$disparity = intval($disparity * ($target_price/$price_day));
                        }

                        echo $val->webmaster_id . ">set:" . $target_price . ">hour:" . $price_hour . '>day:' . $price_day . '>res:'.$disparity;
                        echo "\r\n";

                        if($disparity!=0)
                        {
                            #数据
                            $date = [
                                'in_advertiser_price' => $disparity,
                                'out_advertiser_price' => $disparity,
                            ];

                            #安全控制值在：50-150之间
                            if($date['in_advertiser_price'] < 50){
                                $date['in_advertiser_price'] = 50;
                            }
                            if($date['in_advertiser_price'] > 200){
                                $date['in_advertiser_price'] = 200;
                            }
                            if($date['out_advertiser_price'] < 50){
                                $date['out_advertiser_price'] = 50;
                            }
                            if($date['out_advertiser_price'] > 200){
                                $date['out_advertiser_price'] = 200;
                            }
                            #修改
                            WebmasterAds::where('id', '=', $val->id)->update($date);
                        }
                        
                    }
                }

            }
        }

    }
}