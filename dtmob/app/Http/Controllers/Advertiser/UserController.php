<?php
namespace App\Http\Controllers\Advertiser;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\Advertiser;
use App\Model\AdvertiserLoginLog;
use Hash;

class UserController extends ApiController
{
    //获取登录日志
    public function getLoginlogs(Request $request)
    {
        self::Advertiser();

        $date = trim($request->input('date'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $loginlogs = AdvertiserLoginLog::where('advertiser_id', '=', self::$user->id);

        if(!empty($date))
        {
            $loginlogs = $loginlogs->where('created_at', '>=', $date.' 00:00:00')->where('created_at', '<=', $date.' 23:59:59');
        }

        $count = $loginlogs->count();
        $loginlogs = $loginlogs->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'loginlogs'=>$loginlogs,
        ];
        
        return response()->json(['data'=>$data], 200);
    }
    //获取
    public function getUser(Request $request)
    {
        self::Advertiser();

        if(self::$user->is_signa=='0')
        {
            self::$user->signa_time = date('Y-m-d');
            self::$user->signa_number = 'LHMOB'.date('YmdHi');
        }

        return response()->json(['data'=>['user'=>self::$user]], 200);
    }

    //修改
    public function putUser(Request $request)
    {
 
        self::Advertiser();

        //接收参数
        $present = $request->input();

        $advertiser = Advertiser::where('id', '=', self::$user->id)->first();

        if(!empty($present['company']))
            $advertiser->company = $present['company'];

        if(!empty($present['nickname']))
            $advertiser->nickname = $present['nickname'];
        
        
        if(!empty($present['signa_company']) && !empty($present['signa_name']) && empty($advertiser->signa_name) && empty($advertiser->signa_company)) 
        {
            $advertiser->signa_company = $present['signa_company'];
            $advertiser->signa_name = $present['signa_name'];
            $advertiser->is_signa = '1';
            $advertiser->signa_time = date('Y-m-d');
            $advertiser->signa_number = 'LHMOB'.date('YmdHi');
        }

        if(!empty($present['signa_company']) && !empty($present['signa_name']))
            

        if(!empty($present['mobile']))
            $advertiser->mobile = $present['mobile'];
        
        if(!empty($present['qq']))
            $advertiser->qq = $present['qq'];

        if($advertiser->save())
        {
            return response()->json(['message'=>'保存成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'保存失败'], 300);
        }
    }

    //修改密码
    public function putPasswd(Request $request)
    {
        self::Advertiser();

        $present = $request->input();

        //验证
        if(empty($present['oldpasswd']))
        {
            return response()->json(['message'=>'请填写老密码'], 300);
        }
        if(empty($present['newpasswd']))
        {
            return response()->json(['message'=>'请填写新密码'], 300);
        }
        if(empty($present['newpasswd1']))
        {
            return response()->json(['message'=>'请填写确认密码'], 300);
        }
        if(strlen($present['newpasswd'])<6 || strlen($present['newpasswd'])>18)
        {
            return response()->json(['message'=>'密码必须大于6位数，小于18位数'], 300);
        }
        if($present['newpasswd'] != $present['newpasswd1'])
        {
            return response()->json(['message'=>'两次密码输入不一样'], 300);
        }

        $advertiser = Advertiser::where('id', '=', self::$user->id)->first();

        if (!Hash::check($present['oldpasswd'], $advertiser->password))
        {
            return response()->json(['message'=>'旧密码错误'], 300);
        }
        $advertiser->password = bcrypt($present['newpasswd']);

        if($advertiser->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
    }
}