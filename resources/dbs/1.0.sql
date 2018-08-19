CREATE TABLE `sounds` (
  `sound_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `create_time` int(11) DEFAULT '0' COMMENT '上传时间',
  `file_path` varchar(255) DEFAULT '' COMMENT '文件路径',
  `file_name` varchar(255) DEFAULT '' COMMENT '文件名',
  `author` varchar(255) DEFAULT '' COMMENT '上传用户',
  PRIMARY KEY (`sound_id`),
  KEY `key_time` (`create_time`)
) ENGINE=innodb AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `sounds_listener` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `sound_id` int(11) NOT NULL COMMENT '录音id',
  `author` varchar(255) NOT NULL COMMENT '接收用户',
  `create_time` int(11) DEFAULT '0' COMMENT '接收时间',
  PRIMARY KEY (`id`),
  KEY `key_listener` (`author`)
) ENGINE=innodb AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

# 后台用户表
CREATE TABLE `admin_users` (
  `admin_user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_name` varchar(50) NOT NULL COMMENT '用户名',
  `real_name` varchar(50) DEFAULT '' COMMENT '用户姓名',
  `user_email` varchar(100) NOT NULL DEFAULT '' COMMENT '用户邮箱',
  `user_email_active` tinyint(2) DEFAULT 0 COMMENT '用户邮箱激活状态，1已激活',
  `user_password` varchar(100) DEFAULT '' COMMENT '用户密码，hash',
  `user_salt` varchar(50) DEFAULT '' COMMENT '用户密码盐',
  `role_id` int(11) DEFAULT '0' COMMENT '角色id，-1管理员，0无角色，其他为角色id',
  `is_frozen` tinyint(2) DEFAULT 0 COMMENT '冻结状态，1是',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `last_login_time` int(11) DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` varchar(20) DEFAULT '' COMMENT '最后登录ip',
  PRIMARY KEY (`admin_user_id`),
  INDEX `key_user_name` (`user_name`),
  INDEX `key_user_email` (`user_email`)
) ENGINE=innodb AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

# 后台用户角色表
CREATE TABLE `admin_role` (
  `admin_role_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `role_name` varchar(50) NOT NULL COMMENT '用户角色名',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`admin_role_id`)
) ENGINE=innodb AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

# 后台用户权限表
CREATE TABLE `admin_power` (
  `admin_power_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `power_name` varchar(50) NOT NULL COMMENT '权限名',
  `power_key` varchar(255) NOT NULL COMMENT '权限关键字',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`admin_power_id`)
) ENGINE=innodb AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

# 后台用户角色权限表
CREATE TABLE `admin_role_power` (
  `role_power_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `admin_role_id` int(11) NOT NULL COMMENT '角色id',
  `admin_power_id` int(11) NOT NULL COMMENT '权限id',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`role_power_id`)
) ENGINE=innodb AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;