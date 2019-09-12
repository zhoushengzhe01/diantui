<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Agents extends Model
{
    protected $table = 'agents';

    protected $hidden = ['password'];
}