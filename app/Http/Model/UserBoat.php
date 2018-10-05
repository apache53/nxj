<?php
/**
 * Date: 2018/10/3
 * Time: 22:58
 */

namespace App\Http\Model;

use App\Library\Utils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserBoat extends Model
{
    protected $connection = 'mysql';
    protected $table = 'user_boat';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected static $connection_name = 'mysql';
    protected static $table_name = 'user_boat';

    public static function reportDriving($driving_data, $user, $request_info = [])
    {
        $scenic_id = $driving_data["scenic_id"];
        $where = [
            "scenic_id" => $scenic_id
        ];
        $scenic = Scenic::getScenic($where);
        if(is_null($scenic) || empty($scenic)){
            return [
                "error"=>21,"msg"=>"景点不存在","res"=>[]
            ];
        }

        //验证数据
        //TODO

        $data = [
            "admin_user_id" => $user["admin_user_id"],
            "drive_day" => date('Ymd'),
            "latitude" => $driving_data["latitude"],
            "longitude" => $driving_data["longitude"],
            "scenic_id" => $scenic_id,
            "out_distance" => $driving_data["out_distance"],
            //"distance" => $driving_data["distance"],
            "speed" => $driving_data["speed"],
            "boat_name" => ""
        ];
        $res = self::saveReport($data);

        $data["distance"] = $res["distance"];
        UserBoatLog::add($data);

        return [
            "error"=>1,"msg"=>"保存成功","res"=>[
                "out_distance" => $driving_data["out_distance"],
                "scenic_id" => $driving_data["scenic_id"]
            ]
        ];

    }

    public static function saveReport($data){
        $now = time();

        $where = [
            "drive_day" => $data["drive_day"],
            "admin_user_id" => $data["admin_user_id"]
        ];
        $report = self::getReport($where);
        $current_distance = 0;
        $distance = 0;
        if(is_null($report) || empty($report) || !isset($report->id)){
            $add = $data;
            $add["create_time"] = $now;
            $add["update_time"] = $now;
            $add["start_latitude"] = $data["latitude"];
            $add["start_longitude"] = $data["longitude"];
            self::addReport($add);
        }else{
            $update = $data;
            $update["update_time"] = $now;
            $current_distance = Utils::GetDistance($report->latitude,$report->longitude,$data["latitude"],$data["longitude"]);
            $distance = $report->distance+$current_distance;
            $update["distance"] = $distance;
            self::updateReport($report->id,$update);
        }
        return ["current_ditance"=>$current_distance,"distance"=>$distance];
    }

    public static function getReport($where){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;
        $db = $db->table($table);

        if(isset($where["drive_day"]) && $where["drive_day"]!=""){
            $db->where('drive_day','=', $where["drive_day"]);
        }
        if(isset($where["admin_user_id"]) && $where["admin_user_id"]!=""){
            $db->where('admin_user_id','=', $where["admin_user_id"]);
        }

        $res = $db->first();
        return $res;
    }

    public static function addReport($data){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;
        $id = $db->table($table)->insertGetId($data);
        return $id;
    }

    public static function updateReport($id,$data){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;
        $db->table($table)->where('id', $id)->update($data);
        return true;
    }

    public static function getList($where){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;

        $db = $db->table($table);

        if(isset($where["user_id"]) && !empty($where["user_id"])){
            $db->where('admin_user_id','=', $where["user_id"]);
        }

        if(isset($where["drive_day"]) && !empty($where["drive_day"])){
            $db->where('drive_day','=', $where["drive_day"]);
        }

        $res = $db->get();
        $data = [];
        if(!is_null($res)){
            $data = $res->toArray();
        }

        return $data;
    }
}