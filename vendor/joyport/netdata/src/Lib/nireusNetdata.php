<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 8/20/15
 * Time: 22:13
 */

namespace NetData\Lib;


use NetData\NetdataInterface;

class nireusNetdata implements NetdataInterface
{
    private $pack_type = 1;
    private $proc_type = 0;
    private $proc;//协议号
    private $client_order;
    private $client_id;
    private $data;
    private $response;
    private $read_point = 0;
    private $put_datas = [];


    public function __construct(){}

    function getData()
    {
        $net_data = net_data_create(intval($this->proc));

        net_data_set_pack_type($net_data,$this->pack_type);

        if(!is_null($this->proc)){
            net_data_set_proc($net_data,$this->proc);
        }

        net_data_set_proc_type($net_data,$this->proc_type);

        if(!is_null($this->client_id)){
            net_data_set_client_id($net_data,$this->client_id);
        }

        if(empty($this->put_datas)){
            throw new \Exception("there is no data to send");
        }
        foreach($this->put_datas as $k => $v){
            if(is_array($v)){
                foreach($v as $func => $value){
                    call_user_func($func,$net_data,$value);
                }
            }
        }

        $this->data = $net_data;
        return $net_data;
    }

    function setData($response)
    {
        $this->data = null;
        $this->response = $response;
    }

    function setProc($proc)
    {
        $this->proc = intval($proc);
    }

    function getProc()
    {
        return $this->proc;
    }
    public function setPackType($value)
    {
        $this->pack_type = $value;
    }
    public function getPackType()
    {
        return $this->pack_type;
    }

    public function setProcType($value)
    {
        $this->proc_type = $value;
    }
    public function getProcType()
    {
        return $this->proc_type;
    }

    public function setClientId($value)
    {
        $this->client_id = intval($value);
    }
    public function getClientId()
    {
        return $this->client_id;
    }
    public function readBool()
    {
        return net_data_read_bool($this->response);
    }

    public function readInt8()
    {
        return net_data_read_int8($this->response);
    }

    public function readInt16()
    {
        return net_data_read_int16($this->response);
    }

    public function readInt32()
    {
        return net_data_read_int32($this->response);
    }

    public function readFloat32()
    {
        return net_data_read_float32($this->response);
    }

    public function readFloat64()
    {
        return net_data_read_float64($this->response);
    }

    public function readString()
    {
        return net_data_read_string($this->response);
    }

    public function writeBool($v)
    {
        $this->put_datas[]['net_data_write_bool'] = $v;
    }

    public function writeInt8($v)
    {
        $this->put_datas[]['net_data_write_int8'] = $v;
    }

    public function writeInt16($v)
    {
        $this->put_datas[]['net_data_write_int16'] = $v;
    }

    public function writeInt32($v)
    {
        $this->put_datas[]['net_data_write_int32'] = $v;
    }

    public function writeFloat32($v)
    {
        $this->put_datas[]['net_data_write_float32'] = $v;
    }

    public function writeFloat64($v)
    {
        $this->put_datas[]['net_data_write_float64'] = $v;
    }

    public function writeString($v)
    {
        $this->put_datas[]['net_data_write_string'] = $v;
    }
//    public function readInt64()
//    {
//        //TODO
//    }
//    public function readId()
//    {
//        //TODO
//    }
//    public function writeInt64($v)
//    {
//        //TODO
//    }
//    public function writeId($v)
//    {
//        //TODO
//    }
}