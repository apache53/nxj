<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 8/5/15
 * Time: 16:52
 */

namespace NetData\Lib;


use NetData\SocketInterface;

class LongSocket implements SocketInterface
{
    private static $__flag = false;
    private static $__instance;

    private function __construct($params){
        if (!isset($params[0]['host']) || !isset($params[0]['port'])) {
            throw new \Exception('socket config error');
        }
        foreach($params as $k => $v){
            if(isset($v['host']) && isset($v['port'])){
                try{
                    $this->socket = nireus_net_connect($v['host'], $v['port']);
                    if($this->socket){
                        self::$__flag = true;
                        break;
                    }
                }catch (\Exception $e){
                    continue;
                }
            }
        }
    }
    public static function connect($params)
    {
        if (!(self::$__instance instanceof self))
        {
            self::$__instance = new self($params);
            if(!self::$__flag){
                throw new \Exception('connection error:there is not connection with all hosts');
            }
        }
        return self::$__instance;
    }

    public function __clone(){}

    /**
     * 同步访问
     * @param $netdata
     * @return mixed
     * @throws \Exception
     */
    public function call($netdata)
    {
        $response = nireus_net_call($this->socket, $netdata->getData());
        if(empty($response)){
            throw new \Exception('server reply error:server reply nothing or reply is illegal');
        }
        $netdata->setData($response);

    }

    /**
     * 异步访问
     * @param $netdata
     * @return mixed
     */
    public function send($netdata)
    {
        nireus_net_send($this->socket, $netdata->getData());
    }

    /**
     * 服务端下发
     * @return mixed
     */
    public function notify()
    {
    }
}