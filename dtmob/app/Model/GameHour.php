<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GameHour extends Model
{
    protected $table = 'game_hour';

    protected $fillable = ['money','out_money','game_sign','pv_number','pc_number','ip_number','time','state'];
}