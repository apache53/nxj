<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Model\File;
use App\Http\Model\Scenic;
use App\Library\Utils;
use Cache;
use Config;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        //判断请求中是否包含name=file的上传文件
        if(!$request->hasFile('file')){
            Utils::outputJson(10,'文件不存在',[]);
        }
        $file = $request->file('file');
        //判断文件上传过程中是否出错
        if(!$file->isValid()){
            Utils::outputJson(11,'文件错误',[]);
        }

        $user = $request->get('user');
        $type = Utils::safeInput($request->input('type', ''), array("filter_sql" => true, "filter_html" => true));
        if(!in_array($type,['image','voice','user_voice'])){
            Utils::outputJson(12,'类型有误',[]);
        }

        $image_res = File::storeFile($file,$type,$user["admin_user_id"]);
        $file_path = "";
        if($image_res["error"]==1){
            $file_path = $image_res["res"]["file_path"];
        }

        $data = [
            "url" => Utils::getImageUrl($file_path),
            "path" => $file_path
        ];
        Utils::outputJson(1,'ok',$data);
    }

    public function scenic_upload(Request $request)
    {
        //判断请求中是否包含name=file的上传文件
        if(!$request->hasFile('file')){
            Utils::outputJson(10,'文件不存在',[]);
        }
        $file = $request->file('file');
        //判断文件上传过程中是否出错
        if(!$file->isValid()){
            Utils::outputJson(11,'文件错误',[]);
        }

        $scenic_id = Utils::safeInput($request->input('scenic_id', ''), array("filter_num" => true));
        $where = [
            "scenic_id" => $scenic_id
        ];
        $scenic = Scenic::getScenic($where);
        if(is_null($scenic) || !isset($scenic->id)){
            return [
                "error"=>12,"msg"=>"景点不存在","res"=>[]
            ];
        }

        $user = $request->get('user');

        $image_res = File::storeFile($file,'voice',$user["admin_user_id"]);
        $file_path = "";
        if($image_res["error"]==1){
            $file_path = $image_res["res"]["file_path"];
        }

        $where = [
            "id" => $scenic_id
        ];
        $update = [
            "voice_path" => $file_path
        ];
        Scenic::updateScenic($where,$update);

        $data = [
            "url" => Utils::getImageUrl($file_path),
            "path" => $file_path
        ];
        Utils::outputJson(1,'ok',$data);
    }

}
