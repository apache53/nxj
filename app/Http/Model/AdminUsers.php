<?php


namespace App\Http\Model;

use App\Library\Utils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminUsers extends Model
{
    protected $connection = 'mysql';
    protected $table = 'admin_users';
    public $timestamps = false;
    protected $primaryKey = 'admin_user_id';

    protected static $connection_name = 'mysql';
    protected static $table_name = 'admin_users';

    public static function checkUser($user_name,$password){
        $where = [
            "user_name" => $user_name,
            "is_frozen" => 0
        ];
        $user = self::getUser($where);
        if(!is_null($user)){

            $user_pwd = $user->user_password;
            $salt = $user->user_salt;

            $res_pwd = self::getDbPassword($password,$salt);
            if($res_pwd["db_pwd"] == $user_pwd){
                $user_data = [
                    "admin_user_id"=>$user->admin_user_id,
                    "user_name"=>$user->user_name,
                    "real_name"=>$user->real_name,
                    "user_email"=>$user->user_email,
                    "user_email_active"=>$user->user_email_active,
                    "role_id"=>$user->role_id
                ];
                return ["error"=>1,"msg"=>"成功","res"=>$user_data];
            }
            return ["error"=>21,"msg"=>"用户名或密码错误","res"=>[]];
        }

        return ["error"=>20,"msg"=>"用户名或密码错误","res"=>[]];
    }

    private static function getDbPassword($password,$salt=""){
        if($salt == ""){
            $salt = Utils::getRandChar(16);
        }
        $db_pwd = md5($salt.$password);
        return [
            "db_pwd" => $db_pwd,
            "salt" => $salt
        ];
    }

    /**
     * 获取用户
     */
    public static function getUser($where){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;

        $db = $db->table($table);

        if(isset($where["admin_user_id"])){
            $db->where('admin_user_id','=', $where["admin_user_id"]);
        }

        if(isset($where["user_name"])){
            $db->where('user_name','=', $where["user_name"]);
        }

        if(isset($where["is_frozen"])){
            $db->where('is_frozen','=', $where["is_frozen"]);
        }

        $res = $db->first();

        return $res;
    }
}
