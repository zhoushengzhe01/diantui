<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController
{
    //PV数据
    public function getImageFlow(Request $request, $year, $month, $name, $type)
    {
        if(empty($year) || empty($month) || empty($name) || empty($type))
        {
            die('无数据');
        }

        $type = "gif";

        $path = $_SERVER['DOCUMENT_ROOT'].'/'.$year.'/'.$month.'/'.$name.'.'.$type;

        if(!file_exists($path))
        {
            echo "文件不存在";
        }

        
        $img_file = file_get_contents($path);
        echo base64_encode($img_file);
    }
}