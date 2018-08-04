<?php
/**
 * Created by PhpStorm.
 * User: garming
 * Date: 8/5/15
 * Time: 17:33
 */
require '../../vendor/autoload.php';

$params = [
    [
        'host' => '10.0.19.41',
        'port' => 1234,
    ]
];
/*$nd = \NetData\Instance::get();
$socket = \NetData\SocketService::start($params);
$nd->setProc(20000);

$nd->setProc(10001);
$nd->writeInt32(218);
$nd->writeInt32(52);
$nd->writeInt32(0);

$nd->writeString('["4","7"]');

$socket->call($nd);
//print_r($nd->readBool().'-');
//print_r($nd->readInt8().'-');
//print_r($nd->readInt16().'-');
//print_r($nd->readInt32().'-');
//print_r($nd->readFloat32().'-');
//print_r($nd->readFloat64().'-');
//print_r($nd->readString().'-');
$res = $nd->readString();
var_dump($res);*/



$nd = \NetData\Instance::get();
$socket = \NetData\SocketService::start($params);
print_r($socket);
$nd->setProc(20000);
$nd->writeBool(1);
$nd->writeInt8(97);
$nd->writeInt16(10086);
$nd->writeInt32(-10086);
$nd->writeFloat32(3.14);
$nd->writeFloat64(4.123456789);
$nd->writeString('中guoRen');
$socket->call($nd);

print_r($nd->readBool().' ');
print_r($nd->readInt8().' ');
print_r($nd->readInt16().' ');
print_r($nd->readInt32().' ');
print_r($nd->readFloat32().' ');
print_r($nd->readFloat64().' ');
print_r($nd->readString().' ');

//$nd->setProc(20001);
//$nd->writeBool(1);
//$nd->writeInt8(123);
//$nd->writeInt16(6666);
//$nd->writeInt32(-999);
//$nd->writeFloat32(5.14);
//$nd->writeFloat64(6.123456789);
//$nd->writeString('xxxx中guoRen123');
//
//$socket->call($nd);
//
//print_r($nd->readBool().' ');
//print_r($nd->readInt8().' ');
//print_r($nd->readInt16().' ');
//print_r($nd->readInt32().' ');
//print_r($nd->readFloat32().' ');
//print_r($nd->readFloat64().' ');
//print_r($nd->readString().' ');

//$nd = \NetData\Instance::get();
//$socket = \NetData\SocketService::start($params);
//$nd->setProc(20000);
//$nd->writeInt32(32);
//$nd->writeInt32(123);
//$nd->writeInt32(444);
//$nd->writeInt32(time());
//$nd->writeFloat32(100.123);
//$nd->writeString('CCCChannel');
//$nd->writeString('Accounttt');
//$nd->writeString('MyName');
//$nd->writeString('127.0.0.2');
//$nd->writeString('mycontnetHahaha');
//$socket->call($nd);

//print_r($nd->readBool().' ');
//print_r($nd->readInt8().' ');
//print_r($nd->readInt16().' ');
//print_r($nd->readInt32().' ');
//print_r($nd->readFloat32().' ');
//print_r($nd->readFloat64().' ');
//print_r($nd->readString().' ');