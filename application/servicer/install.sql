CREATE TABLE IF NOT EXISTS `wp_servicer` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`uid`  int(10) NULL  COMMENT '用户选择',
`truename`  varchar(255) NULL  COMMENT '真实姓名',
`mobile`  varchar(255) NULL  COMMENT '手机号',
`role`  varchar(100) NULL  COMMENT '授权列表',
`enable`  int(10) NULL  DEFAULT 1 COMMENT '是否启用',
`wpid`  int(10) NOT NULL  COMMENT 'wpid',
`update_at`  int(10) NOT NULL  COMMENT '更新时间',
`pbid`  int(10) NULL  DEFAULT 0 COMMENT '公众号id',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('servicer','授权用户','0','','1','["uid","truename","mobile","role","enable"]','1:基础','','','','','truename:姓名\r\nrole:权限列表\r\nnickname:微信名称\r\nenable|get_name_by_status:是否启用\r\nids:操作:set_enable?id=[id]&enable=[enable]|改变启用状态,[EDIT]|编辑,[DELETE]|删除','10','truename:请输入姓名搜索','','1443066649','1490713267','1','MyISAM','servicer');



