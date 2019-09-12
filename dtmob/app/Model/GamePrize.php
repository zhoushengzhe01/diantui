<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GamePrize extends Model
{
    protected $table = 'game_prize';

    protected $fillable = ['uid','game_sign','advertiser_ad_id','webmaster_ad_id','source_time','title','picture','time','state'];   
}