CREATE TABLE `sounds` (
  `sound_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `create_time` int(11) DEFAULT '0' COMMENT '上传时间',
  `file_path` varchar(255) DEFAULT '' COMMENT '文件路径',
  `file_name` varchar(255) DEFAULT '' COMMENT '文件名',
  `author` varchar(255) DEFAULT '' COMMENT '上传用户',
  PRIMARY KEY (`sound_id`),
  KEY `key_time` (`create_time`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `sounds_listener` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `sound_id` int(11) NOT NULL COMMENT '录音id',
  `author` varchar(255) NOT NULL COMMENT '接收用户',
  `create_time` int(11) DEFAULT '0' COMMENT '接收时间',
  PRIMARY KEY (`id`),
  KEY `key_listener` (`author`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;