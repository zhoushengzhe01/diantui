<?php
namespace App\Http\Controllers\Jihua;

use Illuminate\Routing\Controller as BaseController;

use App\Model\EarningClick;
use App\Model\EarningMinute;
use App\Model\EarningHour;
use App\Model\EarningDay;
use App\Model\WebmasterMoneyLog;
use App\Model\WebmasterMoney;
use App\Model\WebmasterBank;
use App\Model\Alliance;
use App\Model\Webmaster;
use App\Model\AllianceHour;


class MoneyController extends BaseController
{
    //提现日结算
    public static function MoneyDay()
    {
        //周日周六 不提现
        //包含的日期
        $date = ['20190913', '20191005', '20191006', '20191007'];

        if( date("w")!='6' && !in_array(date("Ymd"), $date) )
        {
            start:

            //查找日结站长
            $webmaster = Webmaster::where('withdrawals_type', '=', '1')
                ->where('withdrawals_state', '=', '1')
                ->where('state', '=', '1')
                ->where('money', '>=', 200)
                ->offset(0)->limit(50)->get();
            
            if( count($webmaster)>0 )
            {
                foreach($webmaster as $key=>$val)
                {
                    Webmaster::where('id', '=', $val->id)->update(['withdrawals_state'=>'2']);

                    //查找银行账户
                    $bank = WebmasterBank::where('webmaster_id', '=', $val->id)
                        ->where('bankname', '<>', '')
                        ->where('branch', '<>', '')
                        ->where('account', '<>', '')
                        ->where('accountid', '<>', '')
                        ->first();
        
                    if(!empty($bank))
                    {

                        $webmasterMoney = new WebmasterMoney;
                        $webmasterMoney->webmaster_id = $val->id;
                        $webmasterMoney->type = '1';
                        $webmasterMoney->money = $val->money;
                        $webmasterMoney->state = '1';
                        $webmasterMoney->bank_branch = $bank->branch;
                        $webmasterMoney->bank_name = $bank->bankname;
                        $webmasterMoney->bank_card = $bank->accountid;
                        $webmasterMoney->bank_account = $bank->account;
        
                        if($webmasterMoney->save())
                        {
                            Webmaster::where('id', '=', $val->id)->update(['money'=>0]);
        
                            //插入余额变动记录
                            $webmasterMoneyLog = new WebmasterMoneyLog;
                            $webmasterMoneyLog->webmaster_id = $val->id;
                            $webmasterMoneyLog->money = '-'.$val->money;
                            $webmasterMoneyLog->message = "提现";
                            $webmasterMoneyLog->save();

                            Webmaster::where('id', '=', $val->id)->update(['withdrawals_state'=>'4']);
                        }
        
                    }
                }

                sleep(1);

                goto start;
            }
            else
            {
                Webmaster::orwhere('withdrawals_state', '=', '4')
                    ->orwhere('withdrawals_state', '=', '2')
                    ->update(['withdrawals_state'=>'1']);
            }
        }
        
    }

    //提现周结算
    public static function MoneyWeek()
    {
        start:

        //查找日结站长
        $webmaster = Webmaster::where('withdrawals_type', '=', '1')
            ->where('withdrawals_state', '=', '2')
            ->where('state', '=', '1')
            ->where('money', '>=', 50)
            ->offset(0)->limit(10)->get();
        
        if( count($webmaster)>0 )
        {
            foreach($webmaster as $key=>$val)
            {
                Webmaster::where('id', '=', $val->id)->update(['withdrawals_state'=>'2']);

                //查找银行账户
                $bank = WebmasterBank::where('webmaster_id', '=', $val->id)
                    ->where('bankname', '<>', '')
                    ->where('branch', '<>', '')
                    ->where('account', '<>', '')
                    ->where('accountid', '<>', '')
                    ->first();
    
                if(!empty($bank))
                {

                    $webmasterMoney = new WebmasterMoney;
                    $webmasterMoney->webmaster_id = $val->id;
                    $webmasterMoney->type = '2';
                    $webmasterMoney->money = $val->money;
                    $webmasterMoney->state = '1';
                    $webmasterMoney->bank_branch = $bank->branch;
                    $webmasterMoney->bank_name = $bank->bankname;
                    $webmasterMoney->bank_card = $bank->accountid;
                    $webmasterMoney->bank_account = $bank->account;
    
                    if($webmasterMoney->save())
                    {
                        Webmaster::where('id', '=', $val->id)->update(['money'=>0]);
    
                        //插入余额变动记录
                        $webmasterMoneyLog = new WebmasterMoneyLog;
                        $webmasterMoneyLog->webmaster_id = $val->id;
                        $webmasterMoneyLog->money = '-'.$val->money;
                        $webmasterMoneyLog->message = "提现";
                        $webmasterMoneyLog->save();

                        Webmaster::where('id', '=', $val->id)->update(['withdrawals_state'=>'4']);
                    }
    
                }
            }

            sleep(1);

            goto start;
        }
        else
        {
            Webmaster::orwhere('withdrawals_state', '=', '4')
                ->orwhere('withdrawals_state', '=', '2')
                ->update(['withdrawals_state'=>1]);
        }
    }
   
}