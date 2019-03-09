CREATE TABLE IF NOT EXISTS `wp_custom_menu` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`pid`  int(10) NULL  COMMENT '一级菜单',
`title`  varchar(50) NOT NULL  COMMENT '菜单名',
`from_type`  char(50) NULL  COMMENT '菜单内容',
`type`  varchar(30) NULL  COMMENT '类型',
`jump_type`  char(10) NULL  COMMENT '推送类型',
`addon`  char(50) NULL  COMMENT '选择插件',
`sucai_type`  varchar(50) NULL  COMMENT '素材类型',
`keyword`  varchar(100) NULL  COMMENT '关联关键词',
`url`  varchar(255) NULL   COMMENT '关联URL',
`sort`  tinyint(4) NULL   COMMENT '排序号',
`pbid`  int(10) NULL  COMMENT '公众号id',
`target_id`  int(10) NULL  COMMENT '选择内容',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('custom_menu','自定义菜单','0','','1','["pid","title","from_type","type","jump_type","addon","sucai_type","keyword","url","sort"]','1:基础','','','','','title:10%菜单名\r\nkeyword:10%关联关键词\r\nurl:50%关联URL\r\nsort:5%排序号\r\nid:10%操作:[EDIT]|编辑,[DELETE]|删除','20','title','','1394518309','1446533816','1','MyISAM','weixin');



