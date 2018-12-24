<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class DelHistoryResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delhistoryresource {param1} {param2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        echo date("Y-m-d H:i:s")." start\n";
        $end = strtotime(date('Y-m-d'))-10*86400;
        $start = $end-86400+1;
        $param1 = $this->argument('param1');
        $param2 = $this->argument('param2');
        if(!empty($param1)){
            $start = strtotime($param1);
        }
        if(!empty($param2)){
            $end = strtotime($param2);
        }

        $this->delUserVoice($end);

        echo date("Y-m-d H:i:s")." end\n";
        exit;

    }

    function delUserVoice($time){
        echo date("Y-m-d H:i:s")." delUserVoice ".date("Y-m-d H:i:s",$time)."\n";
        $dir = public_path()."/file/user_voice";
        $files = array();
        //opendir() 打开目录句柄
        if($handle = @opendir($dir)) {
            //readdir()从目录句柄中（resource，之前由opendir()打开）读取条目,
            // 如果没有则返回false
            while (($file = readdir($handle)) !== false) {
                //读取条目
                if ($file != ".." && $file != ".") {
                    //排除根目录
                    if (is_dir($dir . "/" . $file)) {
                        continue;
                    } else {
                        //获取文件修改日期
                        $file_time = filemtime($dir . "/" . $file);
                        $filetime = date('Y-m-d H:i:s', $file_time);
                        if($time>$file_time){

                            $res = unlink($dir."/".$file);
                            echo date("Y-m-d H:i:s")." del ".$file." ".$filetime." ".var_export($res,true)."\n";
                        }
                        //文件修改时间作为健值
                        $files[$filetime] = $file;
                    }
                }
            }
            @closedir($handle);
        }
        echo date("Y-m-d H:i:s")." left files:";
        print_r($files);
    }
}
