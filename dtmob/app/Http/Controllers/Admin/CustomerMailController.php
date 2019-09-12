<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\CustomersMail;
use App\Model\Customers;
use App\Model\Mail;
use Mail as SendMail;

class CustomerMailController extends ApiController
{
    protected static $customer_email;
    protected static $email_title;

    public function getMails(Request $request)
    {
        self::Admin();

        //搜索
        $title = trim($request->input('title'));
        $user_id = trim($request->input('user_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $mails = CustomersMail::where('id', '<>', '0');

        if(!empty($title)){
            $mails = $mails->where('title', 'like', '%'.$title.'%');
        }

        if(!empty($domain)){
            $mails = $mails->where('user_id', '=', $user_id);
        }

        $count = $mails->count();
        $mails = $mails->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'mails'=>$mails,
        ];

        return response()->json(['data'=>$data], 200);
    }

    public function postMail(Request $request, $customer_id)
    {
        self::Admin();

        if(empty($customer_id)){
            return response()->json(['message'=>'错误入口'], 300);
        }
        $present = $request->input();

        if(empty($present['type'])){
            return response()->json(['message'=>'请选择发送类型'], 300);
        }
        
        if(empty($present['title'])){
            return response()->json(['message'=>'填写邮件发送标题'], 300);
        }

        if($present['type']=='1')
        {
            if(empty($present['mail_id'])){
                return response()->json(['message'=>'请选择发送邮件ID'], 300);
            }

            $mail = Mail::where('id', '=', $present['mail_id'])->first();

            if(empty($mail)){
                return response()->json(['message'=>'找不到系统邮件'], 300);
            }

            $content = $mail->content;
        }
        if($present['type']=='2')
        {
            if(empty($present['content'])){
                return response()->json(['message'=>'情输入发送内容'], 300);
            }
            $content = $present['content'];
        }

        $customer = Customers::where('id', '=', $customer_id)->first();
        if(empty($customer)){
            return response()->json(['message'=>'未找到客户'], 300);
        }

        if(substr($customer->mail_time, 0, 13)==date('Y-m-d H') )
        {
            //return response()->json(['message'=>'每小时只能发一条邮件。'], 300);
        }
        else
        {
            if($present['type']=='1' && $customer->mail_id==$present['mail_id'])
            {
                return response()->json(['message'=>'此邮件已经发送过了不必在发送'], 300);
            }
        }


        if(empty($customer->email))
        {
            return response()->json(['message'=>'此客户资料没有邮件'], 300);
        }

        self::$customer_email = $customer->email;
        self::$email_title = $present['title'];

        $flag = SendMail::send('mail', ['content'=>$content], function($message)
        {
            $message->to(self::$customer_email)->subject(self::$email_title);
        });

        if(!$flag)
        {
            $mail = new CustomersMail;
            $mail->customer_id = $customer_id;
            $mail->customer_mail = $customer->email;
            $mail->user_id = self::$user->id;
            $mail->type = $present['type'];
            $mail->title = $present['title'];
            if($mail->type=='1')
                $mail->mail_id = $present['mail_id'];
            
            if($mail->type=='2')
                $mail->content = $present['content'];
            
            $mail->save();

            $customer->mail_time = date('Y-m-d H:i:s');
            $customer->mail_id = $present['mail_id'];

            $customer->save();

            return response()->json(['message'=>'发送成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'发送失败'], 300);
        }
    }
}