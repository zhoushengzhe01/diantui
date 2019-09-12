<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdvertiserExpendDay extends Model
{
    protected $table = 'advertiser_expend_day';
    protected $fillable = ['advertiser_ad_id','pv_number','pc_number','money','out_money','date','state'];
}