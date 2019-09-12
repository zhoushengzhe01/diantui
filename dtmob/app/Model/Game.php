<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'game';

    protected $fillable = ['sign','chance','type','title','weight','view','picture','state'];
    
}