<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Jihua\OtherController;
use App\Http\Controllers\Jihua\EarningController;
use App\Http\Controllers\Jihua\HourToDayController;
use App\Http\Controllers\Jihua\MoneyController;
use App\Http\Controllers\Jihua\AutoPriceController;
use App\Http\Controllers\Jihua\ExtractController;
use App\Http\Controllers\Jihua\WaveController;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

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
        OtherController::ip_point();
        // HourToDayController::AdvertiserDay();      //广告主
    }
    
}
