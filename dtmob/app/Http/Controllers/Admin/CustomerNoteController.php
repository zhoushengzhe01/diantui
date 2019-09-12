<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Customers;
use App\Model\Users;
use App\Model\CustomersNote;
use App\Model\CustomersSource;

class CustomerNoteController extends ApiController
{
    
    public function getNotes(Request $request)
    {
        self::Admin();

        //网站搜索
        $customer_id = trim($request->input('customer_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $note = CustomersNote::where('id', '<>', '0');

        if(!empty($customer_id))
            $note = $note->where('customer_id', '=', $customer_id);

        $count = $note->count();
        $notes = $note->orderBy('id', 'desc')->get();

        foreach($notes as $key=>$val)
        {
            if($val->usre_id == self::$user->id)
            {
                $notes[$key]->username = self::$user->username;
            }
            else
            {
                $user = Users::where('id', '=', $val->user_id)->first(['username']);
                $notes[$key]->username = $user->username;
            }
        }

        $data = [
            'count'=>$count,
            'notes'=>$notes,
        ];

        return response()->json(['data'=>$data], 200);
    }

    public function postNote(Request $request)
    {
        self::Admin();

        $present = $request->input();

        if(empty($present['customer_id']))
            return response()->json(['message'=>'选择备注客户'], 300);

        if(empty($present['note']))
            return response()->json(['message'=>'输入备注内容'], 300);

        $note = new CustomersNote;
        $note->user_id = self::$user->id;
        $note->customer_id = $present['customer_id'];
        $note->note = $present['note'];

        if($note->save())
        {
            Customers::where('id', '=', $present['customer_id'])->update(['contact_time'=>date('Y-m-d')]);

            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }
}