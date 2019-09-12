<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Customers;
use App\Model\Users;
use App\Model\CustomersNote;
use App\Model\CustomersSource;

class CustomerController extends ApiController
{

    public function getCustomers(Request $request)
    {
        self::Admin();

        //网站搜索
        $user_id = trim($request->input('user_id'));
        $company = trim($request->input('company'));
        $mobile = trim($request->input('mobile'));
        $email = trim($request->input('email'));
        $qq = trim($request->input('qq'));

        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $customers = Customers::where('id', '<>', '0');

        if(!empty($user_id))
            $customers = $customers->where('user_id', '=', $user_id);

        if(!empty($company))
            $customers = $customers->where('company', 'like', '%'.$company.'%');

        if(!empty($mobile))
            $customers = $customers->where('mobile', '=', $mobile);
        
        if(!empty($email))
            $customers = $customers->where('email', 'like', '%'.$email.'%');

        if(!empty($mobile))
            $customers = $customers->where('mobile', 'like', '%'.$mobile.'%');

        if(!empty($qq))
            $customers = $customers->where('qq', 'like', '%'.$qq.'%');

        $count = $customers->count();
        $customers = $customers->orderBy('level', 'desc')->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        foreach($customers as $key=>$val)
        {
            if($val->usre_id == self::$user->id)
            {
                $customers[$key]->username = self::$user->username;
            }
            else
            {
                $user = Users::where('id', '=', $val->user_id)->first(['username']);
                $customers[$key]->username = $user->username;
            }
        }

        $data = [
            'count'=>$count,
            'customers'=>$customers,
        ];

        return response()->json(['data'=>$data], 200);
    }

    /*
    public function getCustomer(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $customer = Customers::where('id', '=', $id)->first();

        if(empty($customer))
            return response()->json(['message'=>'未找到数据'], 300);

        return response()->json(['data'=>['customer'=>$customer]], 200);
    }
    */

    public function putCustomer(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $customer = Customers::where('id', '=', $id)->first();

        if(empty($customer))
            return response()->json(['message'=>'未找到数据'], 300);

        $present = $request->input();

        if(!empty($present['source_id'])){ $customer->source_id = $present['source_id']; }

        if(!empty($present['user_id'])){ $customer->user_id = $present['user_id']; }

        if(!empty($present['company'])){ $customer->company = $present['company']; }

        if(!empty($present['website'])){ $customer->website = $present['website']; }
        
        if(!empty($present['contact'])){ $customer->contact = $present['contact']; }

        if(!empty($present['mobile'])){ $customer->mobile = $present['mobile']; }

        if(!empty($present['email'])){ $customer->email = $present['email']; }

        if(!empty($present['qq'])){ $customer->qq = $present['qq']; }

        if(!empty($present['level']) || $present['level']=='0'){ $customer->level = $present['level']; }

        if($customer->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

    public function postCustomer(Request $request)
    {
        self::Admin();

        $present = $request->input();

        if(empty($present['source_id']))
            return response()->json(['message'=>'请选择来源'], 300);

        if(empty($present['company']))
            return response()->json(['message'=>'请输入公司名称'], 300);
        
        if(empty($present['contact']))
            $present['contact'] = '未知';
            
        $customer = new Customers;
        $customer->source_id = trim($present['source_id']);
        $customer->user_id = self::$user->id;
        $customer->contact_time = date('Y-m-d');
        $customer->level = 1;
        
        if(!empty($present['company'])) $customer->company = $present['company'];
        if(!empty($present['website'])) $customer->website = $present['website'];
        if(!empty($present['contact'])) $customer->contact = $present['contact'];
        if(!empty($present['mobile'])) $customer->mobile = $present['mobile'];
        if(!empty($present['email'])) $customer->email = $present['email'];
        if(!empty($present['qq'])) $customer->qq = $present['qq'];
        
        if($customer->save())
        {   
            return response()->json(['message'=>'添加成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'添加失败'], 300);
        }

    }

}