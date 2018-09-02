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

# 用户表
CREATE TABLE `admin_users` (
  `admin_user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_name` varchar(50) NOT NULL COMMENT '用户名',
  `real_name` varchar(50) DEFAULT '' COMMENT '用户姓名',
  `user_email` varchar(100) NOT NULL DEFAULT '' COMMENT '用户邮箱',
  `user_email_active` tinyint(2) DEFAULT 0 COMMENT '用户邮箱激活状态，1已激活',
  `user_mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '用户手机',
  `user_mobile_active` tinyint(2) DEFAULT 0 COMMENT '用户手机激活状态，1已激活',
  `user_password` varchar(100) DEFAULT '' COMMENT '用户密码，hash',
  `user_salt` varchar(50) DEFAULT '' COMMENT '用户密码盐',
  `role_id` int(11) DEFAULT '0' COMMENT '角色id，1管理员，2船长',
  `is_frozen` tinyint(2) DEFAULT 0 COMMENT '冻结状态，1是',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `last_login_time` int(11) DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` varchar(20) DEFAULT '' COMMENT '最后登录ip',
  PRIMARY KEY (`admin_user_id`),
  INDEX `key_user_name` (`user_name`)
) ENGINE=innodb AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

# 用户登录态表
CREATE TABLE `admin_users_session` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `admin_user_id` int(11) NOT NULL COMMENT '用户id',
  `login_token` varchar(100) NOT NULL COMMENT '登录token',
  `login_ip` varchar(50) DEFAULT '' COMMENT '登录ip',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `expire_time` int(11) DEFAULT '0' COMMENT '到期时间',
  PRIMARY KEY (`session_id`),
  INDEX `key_user` (`admin_user_id`),
  INDEX `key_login_token` (`login_token`)
) ENGINE=innodb AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

# 用户操作日志表
CREATE TABLE `user_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `admin_user_id` int(11) NOT NULL COMMENT '用户id',
  `user_name` int(11) NOT NULL COMMENT '用户名',
  `log_type` varchar(50) NOT NULL COMMENT '操作类型',
  `log_ip` varchar(50) DEFAULT '' COMMENT '操作ip',
  `before_value` varchar(255) DEFAULT '0' COMMENT '操作前值',
  `after_value` varchar(255) DEFAULT '0' COMMENT '操作后值',
  `create_time` int(11) DEFAULT '0' COMMENT '操作时间',
  `remark` varchar(255) DEFAULT '0' COMMENT '备注',
  PRIMARY KEY (`log_id`),
  INDEX `key_user` (`admin_user_id`)
) ENGINE=innodb AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



# 景点表
CREATE TABLE `scenic` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `scenic_name` varchar(50) NOT NULL COMMENT '景点名称',
  `scenic_img` varchar(255) DEFAULT '' COMMENT '景点图片',
  `latitude` decimal(10,6) DEFAULT '' COMMENT '纬度，范围为-90~90，负数表示南纬',
  `longitude` decimal(10,6) DEFAULT '' COMMENT '经度，范围为-180~180，负数表示西经',
  `voice_path` varchar(255) DEFAULT '' COMMENT '语音地址',
  `radius` int(11) DEFAULT '0' COMMENT '坐标半径',
  `pre_id` int(11) DEFAULT '0' COMMENT '上一个景点id',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  `remark` varchar(255) DEFAULT '0' COMMENT '备注',
  PRIMARY KEY (`id`),
  INDEX `index_pre_id` (`pre_id`)
) ENGINE=innodb AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;