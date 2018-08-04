<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 8/5/15
 * Time: 16:10
 */

namespace NetData;


interface SocketInterface
{
    public static function connect($params);

    /**
     * 同步访问
     * @return mixed
     */
    public function call($netdata);

    /**
     * 异步访问
     * @return mixed
     */
    public function send($netdata);

    /**
     * 服务端下发
     * @return mixed
     */
    public function notify();
}