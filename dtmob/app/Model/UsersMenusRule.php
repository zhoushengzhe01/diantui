<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UsersMenusRule extends Model
{
    protected $table = 'users_menus_rule';

    protected $fillable = ['department_id','rules'];
}