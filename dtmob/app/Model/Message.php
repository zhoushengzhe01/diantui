<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'message';

    //获取列表
    public static function getMessages($where=[], $order=[], $offset=0, $limit='')
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

    //获取单个
    public static function getMessage($where=[], $order=[])
    {
        $fiction = self::where('id', '<>', '0');

        if(!empty($where))
        {
            $fiction = $fiction->where($where);
        }

        if(!empty($order))
        {
            $fiction = $fiction->orderBy($order[0], $order[1]);
        }

        $fiction = $fiction->first();

        if(empty($fiction))
        {
            return false;
        }else
        {
            return $fiction;
        }
    }

    //添加
    public static function postMessage($data)
    {
        if(empty($data) || count($data)<1 )
            return false;

        $fiction = new self;

        foreach($data as $k=>$v)
        {
            $fiction->{$k} = $v;
        }

        if($fiction->save())
        {
            return $fiction->id;
        }
        else
        {
            return false;
        }
    }

    //修改
    public static function putMessage($where, $data)
    {
        if(empty($where) || empty($data) || count($data)<1 )
            return false;
        
        $fiction = self::where($where)->first();

        foreach($data as $k=>$v)
        {
            $fiction->{$k} = $v;
        }

        if($fiction->save())
        {
            return $fiction->id;
        }
        else
        {
            return false;
        }
    }

    //删除
    public static function delMessage($where)
    {
        if(empty($where))
            return false;

        if(self::where($where)->delete())
        {
            return true;
        }
        else
        {
            return true;
        }
    }
}