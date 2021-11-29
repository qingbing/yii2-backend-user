-- ----------------------------
--  Table structure for `{{%permission_api}}`
-- ----------------------------
CREATE TABLE `{{%permission_api}}` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `code` varchar(50) NOT NULL COMMENT '路径标识',
  `path` varchar(200) NOT NULL DEFAULT '' COMMENT 'API路径',
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '路径描述',
  `exts` json DEFAULT NULL COMMENT '扩展信息',

  `is_public` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否公共路径，公共路径不需要权限',
  `is_enable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用',

  `operate_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '操作IP',
  `operate_uid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '操作UID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_path` (`path`),
  KEY `idx_isPublic` (`is_public`),
  KEY `idx_isEnable` (`is_enable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后端api路径信息表';

-- ----------------------------
--  Table structure for `{{%permission_menu}}`
-- ----------------------------
CREATE TABLE `{{%permission_menu}}` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `type` varchar(20) NOT NULL COMMENT '类型[menu:菜单,footer:底部菜单,top:顶端菜单,button:按钮]',
  `path` varchar(200) NOT NULL DEFAULT '' COMMENT '菜单路径',
  `parent_code` varchar(50) NOT NULL DEFAULT '' COMMENT '上级标识',
  `code` varchar(50) NOT NULL COMMENT '菜单标识',
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '路径描述',
  `exts` json DEFAULT NULL COMMENT '扩展信息',

  `is_public` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否公共路径，公共路径不需要权限',
  `is_enable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用',

  `operate_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '操作IP',
  `operate_uid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '操作UID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_type_path` (`type`, `path`),
  UNIQUE KEY `uk_code` (`code`),
  KEY `idx_path` (`path`),
  KEY `idx_isPublic` (`is_public`),
  KEY `idx_isEnable` (`is_enable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='前端url路径信息表';


-- ----------------------------
--  Table structure for `{{%permission_menu_api}}`
-- ----------------------------
CREATE TABLE `{{%permission_menu_api}}` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `menu_code` varchar(50) NOT NULL COMMENT '菜单、按钮代码',
  `api_code` varchar(50) NOT NULL COMMENT 'api代码',

  `operate_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '操作IP',
  `operate_uid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '操作UID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_menuCode_apiCode` (`menu_code`, `api_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单、按钮拥有的api关联';

-- ----------------------------
--  Table structure for `{{%permission_role}}`
-- ----------------------------
CREATE TABLE `{{%permission_role}}` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `code` varchar(50) NOT NULL COMMENT '角色代码',
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '角色描述',

  `is_enable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用',

  `operate_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '操作IP',
  `operate_uid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '操作UID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色信息表';



-- ----------------------------
--  Table structure for `{{%permission_role_menu}}`
-- ----------------------------
CREATE TABLE `{{%permission_role_menu}}` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `role_code` varchar(50) NOT NULL COMMENT '角色代码',
  `menu_code` varchar(50) NOT NULL COMMENT '菜单、按钮代码',

  `operate_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '操作IP',
  `operate_uid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '操作UID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_roleCode_menuCode` (`role_code`, `menu_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色、菜单关联信息表';


-- ----------------------------
--  Table structure for `{{%permission_user_role}}`
-- ----------------------------
CREATE TABLE `{{%permission_user_role}}` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uid` bigint(20) unsigned NOT NULL COMMENT '用户ID',
  `role_code` varchar(50) NOT NULL COMMENT '角色代码',

  `operate_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '操作IP',
  `operate_uid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '操作UID',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_uid_roleCode` (`uid`, `role_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户、角色关联信息表';

