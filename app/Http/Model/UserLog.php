<?php

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class UserLog extends Model
{
    protected $connection = 'mysql';
    protected $table = 'user_log';
    public $timestamps = false;
    protected $primaryKey = 'log_id';

    protected static $connection_name = 'mysql';
    protected static $table_name = 'user_log_';

    /**
     *添加上报日志
     */
    public static function add($params){
        $log_model = new UserLog();
        $time = time();
        $log_model->table = self::getSubTable();
        $log_model->admin_user_id = $params['admin_user_id'];
        $log_model->user_name = $params['user_name'];
        $log_model->log_type = $params['log_type'];
        $log_model->log_ip = $params['log_ip'];
        $log_model->before_value = $params['before_value'];
        $log_model->after_value = $params['after_value'];
        $log_model->create_time = $time;
        $log_model->remark = $params['remark'];
        $res = $log_model->save();

        if($res){
            $log_id = $log_model->log_id;
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
                $table->increments('log_id')->comment('自增id');
                $table->integer('admin_user_id')->default(0)->comment('用户id');
                $table->string('user_name')->default('')->comment('用户名');
                $table->string('log_type')->default('')->comment('操作类型');
                $table->string('log_ip')->default('')->comment('操作ip');
                $table->text('before_value')->comment('操作前值');
                $table->text('after_value')->comment('操作后值');
                $table->integer('create_time')->default(0)->comment('操作时间');
                $table->string('remark')->default('')->comment('备注');
                $table->index(['admin_user_id']);
            });
        }

    }
}
