<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use App\Http\Controllers\ApiController;
use App\Model\ClickPushLogs;


class ClickSmkeywordController extends ApiController
{

    //列表
    public function getSmkeyword(Request $request)
    {
        self::Admin();

        return response()->json(['data'=>$data], 200);
    }
}
