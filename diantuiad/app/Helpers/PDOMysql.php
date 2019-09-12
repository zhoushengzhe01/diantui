<?php
namespace app\Helpers;

use PDO;

class PDOMysql
{
    protected $db;

    protected $table;

    protected $field;
    
    protected $where;

    protected $data;

    protected $orderBy;

    protected $attributes;

    #public function __construct($host, $username, $password, $dbname)
    public function __construct()
    {
        try {
            $this->db = new PDO('mysql:host=127.0.0.1;dbname=diantui', 'diantui', 'diantui@883513',
                array(PDO::ATTR_PERSISTENT => true)
            );
        } catch (PDOException $e) {
            print "数据库链接失败 Error!:" . $e->getMessage();
            die;
        }
    }

    //where条件
    public function where($field='', $symbol='=', $value='')
    {
        if(!empty($field) && !empty($symbol) && !empty($value))
        {
            $this->where[] = [
                'relation' => 'and',
                'field' => $field,
                'symbol' => $symbol,
                'value' => $value,
            ];
        }
        
        return $this;
    }

    //whereOR条件
    public function whereOR($field='', $symbol='', $value='')
    {
        if(!empty($field) && !empty($symbol) && !empty($value))
        {
            $this->where[] = [
                'relation' => 'or',
                'field' => $field,
                'symbol' => $symbol,
                'value' => $value,
            ];
        }

        return $this;
    }

    //组合where语句
    public function getWhere($state)
    {
        if($state=='prepare')
        {
            $where = '';
            if( $this->where )
            {
                foreach($this->where as $key=>$val)
                {
                    if($key==0)
                    {
                        $val['relation'] = " where";
                    }

                    $where .= " ".$val['relation']." ".$val['field']." ".$val['symbol']."? ";  
                }
            }

            return $where;
        }
        if($state=='value')
        {
            $value = [];

            if( $this->where )
            {
                foreach($this->where as $key=>$val)
                {
                    $value[] = $val['value'];
                }
            }

            return $value;
        }
    }

    //查找字段
    public function field($field)
    {

    }

    //插入语句
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    //获得查询SQL
    public function getSelectSql()
    {
        $sql = "select ";
        $value = [];

        //字段
        if($this->field)
        {
            $sql .= $this->field." from ";
        }
        else
        {
            $sql .= "* from ";
        }

        //表名字
        $sql .= $this->table." ";
        
        //条件
        if($this->where)
        {
            $sql .= " ".$this->getWhere('prepare');
            $value = array_merge($value, $this->getWhere('value'));
        }

        return ['sql'=>$sql, 'value'=>$value];
    }

    //获得插入SQL
    public function getInsertSql()
    {
        $value = [];
        $field = [];
        $sub = [];
        if($this->data)
        {
            foreach($this->data as $key=>$val)
            {
                $field[] = $key;
                $sub[] = '?';
                $value[] = $val;
            }
        }

        $sql = "insert into `" . $this->table . "`(`" . implode('`,`', $field) . "`) values(" . implode(', ', $sub) . ")";

        return ['sql'=>$sql, 'value'=>$value];
    }

    //获得插入SQL
    public function getUpdateSql()
    {
        $sql = "update `" . $this->table . "` set name=:name,num=:num,price=:price,desn=:desn where id=:id";
        $value = [];

        $field = [];
        if($this->data)
        {
            foreach($this->data as $key=>$val)
            {
                $field[] = $key;
                $value[] = $val;
            }
        }
        $sql = "update `" . $this->table . "` set `" . implode('`=?, `', $field) . "`=? where id=?";

        //条件
        if($this->where)
        {
            $sql .= " ".$this->getWhere('prepare');
            $value = array_merge($value, $this->getWhere('value'));
        }

        return ['sql'=>$sql, 'value'=>$value];
    }
    
    //查找
    public function first()
    {
        $data = $this->getSelectSql();
        $dbh = $this->db->prepare($data['sql']);
        $dbh->execute($data['value']);
        $row = $dbh->fetchObject();
        return json_decode(json_encode($row, true), true);
    }

    //查找
    public function get($field='*')
    {
        $data = $this->getSelectSql();
        $dbh = $this->db->prepare($data['sql']);
        $dbh->execute($data['value']);
        $items = [];
        while ($row = $dbh->fetchObject())
        {
            $items[] =  json_decode(json_encode($row, true), true);
        }
        return $items;
    }

    //保存
    public function save()
    {
        $data = $this->getInsertSql();
        $dbh = $this->db->prepare($data['sql']);
        $dbh->execute($data['value']);
        $insert_id = $this->db->lastInsertId();
        if($insert_id)
        {
            return $insert_id;
        }
        else
        {
            return false;
        }
    }

    //更新
    public function update()
    {
        $data = $this->getUpdateSql();
        $dbh = $this->db->prepare($data['sql']);
        $dbh->execute($data['value']);
        $insert_id = $this->db->lastInsertId();
        if($insert_id)
        {
            return $insert_id;
        }
        else
        {
            return false;
        }
    }

    //数量
    public function count()
    {
        $this->field = "count(*) as count";
        
        $data = $this->getSelectSql();
        $dbh = $this->db->prepare($data['sql']);
        $dbh->execute($data['value']);
        $row = $dbh->fetchObject();
        
        if( !empty($row->count) )
        {
            return $row->count;
        }
        else
        {
            return 0;
        }
    }

    public function __get($key)
    {
        if( !empty($this->attributes[$key]) )
        {
            return $this->attributes[$key];
        }
        else
        {
            return "";
        }
    }

    public function __set($key, $value)
    {
        if( !empty($this->attributes[$key]) )
        {
            $this->attributes[$key] = $value;
        }
    }
}