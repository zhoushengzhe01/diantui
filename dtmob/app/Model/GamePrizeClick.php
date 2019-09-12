<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GamePrizeClick extends Model
{
    protected $table = 'game_prize_click';

    protected $fillable = ['sign','chance','type','title','weight','view','picture','state'];
}