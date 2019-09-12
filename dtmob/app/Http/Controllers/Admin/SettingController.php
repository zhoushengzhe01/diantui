<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Setting;

class SettingController extends ApiController
{

    public function getSetting(Request $request)
    {
        self::Admin();

        $settings = Setting::orderBy('id', 'desc')->get(['key', 'value']);

        $setting = (object)[];
        foreach($settings as $key=>$val)
        {
            $setting->{$val->key} = $val->value;
        }

        $setting->ad_ratio = intval($setting->ad_ratio);


        return response()->json(['data'=>['setting'=>$setting]], 200);
    }

    public function putSetting(Request $request)
    {
        self::Admin();
        
        $present = $request->input();

        foreach($present as $key=>$val)
        {
            Setting::where('key', '=', $key)->update(['value'=>$val]);
        }

        return response()->json(['message'=>'修改成功'], 200);
    }
}
