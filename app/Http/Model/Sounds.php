<?php


namespace App\Http\Model;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sounds extends Model
{
    protected $connection = 'mysql';
    protected $table = 'sounds';
    public $timestamps = false;
    protected $primaryKey = 'sound_id';

    protected static $connection_name = 'mysql';
    protected static $table_name = 'sounds';

    /**
     * 添加录音
     */
    public static function addSounds($data){
        $now = time();

        $model = new Sounds();
        $model->create_time = $now;
        $model->file_path = $data["file_path"];
        $model->file_name = $data["file_name"];
        $model->author = $data["author"];

        $res = $model->save();
        if ($res) {
            $data["sound_id"] = $model->sound_id;
        }
        return $data;
    }

    /**
     * 获取当前第一条的录音
     */
    public static function getCurrentSounds($where){
        $db = DB::connection(self::$connection_name);
        $table = self::$table_name;

        $end = time();
        $start = $end-12*3600;

        $sounds = $db->table($table)
            ->whereNotIn('sound_id', $where["sound_ids"])
            ->where('create_time','>=', $start)
            ->where('create_time','<=', $end)
            ->orderBy('create_time', 'asc')->first();

        return $sounds;
    }
}
