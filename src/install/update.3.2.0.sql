-- 创建GitHub认证关联表
CREATE TABLE `osfc_github_auths` (
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

-- 添加GitHub认证配置
INSERT INTO `osfc_configs` VALUES ('github_auth_enabled', '0');
INSERT INTO `osfc_configs` VALUES ('github_client_id', '');
INSERT INTO `osfc_configs` VALUES ('github_client_secret', '');
INSERT INTO `osfc_configs` VALUES ('github_auth_required_days', '180'); 