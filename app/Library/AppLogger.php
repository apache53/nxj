<?php
namespace App\Library;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\WebProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Formatter\LineFormatter;
use Request;
use Sentry;
use Config;

/**
* UserLog
*
* Custom monolog logger for CMS user : DEBUG,INFO,NOTICE,WARNING,ERROR,CRITICAL,ALERT,EMERGENCY
*
* @author
*/
class AppLogger {


    public static function info($log,$path="app",$prefix="log")
    {
        self::write($log,$path,$prefix,Logger::INFO);
    }

    private static function write($logtext='',$path="app",$prefix="log",$level=Logger::INFO)
    {
        $log = new Logger('applog');
        // handler init, making days separated logs
        $handler = new RotatingFileHandler(storage_path().'/logs/'.$path.'/'.$prefix, 0, $level);
        // formatter, ordering log rows
        //$handler->setFormatter(new LineFormatter("[%datetime%]: %message% %extra% %context%\n"));
        $handler->setFormatter(new LineFormatter("[%datetime%]: %message% %extra% \n"));
        // add handler to the logger
        $log->pushHandler($handler);
        // processor, adding URI, IP address etc. to the log
        $log->pushProcessor(new WebProcessor);
        // processor, memory usage
        //$log->pushProcessor(new MemoryUsageProcessor);

        if(is_array($logtext)){
            $logtext = json_encode($logtext);
        }
        $log->addInfo($logtext);
    }
}