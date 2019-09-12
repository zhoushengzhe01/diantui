<?php
namespace App\Http\Controllers\Admin\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Banks;
use App\Model\Webmaster;
use App\Model\WebmasterBank;

class BankController extends ApiController
{
    public function getBanks(Request $request)
    {
        self::Admin();
        
        $banks = Banks::where('state', '=', '1')->orderBy('id', 'asc')->get(['name', 'id']);

        return response()->json(['data'=>['banks'=>$banks]], 200);
    }

    public function getBank(Request $request, $webmaster_id)
    {
        self::Admin();

        if(empty($webmaster_id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }
        
        $webmasterBank = WebmasterBank::where('webmaster_id', '=', $webmaster_id)->first();

        return response()->json(['data'=>['bank'=>$webmasterBank]], 200);
    }


    public function putBank(Request $request, $webmaster_id)
    {
        self::Admin();

        if(empty($webmaster_id))
            return response()->json(['message'=>'缺少参数'], 400);

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
        
        $bank = WebmasterBank::where('webmaster_id', '=', $webmaster_id)->first();
        if(empty($bank))
        {
            $bank = new WebmasterBank;
        }

        $bank->webmaster_id = $webmaster_id;
        $bank->bankname = trim($present['bankname']);
        $bank->branch = trim($present['branch']);
        $bank->accountid = trim($present['accountid']);
        $bank->account = trim($present['account']);

        if($bank->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }
}