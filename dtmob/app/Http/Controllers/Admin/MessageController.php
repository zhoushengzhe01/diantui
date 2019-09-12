<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Message;

class MessageController extends ApiController
{

    public function getMessages(Request $request)
    {
        self::Admin();

        //网站搜索
        $title = trim($request->input('title'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $messages = Message::where('id', '<>', '0');

        if(!empty($title))
            $messages = $messages->where('title', 'like', '%'.$title.'%');

        $count = $messages->count();
        $messages = $messages->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'messages'=>$messages,
        ];
        
        return response()->json(['data'=>$data], 200);

    }

    public function getMessage(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $message = Message::where('id', '=', $id)->first();

        if(empty($message))
            return response()->json(['message'=>'未找到数据'], 300);

        return response()->json(['data'=>['message'=>$message]], 200);

    }

    public function putMessage(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $message = Message::where('id', '=', $id)->first();

        if(empty($message))
            return response()->json(['message'=>'未找到数据'], 300);
        
        
        $present = $request->input();

        if(!empty($present['title'])){ $message->title = $present['title']; }

        if(!empty($present['recommend'])){ $message->recommend = $present['recommend']; }

        if(!empty($present['content'])){ $message->content = $present['content']; }
        
        if(!empty($present['state'])){ $message->state = $present['state']; }

        if($message->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

    public function postMessage(Request $request)
    {
        self::Admin();

        $present = $request->input();

        if(empty($present['title']))
            return response()->json(['message'=>'请输入公告标题'], 300);
        
        if(empty($present['recommend']) && $present['recommend']!=0)
            return response()->json(['message'=>'请选择是否推荐'], 300);

        if(empty($present['content']))
            return response()->json(['message'=>'请输入公告内容'], 300);
        
        if(empty($present['state']))
            return response()->json(['message'=>'请选择状态'], 300);
        
        $message = new Message;
        $message->title = trim($present['title']);
        $message->recommend = trim($present['recommend']);
        $message->content = trim($present['content']);
        $message->state = trim($present['state']);
        
        if($message->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }
}
