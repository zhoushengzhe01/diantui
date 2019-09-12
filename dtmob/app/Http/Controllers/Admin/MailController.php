<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\CustomersMail;
use App\Model\Mail;

class MailController extends ApiController
{
    
    public function getMails(Request $request)
    {
        self::Admin();

        //网站搜索
        $title = trim($request->input('title'));
        $type = trim($request->input('type'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $mails = Mail::where('id', '<>', '0');

        if(!empty($title))
            $mails = $mails->where('title', 'like', '%'.$title.'%');

        if(!empty($type))
            $mails = $mails->where('type', '=', $type);
        
        $count = $mails->count();
        $mails = $mails->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'mails'=>$mails,
        ];

        return response()->json(['data'=>$data], 200);
    }


    public function getMail(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);

        $mail = Mail::where('id', '=', $id)->first();

        if(empty($mail))
            return response()->json(['message'=>'未找到数据'], 300);

        return response()->json(['data'=>['mail'=>$mail]], 200);
    }

    public function putMail(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $mail = Mail::where('id', '=', $id)->first();

        if(empty($mail))
            return response()->json(['message'=>'未找到数据'], 300);

        $present = $request->input();

        if(!empty($present['title'])){ $mail->title = $present['title']; }

        if(!empty($present['type'])){ $mail->type = $present['type']; }

        if(!empty($present['state'])){ $mail->state = $present['state']; }

        if(!empty($present['content'])){ $mail->content = $present['content']; }
        
        if($mail->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }

    }


    public function postMail(Request $request)
    {
        self::Admin();

        $present = $request->input();

        if(empty($present['title']))
            return response()->json(['message'=>'请输入邮件标题'], 300);
        
        if(empty($present['type']))
            return response()->json(['message'=>'请选择邮件类型'], 300);

        if(empty($present['state']))
            return response()->json(['message'=>'请选择邮件状态'], 300);

        if(empty($present['content']))
            return response()->json(['message'=>'请输入邮件内容'], 300);
        
        $mail = new Mail;
        $mail->title = trim($present['title']);
        $mail->type = trim($present['type']);
        $mail->state = trim($present['state']);
        $mail->content = trim($present['content']);
        
        if($mail->save())
        {
            return response()->json(['message'=>'添加成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'添加失败'], 300);
        }

    }

}