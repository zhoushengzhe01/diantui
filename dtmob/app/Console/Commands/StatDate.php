<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Jihua\EarningController;
use App\Http\Controllers\Jihua\HourToDayController;
use App\Http\Controllers\Jihua\MoneyController;
use App\Http\Controllers\Jihua\AutoPriceController;
use App\Http\Controllers\Jihua\ExtractController;
use App\Http\Controllers\Jihua\WaveController;
use App\Http\Controllers\Jihua\OtherController;


class StatDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //统计
        EarningController::EarningPcHour();
        EarningController::EarningPvHour();

        //每小时清算
        if( intval(date('i')) >= 10 && intval(date('i')) <20 )
        {
            HourToDayController::WebmasterDay();       //站长
            HourToDayController::AdvertiserDay();      //广告主
            HourToDayController::AllianceDay();        //联盟
            HourToDayController::AllianceFluxDay();    //联盟

            //自动价格调整
            AutoPriceController::AutoPriceByIp();
            WaveController::SetWebmasterWave();

            //广告主上个小时消耗的金额
            OtherController::lastHourConsume();

            #订单记录余额
            if( intval(date('H'))==0 )
            {
                OtherController::AdvertiserWholePointMoney();
            }
        }

        
        //0点清楚数据
        if( intval(date('H'))==0 && intval(date('i'))>=0 && intval(date('i'))<10 )
        {
            EarningController::clearDate();
        }
        //2点提成
        if( intval(date('H'))==2 && intval(date('i'))>=0 && intval(date('i'))<10 )
        {
            ExtractController::WebmasterExtract();
            ExtractController::ServiceExtract();
            ExtractController::AgentExtract();
        }
        //3点提现
        if( intval(date('H'))==3 && intval(date('i'))>=0 && intval(date('i'))<10 )
        {
            EarningController::EarningDaySaveBalance();     //一整天的收益存入账户余额
            MoneyController::MoneyDay();

            //周结算提现
            if( intval(date('w'))==1 )
            {
                MoneyController::MoneyWeek();
            }
        }
    }
}
