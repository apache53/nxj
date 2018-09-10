<?php
/**
 * Date: 2018/9/10
 * Time: 21:58
 */

namespace App\Http\Model;

use Illuminate\Support\Facades\Cache;

class File
{
    public static function storeFile($file,$type,$id=0){
        $originalName = $file->getClientOriginalName(); // 文件原名
        $ext = $file->getClientOriginalExtension();     // 扩展名
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        $type = $file->getClientMimeType();     // image/jpeg

        // 上传文件
        $filename = date('YmdHis') . '-' . md5($id)  . rand(100,999) . '.' . $ext;
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