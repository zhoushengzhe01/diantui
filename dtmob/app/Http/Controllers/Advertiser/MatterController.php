<?php
namespace App\Http\Controllers\Advertiser;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Model\Advertiser;
use App\Model\MatterPackage;
use App\Model\Matter;
use App\Model\AdsPosition;
use zgldh\QiniuStorage\QiniuStorage;

class MatterController extends ApiController
{
    public function getMatters(Request $request)
    {
        self::Advertiser();

        $sizeNum = trim($request->input('sizeNum'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $imgSize = config('other.imgSize');
        $width = $imgSize[$sizeNum]['width'];
        $height = $imgSize[$sizeNum]['height'];

        $matters = Matter::where('advertiser_id', '=', self::$user->id);

        if(!empty($width))
        {
            $matters = $matters->where('width', '=', $width);
        }
        if(!empty($height))
        {
            $matters = $matters->where('height', '=', $height);
        }

        $count = $matters->count();
        $matters = $matters->offset($offset)->limit($limit)->orderBy('id', 'desc')->get();

        $data = [
            'count'=>$count,
            'matters'=>$matters,
        ];

        return response()->json(['data'=>$data], 200);
    }

    public function postMatter(Request $request)
    {
        self::Advertiser();

        $file = $request->file('file');

        $sizeNum = intval($request->input('sizeNum'));

        if(empty($sizeNum) || $sizeNum<0 || $sizeNum>8)
            return response()->json(['message'=>'错误入口'], 300);
        
        $imgSize = config('other.imgSize');

        $width = $imgSize[$sizeNum]['width'];

        $height = $imgSize[$sizeNum]['height'];

        if($file->isValid()) {

            $realPath = $file->getRealPath();  // 缓存在 tmp 文件夹的文件绝对路径

            $tmpName = $file->getFileName();   // 缓存在 tmp 文件夹的文件名

            $size = intval(($file->getSize()) / 1024);   // 缓存在 tmp 文件夹的文件名
            
            $clientName = $file->getClientOriginalName();     // 获取原文件名称
            
            $extension = $file->getClientOriginalExtension(); // 上传文件的后缀

            if($size > 200)
                return response()->json(['message'=>'上传的图片不能超过200KB'], 200);

            if( !in_array($extension, ['gif', 'jpeg', 'jpg', 'gif']) )
                return response()->json(['message'=>'图片格式不正确'], 300);


            $dir = date('Y').'/'.date('m').'/';

            $filename = date('dHis') . mt_rand(100, 999) . '.' . $file->getClientOriginalExtension();

            $file->move($dir, $filename);
            
            $path = $dir . $filename;

            $img_info = getimagesize($path);

            if($img_info[0] != $width || $img_info[1] != $height)
            {
                unlink($path);
                
                return response()->json(['message'=>'你上传的图片尺寸不对'], 300);
            }
            else
            {
                //储存base64格式
                $base64 = base64_encode(file_get_contents($path));
                file_put_contents(preg_replace("/\.[a-zA-Z]+$/",".txt",$path), $base64);
                
                //保存
                $matter = new Matter;
                $matter->advertiser_id = self::$user->id;
                $matter->path = $path;
                $matter->width = $width;
                $matter->height = $height;
                $matter->size = $size;

                if($matter->save())
                {
                    return response()->json(['message'=>'上传成功'], 200);
                }
                else
                {
                    return response()->json(['message'=>'上传失败'], 300);
                }
            }
        }
        else
        {
            return response()->json(['message'=>'不可上传文件'], 300);
        }
    }
}








