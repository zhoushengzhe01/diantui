<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Advertiser extends Model
{
    protected $table = 'advertiser';

    protected $hidden = ['password'];
}