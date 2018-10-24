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
  `head_img` varchar(255) DEFAULT '' COMMENT '头像',
  `is_frozen` tinyint(2) DEFAULT 0 COMMENT '冻结状态，1是',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `last_login_time` int(11) DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` varchar(20) DEFAULT '' COMMENT '最后登录ip',
  PRIMARY KEY (`admin_user_id`),
  INDEX `key_user_name` (`user_name`)
) ENGINE=innodb AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
INSERT INTO `nxj`.`admin_users` (`admin_user_id`, `user_name`, `real_name`, `user_email`, `user_email_active`, `user_mobile`, `user_mobile_active`, `user_password`, `user_salt`, `role_id`, `is_frozen`, `create_time`, `last_login_time`, `last_login_ip`) VALUES ('1', 'xumin', '大兄弟', '', '0', '13344444444', '0', 'addfac31cfcd33f8aa86c5ea6808e011', '222222', '1', '0', '0', '1536155672', '127.0.0.1');


# 用户登录态表
CREATE TABLE `admin_users_session` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `admin_user_id` int(11) NOT NULL COMMENT '用户id',
  `login_token` varchar(100) NOT NULL COMMENT '登录token',
  `login_ip` varchar(50) DEFAULT '' COMMENT '登录ip',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
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
  `before_value` text DEFAULT null COMMENT '操作前值',
  `after_value` text DEFAULT null COMMENT '操作后值',
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
  `latitude` decimal(10,6) DEFAULT '0' COMMENT '纬度，范围为-90~90，负数表示南纬',
  `longitude` decimal(10,6) DEFAULT '0' COMMENT '经度，范围为-180~180，负数表示西经',
  `voice_path` varchar(255) DEFAULT '' COMMENT '语音地址',
  `radius` int(11) DEFAULT '0' COMMENT '坐标半径',
  `pre_id` int(11) DEFAULT '0' COMMENT '上一个景点id',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  `remark` varchar(500) DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  INDEX `index_pre_id` (`pre_id`)
) ENGINE=innodb AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

# 船长游船表
CREATE TABLE `user_boat` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `drive_day` varchar(50) NOT NULL COMMENT '行驶日期',
  `boat_name` varchar(50) default '' COMMENT '游船名称',
  `admin_user_id` int(11) NOT NULL COMMENT '用户id',
  `latitude` decimal(10,6) DEFAULT '0' COMMENT '当前纬度，范围为-90~90，负数表示南纬',
  `longitude` decimal(10,6) DEFAULT '0' COMMENT '当前经度，范围为-180~180，负数表示西经',
  `out_distance` decimal(10,2) DEFAULT 0 COMMENT '越界距离，0不越界，大于0则为超出最近景点的距离',
  `scenic_id` int(11) DEFAULT '0' COMMENT '当前景点',
  `start_latitude` decimal(10,6) DEFAULT '0' COMMENT '开始纬度，范围为-90~90，负数表示南纬',
  `start_longitude` decimal(10,6) DEFAULT '0' COMMENT '开始经度，范围为-180~180，负数表示西经',
  `distance` decimal(10,2) DEFAULT '0' COMMENT '行驶距离',
	`speed` decimal(10,2) DEFAULT '0' COMMENT '速度',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_key` (`drive_day`,`admin_user_id`),
  INDEX `index_user` (`admin_user_id`,`drive_day`)
) ENGINE=innodb AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

# 船长游船行驶日志表,按月分表
CREATE TABLE `user_boat_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `drive_day` varchar(50) NOT NULL COMMENT '行驶日期',
  `boat_name` varchar(50) default '' COMMENT '游船名称',
  `admin_user_id` int(11) NOT NULL COMMENT '用户id',
  `latitude` decimal(10,6) DEFAULT '0' COMMENT '当前纬度，范围为-90~90，负数表示南纬',
  `longitude` decimal(10,6) DEFAULT '0' COMMENT '当前经度，范围为-180~180，负数表示西经',
  `out_distance` decimal(10,2) DEFAULT 0 COMMENT '越界距离，0不越界，大于0则为超出最近景点的距离',
  `scenic_id` int(11) DEFAULT '0' COMMENT '当前景点',
  `distance` decimal(10,2) DEFAULT '0' COMMENT '行驶距离',
	`speed` decimal(10,2) DEFAULT '0' COMMENT '速度',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  INDEX `index_user` (`admin_user_id`)
) ENGINE=innodb AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;