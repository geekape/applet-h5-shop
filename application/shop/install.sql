CREATE TABLE IF NOT EXISTS `wp_stores` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`name`  varchar(100) NULL  COMMENT '店名',
`address`  varchar(255) NULL  COMMENT '详细地址',
`phone`  varchar(30) NULL  COMMENT '联系电话',
`gps`  varchar(50) NULL  COMMENT 'GPS经纬度',
`coupon_id`  int(10) NULL  COMMENT '所属优惠券编号',
`wpid`  int(10) NULL  COMMENT 'token',
`open_time`  varchar(50) NULL  COMMENT '营业时间',
`img`  int(10) unsigned NULL  COMMENT '门店展示图',
`auth_group`  int(10) NULL  COMMENT '门店用户组',
`shop_code`  varchar(255) NULL  COMMENT '地点编码',
`password`  varchar(255) NULL  COMMENT '确认收款密码',
`img_url`  varchar(255) NULL  COMMENT 'erp门店图片链接',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('stores','门店','0','','1','["name","address","gps","phone"]','1:基础','','','','','name:店名\r\nphone:联系电话\r\naddress:详细地址\r\nids:操作:[EDIT]|编辑,[DELETE]|删除','20','name:店名搜索','','1427164604','1439465222','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_user_level_link` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`uid`  int(10) NULL  COMMENT '分销用户',
`upper_user`  int(10) NULL  COMMENT '上级分销用户',
`level`  int(10) NULL  COMMENT '分销级别',
`cTime`  int(10) NULL  COMMENT '创建时间',
`wpid`  int(10) NULL  COMMENT 'wpid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_user_level_link','分销用户级别关系','0','','1','["uid","upper_user","level"]','1:基础','','','','','','10','','','1459826468','1459838196','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_stores_link` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('stores_link','门店关联','0','','1','','1:基础','','','','','','20','','','1427356350','1427356350','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_address` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`truename`  varchar(100) NULL  COMMENT '收货人姓名',
`uid`  int(10) NULL  COMMENT '用户ID',
`mobile`  varchar(50) NULL  COMMENT '手机号码',
`city`  varchar(255) NULL  COMMENT '城市',
`address`  varchar(255) NULL  COMMENT '选择的地址',
`address_detail`  varchar(255) NULL  COMMENT '详细地址',
`is_use`  tinyint(2) NULL  COMMENT '是否设置为默认',
`is_del`  tinyint(2) NULL  DEFAULT 0 COMMENT '是否删除',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_address','收货地址','0','','1','','1:基础','','','','','','20','','','1423477477','1423477477','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_goods_category_link` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`wpid`  int(10) NULL  COMMENT 'wpid',
`goods_id`  int(10) NULL  COMMENT '商品编号',
`sort`  int(10) NULL  COMMENT '排序',
`category_first`  int(10) NULL  COMMENT '一级分类',
`category_second`  int(10) NULL  COMMENT '二级分类',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('goods_category_link','商品所属分类','0','','1','','1:基础','','','','','','10','','','1457933153','1457933153','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_goods_param_link` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`title`  varchar(255) NULL  COMMENT '参数名',
`wpid`  int(10) NOT NULL  COMMENT 'wpid',
`goods_id`  int(10) NULL  COMMENT '商品编号',
`param_value`  varchar(255) NULL  COMMENT '参数值',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('goods_param_link','商品参数表','0','','1','','1:基础','','','','','','10','','','1457941322','1457941322','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_goods_store_link` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`goods_id`  int(10) NULL  COMMENT '商品编号',
`store_id`  int(10) NULL  COMMENT '门店编号',
`store_num`  int(10) NULL  COMMENT '门店库存',
`wpid`  int(10) NULL  COMMENT 'wpid',
PRIMARY KEY (`id`),
KEY `goods` (`goods_id`,event_type)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('goods_store_link','商品所属门店','0','','1','','1:基础','','','','','','10','','','1458551555','1458551555','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_goods_comment` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`goods_id`  int(10) NULL  COMMENT '商品编号',
`score`  int(10) NULL  COMMENT '评价',
`content`  text NULL  COMMENT '内容',
`order_id`  int(10) NULL  COMMENT '订单编号',
`uid`  int(10) NULL  COMMENT '用户uid',
`cTime`  int(10) NULL  COMMENT '创建时间',
`is_show`  int(10) NULL  COMMENT '是否显示',
`pic`  varchar(255) NULL  COMMENT '图片',
`wpid`  int(10) NULL  COMMENT 'wpid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_goods_comment','商品评论信息','0','','1','["goods_id","score","content","order_id","uid","cTime","shop_id","is_show"]','1:基础','','','','','id:10%编号\r\nuid|get_username:15%用户昵称\r\ncTime|time_format:15%评论时间\r\nscore:15%星星数\r\ncontent:25%评论内容\r\nis_show|get_name_by_status:10%是否显示\r\nids:编辑:changeShow?id=[id]&is_show=[is_show]&goods_id=[goods_id]|设置显示状态','10','','','1457430858','1458901414','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_card_member` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`username`  varchar(255) NULL  COMMENT '姓名',
`phone`  varchar(255) NULL  COMMENT '手机号',
`sex`  varchar(10) NULL  COMMENT '性别',
`birthday`  int(10) NULL  COMMENT '生日',
`address`  varchar(255) NULL  COMMENT '地址',
`ctime`  int(10) NULL  COMMENT '导入时间',
`is_get`  tinyint(2) NULL  COMMENT '是否领取',
`wpid`  int(10) NULL  COMMENT 'wpid',
`shop_code`  varchar(255) NULL  COMMENT '地址编码',
`score`  int(10) NULL  COMMENT '积分余额',
`card_number`  varchar(255) NULL  COMMENT '会员卡号',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_card_member','实体店会员卡','0','','1','["username","phone","sex","birthday","address","ctime","is_get","token","shop_code","score","card_number"]','1:基础','','','','','username:姓名\r\nphone:手机号\r\nsex:性别\r\nbirthday|time_format:生日\r\naddress:地址\r\nscore:导入积分\r\ncard_number:绑定会员卡号\r\nctime|time_format:导入时间\r\nis_get|get_name_by_status:是否领取\r\nid:操作:changeGet&id=[id]&is_get=[is_get]|改变领取状态','10','username:请输入姓名','','1444362335','1453951928','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_statistics_follow` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`uid`  int(10) NULL  COMMENT '粉丝id',
`duid`  int(10) NULL  COMMENT '分销用户id',
`ctime`  int(10) NULL  COMMENT '关注时间',
`wpid`  int(10) NULL  COMMENT 'wpid',
`openid`  varchar(255) NULL  COMMENT '粉丝openid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_statistics_follow','分销粉丝统计表','0','','1','["uid","duid","ctime","token"]','1:基础','','','','','','10','','','1443001407','1443002218','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_distribution_user` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`uid`  int(10) NULL  COMMENT '用户id',
`qr_code`  varchar(255) NULL  COMMENT '二维码',
`wechat`  varchar(255) NULL  COMMENT '微信号',
`inviter`  varchar(255) NULL  COMMENT '邀请人',
`level`  int(10) NULL  COMMENT '分佣级别',
`is_audit`  int(10) NULL  COMMENT '是否审核',
`enable`  char(10) NULL  COMMENT '是否启用',
`branch_id`  varchar(255) NULL  COMMENT '授权门店',
`ctime`  int(10) NULL  COMMENT '创建时间',
`wpid`  int(10) NULL  COMMENT 'wpid',
`fans_gift_money`  int(10) NULL  COMMENT '转发增粉奖励金额',
`fans_gift_score`  int(10) NULL  COMMENT '转发增粉奖励积分',
`fans_gift_coupon`  int(10) NULL  COMMENT '转发增粉奖励优惠券',
`is_delete`  int(10) NULL  COMMENT '是否删除',
`shop_name`  varchar(255) NULL  COMMENT '商城店名字',
`shop_logo`  varchar(300) NULL  COMMENT '商城图标',
`profit_money`  float(10) NULL  COMMENT '盈利金额',
`zfb_name`  varchar(255) NULL  COMMENT '支付宝名称',
`zfb_account`  varchar(255) NULL  COMMENT '支付宝账号',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_distribution_user','分销用户','0','','1','["uid","qr_code","wechat","inviter","level","is_audit"]','1:基础','','','','','id:序号\r\ntruename:姓名\r\nmobile:手机号\r\nuid:微信昵称\r\nwechat:微信号\r\ninviter:邀请人\r\nctime|time_format:创建时间\r\nlevel:分销级别\r\nis_audit:审核\r\nids:操作:[EDIT]|编辑','10','truename:请输入姓名','','1442922612','1460357351','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_goods_sku_config` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`goods_id`  int(10) NULL  COMMENT '商品ID',
`spec_id`  int(10) NULL  COMMENT '规格ID',
`option_id`  int(10) NULL  COMMENT '属性ID',
`img`  int(10) UNSIGNED NULL  COMMENT '属性加图',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_goods_sku_config','商品规格配置','0','','1','','1:基础','','','','','','10','','','1442309511','1442309511','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_cashout_log` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`cashout_amount`  float(10) NULL  COMMENT '提现金额',
`remark`  text NULL  COMMENT '备注',
`cashout_status`  int(10) NULL  COMMENT '提现处理状态',
`ctime`  int(10) NULL  COMMENT '提现时间',
`wpid`  int(10) NULL  COMMENT 'wpid',
`cashout_account`  varchar(300) NULL  COMMENT '提现账号',
`uid`  int(10) NULL  COMMENT 'uid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_cashout_log','提现记录表','0','','1','["cashout_amount","remark","cashout_status","ctime","token","cashout_account"]','1:基础','','','','','ctime|time_format:申请日期\r\ncashout_amount:申请金额（￥）\r\ntype:提现方式\r\ncashout_account:提现账号\r\nname:账号名称\r\ncashout_status|get_name_by_status:审核状态\r\nremark:详细','10','','','1442315168','1442478119','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_goods_sku_data` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`cost_price`  decimal(11,2) NULL  COMMENT '成本价',
`goods_id`  int(10) NULL  COMMENT '商品ID',
`market_price`  decimal(11,2) NULL  COMMENT '市场价',
`sale_price`  decimal(11,2) NULL  COMMENT '促销价',
`stock`  int(10) NULL  COMMENT '库存量',
`sn_code`  varchar(50) NULL  COMMENT '商品编号',
`sku_int_0`  int(10) NULL  COMMENT '数量规格0',
`sku_int_1`  int(10) NULL  COMMENT '数量规格1',
`sku_int_2`  int(10) NULL  COMMENT '数量规格2',
`sku_varchar_0`  varchar(255) NULL  COMMENT '文本规格0',
`sku_varchar_1`  varchar(255) NULL  COMMENT '文本规格1',
`sku_varchar_2`  varchar(255) NULL  COMMENT '文本规格2',
`spec_option_ids`  varchar(100) NULL  COMMENT '规格属性ID串',
`lock_num`  int(10) NULL  COMMENT '商品锁定库存',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_goods_sku_data','商品规格表','0','','1','["cost_price"]','1:基础','','','','','','10','','','1442221199','1442309479','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_value` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`cate_id`  int(10) UNSIGNED NULL  COMMENT '所属分类ID',
`value`  text NULL  COMMENT '表单值',
`cTime`  int(10) NULL  COMMENT '增加时间',
`openid`  varchar(255) NULL  COMMENT 'OpenId',
`uid`  int(10) NULL  COMMENT '用户ID',
`wpid`  int(10) NULL  COMMENT 'wpid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_value','分类扩展属性数据表','0','','1','','1:基础','','','','','','20','','','1396687959','1396687959','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_page` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`title`  varchar(255) NULL  COMMENT '页面名称',
`ctime`  int(15) NULL  COMMENT '创建时间',
`config`  text NULL  COMMENT '配置参数',
`desc`  text NULL  COMMENT '描述',
`wpid`  int(10) NULL  COMMENT 'wpid',
`manager_id`  int(10) NULL  COMMENT '创建者ID',
`use`  varchar(255) NULL  COMMENT '哪里使用',
`is_show`  tinyint(2) NULL  COMMENT '是否显示底部导航',
`is_index`  int(10) NULL  COMMENT '设为首页',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_page','自定义页面','0','','1','["title","ctime","config","desc","shop_id","token","manager_id","use"]','1:基础','','','','','title:页面标题\r\nctime|time_format:创建时间\r\nids:操作:preview?id=[id]&target=_blank|预览,[EDIT]|编辑,[DELETE]|删除\r\ncopy:复制链接','10','','','1442202619','1442821956','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_distribution_profit` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`wpid`  int(10) NOT NULL  COMMENT 'wpid',
`uid`  int(10) NULL  COMMENT 'Uid',
`ctime`  int(10) NULL  COMMENT '返利时间',
`profit`  float(10)  NULL  COMMENT '拥金',
`profit_shop`  int(10) NULL  COMMENT '获得佣金的店铺',
`distribution_percent`  varchar(255) NULL  COMMENT '分销比例',
`order_id`  int(10) NULL  COMMENT '订单id',
`upper_user`  int(10) NULL  COMMENT '分销用户',
`upper_level`  int(10) NULL  COMMENT '分销用户级别',
`duser`  int(10) NULL  COMMENT '该用户带来的消费用户',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_distribution_profit','分销用户返利表','0','','1','','1:基础','','','','','','10','','','1441957173','1441957173','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_attribute` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`title`  varchar(255) NOT NULL  COMMENT '字段标题',
`attr_type`  char(50) NOT NULL  COMMENT '字段类型',
`extra`  text NULL  COMMENT '参数',
`value`  varchar(255) NULL  COMMENT '默认值',
`sort`  int(10) UNSIGNED NULL  COMMENT '排序号',
`is_show`  tinyint(2) NULL  COMMENT '是否显示',
`cate_id`  int(10) UNSIGNED NULL  COMMENT '所属分类ID',
`error_info`  varchar(255) NULL  COMMENT '出错提示',
`validate_rule`  varchar(255) NULL  COMMENT '正则验证',
`is_must`  tinyint(2) NULL  COMMENT '是否必填',
`remark`  varchar(255) NULL  COMMENT '字段备注',
`wpid`  int(10) NULL  COMMENT 'wpid',
`mTime`  int(10) NULL  COMMENT '修改时间',
`type`  tinyint(2) NULL  COMMENT '属性类型',
`name`  varchar(30) NULL  COMMENT '属性标识',
`goods_field`  varchar(50) NULL  COMMENT '商品表中所占用的字段名',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_attribute','分类属性','0','','1','["title","attr_type","extra","value","sort"]','1:基础','','','','','title:字段标题\r\ntype|get_name_by_status:字段类型\r\nextra:参数\r\nsort:排序\r\nids:操作:[EDIT]&cate_id=[cate_id]|编辑,[DELETE]|删除','20','title','','1396061373','1442368516','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_spec_option` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`spec_id`  int(10) NULL  COMMENT '规格ID',
`name`  varchar(100) NULL  COMMENT '规格属性名称',
`sort`  int(10) NULL  COMMENT '排序号',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_spec_option','商品规格选项','0','','1','','1:基础','','','','','','10','','','1441942503','1441942503','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_spec` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`title`  varchar(30) NULL  COMMENT '规格名称',
`remark`  varchar(100) NULL  COMMENT '备注',
`spec_sort`  int(10) NULL  COMMENT '排序',
`uid`  int(10) NULL  COMMENT '用户ID',
`wpid`  int(10) NULL  COMMENT 'wpid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_spec','商品规格','0','','1','["title","remark","sort"]','1:基础','','','','','title:规格名称\r\nremark:规格属性\r\nid:操作:[EDIT]|编辑,[DELETE]|删除','10','title:请输入规格名称','','1441942151','1441943264','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_virtual` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`goods_id`  int(10) NULL  COMMENT '商品ID',
`account`  varchar(255) NULL  COMMENT '账号',
`password`  varchar(255) NULL  COMMENT '密码',
`is_use`  char(10) NULL  COMMENT '是否已经使用',
`order_id`  int(10) NULL  COMMENT '订单号',
`card_codes`  varchar(255) NULL  COMMENT '点卡序列号',
`wpid`  int(10) NULL  COMMENT 'wpid',
`uid`  int(10) NULL  COMMENT '购买用户uid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_virtual','虚拟物品信息','0','','1','','1:基础','','','','','','10','','','1441006502','1441006502','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_slideshow` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`title`  varchar(255) NULL  COMMENT '标题',
`img`  int(10) unsigned NOT NULL   COMMENT '图片',
`url`  varchar(255) NULL  COMMENT '链接地址',
`is_show`  tinyint(2) NULL  DEFAULT 1 COMMENT '是否显示',
`sort`  int(10) unsigned NULL   COMMENT '排序',
`wpid`  int(10) NULL  COMMENT 'wpid',
`uid`  int(10) NULL  COMMENT '用户id',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_slideshow','幻灯片','0','','1','["title","img","url","is_show","sort"]','1:基础','','','','','title:标题\r\nimg:图片\r\nurl:链接地址\r\nis_show|get_name_by_status:显示\r\nsort:排序\r\nids:操作:[EDIT]&module_id=[pid]|编辑,[DELETE]|删除','20','title','','1396098264','1408323347','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_order` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`uid`  int(10) UNSIGNED NOT NULL  COMMENT '用户id',
`order_number`  varchar(255) NOT NULL  COMMENT '订单编号',
`goods_datas`  text NOT NULL  COMMENT '商品序列化数据',
`remark`  text NOT NULL  COMMENT '备注',
`cTime`  int(10) NOT NULL  COMMENT '订单时间',
`total_price`  decimal(10,2) NULL  COMMENT '总价',
`address_id`  int(10) NULL  COMMENT '配送信息',
`is_send`  int(10) NULL  COMMENT '是否发货',
`send_code`  varchar(255) NULL  COMMENT '快递公司编号',
`send_number`  varchar(255) NULL  COMMENT '快递单号',
`send_type`  char(10) NULL  COMMENT '发货类型',
`wpid`  int(10) NULL  COMMENT 'wpid',
`openid`  varchar(255) NOT NULL  COMMENT 'OpenID',
`pay_status`  int(10)  NULL  COMMENT '支付状态',
`pay_type`  tinyint(2) NULL  COMMENT '支付类型',
`is_new`  tinyint(2) NULL  COMMENT '是否为新订单',
`status_code`  char(50) NULL  COMMENT '订单跟踪状态码',
`is_lock`  int(10) NULL  COMMENT '数量是否锁定',
`erp_lock_code`  text NULL  COMMENT 'ERP锁定商品编号',
`mail_money`  float(10) NULL  COMMENT '邮费金额',
`stores_id`  int(10) NULL  COMMENT '门店编号',
`pay_time`  int(10) NULL  COMMENT '支付时间',
`send_time`  int(10) NULL  COMMENT '发货时间',
`extra`  text NULL  COMMENT '扩展参数',
`order_state`  int(10) NULL  COMMENT '订单状态',
`out_trade_no`  varchar(100) NULL  COMMENT '支付的订单号',
`event_type`  tinyint(2) NULL  COMMENT '订单来源',
`event_id`  int(10) NULL  COMMENT '活动ID',
`is_original`  tinyint(2) NULL  DEFAULT 0 COMMENT '活动中是否原价购买',
`refund`  tinyint(2) NULL  COMMENT '退款状态',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_order','订单记录','0','','1','["uid","goods_datas","remark","order_number","cTime","total_price","address_id","is_send","send_code","send_number","send_type","shop_id"]','1:基础','','','','','order_number:15%订单编号\r\ngoods:20%下单商品\r\nuid:10%客户\r\ntotal_price:7%总价\r\ncTime|time_format:17%下单时间\r\ncommon|get_name_by_status:10%支付类型\r\nstatus_code|get_name_by_status:10%订单跟踪\r\naction:11%操作','20','key:请输入订单编号 或 客户昵称','','1420269240','1440147136','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_order_log` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`order_id`  int(10) NULL  COMMENT '订单ID',
`status_code`  char(50) NULL  COMMENT '状态码',
`remark`  varchar(255) NULL  COMMENT '备注内容',
`cTime`  int(10) NULL  COMMENT '时间',
`extend`  varchar(255) NULL  COMMENT '扩展信息',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_order_log','订单跟踪','0','','1','','1:基础','','','','','','10','','','1439525562','1439525562','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_goods_score` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`uid`  int(10) NULL  COMMENT '用户ID',
`goods_id`  int(10) NULL  COMMENT '商品ID',
`score`  int(10) NULL  COMMENT '得分',
`cTime`  int(10) NULL  COMMENT '创建时间',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_goods_score','商品评分记录','0','','1','','1:基础','','','','','','20','','','1422930901','1422930901','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_goods_category` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`pid`  int(10) unsigned NULL  COMMENT '上一级分类',
`title`  varchar(255) NOT NULL  COMMENT '分类标题',
`icon`  int(10) unsigned NULL   COMMENT '分类图标',
`sort`  int(10) unsigned NULL   COMMENT '排序号',
`is_recommend`  tinyint(2) NULL  COMMENT '是否推荐',
`is_show`  tinyint(2) NULL  DEFAULT 1 COMMENT '是否显示',
`path`  varchar(255) NULL  COMMENT '分类路径',
`wpid`  int(10) NOT NULL  COMMENT 'wpid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_goods_category','商品分类','0','','1','["pid","title","icon","sort","is_recommend","is_show"]','1:基础','','','','','title:30%分组\r\nicon|get_img_html:20%图标\r\nsort:10%排序号\r\nis_show|get_name_by_status:20%显示\r\nids:20%操作:[EDIT]|编辑,[DELETE]|删除','20','title','','1397529095','1467365556','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_collect` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`uid`  int(10) NULL  COMMENT '使用UID',
`goods_id`  int(10) NULL  COMMENT '商品ID',
`cTime`  int(10) NULL  COMMENT '收藏时间',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_collect','商品收藏','0','','1','','1:基础','','','','','','20','','','1423471275','1423471275','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_goods` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`title`  varchar(255) NOT NULL  COMMENT '商品名称',
`category_id`  char(50) NULL  COMMENT '商品分类',
`imgs`  varchar(255) NULL  COMMENT '商品图片',
`cover`  int(10) UNSIGNED NULL  COMMENT '商品封面图',
`is_recommend`  tinyint(2) NULL  COMMENT '是否推荐',
`auto_send`  char(10) NULL  COMMENT '自动发货',
`virtual_textarea`  text NULL  COMMENT '虚拟物品信息',
`is_show`  tinyint(2) NULL  COMMENT '是否上架',
`cost_price`  decimal(10,2) NULL  COMMENT '成本价',
`weight`  float(10) NULL  COMMENT '重量',
`sn_code`  text NULL  COMMENT '商品编号',
`is_delete`  int(10) NULL  COMMENT '是否删除',
`is_new`  varchar(100) NULL  COMMENT '新品类型',
`rank`  int(10) NULL  COMMENT '热销度',
`show_time`  int(10) NULL  COMMENT '上架时间',
`wpid`  int(10) NULL  COMMENT 'wpid',
`diy_id`  int(10) NULL  COMMENT '详情页面DidId',
`reduce_score`  int(10) NULL  COMMENT '可抵扣积分',
`distribution_price`  float(10) NULL  COMMENT '分销返佣金额',
`is_spec`  int(10) NULL  COMMENT '是否有规格',
`file_url`  varchar(255) NULL  COMMENT '文件下载链接',
`express`  decimal(10,2) NULL  COMMENT '邮费',
`send_type`  varchar(30) NULL  DEFAULT 1 COMMENT '收货方式',
`stores_ids`  varchar(100) NULL  COMMENT '自提门店',
`is_all_store`  tinyint(2) NULL  COMMENT '店门类型',
`tab`  varchar(100) NULL  COMMENT '同款标签',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_goods','商品列表','0','','1','["title","category_id","imgs","content","cover","type","is_recommend","auto_send","virtual_textarea","is_show","market_price","stock_num","cost_price","sale_price","weight","sn_code","is_delete","is_new","can_deposit"]','1:基础','','','','','cover|get_img_html:封面图\r\ntitle:商品名称\r\nmarket_price:价格\r\nstock_num:库存量\r\nsale_count:销售量\r\nis_show|get_name_by_status:是否上架\r\nids:操作:set_show?id=[id]&is_show=[is_show]|改变上架状态,[EDIT]|编辑,[DELETE]|删除,goodsCommentLists?goods_id=[id]&target=_blank|评论列表','20','title:请输入商品名称','','1422672084','1458898390','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_cart` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`uid`  int(10) UNSIGNED NOT NULL  COMMENT '用户ID',
`wpid`  int(10) NULL  COMMENT 'wpid',
`goods_id`  varchar(255) NOT NULL  COMMENT '商品id',
`num`  int(10) UNSIGNED NULL  COMMENT '数量',
`price`  varchar(30) NULL  COMMENT '单价',
`goods_type`  tinyint(2) NULL  COMMENT '商品类型',
`openid`  varchar(255) NULL  COMMENT 'openid',
`spec_option_ids`  varchar(50) NULL  COMMENT '商品SKU',
`cTime`  int(10) NULL  COMMENT '创建时间',
`lock_rid_num`  int(10) NULL  COMMENT '释放库存数',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_cart','购物车','0','','1','','1:基础','','','','','','20','','','1419577864','1419577864','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`title`  varchar(255) NOT NULL  COMMENT '商店名称',
`logo`  int(10) NULL  COMMENT '商店LOGO',
`api_key`  varchar(100) NULL  COMMENT '快递接口的APPKEY',
`order_payok_messageid`  varchar(100) NULL  COMMENT '交易完成通知的模板ID',
`wpid`  varchar(100) NULL  COMMENT 'wpid',
`tcp`  text  NULL  COMMENT '客户协议',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop','微商城','0','','1','["title","logo","intro","mobile","qq","wechat","api_key","custom_tip","content","address","gps"]','1:基础','','','','','title:商店名称\r\nlogo|get_img_html:商店LOGO\r\nmobile:联系电话\r\nqq:QQ号\r\nwechat:微信号\r\nids:操作:[EDIT]&id=[id]|编辑,lists&_controller=Category&target=_blank&shop_id=[id]|商品分类,lists&_controller=Slideshow&target=_blank&shop_id=[id]|幻灯片,lists&_controller=Goods&target=_blank&shop_id=[id]|商品管理,lists&_controller=Order&target=_blank&shop_id=[id]|订单管理,lists&_addons=Payment&_controller=Payment&target=_blank&shop_id=[id]|支付配置,lists&_controller=Template&target=_blank&shop_id=[id]|模板选择,[DELETE]|删除,index&_controller=Wap&target=_blank&shop_id=[id]|预览','20','title:请输入商店名称','','1422670956','1458268970','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_cashout_account` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`uid`  int(10) NULL  COMMENT 'uid',
`wpid`  int(10) NULL  COMMENT 'wpid',
`type`  char(50) NULL  COMMENT '提现方式',
`name`  varchar(255) NULL  COMMENT '姓名',
`account`  varchar(255) NULL  COMMENT '提现账号',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_cashout_account','提现账号','0','','1','','1:基础','','','','','','10','','','1442396922','1442396922','1','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_membership` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`membership`  varchar(255) NULL  COMMENT '会员名',
`img`  int(10) UNSIGNED NULL  COMMENT '图标',
`condition`  int(10) NULL  COMMENT '升级会员条件',
`uid`  int(10) NULL  COMMENT '企业用户id',
`wpid`  int(10) NULL  COMMENT 'wpid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_membership','商城会员设置','0','','1','["membership","img","condition"]','1:基础','','','','','img|get_img_html:20%会员图标\r\nmembership:25%会员名\r\ncondition:20%条件（经历值）\r\nid:30%操作:[EDIT]|编辑,[DELETE]|删除','10','membership:请输入会员名','','1441787383','1441857253','1','MyISAM','shop');






CREATE TABLE IF NOT EXISTS `wp_stores_user` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`wpid`  int(10) NULL  COMMENT 'wpid',
`uid`  int(10) NULL  COMMENT 'uid',
`store_id`  int(10) NULL  COMMENT 'store_id',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('stores_user','用户默认选择的门店ID','0','','1','','1:基础','','','','','','20','','','0','0','0','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_track` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`uid`  int(10) NULL  COMMENT 'uid',
`goods_id`  int(10) NULL  COMMENT 'goods_id',
`create_at`  int(10) NULL  COMMENT 'create_at',
`wpid`  int(10) NULL  COMMENT 'wpid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_track','足迹','0','','1','','1:基础','','','','','','20','','','0','0','0','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_goods_param_temp` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`title`  varchar(30) NOT NULL  COMMENT '模板名称',
`wpid`  int(10) NULL  COMMENT 'wpid',
`param`  text NULL  COMMENT 'param',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('goods_param_temp','商品参数模板','0','','1','','1:基础','','','','','','20','','','0','0','0','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_goods_stock` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`stock`  int(10) NULL  COMMENT '物理库存',
`stock_active`  int(10) NULL  COMMENT '销售库存',
`lock_count`  int(10) NULL  COMMENT '锁定库存',
`sale_count`  int(10) NULL  COMMENT '销售量',
`goods_id`  int(10) NULL  COMMENT '商品ID',
`event_type`  tinyint(2) NULL  COMMENT '商品来源',
`market_price`  decimal(10,2) NULL  COMMENT '原价',
`sale_price`  decimal(10,2) NULL  COMMENT '销售价',
`shop_goods_id`  int(10) NULL  COMMENT '商品来源',
`del_at`  int(10) NULL  COMMENT '删除时间',
PRIMARY KEY (`id`),
KEY `goods_id` (`goods_id`,event_type)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_goods_stock','商品库存','0','','1','','1:基础','','','','','','20','','','0','0','0','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_test` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`keyword`  varchar(100) NOT NULL  COMMENT '关键词',
`keyword_type`  tinyint(2) NOT NULL  DEFAULT 0 COMMENT '关键词匹配类型',
`title`  varchar(255) NOT NULL  COMMENT '问卷标题',
`intro`  text NOT NULL  COMMENT '封面简介',
`mTime`  int(10) NOT NULL  COMMENT '修改时间',
`cover`  int(10) unsigned NOT NULL  COMMENT '封面图片',
`finish_tip`  text NOT NULL  COMMENT '评论语',
`wpid`  int(10) NOT NULL  DEFAULT 0 COMMENT 'wpid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('test','test-modelname','0','','1','','1:基础','','','','','','10','','','0','0','0','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_goods_content` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`goods_id`  int(10) NOT NULL  COMMENT '商品ID',
`content`  text NULL  COMMENT '内容',
PRIMARY KEY (`id`),
KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_goods_content','商品详情','0','','1','','1:基础','','','','','','20','','','0','0','0','MyISAM','shop');



CREATE TABLE IF NOT EXISTS `wp_shop_order_goods` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`order_id`  int(10) NOT NULL  COMMENT '订单ID',
`goods_id`  int(10) NOT NULL  COMMENT '商品ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('shop_order_goods','订单商品关联表','0','','1','','1:基础','','','','','','20','','','0','0','0','MyISAM','shop');



