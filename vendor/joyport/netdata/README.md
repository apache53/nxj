# PHP Socket Library
=======================

## Change Log
### v2.0.4
修复一些在5.4下出现的bug

### v2.0.2
1. 新增netdata新增可以设置包头的函数：
```
$nd->setPackType($value);
$nd->getPackType();

$nd->setProcType($value);
$nd->getProcType();

$nd->setProc($value);
$nd->getProc();

$nd->setClientId($value);
$nd->getClientId();
```

## Requirements

- php5.4+

## Installation

  - composer.json  
  ```
  "require":
        {
             "joyport/netdata":  "v2.0.4"
        }
  ```
  如果是新建composer.json
  ```
    {
      "config": {

      },
      "repositories": [
        {"type": "composer", "url": "http://packagist.phpcomposer.com"}
      ],
      "minimum-stability": "dev",
      "require": {
         "joyport/netdata":  "v2.0.4"
      }
    }
  ```
  - command  
  ```composer install``` or ```composer update```


## Introduction

- socket连接c++服务器的php封装库
- 2.0.0版本为适应新的底层连接确认
- 降低了php的最低版本要求

## How to use Muticall

```
<?php
    $params = [
        [
            'host' => '127.0.0.1',
            'port' => 1234,
        ]
    ];
    $nd = \NetData\Instance::get();
    $socket = \NetData\SocketService::start($params);
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
?>
```
## License
MIT License see http://mit-license.org/
