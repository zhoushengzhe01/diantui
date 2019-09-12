<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EarningDay extends Model
{
    protected $table = 'earning_day';
    protected $fillable = ['webmaster_ad_id','money','pv_number','pc_number','date','state','is_extract'];
}