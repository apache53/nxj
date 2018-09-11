<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Model\File;
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
        if(!in_array($type,['image','voice'])){
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

}
