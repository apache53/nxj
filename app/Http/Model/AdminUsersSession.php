<?php


namespace App\Http\Model;

use App\Library\Utils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminUsersSession extends Model
{
    protected $connection = 'mysql';
    protected $table = 'admin_users_session';
    public $timestamps = false;
    protected $primaryKey = 'session_id';

    protected static $connection_name = 'mysql';
    protected static $table_name = 'admin_users_session';

    public static function getLoginToken($data)
    {
        $token = self::getToken($data);
        $time = time();
        $expire_time = $time+config("constants.login_token_time");

        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;

        $before_token = '';
        $res = $db->table($table)
            ->where('admin_user_id','=', $data["admin_user_id"])->first();
        if(isset($res->session_id)){
            $before_token = $res->login_token;
            $db = DB::connection(self::$connection_name);
            $update = [
                "login_token" => $token,
                "expire_time" => $expire_time,
                "update_time" => $time,
                "login_ip" => $data["ip"],
            ];
            $db->table($table)->where('session_id', $res->session_id)->update($update);
        }else{
            $db = DB::connection(self::$connection_name);
            $insert = [
                "login_token" => $token,
                "expire_time" => $expire_time,
                "update_time" => $time,
                "login_ip" => $data["ip"],
                "admin_user_id" => $data["admin_user_id"],
                "create_time" => $time
            ];
            $session_id = $db->table($table)->insertGetId($insert);
        }

        return [
            "error" => 1,
            "msg" => "",
            "res" => [
                "token" => $token,
                "before_token" => $before_token,
                "expire_time" => $expire_time
            ]
        ];
    }

    private static function getToken($data){
        $token = md5(implode("#",$data)."#".time()."#".str_random(16));
        return $token;
    }

    public static function checkToken($token_param){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;

        $res = $db->table($table)
            ->where('login_token','=', $token_param["login_token"])->first();

        if(isset($res->session_id)){
            $admin_user_id = $res->admin_user_id;
            $expire_time = $res->expire_time;
            $now = time();
            if($now <= $expire_time){
                $db = DB::connection(self::$connection_name);
                $update = [
                    "update_time" => $now,
                    "login_ip" => $token_param["ip"],
                ];
                $db->table($table)->where('session_id', $res->session_id)->update($update);

                return [
                    "error" => 1,
                    "msg" => "成功",
                    "res" => [
                        "admin_user_id" => $admin_user_id
                    ]
                ];
            }

            return [
                "error" => 31,
                "msg" => "登录态失效，请重新登录",
                "res" => []
            ];
        }

        return [
            "error" => 30,
            "msg" => "登录态失效，请重新登录",
            "res" => []
        ];

    }
}