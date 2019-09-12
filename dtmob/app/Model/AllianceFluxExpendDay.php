<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AllianceFluxExpendDay extends Model
{
    protected $table = 'alliance_flux_expend_day';
    protected $fillable = ['alliance_flux_id','adstype_id','pv_number','click_number','in_money','out_money','date'];
}