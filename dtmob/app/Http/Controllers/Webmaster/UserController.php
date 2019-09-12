<?php
namespace App\Http\Controllers\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Banks;
use App\Model\Webmaster;
use App\Model\WebmasterBank;

use Hash;
use Session;

class UserController extends ApiController
{

    //获取
    public function getUser(Request $request)
    {
        //验证登陆
        self::Webmaster();
        
        return response()->json(['data'=>['user'=>self::$user]], 200);
    }

    //获取
    public function getBank(Request $request)
    {
        //验证登陆
        self::Webmaster();

        $bank = WebmasterBank::where('webmaster_id', '=', self::$user->id)->first();
        
        if(empty($bank)){
            $bank = (object)[];
        }
        
        return response()->json(['data'=>['bank'=>$bank]], 200);
    }

    //修改用户
    public function putUser(Request $request)
    {
        self::Webmaster();
        
        $present = $request->input();

        if(empty($present['nickname']))
        {
            return response()->json(['message'=>'请填写真实姓名'], 300);
        }
        if(empty($present['mobile']))
        {
            return response()->json(['message'=>'请填写手机号码'], 300);
        }
        if(empty($present['email']))
        {
            return response()->json(['message'=>'请天填写邮箱'], 300);
        }
        if(empty($present['qq']))
        {
            return response()->json(['message'=>'请填写QQ'], 300);
        }
        
        $webmaster = Webmaster::where('id', '=', self::$user->id)->first();

        $webmaster->nickname = trim($present['nickname']);
        $webmaster->mobile = trim($present['mobile']);
        $webmaster->email = trim($present['email']);
        $webmaster->qq = trim($present['qq']);
        
        if($webmaster->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

    //修改
    public function putBank(Request $request)
    {
        self::Webmaster();
        
        $present = $request->input();

        if(empty($present['bankname']))
        {
            return response()->json(['message'=>'请选择银行'], 300);
        }
        if(empty($present['branch']))
        {
            return response()->json(['message'=>'请填写支行'], 300);
        }
        if(empty($present['accountid']))
        {
            return response()->json(['message'=>'请填写银行账号'], 300);
        }
        if(empty($present['account']))
        {
            return response()->json(['message'=>'请填写收款人'], 300);
        }
        
        $mybank = WebmasterBank::where('webmaster_id', '=', self::$user->id)->first();
        if(!empty($mybank))
        {
            return response()->json(['message'=>'修改银行信息请联系客服'], 300);
        }
        $mybank = new WebmasterBank;
        $mybank->webmaster_id = self::$user->id;
        $mybank->bankname = trim($present['bankname']);
        $mybank->branch = trim($present['branch']);
        $mybank->accountid = trim($present['accountid']);
        $mybank->account = trim($present['account']);
      
        if($mybank->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }

    }

    //修改密码
    public function putPasswd(Request $request)
    {
        self::Webmaster();
        
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
        if($present['newpasswd']!=$present['newpasswd1'])
        {
            return response()->json(['message'=>'两次密码输入不一样'], 300);
        }
        
        
        $webmaster = Webmaster::where('id', '=', self::$user->id)->first();
        
        if (!Hash::check($present['oldpasswd'], $webmaster->password))
        {
            return response()->json(['message'=>'旧密码错误'], 300);
        }

        $webmaster->password = bcrypt($present['newpasswd']);

        if($webmaster->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }
}
