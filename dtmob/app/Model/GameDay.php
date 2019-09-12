<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GameDay extends Model
{
    protected $table = 'game_day';

    protected $fillable = ['game_sign','money','out_money','pv_number','pc_number','ip_number','time','state'];
    
}