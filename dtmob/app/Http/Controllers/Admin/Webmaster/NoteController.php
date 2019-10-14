<?php
namespace App\Http\Controllers\Admin\Webmaster;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Webmaster;
use App\Model\WebmasterNote;
use App\Model\WebmasterAds;

class NoteController extends ApiController
{

    public function getNotes(Request $request, $webmaster_id)
    {
        self::Admin();
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit)){
            $limit = 10;
        }

        if(empty($webmaster_id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }
        $notes = WebmasterNote::where('webmaster_id', '=', $webmaster_id);

        $count = $notes->count();
        $notes = $notes->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'notes'=>$notes,
        ];

        return response()->json(['data'=>$data], 200);
    }

    public function postNote(Request $request, $webmaster_id)
    {
        self::Admin();

        if(empty($webmaster_id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }
            
        
        $present = $request->input();
        if(empty($present['note']))
        {
            return response()->json(['message'=>'输入备注内容'], 300);
        }
        
        $note = new WebmasterNote;
        $note->webmaster_id = $webmaster_id;
        $note->userid = self::$user->id;
        $note->username = self::$user->username;
        $note->grade = $present['grade'];
        $note->note = $present['note'];

        
        if( !empty($present['webmaster_ad_id']) )
        {
            $webmaster_ad_id = intval($present['webmaster_ad_id']);

            $webmaster_ad = WebmasterAds::where('id', $webmaster_ad_id)->first();
            if( !empty($webmaster_ad) )
            {
                $note->webmaster_ad_id = $webmaster_ad_id;
                $note->in_advertiser_price = $webmaster_ad->in_advertiser_price;
                $note->out_advertiser_price = $webmaster_ad->out_advertiser_price;
                $note->is_auto_price = $webmaster_ad->is_auto_price;
                $note->target_price = $webmaster_ad->target_price;
                $note->false_close = $webmaster_ad->false_close;
                $note->hid_height = $webmaster_ad->hid_height;
                $note->hid_height_chance = $webmaster_ad->hid_height_chance;
                $note->compel_skip = $webmaster_ad->compel_skip;
            }
        }

        if($note->save())
        {
            ##如果修改等级则修改站长等级
            if(!empty($present['grade']))
            {
                Webmaster::where('id', $webmaster_id)->update(['grade'=>$present['grade']]);
            }

            return response()->json(['message'=>'备注成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'备注失败'], 300);
        }

    }
}
