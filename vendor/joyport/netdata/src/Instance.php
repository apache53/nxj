<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 8/20/15
 * Time: 22:03
 */

namespace NetData;


use NetData\Lib\Netdata;
use NetData\Lib\nireusNetdata;

class Instance
{
    private function __construct(){}

    public function __clone(){}

    public static function get($is_long = true)
    {
        if($is_long && function_exists('nireus_net_connect')){
            return new nireusNetdata();
        }
        return new Netdata();
    }
}