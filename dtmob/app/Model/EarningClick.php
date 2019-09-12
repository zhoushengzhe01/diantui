<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EarningClick extends Model
{
    protected $connection = 'mysql-data';
    protected $table = 'earning_click';
}