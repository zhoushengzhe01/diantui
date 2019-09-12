<?php
namespace App\Http\Controllers\Advertiser;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\AdvertiserRecharge;


class RechargeController extends ApiController
{
    //获取
    public function getRecharges(Request $request)
    {
        self::Advertiser();
        $date = trim($request->input('date'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $recharges = AdvertiserRecharge::where('advertiser_id', '=', self::$user->id);

        if(!empty($date)) {
            $recharges = $recharges->where('created_at', '>=', $date.' 00:00:00')->where('created_at', '<=', $date.' 23:59:59');
        }

        $count = $recharges->count();
        $recharges = $recharges->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'recharges'=>$recharges,
        ];
        return response()->json(['data'=>$data], 200);
    }

    //获取
    public function getRecharge(Request $request, $id)
    {
        self::Advertiser();

        if(empty($id))
        {
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $recharge = AdvertiserRecharge::where('advertiser_id', '=', self::$user->id)->where('id', '=', $id)->first();

        $data = [
            'recharge'=>$recharge,
        ];
        return response()->json(['data'=>$data], 200);
    }

    //充值
    public function postRecharge(Request $request)
    {
        self::Advertiser();

        $present = $request->input();

        if(empty($present['outname']))
        {
            return response()->json(['message'=>'请填写出款人名称'], 300);
        }
        if(empty($present['money']))
        {
            return response()->json(['message'=>'输入充值金额'], 300);
        }
        if(empty($present['message']))
        {
            return response()->json(['message'=>'输入充值介绍'], 300);
        }

        $recharge = new AdvertiserRecharge;
        $recharge->advertiser_id = self::$user->id;
        $recharge->money = $present['money'];
        $recharge->outname = $present['outname'];
        $recharge->message = $present['message'];
        $recharge->state = '2';

        if($recharge->save())
        {
            return response()->json(['message'=>'操作成功', 'data'=>$recharge], 200);
        }
        else
        {
            return response()->json(['message'=>'操作失败'], 300);
        }
    }

    //修改
    public function putRecharge(Request $request, $id)
    {
        self::Advertiser();

        if(empty($id))
        {
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $present = $request->input();

        if(empty($present['money']))
        {
            return response()->json(['message'=>'输入充值金额'], 300);
        }
        if(empty($present['outname']))
        {
            return response()->json(['message'=>'请填写出款人名称'], 300);
        }
        if(empty($present['message']))
        {
            return response()->json(['message'=>'输入充值介绍'], 300);
        }
        if(empty($present['state']))
        {
            return response()->json(['message'=>'错误操作'], 300);
        }
        else
        {
            if($present['state']!='1' && $present['state']!='2' && $present['state']!='3')
            {
                return response()->json(['message'=>'错误操作'], 300);
            }
        }

        $recharge = AdvertiserRecharge::where('advertiser_id', '=', self::$user->id)->where('id', '=', $id)->first();
        $recharge->money = trim($present['money']);
        $recharge->outname = trim($present['outname']);
        $recharge->message = trim($present['message']);
        $recharge->state = trim($present['state']);

        if($recharge->save())
        {
            return response()->json(['message'=>'修改成功', 'data'=>$recharge], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }

    }
}








