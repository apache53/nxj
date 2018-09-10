<?php
/**
 * User:
 * Date: 2018/9/9
 * Time: 22:21
 */

namespace App\Http\Model;

use App\Library\Utils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Scenic extends Model
{
    protected $connection = 'mysql';
    protected $table = 'scenic';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected static $connection_name = 'mysql';
    protected static $table_name = 'scenic';

    public static function editScenic($scenic, $user, $request_info = [])
    {
        //判断上一个景点信息
        if($scenic["pre_id"]==$scenic["scenic_id"]){
            return [
                "error"=>20,"msg"=>"上一个景点不能是当前景点","res"=>[]
            ];
        }
        if($scenic["pre_id"]){
            $where = [
                "scenic_id" => $scenic["pre_id"]
            ];
            $prev_scenic = self::getScenic($where);
            if(is_null($prev_scenic) || empty($prev_scenic)){
                return [
                    "error"=>21,"msg"=>"上一个景点错误","res"=>[]
                ];
            }
        }

        //判断景点是否存在
        $where = [
            "scenic_id" => $scenic["scenic_id"]
        ];
        $scenic_res = self::getScenic($where);
        if(is_null($scenic_res) || empty($scenic_res)){
            return [
                "error"=>23,"msg"=>"该景点不存在","res"=>[]
            ];
        }

        //判断景点名称是否存在
        if($scenic_res->scenic_name!=$scenic["scenic_name"]){
            $where = [
                "scenic_name" => $scenic["scenic_name"]
            ];
            $scenic_res = self::getScenic($where);
            if(!is_null($scenic_res) && isset($scenic_res->id)){
                return [
                    "error"=>22,"msg"=>"该景点名已存在","res"=>[]
                ];
            }
        }

        $scenic_img = "";
        if(!is_null($scenic["scenic_img"])){
            $image_res = File::storeFile($scenic["scenic_img"],"image",$scenic["scenic_name"]);
            if($image_res["error"]==1){
                $scenic_img = $image_res["res"]["file_path"];
            }
        }

        $scenic_voice = "";
        if(!is_null($scenic["scenic_voice"])){
            $voice_res = File::storeFile($scenic["scenic_voice"],"voice",$scenic["scenic_name"]);
            if($voice_res["error"]==1){
                $scenic_voice = $voice_res["res"]["file_path"];
            }
        }

        $now = time();
        $scenic_data = [
            "scenic_name" => $scenic["scenic_name"],
            "latitude" => $scenic["latitude"],
            "longitude" => $scenic["longitude"],
            "radius" => $scenic["radius"],
            "pre_id" => $scenic["pre_id"],
            "update_time" => $now,
        ];

        if($scenic_img){
            $scenic_data["scenic_img"] = $scenic_img;
        }else{
            $scenic_img = $scenic_res->scenic_img;
        }
        if($scenic_voice){
            $scenic_data["voice_path"] = $scenic_voice;
        }else{
            $scenic_voice = $scenic_res->voice_path;
        }

        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;
        $db->table($table)->where('id', $scenic["scenic_id"])->update($scenic_data);

        //记录操作日志
        $log = [
            "admin_user_id" => $user["admin_user_id"],
            "user_name" => $user["user_name"],
            "log_type" => config('constants.log_edit_scenic'),
            "log_ip" => $request_info["ip"],
            "before_value" => json_encode($scenic_res),
            "after_value" => json_encode($scenic_data),
            "remark" => "编辑景点成功",
        ];
        UserLog::add($log);

        $return_data = [
            "scenic_id" => $scenic["scenic_id"],
            "scenic_img" => Utils::getImageUrl($scenic_img),
            "scenic_voice" => Utils::getVoiceUrl($scenic_voice),
        ];
        return [
            "error"=>1,"msg"=>"保存成功","res"=>$return_data
        ];
    }

    public static function addScenic($scenic, $user, $request_info = [])
    {
        //判断上一个景点信息
        if($scenic["pre_id"]){
            $where = [
                "scenic_id" => $scenic["pre_id"]
            ];
            $prev_scenic = self::getScenic($where);
            if(is_null($prev_scenic) || empty($prev_scenic)){
                return [
                    "error"=>21,"msg"=>"上一个景点错误","res"=>[]
                ];
            }
        }

        //判断景点名称是否存在
        $where = [
            "scenic_name" => $scenic["scenic_name"]
        ];
        $scenic_res = self::getScenic($where);
        if(!is_null($scenic_res) && isset($scenic_res->id)){
            return [
                "error"=>22,"msg"=>"该景点名已存在","res"=>[]
            ];
        }

        $scenic_img = "";
        if(!is_null($scenic["scenic_img"])){
            $image_res = File::storeFile($scenic["scenic_img"],"imgage",$scenic["scenic_name"]);
            if($image_res["error"]==1){
                $scenic_img = $image_res["res"]["file_path"];
            }
        }

        $scenic_voice = "";
        if(!is_null($scenic["scenic_img"])){
            $voice_res = File::storeFile($scenic["scenic_img"],"voice",$scenic["scenic_name"]);
            if($voice_res["error"]==1){
                $scenic_voice = $voice_res["res"]["file_path"];
            }
        }

        $now = time();
        $scenic_data = [
            "scenic_name" => $scenic["scenic_name"],
            "latitude" => $scenic["latitude"],
            "longitude" => $scenic["longitude"],
            "radius" => $scenic["radius"],
            "pre_id" => $scenic["pre_id"],
            "scenic_img" => $scenic_img,
            "voice_path" => $scenic_voice,
            "create_time" => $now,
            "update_time" => $now,
        ];

        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;
        $scenic_id = $db->table($table)->insertGetId($scenic_data);

        //记录操作日志
        $log = [
            "admin_user_id" => $user["admin_user_id"],
            "user_name" => $user["user_name"],
            "log_type" => config('constants.log_edit_scenic'),
            "log_ip" => $request_info["ip"],
            "before_value" => "",
            "after_value" => json_encode($scenic_data),
            "remark" => "添加景点成功",
        ];
        UserLog::add($log);

        $return_data = [
            "scenic_id" => $scenic_id,
            "scenic_img" => Utils::getImageUrl($scenic_img),
            "scenic_voice" => Utils::getVoiceUrl($scenic_voice),
        ];
        return [
            "error"=>1,"msg"=>"保存成功","res"=>$return_data
        ];
    }

    public static function getScenic($where){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;

        $db = $db->table($table);

        if(isset($where["scenic_id"])){
            $db->where('id','=', $where["scenic_id"]);
        }
        if(isset($where["scenic_name"])){
            $db->where('scenic_name','=', $where["scenic_name"]);
        }

        $res = $db->first();

        return $res;
    }

    public static function getList($where){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;

        $db = $db->table($table);

        if(isset($where["scenic_id"]) && $where["scenic_id"]>0){
            $db->where('id','=', $where["scenic_id"]);
        }

        $res = $db->get();
        $data = [];
        if(!is_null($res)){
            $data = $res->toArray();
        }

        return $data;
    }
}