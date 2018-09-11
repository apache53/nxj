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

    public static function checkUser($user_name,$password,$request_info=[]){
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
                $token_param = [
                    "admin_user_id" => $user->admin_user_id,
                    "ip" => $request_info["ip"],
                ];
                //获取登录态
                $token_res = AdminUsersSession::getLoginToken($token_param);
                $token = $token_res["res"]["token"];

                //更新用户表
                $user_data = [
                    "last_login_time" => time(),
                    "last_login_ip" => $request_info["ip"]
                ];
                self::updateUser($user->admin_user_id,$user_data);

                //记录操作日志
                $log = [
                    "admin_user_id" => $user->admin_user_id,
                    "user_name" => $user->user_name,
                    "log_type" => config('constants.log_login'),
                    "log_ip" => $request_info["ip"],
                    "before_value" => $token_res["res"]["before_token"],
                    "after_value" => $token,
                    "remark" => "登陆成功",
                ];
                UserLog::add($log);

                $default_img = env('HOST_IMG')."/file/image/head_default.png";
                $user_data = [
                    "user_name"=>$user->user_name,
                    "real_name"=>$user->real_name,
                    "user_mobile"=>$user->user_mobile,
                    "role_id"=>$user->role_id,
                    "head_img"=>empty($user->head_img)?$default_img:$user->head_img,
                    "login_token" => $token
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

    public static function updateUser($admin_user_id,$data){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;
        $update = $data;
        $db->table($table)->where('admin_user_id', $admin_user_id)->update($update);
    }

    public static function checkToken($login_token,$request_info=[]){
        //检查登录态
        $token_param = [
            "login_token" => $login_token,
            "ip" => $request_info["ip"],
        ];
        $token_res = AdminUsersSession::checkToken($token_param);
        if($token_res["error"]!=1){
            return $token_res;
        }

        $session_data = $token_res["res"];
        $admin_user_id = $session_data["admin_user_id"];

        $where = [
            "admin_user_id" => $admin_user_id,
            "is_frozen" => 0
        ];
        $user = self::getUser($where);
        if(!is_null($user)){
            $default_img = env('HOST_IMG')."/file/image/head_default.png";
            $user_data = [
                "admin_user_id" => $admin_user_id,
                "user_name"=>$user->user_name,
                "real_name"=>$user->real_name,
                "user_mobile"=>$user->user_mobile,
                "role_id"=>$user->role_id,
                "head_img"=>empty($user->head_img)?$default_img:$user->head_img,
            ];
            return ["error"=>1,"msg"=>"成功","res"=>$user_data];
        }

        return [
            "error" => 20,
            "msg" => "用户不存在",
            "res" => []
        ];
    }

    public static function logout($login_token,$request_info=[]){
        //检查登录态
        $token_param = [
            "login_token" => $login_token,
            "ip" => $request_info["ip"],
        ];
        $token_res = AdminUsersSession::checkToken($token_param);
        if($token_res["error"]!=1){
            return $token_res;
        }

        $res = AdminUsersSession::expired($token_res["res"]["admin_user_id"],$login_token);
        return $res;
    }

    public static function getList($where){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;

        $db = $db->table($table);

        if(isset($where["user_name"]) && !empty($where["user_name"])){
            $db->where('user_name','=', $where["user_name"]);
        }

        if(isset($where["real_name"]) && !empty($where["real_name"])){
            $db->where('real_name','=', $where["real_name"]);
        }
        if(isset($where["role_id"]) && !empty($where["role_id"])){
            $db->where('role_id','=', $where["role_id"]);
        }

        $res = $db->get();
        $data = [];
        if(!is_null($res)){
            $data = $res->toArray();
        }

        return $data;
    }

    public static function addUser($user_data,$user,$request_info){
        if(
            !isset($user_data["user_name"]) ||
            !isset($user_data["password"]) ||
            empty($user_data["user_name"]) ||
            empty($user_data["password"])
        ){
            return ["error"=>21,"msg"=>"用户名或密码不能为空","res"=>[]];
        }

        $user_name_res = Utils::isUsername($user_data["user_name"]);
        if($user_name_res["error"]!=1){
            return $user_name_res;
        }
        $password_res = Utils::isPassword($user_data["password"]);
        if($password_res["error"]!=1){
            return $password_res;
        }
        $password = self::getDbPassword($user_data["password"]);

        if(isset($user_data["user_mobile"]) && !empty($user_data["user_mobile"])){
            $mobile_res = Utils::isMobile($user_data["user_mobile"]);
            if($mobile_res["error"]!=1){
                return $mobile_res;
            }
        }

        //判断用户名是否存在
        $where = [
            "user_name" => $user_data["user_name"]
        ];
        $exists = self::getUser($where);
        if(!is_null($exists) && isset($exists->admin_user_id)){
            return ["error"=>22,"msg"=>"用户名已存在","res"=>[]];
        }

        $now = time();
        $data = [
            "user_name" => $user_data["user_name"],
            "real_name" => $user_data["real_name"],
            "user_mobile" => $user_data["user_mobile"],
            "user_password" => $password["db_pwd"],
            "user_salt" => $password["salt"],
            "role_id" => $user_data["role_id"],
            "is_frozen" => 0,
            "create_time" => $now,
            "head_img" => $user_data["head_img"],
        ];

        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;
        $admin_user_id = $db->table($table)->insertGetId($data);

        //记录操作日志
        $log = [
            "admin_user_id" => $user["admin_user_id"],
            "user_name" => $user["user_name"],
            "log_type" => config('constants.log_add_user'),
            "log_ip" => $request_info["ip"],
            "before_value" => "",
            "after_value" => json_encode($data),
            "remark" => "添加用户成功",
        ];
        UserLog::add($log);

        $return_data = [
            "admin_user_id" => $admin_user_id,
        ];
        return [
            "error"=>1,"msg"=>"添加用户成功","res"=>$return_data
        ];

    }

    public static function editUser($user_data,$user,$request_info){

        $data = [
            "real_name" => $user_data["real_name"],
            "role_id" => $user_data["role_id"],
            "is_frozen" => 0,
        ];

        if(
            !isset($user_data["user_id"]) ||
            empty($user_data["user_id"])
        ){
            return ["error"=>21,"msg"=>"用户有误","res"=>[]];
        }

        //判断用户是否存在
        $where = [
            "admin_user_id" => $user_data["user_id"]
        ];
        $exists = self::getUser($where);
        if(is_null($exists) || !isset($exists->admin_user_id)){
            return ["error"=>22,"msg"=>"用户不存在","res"=>[]];
        }

        if(isset($user_data["password"]) && $user_data["password"]!=""){
            $password_res = Utils::isPassword($user_data["password"]);
            if($password_res["error"]!=1){
                return $password_res;
            }
            $password = self::getDbPassword($user_data["password"]);
            $data["user_password"] = $password["db_pwd"];
            $data["user_salt"] = $password["salt"];
        }


        if(isset($user_data["user_mobile"]) && !empty($user_data["user_mobile"])){
            $mobile_res = Utils::isMobile($user_data["user_mobile"]);
            if($mobile_res["error"]!=1){
                return $mobile_res;
            }
            $data["user_mobile"] = $user_data["user_mobile"];
        }

        if(isset($user_data["head_img"]) && $user_data["head_img"]!=""){
            $data["head_img"] = $user_data["head_img"];
        }

        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;
        $db->table($table)->where('admin_user_id', $user_data["user_id"])->update($data);

        //记录操作日志
        $log = [
            "admin_user_id" => $user["admin_user_id"],
            "user_name" => $user["user_name"],
            "log_type" => config('constants.log_edit_user'),
            "log_ip" => $request_info["ip"],
            "before_value" => json_encode($exists),
            "after_value" => json_encode($data),
            "remark" => "编辑用户成功",
        ];
        UserLog::add($log);

        $return_data = [
            "admin_user_id" => $user_data["user_id"],
        ];
        return [
            "error"=>1,"msg"=>"编辑用户成功","res"=>$return_data
        ];

    }
}
