<?php
namespace App\Http\Controllers\Admin\Data;

use Illuminate\Http\Request;

use App\Http\Controllers\ApiController;
use App\Model\ClickPushLogs;


class ClickBaidukeywordController extends ApiController
{

    //列表
    public function getBaidukeyword(Request $request)
    {
        self::Admin();

        

        return response()->json(['data'=>$data], 200);

    }
}
