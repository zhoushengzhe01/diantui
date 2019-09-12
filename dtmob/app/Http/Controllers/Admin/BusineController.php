<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\EarningHour;

class BusineController extends ApiController
{
    //业务员的收入
    public function getEarnings(Request $request)
    {
        self::Admin();

    }
}
