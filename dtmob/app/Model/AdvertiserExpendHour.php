<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdvertiserExpendHour extends Model
{
    protected $table = 'advertiser_expend_hour';
    protected $fillable = ['advertiser_ad_id','pv_number','pc_number','money','out_money','time','state'];
}