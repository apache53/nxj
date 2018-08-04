<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 8/20/15
 * Time: 22:04
 */

namespace NetData;


interface NetdataInterface
{
    public function getData();
    public function setData($response);

    public function setPackType($value);
    public function getPackType();

    public function setProcType($value);
    public function getProcType();

    public function setProc($value);
    public function getProc();

    public function setClientId($value);
    public function getClientId();

    public function readBool();
    public function readInt8();
    public function readInt16();
    public function readInt32();
    public function readFloat32();
    public function readFloat64();
    public function readString();
    public function writeBool($v);
    public function writeInt8($v);
    public function writeInt16($v);
    public function writeInt32($v);
    public function writeFloat32($v);
    public function writeFloat64($v);
    public function writeString($v);
//    public function readInt64();
//    public function readId();
//    public function writeInt64($v);
//    public function writeId($v);
}