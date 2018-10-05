<?php
/**
 * Date: 2018/10/4
 * Time: 22:00
 */

namespace App\Http\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class UserBoatLog extends Model
{
    protected $connection = 'mysql';
    protected $table = 'user_boat_log';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected static $connection_name = 'mysql';
    protected static $table_name = 'user_boat_log_';

    /**
     *添加上报日志
     */
    public static function add($params){
        $log_model = new UserBoatLog();
        $time = time();
        $log_model->table = self::getSubTable();
        $log_model->admin_user_id = $params['admin_user_id'];
        $log_model->drive_day = $params['drive_day'];
        $log_model->boat_name = $params['boat_name'];
        $log_model->latitude = $params['latitude'];
        $log_model->longitude = $params['longitude'];
        $log_model->out_distance = $params['out_distance'];
        $log_model->scenic_id = $params['scenic_id'];
        $log_model->distance = $params['distance'];
        $log_model->speed = $params['speed'];
        $log_model->create_time = $time;
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
                $table->integer('admin_user_id')->default(0)->comment('用户id');
                $table->string('drive_day')->default('')->comment('行驶日期');
                $table->string('boat_name')->default('')->comment('游船名称');
                $table->decimal('latitude',10,6)->default(0)->comment('当前纬度');
                $table->decimal('longitude',10,6)->default(0)->comment('当前经度');
                $table->decimal('out_distance',10,2)->default(0)->comment('越界距离');
                $table->integer('scenic_id')->default(0)->comment('当前景点');
                $table->decimal('distance',10,2)->default(0)->comment('行驶距离');
                $table->decimal('speed',10,2)->default(0)->comment('速度');
                $table->integer('create_time')->default(0)->comment('创建时间');
                $table->index(['admin_user_id']);
            });
        }

    }
}