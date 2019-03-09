CREATE TABLE IF NOT EXISTS `wp_sms` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`from_type`  varchar(255) NULL  COMMENT '用途',
`code`  varchar(255) NULL  COMMENT '验证码',
`smsId`  varchar(255) NULL  COMMENT '短信唯一标识',
`phone`  varchar(255) NULL  COMMENT '手机号',
`cTime`  int(10) NULL  COMMENT '创建时间',
`status`  int(10) NULL  COMMENT '使用状态',
`plat_type`  int(10) NULL  COMMENT '平台标识',
`wpid`  int(10) NULL  DEFAULT 0 COMMENT '公众号id',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('sms','短信记录','0','','1','','1:基础','','','','','','10','','','1446107661','1446107661','1','MyISAM','sms');



