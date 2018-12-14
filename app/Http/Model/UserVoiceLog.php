<?php
/**
 * User: xumin
 * Date: 2018/12/8
 * Time: 15:18
 */

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UserVoiceLog extends Model
{
    protected $connection = 'mysql';
    protected $table = 'user_voice_log';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected static $connection_name = 'mysql';
    protected static $table_name = 'user_voice_log_';

    /**
     *添加上报日志
     */
    public static function add($params){
        $log_model = new UserVoiceLog();

        $log_model->table = self::getSubTable();
        $log_model->from_user = $params['from_user'];
        $log_model->to_user = $params['to_user'];
        $log_model->voice_path = $params['voice_path'];
        $log_model->voice_time = $params['voice_time'];
        $log_model->create_time = isset($params['create_time'])?$params['create_time']:time();
        $log_model->client_ip = isset($params['client_ip'])?$params['client_ip']:"";
        $log_model->is_played = isset($params['is_played'])?$params['is_played']:0;
        $res = $log_model->save();

        if($res){
            $log_id = $log_model->id;
        }
        return $log_id;
    }

    //获取分表名
    private static function getSubTable(){
        $table_name = self::$table_name.date('Ym');
        self::createSubTable($table_name);
        return $table_name;
    }

    //创建分表
    private static function createSubTable($value){
        $db = Schema::connection(self::$connection_name);
        $has_table = $db->hasTable($value);
        if($has_table==0){
            $db->create($value, function($table){
                $table->increments('id')->comment('自增id');
                $table->integer('from_user')->default(0)->comment('用户id');
                $table->integer('to_user')->default(0)->comment('用户id,全体为0，全体管理员为-1，全体船长-2');
                $table->string('voice_path')->default('')->comment('语音地址');
                $table->integer('voice_time')->default(0)->comment('语音时长');
                $table->integer('create_time')->default(0)->comment('创建时间');
                $table->string('client_ip')->default('')->comment('操作ip');
                $table->integer('is_played')->default(0)->comment('1：已播放，0：未播放');
                $table->index(['from_user']);
                $table->index(['to_user']);
                $table->index(['create_time']);
            });
        }

    }

    public static function sendVoice($voice,$user,$request_info){

        $all_user_arr = [0,-1,-2];
        if(!in_array($voice["to_user"],$all_user_arr)){
            $where = [
                "admin_user_id" => $voice["to_user"]
            ];
            $to_user = AdminUsers::getUser($where);
            if(is_null($to_user) || !isset($to_user->admin_user_id)){
                return [
                    "error"=>21,"msg"=>"找不到该用户","res"=>[]
                ];
            }
        }

        $res = self::add($voice);
        if($res>0){
            return [
                "error"=>1,"msg"=>"成功","res"=>[]
            ];
        }

        return [
            "error"=>22,"msg"=>"保存失败","res"=>[]
        ];

    }

    public static function sendUserList($where,$user,$request_info){
        $start_month = date('Ym',$where["start_time"]);
        $end_month = date('Ym',$where["end_time"]);
        $result = self::getSendUserList($end_month,$where);
        if($end_month!=$start_month){
            $result_start = self::getSendUserList($start_month,$where);
            foreach($result_start as $k=>$v){
                if(!isset($result[$k])){
                    $result[$k] = $v;
                }
            }
        }
        $data = [];
        foreach($result as $k=>$v){
            $data[$k]["user_id"] = $v->to_user;
            $data[$k]["user_name"] = $v->user_name;
            $data[$k]["real_name"] = $v->real_name;
            $data[$k]["head_img"] = $v->head_img;
            $data[$k]["last_time"] = $v->last_time;
        }
        return ["error"=>1,"msg"=>"","res"=>$result];
    }

    public static function  getSendUserList($month,$where){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name.$month;

        $db = $db->table($table);
        $db->select(DB::raw('max('.$table.'.create_time) as last_time, to_user,admin_users.user_name as user_name,admin_users.real_name as real_name,admin_users.head_img as head_img'));
        $db->leftJoin('admin_users', 'admin_users.admin_user_id', '=', $table.'.to_user');
        if(isset($where["from_user"])){
            $db->where('from_user','=', $where["from_user"]);
        }
        if(isset($where["start_time"])){
            $db->where($table.'.create_time','>=', $where["start_time"]);
        }
        if(isset($where["end_time"])){
            $db->where($table.'.create_time','<=', $where["end_time"]);
        }
        $db->groupBy('to_user');
        $db->groupBy('admin_users.user_name');
        $db->groupBy('admin_users.real_name');
        $db->groupBy('admin_users.head_img');
        $db->orderBy('last_time', 'desc');
        $res = $db->get();
        $data = [];
        if(!is_null($res)){
            $data = $res->toArray();
            $data_arr = [];
            $default_img = env('HOST_IMG')."/file/image/head_default.png";
            foreach($data as $k=>$v){
                if($v->to_user==0){
                    $v->user_name = '';
                    $v->real_name = '全体用户';
                    $v->head_img = $default_img;
                }else if($v->to_user==-1){
                    $v->user_name = '';
                    $v->real_name = '全体管理员';
                    $v->head_img = $default_img;
                }else if($v->to_user==-2){
                    $v->user_name = '';
                    $v->real_name = '全体船长';
                    $v->head_img = $default_img;
                }
                $data_arr[] = $v;

            }
            return $data_arr;
        }

        return $data;
    }

    public static function sendList($where,$user,$request_info){
        $start_month = date('Ym',$where["start_time"]);
        $end_month = date('Ym',$where["end_time"]);
        $result = self::getSendVoiceList($end_month,$where);
        if($end_month!=$start_month){
            $result_start = self::getSendVoiceList($start_month,$where);
            foreach($result_start as $k=>$v){
                if(!isset($result[$k])){
                    $result[$k] = $v;
                }
            }
        }

        $data = [];
        $count = 0;
        foreach($result as $k=>$v){
            $count++;
            $data["user_id"] = $v->to_user;
            $data["user_name"] = $v->user_name;
            $data["real_name"] = $v->real_name;
            $data["head_img"] = $v->head_img;
            $data["list"][$k]["create_time"] = $v->create_time;
            $data["list"][$k]["voice_path"] = $v->voice_path;
            $data["list"][$k]["voice_time"] = $v->voice_time;
            $data["list"][$k]["is_played"] = $v->is_played;
        }
        $data["count"] = $count;
        return ["error"=>1,"msg"=>"","res"=>$data];
    }

    public static function getSendVoiceList($month,$where){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name.$month;

        $db = $db->table($table);
        $db->select(DB::raw($table.'.create_time as create_time,voice_path,voice_time,is_played, to_user,admin_users.user_name as user_name,admin_users.real_name as real_name,admin_users.head_img as head_img'));
        $db->leftJoin('admin_users', 'admin_users.admin_user_id', '=', $table.'.to_user');
        if(isset($where["from_user"])){
            $db->where('from_user','=', $where["from_user"]);
        }
        if(isset($where["start_time"])){
            $db->where($table.'.create_time','>=', $where["start_time"]);
        }
        if(isset($where["end_time"])){
            $db->where($table.'.create_time','<=', $where["end_time"]);
        }
        $db->where('to_user','=', $where["to_user"]);
        $db->orderBy('create_time', 'desc');
        $res = $db->get();
        $data = [];
        if(!is_null($res)){
            $data = $res->toArray();
            $data_arr = [];
            $default_img = env('HOST_IMG')."/file/image/head_default.png";
            foreach($data as $k=>$v){
                if($v->to_user==0){
                    $v->user_name = '';
                    $v->real_name = '全体用户';
                    $v->head_img = $default_img;
                }else if($v->to_user==-1){
                    $v->user_name = '';
                    $v->real_name = '全体管理员';
                    $v->head_img = $default_img;
                }else if($v->to_user==-2){
                    $v->user_name = '';
                    $v->real_name = '全体船长';
                    $v->head_img = $default_img;
                }
                $data_arr[] = $v;

            }
            return $data_arr;
        }

        return $data;
    }

    public static function receiveList($where,$user,$request_info){
        $start_month = date('Ym',$where["start_time"]);
        $end_month = date('Ym',$where["end_time"]);
        $result = self::getReceiveVoiceList($end_month,$where);
        if($end_month!=$start_month){
            $result_start = self::getReceiveVoiceList($start_month,$where);
            foreach($result_start as $k=>$v){
                if(!isset($result[$k])){
                    $result[$k] = $v;
                }
            }
        }

        $data = [];
        $count = 0;
        foreach($result as $k=>$v){
            $count++;
            $data["list"][$k]["send_user_id"] = $v->from_user;
            $data["list"][$k]["send_user_name"] = $v->user_name;
            $data["list"][$k]["send_real_name"] = $v->real_name;
            $data["list"][$k]["send_head_img"] = $v->head_img;
            $data["list"][$k]["create_time"] = $v->create_time;
            $data["list"][$k]["voice_path"] = $v->voice_path;
            $data["list"][$k]["voice_time"] = $v->voice_time;
            $data["list"][$k]["is_played"] = $v->is_played;
        }
        $data["count"] = $count;
        return ["error"=>1,"msg"=>"","res"=>$data];
    }

    public static function getReceiveVoiceList($month,$where){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name.$month;

        $db = $db->table($table);
        $db->select(DB::raw($table.'.create_time as create_time,voice_path,voice_time,is_played, to_user,from_user,admin_users.user_name as user_name,admin_users.real_name as real_name,admin_users.head_img as head_img'));
        $db->leftJoin('admin_users', 'admin_users.admin_user_id', '=', $table.'.from_user');

        if(isset($where["start_time"])){
            $db->where($table.'.create_time','>=', $where["start_time"]);
        }
        if(isset($where["end_time"])){
            $db->where($table.'.create_time','<=', $where["end_time"]);
        }
        $db->whereIn('to_user', [$where["to_user"], -2, 0]);
        $db->where('is_played','=', $where["is_played"]);
        $db->orderBy('create_time', 'desc');
        $res = $db->get();
        $data = [];
        if(!is_null($res)){
            $data = $res->toArray();
            $data_arr = [];
            foreach($data as $k=>$v){
                $data_arr[] = $v;

            }
            return $data_arr;
        }

        return $data;
    }

    public static function voicePlayed($where,$user,$request_info){
        $start_month = date('Ym',$where["start_time"]);
        $end_month = date('Ym',$where["end_time"]);

        self::updateVoicePlayed($end_month,$where);
        if($end_month!=$start_month) {
            self::updateVoicePlayed($start_month, $where);
        }
        return true;
    }

    public static function updateVoicePlayed($month,$where){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name.$month;

        $db = $db->table($table);
        $db->where($table.'.create_time','>=', $where["start_time"]);
        $db->where($table.'.create_time','<=', $where["end_time"]);
        $db->where($table.'.is_played','=', 0);
        $db->update(['is_played' => 1]);

    }

}