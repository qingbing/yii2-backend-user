
-- ----------------------------
--  Table structure for `{{%user}}`
-- ----------------------------
CREATE TABLE `{{%user}}` (
  `uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `nickname` varchar(50) NOT NULL COMMENT '用户昵称',
  `real_name` varchar(30) NOT NULL DEFAULT '' COMMENT '姓名',
  `password` char(60) NOT NULL DEFAULT '' COMMENT '密码',
  `auth_key` varchar(32) NOT NULL DEFAULT 'auth_key' COMMENT '登录的auth_key',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别[0:保密,1:男士,2:女士]',
  `avatar` varchar(200) NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱账户',
  `mobile` varchar(15) NOT NULL DEFAULT '' COMMENT '手机号码',
  `phone` varchar(15) NOT NULL DEFAULT '' COMMENT '固定电话',
  `qq` varchar(15) NOT NULL DEFAULT '' COMMENT 'QQ',
  `id_card` varchar(18) NOT NULL DEFAULT '' COMMENT '身份证号',
  `birthday` date NOT NULL DEFAULT '1000-01-01' COMMENT '生日',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '联系地址',
  `zip_code` char(6) NOT NULL DEFAULT '' COMMENT '邮政编码',
  `is_enable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '用户启用状态',
  `is_super` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否超级用户',
  `refer_uid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '引荐人或添加人UID',
  `expire_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '有效IP地址',
  `expire_begin_at` date NOT NULL DEFAULT '1000-01-01' COMMENT '生效日期',
  `expire_end_at` date NOT NULL DEFAULT '1000-01-01' COMMENT '失效日期',
  `login_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `last_login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_at` datetime NOT NULL DEFAULT '1000-01-01 01:01:01' COMMENT '最后登录时间',
  `register_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '注册或添加IP',
  `register_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '注册或添加时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后数据更新时间',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uk_nickname` (`nickname`),
  KEY `idx_realName` (`real_name`),
  KEY `idx_email` (`email`),
  KEY `idx_mobile` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=100000000 DEFAULT CHARSET=utf8 COMMENT='用户主体';


-- ----------------------------
--  Table structure for `{{%user}}_account`
-- ----------------------------
CREATE TABLE `{{%user}}_account` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uid` bigint(20) unsigned NOT NULL COMMENT '用户ID',
  `type` varchar(20) NOT NULL COMMENT '账户类型:username,email,phone,name,weixin,qq等',
  `account` varchar(100) NOT NULL COMMENT '登录账户',
  `is_enable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态',
  `login_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `last_login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_at` datetime NOT NULL DEFAULT '1000-01-01 01:01:01' COMMENT '最后登录时间',
  `register_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '注册或添加时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后数据更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_type_account` (`type`, `account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='网站用户账户';


insert into `{{%user}}`
(`password`, `auth_key`,  `nickname`, `real_name`, `sex`, `avatar`, `email`, `mobile`, `phone`, `qq`, `id_card`, `birthday`, `address`, `zip_code`, `is_enable`, `is_super`, `refer_uid`, `expire_ip`, `expire_begin_at`, `expire_end_at`, `login_times`, `last_login_ip`, `last_login_at`, `register_ip`) values
('$2y$13$10RjkwZ8kbam8hRAYqAoxuxMHnnPScxDljb1wxrXlTniY8kIjDaBm', 'auth_key',  '追xin族', 'qingbing', '0', '', 'top-world@qq.com', '', '', '', '', '1000-01-01', '', '', '1', '1', '0', '', '1000-01-01', '1000-01-01', '0', '', '1000-01-01 01:01:01', '');


insert into `{{%user}}_account`
( `uid`, `type`, `account`, `is_enable`, `login_times`, `last_login_ip`, `last_login_at`) values
( '100000000', 'email', 'top-world@qq.com', '1', '0', '', '1000-01-01 01:01:01'),
( '100000000', 'username', 'qingbing', '1', '0', '', '1000-01-01 01:01:01');
