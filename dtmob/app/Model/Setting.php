<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'setting';

    //获取列表
    public static function getSettings($where=[], $order=[], $offset=0, $limit='')
    {
        $fictions = self::where('id', '<>', '0');

        if(!empty($where))
        {
            $fictions = $fictions->where($where);
        }

        if(!empty($order))
        {
            $fictions = $fictions->orderBy($order[0], $order[1]);
        }

        if(!empty($limit))
        {
            $fictions = $fictions->offset($offset)->limit($limit);
        }
   
        $fictions = $fictions->get();

        if(empty($fictions))
        {
            return false;
        }else
        {
            return $fictions;
        }
    }
}