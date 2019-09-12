<?php
namespace App\Http\Controllers\Advertiser;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\Message;

class MessageController extends ApiController
{
    //获取登录日志
    public function getMessages(Request $request)
    {
        self::Advertiser();
        
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $messages = Message::where('id', '<>', '0');
        
        $count = $messages->count();
        $messages = $messages->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'messages'=>$messages,
        ];

        return response()->json(['data'=>$data], 200);
    }
    //获取
    public function getMessage(Request $request, $id)
    {
        self::Advertiser();
        
        if(empty($id))
        {
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $message = MatterPackage::where('id', '=', $id)->first();

        if(empty($message))
        {
            return response()->json(['message'=>'未找到数据'], 300);
        }
        
        return response()->json(['data'=>['message'=>$message]], 200);
    }

}