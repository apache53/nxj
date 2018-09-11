<?php
/**
 * Date: 2018/9/10
 * Time: 21:58
 */

namespace App\Http\Model;

use App\Library\AppLogger;
use Illuminate\Support\Facades\Cache;

class File
{
    public static function storeFile($file,$type,$id=0){
        $originalName = $file->getClientOriginalName(); // 文件原名
        $ext = $file->getClientOriginalExtension();     // 扩展名
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        $file_type = $file->getClientMimeType();     // image/jpeg
        $size = $file->getSize();
        $fileTypes = array('jpg','mp3','png','jpeg','gif');
        AppLogger::info($type."#".$ext."#".$file_type."#".$size);
        //是否是要求的文件
        $isInFileType = in_array($ext,$fileTypes);
        if(!$isInFileType){
            return ["error"=>21,"msg"=>"文件类型有误","res"=>[]];
        }
        if($size > 10*1024*1024){
            return ["error"=>22,"msg"=>"文件不得超过10M","res"=>[]];
        }

        // 上传文件
        $filename = date('YmdHis') . '-' . md5($originalName.$id)  . rand(100,999) . '.' . $ext;
        // 使用我们新建的uploads本地存储空间（目录）
        $file_dir = "/".$type."/";
        $file_path = $file_dir.$filename;
        file_put_contents(public_path().$file_dir.$filename, file_get_contents($realPath));

        $data = [
            "file_path" => $file_path,
            "file_name" => $filename,
        ];

        return ["error"=>1,"msg"=>"ok","res"=>$data];
    }
}