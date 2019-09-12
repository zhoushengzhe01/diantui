<?php
namespace App\Http\Controllers\Admin\Property;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\WebmasterMoney;
use App\Model\Webmaster;
use App\Model\WebmasterBank;
use App\Model\WebmasterMoneyLog;
use Excel;

class TakemoneyController extends ApiController
{

    public function getTakemoneys(Request $request)
    {
        self::Admin();

        //网站搜索
        $start_date = trim($request->get('start_date'));
        $stop_date = trim($request->get('stop_date'));
        $webmaster_id = trim($request->input('webmaster_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $moneys = WebmasterMoney::select('webmaster_money.*', 'webmaster.state as webmaster_state', 'webmaster.alliance_agent_id')
            ->join('webmaster', 'webmaster.id', '=', 'webmaster_money.webmaster_id');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $moneys = $moneys->where('webmaster.alliance_agent_id', self::$user->alliance_agent_id);
        }

        if(!empty($start_date)){
            $moneys = $moneys->where('webmaster_money.created_at', '>=', $start_date.' 00:00:00');
        }
        if(!empty($stop_date)){
            $moneys = $moneys->where('webmaster_money.created_at', '<=', $stop_date.' 23:59:59');
        }

        if(!empty($webmaster_id)){
            $moneys = $moneys->where('webmaster_money.webmaster_id', '=', $webmaster_id);
        }

        $count = $moneys->count();
        $all_money = $moneys->sum('webmaster_money.money');
        $moneys = $moneys->orderBy('webmaster_money.id', 'desc')->offset($offset)->limit($limit)->get();

        //数据处理
        foreach($moneys as $key=>$val)
        {
            $card = preg_replace("/\ |\-|\_/","", $moneys[$key]->bank_card);
            preg_match('/([\d]{4})([\d]{4})([\d]{4})([\d]{4})([\d]{0,})?/', $card, $match);
            unset($match[0]);
            $moneys[$key]->bank_card = implode(' ', $match);
        }
        $data = [
            'all_money'=>$all_money,
            'count'=>$count,
            'takemoneys'=>$moneys,
        ];
        
        return response()->json(['data'=>$data], 200);

    }

    public function getTakemoney(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $money = WebmasterMoney::where('id', '=', $id)->first();

        if(empty($money)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        return response()->json(['data'=>['takemoney'=>$money]], 200);
    }

    public function putTakemoney(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }
        
        $money = WebmasterMoney::where('id', '=', $id)->first();
        
        if(empty($money)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        $present = $request->input();
        if(!empty($present['money'])){ $money->money = $present['money']; }
        if(!empty($present['state'])){ $money->state = $present['state']; }
        if(!empty($present['message'])){ $money->message = $present['message']; }
        if(!empty($present['bank_branch'])){ $money->bank_branch = $present['bank_branch']; }
        if(!empty($present['bank_name'])){ $money->bank_name = $present['bank_name']; }
        if(!empty($present['bank_card'])){ $money->bank_card = $present['bank_card']; }
        if(!empty($present['bank_account'])){ $money->bank_account = $present['bank_account']; }

        if($money->save())
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

    public function postTakemoney(Request $request)
    {
        self::Admin();

        $webmaster_id = $request->input('webmaster_id');
        $money = $request->input('money');

        //查找日结站长
        $webmaster = Webmaster::where('id', $webmaster_id)->first();

        if(empty($webmaster)){
            return response()->json(['message'=>'找不到站长信息'], 300);
        }

        if($webmaster->state!='1'){
            return response()->json(['message'=>'账号已经被封，不能提现'], 300);
        }

        if($webmaster->withdrawals_state!='1'){
            return response()->json(['message'=>'账号正在系统提现，稍后再试'], 300);
        }
        if($money > $webmaster->money)
        {
            return response()->json(['message'=>'提现金额超出，最多只能提现'. $webmaster->money], 300);
        }

        if($money<50){
            return response()->json(['message'=>'提现金额少于50元，拒绝提现'], 300);
        }
        
        //查找银行账户
        $bank = WebmasterBank::where('webmaster_id', $webmaster->id)
            ->where('bankname', '<>', '')
            ->where('branch', '<>', '')
            ->where('account', '<>', '')
            ->where('accountid', '<>', '')
            ->first();
        
        if(empty($bank)){
            return response()->json(['message'=>'银行信息未填写，提现失败'], 300);
        }
        
        $webmasterMoney = new WebmasterMoney;
        $webmasterMoney->webmaster_id = $webmaster->id;
        $webmasterMoney->type = '2';
        $webmasterMoney->money = $money;
        $webmasterMoney->state = '1';
        $webmasterMoney->bank_branch = $bank->branch;
        $webmasterMoney->bank_name = $bank->bankname;
        $webmasterMoney->bank_card = $bank->accountid;
        $webmasterMoney->bank_account = $bank->account;
    
        if($webmasterMoney->save())
        {
            Webmaster::where('id', '=', $webmaster->id)->update(['money'=> $webmaster->money-$money ]);

            //插入余额变动记录
            $webmasterMoneyLog = new WebmasterMoneyLog;
            $webmasterMoneyLog->webmaster_id = $webmaster->id;
            $webmasterMoneyLog->money = '-'.$money;
            $webmasterMoneyLog->message = "提现";
            $webmasterMoneyLog->save();

            return response()->json(['message'=>'提现成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'提现失败'], 300);
        }
    }

    //导出文件
    public function exportTakemoney(Request $request)
    {
        self::Admin();

        //网站搜索
        $start_date = trim($request->get('start_date'));
        $stop_date = trim($request->get('stop_date'));
        $webmaster_id = trim($request->input('webmaster_id'));

        if(empty($start_date)){
            $start_date = date('Y-m-d');
        }
        if(empty($stop_date)){
            $stop_date = date('Y-m-d');
        }

        $moneys = WebmasterMoney::select('webmaster_money.webmaster_id', 'webmaster_money.state', 'webmaster_money.bank_branch', 'webmaster_money.bank_name', 'webmaster_money.bank_card', 'webmaster_money.bank_account', 'webmaster_money.money', 'webmaster_money.created_at')
            ->join('webmaster', 'webmaster.id', '=', 'webmaster_money.webmaster_id')
            ->where('webmaster_money.created_at', '>=', $start_date.' 00:00:00')
            ->where('webmaster_money.created_at', '<=', $stop_date.' 23:59:59')
            ->where('webmaster.state', '1');

        if(!empty($webmaster_id))
        {
            $moneys = $moneys->where('webmaster_money.webmaster_id', '=', $webmaster_id);
        }

        $moneys = $moneys->orderBy('webmaster_money.id', 'desc')->offset(0)->limit(2000)->get()->toArray();

        //数据处理
        foreach($moneys as $key=>$val)
        {
            $moneys[$key]['bank_card'] = "#".preg_replace("/\ |\-|\_/","", $moneys[$key]['bank_card']);
            $moneys[$key]['state'] = ($val['state']=='1')?'未打款':'已打款';
        }

        //字段
        $fields = [['站长ID', '打款状态', '所属支行', '所属银行', '收款账号', '收款人', '提现金额', '提现时间']];

        $moneys = array_merge($fields, $moneys);

        Excel::create( date('Y-m-d').'@打款',function($excel) use ($moneys){
            $excel->sheet('score', function($sheet) use ($moneys){
                $sheet->rows($moneys);
            });
        })->export('xls');
    }
 
}
