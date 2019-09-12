<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AllianceHour extends Model
{
    protected $table = 'alliance_hour';
    protected $fillable = ['alliance_id','pv_number','pc_number','out_money','time'];
}