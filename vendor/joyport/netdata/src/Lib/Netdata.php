<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 8/20/15
 * Time: 11:33
 */

namespace NetData\Lib;


use NetData\NetdataInterface;

class Netdata implements NetdataInterface
{
    private $pack_type = 1;
    private $proc_type = 0;//协议类型，为0就好
    private $proc;//协议号
    private $client_order;//区分包，自增
    private $client_id;//用于区分链接，可忽略
    private $data;
    private $response;
    private $read_point = 0;
    public  $socket;

    function getData()
    {
        $send = pack("CCVVVV", $this->pack_type, $this->proc_type, $this->proc, $this->client_order, $this->client_id,
                strlen($this->data)) . $this->data;
        $this->data = null;
        return $send;
    }

    function setData($response = null)
    {
        $this->read_point = 0;
        try{
            $response = $this->readHandler(18);//read the header
            if (empty($response) || strlen($response) < 18) {
                throw new \Exception('Illegal response');
            }

            $this->pack_type    = unpack("C",substr($response, 0, 1))[1];
            $this->proc_type    = unpack("C",substr($response, 1, 1))[1];
            $this->proc         = unpack("V",substr($response, 2, 4))[1];
            $this->client_order = unpack("V",substr($response, 6, 4))[1];
            $this->client_id    = unpack("V",substr($response, 10, 4))[1];
            $len                = unpack("V",substr($response, 14, 4))[1];

            if(!is_numeric($len) || empty($len)){
                throw new \Exception('Illegal response header');
            }

            $len = intval($len);
            $this->response = $this->readHandler($len);
        }catch (\Exception $e){
            //pass
        }
    }

    public function setProc($proc)
    {
        $this->proc = $proc;
    }

    public function getProc()
    {
        return $this->proc;
    }
    public function setPackType($value)
    {
        $this->pack_type = intval($value);
    }
    public function getPackType()
    {
        return $this->pack_type;
    }

    public function setProcType($value)
    {
        $this->proc_type = intval($value);
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
        if (!isset($this->response[$this->read_point])) {
            return null;
        }
        $b = hexdec(bin2hex($this->response[$this->read_point]));
        ++$this->read_point;
        return $b;
    }

    public function readInt8()
    {
        if (!isset($this->response[$this->read_point])) {
            return null;
        }
        $rs = unpack("C", $this->response[$this->read_point]);
        ++$this->read_point;
        return $rs[1];
    }

    public function readInt16()
    {
        if (!isset($this->response[$this->read_point]) || !isset($this->response[$this->read_point+1])) {
            return null;
        }
        $n = $this->read_point;
        $rs = $this->response[$n + 1] . $this->response[$n];
        $this->read_point += 2;
        return hexdec(bin2hex($rs));
    }

    public function readInt32()
    {
        if (!isset($this->response[$this->read_point]) || !isset($this->response[$this->read_point+3])) {
            return null;
        }
        $n = $this->read_point;
        $rs = $this->response[$n + 3] . $this->response[$n + 2] . $this->response[$n + 1] . $this->response[$n];
        $this->read_point += 4;
        return hexdec(bin2hex($rs));
    }

    public function readFloat32()
    {
        if (!isset($this->response[$this->read_point]) || !isset($this->response[$this->read_point+3])) {
            return null;
        }
        $n = $this->read_point;
        $rs = $this->response[$n] . $this->response[$n + 1] . $this->response[$n + 2] . $this->response[$n + 3];
        $this->read_point += 4;
        $binarydata32 = bin2hex($rs);
        return unpack("f", pack('H*', $binarydata32))[1];
    }

    public function readFloat64()
    {
        if (!isset($this->response[$this->read_point]) || !isset($this->response[$this->read_point+7])) {
            return null;
        }
        $n = $this->read_point;
        $d = $this->response;
        $rs = $d[$n] . $d[$n + 1] . $d[$n + 2] . $d[$n + 3] . $d[$n + 4] . $d[$n + 5] . $d[$n + 6] . $d[$n + 7];
        $this->read_point += 8;
        $binarydata64 = bin2hex($rs);
        return unpack("d", pack('H*', $binarydata64))[1];
    }

    public function readString()
    {
        $len = intval($this->readInt16());
        if($len < 1){
            return '';
        }
        if (!isset($this->response[$this->read_point]) || !isset($this->response[$this->read_point+$len-1])) {
            return null;
        }
        $n = $this->read_point;
        $d = $this->response;
        $str = null;
        for ($i = 0; $i < $len; $i++) {
            $str .= $d[$this->read_point + $i];
        }
        $this->read_point += $len;

        return $str;
    }

    public function writeBool($v)
    {
        if ($v) {
            $this->data .= "\x01";
        } else {
            $this->data .= "\x00";
        }

    }

    public function writeInt8($v)
    {
        $this->data .= pack("C", $v);
    }

    public function writeInt16($v)
    {
        $this->data .= pack("v", $v);
    }

    public function writeInt32($v)
    {
        $this->data .= pack("V", $v);
    }

    public function writeFloat32($v)
    {
        $this->data .= pack("f", $v);
    }

    public function writeFloat64($v)
    {
        $this->data .= pack("d", $v);
    }

    public function writeString($v)
    {
        $this->writeInt16(strlen($v));
        $this->data .= $v;
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
    private function readHandler($len){
        $response = socket_read($this->socket,$len);
        while(1){
            $rs_len = strlen($response);
            if($rs_len < $len){
                $response .= socket_read($this->socket,($len - $rs_len));
            }else{
                break;
            }
        }
        return $response;
    }
}