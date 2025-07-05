DROP TABLE IF EXISTS `kldns_configs`;
CREATE TABLE `kldns_configs` (
  `k` varchar(150) NOT NULL,
  `v` text,
  PRIMARY KEY (`k`),
  UNIQUE KEY `k` (`k`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `kldns_configs` VALUES ('array_mail', '{\"host\":\"smtp.qq.com\",\"port\":\"465\",\"encryption\":\"ssl\",\"username\":\"815856515@qq.com\",\"password\":\"jxvizloqrcxkertg\",\"test\":\"123456@qq.com\"}');
INSERT INTO `kldns_configs` VALUES ('array_user', '{\"reg\":\"1\",\"email\":\"1\",\"point\":\"100\"}');
INSERT INTO `kldns_configs` VALUES ('array_web', '{\"name\":\"\\u5feb\\u4e50\\u4e8c\\u7ea7\\u57df\\u540d\\u5206\\u53d1\",\"title\":\"\\u5feb\\u4e50\\u4e8c\\u7ea7\\u57df\\u540d\\u5206\\u53d1 - \\u514d\\u8d39\\u4e8c\\u7ea7\\u57df\\u540d\\u6ce8\\u518c\",\"keywords\":\"\\u5feb\\u4e50\\u4e8c\\u7ea7\\u57df\\u540d\\u5206\\u53d1,\\u514d\\u8d39\\u57df\\u540d,\\u514d\\u8d39\\u4e8c\\u7ea7\\u57df\\u540d,\\u514d\\u8d39\\u5907\\u6848\\u57df\\u540d\",\"description\":\"\\u5feb\\u4e50\\u4e8c\\u7ea7\\u57df\\u540d\\u5206\\u53d1\\u7cfb\\u7edf\\uff0c\\u63d0\\u4f9b\\u514d\\u8d39\\u4e8c\\u7ea7\\u57df\\u540d\\u5206\\u53d1\"}');
INSERT INTO `kldns_configs` VALUES ('html_header', '<div class=\"alert alert-primary\">\r\n本站提供免费二级域名用于测试、学习等，请勿将二级域名用于一切非法用途，一切责任自负！\r\n</div>');
INSERT INTO `kldns_configs` VALUES ('html_home', '本站提供免费二级域名用于测试、学习等，请勿将二级域名用于一切非法用途，一切责任自负！');
INSERT INTO `kldns_configs` VALUES ('index_urls', '源码下载|https://github.com/klsf/kldns\r\nQQ交流群|http://shang.qq.com/wpa/qunwpa?idkey=5ee7688c3971cb53cfacc63f9f7fc2f3ca6d41b6f13fc8bc002e310ed1e0a94a');
INSERT INTO `kldns_configs` VALUES ('reserve_domain_name', 'www,w,m,3g,4g,qq');
DROP TABLE IF EXISTS `kldns_dns_configs`;
CREATE TABLE `kldns_dns_configs` (
  `dns` varchar(150) NOT NULL,
  `config` varchar(1024) DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`dns`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `kldns_domain_records`;
CREATE TABLE `kldns_domain_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `did` int(11) unsigned NOT NULL DEFAULT '0',
  `record_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(32) NOT NULL,
  `value` varchar(255) NOT NULL,
  `line_id` varchar(32) NOT NULL DEFAULT '0',
  `line` varchar(255) DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  `checked_at` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `record_id` (`record_id`),
  KEY `did` (`did`),
  KEY `name` (`name`,`type`),
  KEY `checked_at` (`checked_at`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `kldns_domains`;
CREATE TABLE `kldns_domains` (
  `did` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dns` varchar(32) NOT NULL,
  `domain_id` varchar(50) NOT NULL,
  `domain` varchar(50) NOT NULL,
  `groups` varchar(1024) NOT NULL DEFAULT '0',
  `point` int(10) unsigned NOT NULL DEFAULT '0',
  `desc` text,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`did`),
  KEY `domain` (`domain`),
  KEY `domain_id` (`domain_id`),
  KEY `dns` (`dns`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `kldns_user_groups`;
CREATE TABLE `kldns_user_groups` (
  `gid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8;
INSERT INTO `kldns_user_groups` VALUES ('99', '管理组', '1555212209', '1555212209');
INSERT INTO `kldns_user_groups` VALUES ('100', '默认组', '1555212209', '1555235659');
DROP TABLE IF EXISTS `kldns_user_point_records`;
CREATE TABLE `kldns_user_point_records` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `action` varchar(32) NOT NULL,
  `point` int(11) NOT NULL,
  `rest` int(11) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `created_at` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `action` (`action`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `kldns_users`;
CREATE TABLE `kldns_users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gid` int(10) unsigned NOT NULL DEFAULT '100',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '0禁用 1待认证 2已认证',
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `sid` varchar(32) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `point` int(10) unsigned NOT NULL DEFAULT '0',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `gid` (`gid`),
  KEY `email` (`email`),
  KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;
INSERT INTO `kldns_users` VALUES ('99', '99', '1', 'admin', '$2y$10$v9PHTvnccjua/5FlAf/uFOVPprXxdWjoS54YnjmbQGGk8vDtxk9YS', 'tn38nVWJER1r0uj3oa222roN1E0sPYCDIUZIW30Yz6hR4U3DcHZU09l4gMsZ', '21c4bc5c23819b646aff4bb3196d6de5', null, '0', '1555212209', '1555408180');

DROP TABLE IF EXISTS `kldns_invitations`;
CREATE TABLE `kldns_invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `inviter_uid` int(11) NOT NULL COMMENT '邀请人UID',
  `invitee_uid` int(11) NOT NULL COMMENT '被邀请人UID',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0:未验证邮箱 1:已验证邮箱并奖励',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inviter_uid` (`inviter_uid`),
  KEY `invitee_uid` (`invitee_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邀请记录表';

DROP TABLE IF EXISTS `kldns_github_auths`;
CREATE TABLE `kldns_github_auths` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `github_id` varchar(100) NOT NULL COMMENT 'GitHub用户ID',
  `github_login` varchar(100) NOT NULL COMMENT 'GitHub登录名',
  `github_name` varchar(100) DEFAULT NULL COMMENT 'GitHub用户名',
  `github_email` varchar(100) DEFAULT NULL COMMENT 'GitHub邮箱',
  `github_created_at` varchar(50) NOT NULL COMMENT 'GitHub账号创建时间',
  `access_token` varchar(255) DEFAULT NULL COMMENT '访问令牌',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid` (`uid`),
  UNIQUE KEY `github_id` (`github_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='GitHub认证信息表';

-- GitHub认证配置
INSERT INTO `kldns_configs` VALUES ('github_auth_enabled', '0');
INSERT INTO `kldns_configs` VALUES ('github_client_id', '');
INSERT INTO `kldns_configs` VALUES ('github_client_secret', '');
INSERT INTO `kldns_configs` VALUES ('github_auth_required_days', '180');

-- 第三方登录表
DROP TABLE IF EXISTS `kldns_user_third`;
CREATE TABLE `kldns_user_third` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `platform` varchar(20) NOT NULL COMMENT '平台名称',
  `openid` varchar(64) NOT NULL COMMENT '第三方平台用户ID',
  `nickname` varchar(64) DEFAULT NULL COMMENT '第三方平台昵称',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像URL',
  `access_token` text DEFAULT NULL COMMENT '访问令牌',
  `refresh_token` text DEFAULT NULL COMMENT '刷新令牌',
  `expires_at` int(10) unsigned DEFAULT NULL COMMENT '令牌过期时间',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `platform_openid` (`platform`,`openid`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='第三方登录表';

-- Nodeloc认证配置
INSERT INTO `kldns_configs` VALUES ('nodeloc_auth_enabled', '0');
INSERT INTO `kldns_configs` VALUES ('nodeloc_client_id', '');
INSERT INTO `kldns_configs` VALUES ('nodeloc_client_secret', '');