<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AllianceDay extends Model
{
    protected $table = 'alliance_day';
    protected $fillable = ['alliance_id','pv_number','pc_number','ip_number','out_money','date'];
}