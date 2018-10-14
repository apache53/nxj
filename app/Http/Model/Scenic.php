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

        $now = time();
        $scenic_data = [
            "scenic_name" => $scenic["scenic_name"],
            "latitude" => $scenic["latitude"],
            "longitude" => $scenic["longitude"],
            "radius" => $scenic["radius"],
            "pre_id" => $scenic["pre_id"],
            "update_time" => $now,
        ];

        $scenic_img = $scenic["scenic_img"];
        if($scenic["scenic_img"]){
            $scenic_data["scenic_img"] = str_replace(env('HOST_SELF'),"",$scenic["scenic_img"]);
        }else{
            $scenic_img = $scenic_res->scenic_img;
        }
        $scenic_voice = $scenic["scenic_voice"];
        if($scenic["scenic_voice"]){
            $scenic_data["voice_path"] = str_replace(env('HOST_SELF'),"",$scenic["scenic_voice"]);
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

        //检查下上一个景点是否已经是其他景点的上一个景点，把其他景点的上个景点变成当前这个景点
        self::updatePre($scenic,$scenic["scenic_id"],$user,$request_info);

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

        $scenic_img = str_replace(env('HOST_SELF'),"",$scenic["scenic_img"]);
        $scenic_voice = str_replace(env('HOST_SELF'),"",$scenic["scenic_voice"]);

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

        //检查下上一个景点是否已经是其他景点的上一个景点，把其他景点的上个景点变成当前这个景点
        self::updatePre($scenic,$scenic_id,$user,$request_info);

        $return_data = [
            "scenic_id" => $scenic_id,
            "scenic_img" => Utils::getImageUrl($scenic_img),
            "scenic_voice" => Utils::getVoiceUrl($scenic_voice),
        ];
        return [
            "error"=>1,"msg"=>"保存成功","res"=>$return_data
        ];
    }

    public static function updatePre($scenic,$current_scenic_id,$user,$request_info){
        if($scenic["pre_id"]>0){
            $where = [
                "pre_id" => $scenic["pre_id"],
            ];
            $other_scenic = self::getScenicArr($where);
            if(is_null($other_scenic) || empty($other_scenic)){
            }else{
                $other_pre_ids = "";
                foreach($other_scenic as $k=>$v){
                    $other_pre_ids .= $v->id.",";
                }
                $up = [
                    "pre_id" => $current_scenic_id
                ];
                $where = [
                    "pre_id" => $scenic["pre_id"],
                    "id" => -$current_scenic_id
                ];
                self::updateScenic($where,$up);
                //记录操作日志
                $log = [
                    "admin_user_id" => $user["admin_user_id"],
                    "user_name" => $user["user_name"],
                    "log_type" => config('constants.log_update_scenic'),
                    "log_ip" => $request_info["ip"],
                    "before_value" => "id:".$other_pre_ids." # pre_id:".$scenic["pre_id"],
                    "after_value" => "pre_id:".$current_scenic_id,
                    "remark" => "更新景点上个景点",
                ];
                UserLog::add($log);
            }
        }
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

    public static function getScenicArr($where){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;

        $db = $db->table($table);

        if(isset($where["scenic_id"])){
            $db->where('id','=', $where["scenic_id"]);
        }
        if(isset($where["pre_id"])){
            $db->where('pre_id','=', $where["pre_id"]);
        }
        if(isset($where["scenic_name"])){
            $db->where('scenic_name','=', $where["scenic_name"]);
        }

        $res = $db->get();

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
            $arr = $res->toArray();
            if(!empty($arr)) {
                foreach ($arr as $k => $v) {
                    if (isset($v->id)) {
                        $data[$v->id] = [
                            "id" => $v->id,
                            "scenic_name" => $v->scenic_name,
                            "scenic_img" => Utils::getImageUrl($v->scenic_img),
                            "latitude" => $v->latitude,
                            "longitude" => $v->longitude,
                            "voice_path" => Utils::getVoiceUrl($v->voice_path),
                            "radius" => $v->radius,
                            "pre_id" => $v->pre_id,
                            "next_id" => 0,
                        ];
                    }
                }
            }
        }

        return $data;
    }

    public static function updateScenic($where,$update){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;
        $db = $db->table($table);
        foreach($where as $k=>$v){
            if($v<0){
                $db->where($k,"!=", -$v);
            }else{
                $db->where($k, $v);
            }

        }
        $db->update($update);
    }
}