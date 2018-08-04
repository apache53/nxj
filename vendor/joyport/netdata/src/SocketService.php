<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 8/5/15
 * Time: 16:53
 */

namespace NetData;


use NetData\Lib\EmpFunc;
use NetData\Lib\LongSocket;
use NetData\Lib\ShortSocket;

class SocketService
{
    private function __construct(){}

    public function __clone(){}

    public static function start($params,$is_long = true)
    {
        if(empty($params)){
            return new EmpFunc();
        }
        if($is_long && function_exists('nireus_net_connect')){
            return LongSocket::connect($params);
        }
        return ShortSocket::connect($params);
    }
}