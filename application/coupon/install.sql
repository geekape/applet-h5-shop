CREATE TABLE IF NOT EXISTS `wp_coupon` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`title`  varchar(255) NOT NULL  COMMENT '标题',
`use_tips`  text NULL  COMMENT '使用说明',
`start_time`  int(10) NULL  COMMENT '开始时间',
`end_time`  int(10) NULL  COMMENT '领取结束时间',
`num`  int(10) unsigned NULL   COMMENT '优惠券数量',
`max_num`  int(10) unsigned NULL   COMMENT '每人最多允许获取次数',
`over_time`  int(10) NULL  COMMENT '使用的截止时间',
`background`  int(10) UNSIGNED NULL  COMMENT '素材背景图',
`use_start_time`  int(10) NULL  COMMENT '使用开始时间',
`member`  varchar(100) NULL  COMMENT '选择人群',
`intro`  text NULL  COMMENT '封面简介',
`cover`  int(10) unsigned NULL   COMMENT '优惠券图片',
`cTime`  int(10) unsigned NULL   COMMENT '发布时间',
`wpid`  int(10) NOT NULL  COMMENT 'wpid',
`end_tips`  text NULL  COMMENT '领取结束说明',
`end_img`  int(10) unsigned NULL   COMMENT '领取结束提示图片',
`follower_condtion`  char(50) NULL  COMMENT '粉丝状态',
`credit_conditon`  int(10) unsigned NULL   COMMENT '积分限制',
`credit_bug`  int(10) unsigned NULL   COMMENT '积分消费',
`addon_condition`  varchar(255) NULL  COMMENT '插件场景限制',
`collect_count`  int(10) unsigned NULL   COMMENT '已领取数',
`view_count`  int(10) unsigned NULL   COMMENT '浏览人数',
`addon`  char(50) NULL  COMMENT '插件',
`shop_uid`  varchar(255) NULL  COMMENT '商家管理员ID',
`use_count`  int(10) NULL  COMMENT '已使用数',
`empty_prize_tips`  varchar(255) NULL  COMMENT '奖品抽完后的提示',
`start_tips`  varchar(255) NULL  COMMENT '活动还没开始时的提示语',
`shop_name`  varchar(255) NULL  COMMENT '商家名称',
`shop_logo`  int(10) UNSIGNED NULL  COMMENT '商家LOGO',
`is_del`  int(10) NULL  COMMENT '是否删除',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('coupon','优惠券','0','','1','["title","use_tips","start_time","end_time","num","max_num","over_time","background","use_start_time","member"]','1:基础','','','','','id:优惠券编号\r\ntitle:标题\r\nnum:计划发送数\r\ncollect_count:已领取数\r\nuse_count:已使用数\r\nstart_time|time_format:开始时间\r\nend_time|time_format:结束时间\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,Sn/lists?target_id=[id]&target=_blank|成员管理,preview?id=[id]&target=_blank|预览','20','title','','1396061373','1445567658','1','MyISAM','coupon');



