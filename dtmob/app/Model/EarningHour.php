<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EarningHour extends Model
{
    protected $table = 'earning_hour';
    protected $fillable = ['webmaster_ad_id','money','pv_number','pc_number','ip_number','time','state'];
    
}