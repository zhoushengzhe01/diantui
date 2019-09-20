<?php
namespace App\Http\Controllers\Admin\Advertiser;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Matter;
use App\Model\Advertiser;
use zgldh\QiniuStorage\QiniuStorage;

class MatterController extends ApiController
{
    public function getMatters(Request $request)
    {
        self::Admin();
        
        $advertiser_id = trim($request->input('advertiser_id'));

        $advertiser = Advertiser::where('id', '=', $advertiser_id)->first();
        if(empty($advertiser))
        {
            return response()->json(['message'=>'你输入的广告主ID有误'], 300);
        }

        $sizeNum = trim($request->input('sizeNum'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit)){
            $limit = 10;
        }

        $imgSize = config('other.imgSize');
        $width = $imgSize[$sizeNum]['width'];
        $height = $imgSize[$sizeNum]['height'];

        $matters = Matter::select('matter.*', 'adv.alliance_agent_id')
            ->join('advertiser as adv', 'adv.id', '=', 'matter.advertiser_id');
        
        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $matters = $matters->where('adv.alliance_agent_id', '=', self::$user->alliance_agent_id);
        }
        #商务权限限制
        if(self::$user->department_id==4){
            $matters = $matters->where('adv.busine_id', '=', self::$user->id);
        }
        if(!empty($advertiser_id)){
            $matters = $matters->where('advertiser_id', '=', $advertiser_id);
        }
        if(!empty($width)){
            $matters = $matters->where('matter.width', '=', $width);
        }
        if(!empty($height)){
            $matters = $matters->where('matter.height', '=', $height);
        }

        $count = $matters->count();
        $matters = $matters->offset($offset)->limit($limit)->orderBy('id', 'desc')->get();

        $data = [
            'count'=>$count,
            'matters'=>$matters,
        ];

        return response()->json(['data'=>$data], 200);
    }

    //上传广告素材
    public function postMatter(Request $request)
    {
        self::Admin();

        $file = $request->file('file');

        $advertiser_id = trim($request->input('advertiser_id'));
        $advertiser = Advertiser::where('id', '=', $advertiser_id)->first();
        if(empty($advertiser)){
            return response()->json(['message'=>'你输入的广告主ID有误'], 300);
        }

        $sizeNum = intval($request->input('sizeNum'));
        if(empty($sizeNum) || $sizeNum<0 || $sizeNum>7){
            return response()->json(['message'=>'错误入口'], 300);
        }
        
        $imgSize = config('other.imgSize');

        $width = $imgSize[$sizeNum]['width'];

        $height = $imgSize[$sizeNum]['height'];

        if($file->isValid()) {

            $realPath = $file->getRealPath();  // 缓存在 tmp 文件夹的文件绝对路径
            $tmpName = $file->getFileName();   // 缓存在 tmp 文件夹的文件名
            $size = intval(($file->getSize()) / 1024);  // 缓存在 tmp 文件夹的文件名
            $clientName = $file->getClientOriginalName();   // 获取原文件名称
            $extension = $file->getClientOriginalExtension();   // 上传文件的后缀

            if($size > 200){
                return response()->json(['message'=>'上传的图片不能超过200KB'], 300);
            }
            if( !in_array($extension, ['gif', 'jpeg', 'jpg', 'gif']) ){
                return response()->json(['message'=>'图片格式不正确'], 300);
            }

            $dir = date('Y').'/'.date('m').'/';
            $filename = date('dHis') . mt_rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $path = $dir . $filename;
            $img_info = getimagesize($path);

            if($img_info[0] != $width || $img_info[1] != $height)
            {
                unlink($path);
                return response()->json(['message'=>'你上传的图片尺寸不对'.$img_info[1]], 300);
            }
            else
            {
                //储存base64格式
                $base64 = base64_encode(file_get_contents($path));
                file_put_contents(preg_replace("/\.[a-zA-Z]+$/",".txt",$path), $base64);
                

                $matter = new Matter;
                $matter->advertiser_id = $advertiser_id;
                $matter->path = $path;
                $matter->width = $width;
                $matter->height = $height;
                $matter->size = $size;

                if($matter->save()){
                    return response()->json(['message'=>'上传成功'], 200);
                }else{
                    return response()->json(['message'=>'上传失败'], 300);
                }                
            }
        }
        else
        {
            return response()->json(['message'=>'不可上传文件'], 300);
        }
    }


    //上传图片
    public function postUploadImg(Request $request)
    {
        self::Admin();

        $file = $request->file('file');
        $advertiser_ad_id = trim($request->input('advertiser_ad_id'));
        if(empty($advertiser_ad_id)){
            return response()->json(['message'=>'缺少广告ID'], 300);
        }

        if($file->isValid()) {

            $realPath = $file->getRealPath();
            $tmpName = $file->getFileName();
            $size = intval(($file->getSize()) / 1024);
            $clientName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            if($size>200){
                return response()->json(['message'=>'上传的图片不能超过200KB'], 300);
            }
            if( !in_array($extension, ['png']) ){
                return response()->json(['message'=>'图片格式不正确'], 300);
            }

            $dir = 'images/';
            $filename = $advertiser_ad_id . '.' . $file->getClientOriginalExtension();
            $path = $dir . $filename;
            # 文件存在则删除
            if(file_exists($path))
            {
                unlink($path);
            }
            $file->move($dir, $filename);
            $img_info = getimagesize($path);

            if($img_info[0]>360 || $img_info[1]<640)
            {
                unlink($path);
                return response()->json(['message'=>'图片宽度不得大于360像素，高度不得小于640像素'], 300);
            }
            else
            {
                //储存base64格式
                $base64 = base64_encode(file_get_contents($path));
                file_put_contents(preg_replace("/\.[a-zA-Z]+$/",".txt",$path), $base64);

                return response()->json(['message'=>'上传成功'], 200);  
            }
        }
        else
        {
            return response()->json(['message'=>'不可上传文件'], 300);
        }

    }
}