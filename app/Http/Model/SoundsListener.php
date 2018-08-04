<?php


namespace App\Http\Model;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SoundsListener extends Model
{
    protected $connection = 'mysql';
    protected $table = 'sounds_listener';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected static $connection_name = 'mysql';
    protected static $table_name = 'sounds_listener';

    /**
     * 添加录音
     */
    public static function addSoundsListener($data){
        $now = time();

        $model = new SoundsListener();
        $model->create_time = $now;
        $model->sound_id = $data["sound_id"];
        $model->author = $data["author"];

        $res = $model->save();
        if ($res) {
            $data["sound_id"] = $model->sound_id;
        }
        return $data;
    }

    /**
     * 获取今天所有的听过录音
     */
    public static function getCurrentSoundsListener($where){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;

        $end = time();
        $start = $end-12*3600;

        $sounds = $db->table($table)
            ->where('create_time','>=', $start)
            ->where('create_time','<=', $end)
            ->where('author','=', $where["author"])
            ->get();
        $res = [];
        if(!empty($sounds) && !is_null($sounds)){
            $res = $sounds->toArray();
        }
        return $res;
    }
}
