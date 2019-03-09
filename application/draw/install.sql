CREATE TABLE IF NOT EXISTS `wp_draw_follow_log` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`follow_id`  int(10) NULL  COMMENT '粉丝id',
`sports_id`  int(10) NULL  COMMENT '场次id',
`count`  int(10) NULL  COMMENT '抽奖次数',
`cTime`  int(10) NULL  COMMENT '支持时间',
`uid`  int(10) NULL  COMMENT 'uid',
`wpid`  int(10) NOT NULL  COMMENT 'wpid',
PRIMARY KEY (`id`),
KEY `sports_id` (`sports_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('draw_follow_log','粉丝抽奖记录','0','','1','["follow_id","sports_id","count","cTime"]','1:基础','','','','','follow_id:微信名称\r\nopenid:openID\r\narea:地区\r\nsex:性别\r\nhas_prize:是否中奖\r\ncTime:参与时间\r\ntruename:真实姓名\r\nmobile:电话','20','','','1432619171','1491386963','1','MyISAM','draw');



CREATE TABLE IF NOT EXISTS `wp_lucky_follow` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`draw_id`  int(10) NULL  COMMENT '活动编号',
`sport_id`  int(10) NULL  COMMENT '场次编号',
`award_id`  int(10) NULL  COMMENT '奖品编号',
`follow_id`  int(10) NULL  COMMENT '粉丝id',
`address`  varchar(255) NULL  COMMENT '地址',
`num`  int(10) NULL  COMMENT '获奖数',
`state`  tinyint(2) NULL  COMMENT '兑奖状态',
`zjtime`  int(10) NULL  COMMENT '中奖时间',
`djtime`  int(10) NULL  COMMENT '兑奖时间',
`remark`  text NULL  COMMENT '备注',
`scan_code`  varchar(255) NULL  COMMENT '核销码',
`uid`  int(10) NULL  COMMENT 'uid',
`wpid`  int(10) NOT NULL  COMMENT 'wpid',
`aim_table`  varchar(255) NULL  COMMENT '活动标识',
`error_remark`  text NULL  COMMENT '发放失败备注',
`send_aim_id`  int(10) NULL  COMMENT '发送奖品对应id',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('lucky_follow','中奖者信息','0','','1','["draw_id","sport_id","award_id","follow_id","address","num","state","zjtime","djtime","remark","scan_code"]','1:基础','','','','','draw_id:活动名称\r\ndraw_time:活动时间\r\nfollow_id|deal_emoji:8%微信昵称\r\nopenid:中奖人OPENID\r\nzjtime|time_format:中奖时间\r\ntruename:姓名\r\nmobile:手机号\r\naward_id:奖项\r\naward_name:奖品名称\r\nstate|get_name_by_status:发奖状态\r\nids:8%操作:do_fafang?id=[id]|发放奖品','20','award_name:输入奖品名称','','1432618091','1491373747','1','MyISAM','draw');



CREATE TABLE IF NOT EXISTS `wp_award` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`award_type`  varchar(30) NULL  DEFAULT 1 COMMENT '奖品类型',
`name`  varchar(255) NOT NULL  COMMENT '奖项名称',
`img`  int(10) NULL  COMMENT '奖品图片',
`score`  int(10) NULL  COMMENT '积分数',
`money`  decimal(10,2) NULL  DEFAULT 0 COMMENT '现金',
`explain`  text NULL  COMMENT '奖品说明',
`coupon_id`  char(50) NULL  COMMENT '选择赠送券',
`aim_table`  varchar(255) NULL  COMMENT '活动标识',
`uid`  int(10) NULL  COMMENT 'uid',
`wpid`  int(10) NOT NULL  COMMENT 'wpid',
`sort`  int(10) unsigned NULL   COMMENT '排序号',
`count`  int(10) NULL  COMMENT '奖品数量',
`price`  FLOAT(10) NULL  COMMENT '商品价格',
`cTime`  int(10) NULL  COMMENT '创建时间',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('award','奖品库奖品','0','','1','["award_type","name","img","virtual_type","score","money","explain"]','1:基础','','','','','id:6%编号\r\nname:23%奖项名称\r\nimg|get_img_html:10%商品图片\r\naward_type|get_name_by_status:10%奖品类型\r\nexplain:30%奖品说明\r\nids:20%操作:[EDIT]|编辑,[DELETE]|删除','20','name:请输入奖品名称','','1432607100','1462358042','1','MyISAM','draw');



CREATE TABLE IF NOT EXISTS `wp_lottery_games` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`title`  varchar(255) NULL  COMMENT '活动名称',
`keyword`  varchar(255) NULL  COMMENT '微信关键词',
`game_type`  char(10) NULL  COMMENT '游戏类型',
`start_time`  int(10) NULL  COMMENT '开始时间',
`end_time`  int(10) NULL  COMMENT '结束时间',
`status`  char(10) NULL  COMMENT '状态',
`day_attend_limit`  int(10) NULL  COMMENT '每人每天抽奖次数',
`attend_limit`  int(10) NULL  COMMENT '每人总共抽奖次数',
`day_win_limit`  int(10) NULL  COMMENT '每人每天中奖次数',
`win_limit`  int(10) NULL  COMMENT '每人总共中奖次数',
`day_winners_count`  int(10) NULL  COMMENT '每天最多中奖人数',
`remark`  text NULL  COMMENT '活动说明',
`cover`  int(10) UNSIGNED NULL  COMMENT '封面图片',
`wpid`  int(10) NOT NULL  COMMENT 'wpid',
`manager_id`  int(10) NULL  COMMENT '管理员id',
`url`  varchar(300) NULL  COMMENT '关注链接',
`attend_num`  int(10) NULL  COMMENT '参与总人数',
`template`  char(10) NULL  COMMENT '模板',
`need_subscribe`  tinyint(2) NULL  COMMENT '关注公众号才能参与',
`need_member`  tinyint(2) NULL  COMMENT '成为会员才能参与',
`show_prize_num`  tinyint(2) NULL  COMMENT '是否显示奖品数量',
`show_winner`  tinyint(2) NULL  COMMENT '是否显示中奖记录',
`show_prize_details`  tinyint(2) NULL  COMMENT '是否显示奖品详情',
`winning_mess_img`  int(10) UNSIGNED NULL  COMMENT '中奖推送消息封面图片',
`winning_mess_text`  varchar(255) NULL  COMMENT '中奖推送消息封面描述',
`winning_money_text`  text NULL  COMMENT '抽中现金红包推送消息',
`winning_score_text`  text NULL  COMMENT '抽中积分推送消息',
`current_draw_num`  int(10) NULL  COMMENT '当前抽奖次数',
`draw_count`  int(10) NULL  COMMENT '活动抽奖总次数',
`win_num_list`  longtext NULL  COMMENT '抽中奖品对应次数列表',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('lottery_games','抽奖游戏','0','','1','["title","keyword","game_type","start_time","end_time","status","day_attend_limit","attend_limit","day_win_limit","win_limit","day_winners_count","remark","cover"]','1:基础','','','','','id:序号\r\ntitle:活动名称\r\ngame_type|get_name_by_status:游戏类型\r\nkeyword:关键词\r\nstart_time|time_format:开始时间\r\nend_time|time_format:结束时间\r\nstatus:活动状态\r\nattend_num:参与人次\r\nwinners_list:中奖人列表\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,preview&games_id=[id]|预览,draw/Wap/index&games_id=[id]|复制链接,statistics&games_id=[id]|统计分析','10','title:请输入活动名称搜索','','1444877287','1491375194','1','MyISAM','draw');



CREATE TABLE IF NOT EXISTS `wp_lottery_games_award_link` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`award_id`  int(10) NULL  COMMENT '奖品id',
`games_id`  int(10) NULL  COMMENT '抽奖游戏id',
`grade`  varchar(255) NULL  COMMENT '中奖等级',
`num`  int(10) NULL  COMMENT '奖品数量',
`max_count`  int(10) NULL  COMMENT '最多抽奖',
`wpid`  int(10) NOT NULL  COMMENT 'wpid',
`sort`  int(10) NULL  COMMENT '排序',
`unreal_num`  int(10) NULL  DEFAULT 0 COMMENT '假的数量',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('lottery_games_award_link','抽奖游戏奖品设置','0','','1','["award_id","games_id","grade","num","max_count"]','1:基础','','','','','','10','','','1444900969','1491373787','1','MyISAM','draw');



CREATE TABLE IF NOT EXISTS `wp_draw_pv_log` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`cTime`  int(10) NULL  COMMENT '访问时间',
`draw_id`  int(10) NULL  COMMENT '游戏ID',
`uid`  int(10) NULL  COMMENT '用户id',
`wpid`  int(10) NOT NULL  COMMENT 'wpid',
`openid`  varchar(255) NULL  COMMENT 'openid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('draw_pv_log','抽奖游戏浏览记录','0','','1','','1:基础','','','','','','10','','','1491379489','0','0','MyISAM','draw');



CREATE TABLE IF NOT EXISTS `wp_sport_award` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`award_type`  varchar(30) NULL  COMMENT '奖品类型',
`name`  varchar(255) NOT NULL  COMMENT '奖项名称',
`count`  int(10) NULL  COMMENT '奖品数量',
`img`  int(10) NOT NULL  COMMENT '奖品图片',
`price`  FLOAT(10) NULL  COMMENT '商品价格',
`score`  int(10) NULL  COMMENT '积分数',
`explain`  text NULL  COMMENT '奖品说明',
`coupon_id`  char(50) NULL  COMMENT '选择赠送券',
`money`  float(10) NULL  COMMENT '返现金额',
`sort`  int(10) unsigned NULL   COMMENT '排序号',
`uid`  int(10) NULL  COMMENT 'uid',
`wpid`  int(10) NOT NULL  COMMENT 'wpid',
`aim_table`  varchar(255) NULL  COMMENT '活动标识',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('sport_award','抽奖奖品','0','','1','["award_type","name","count","img","price","score","explain","coupon_id","money"]','1:基础','','','','','id:6%编号\r\nname:23%奖项名称\r\nimg|get_img_html:8%商品图片\r\nprice:8%商品价格\r\nexplain:24%奖品说明\r\ncount:8%奖品数量\r\nids:20%操作:[EDIT]|编辑,[DELETE]|删除,getlistByAwardId?awardId=[id]&_controller=LuckyFollow|中奖者列表','20','name:请输入抽奖名称','','1432607100','1444901269','1','MyISAM','draw');



CREATE TABLE IF NOT EXISTS `wp_lzwg_activities_vote` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`lzwg_id`  int(10) NULL  COMMENT '活动编号',
`vote_type`  char(10) NULL  COMMENT '问题类型',
`vote_limit`  int(10) NULL  COMMENT '最多选择几项',
`lzwg_type`  char(10) NULL  COMMENT '活动类型',
`vote_id`  int(10) NULL  COMMENT '题目编号',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('lzwg_activities_vote','投票答题活动','0','','1','["lzwg_id","vote_type","vote_limit","lzwg_type","vote_id"]','1:基础','','','','','lzwg_name:活动名称\r\nstart_time|time_format:活动开始时间\r\nend_time|time_format:活动结束时间\r\nlzwg_type|get_name_by_status:活动类型\r\nvote_title:题目\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,tongji&id=[id]|用户参与分析\r\n','20','lzwg_id:活动名称','','1435734819','1435825972','1','MyISAM','draw');



CREATE TABLE IF NOT EXISTS `wp_lzwg_activities` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`title`  varchar(255) NULL  COMMENT '活动名称',
`remark`  text NULL  COMMENT '活动说明',
`logo_img`  int(10) UNSIGNED NULL  COMMENT '活动LOGO',
`start_time`  int(10) NULL  COMMENT '开始时间',
`end_time`  int(10) NULL  COMMENT '结束时间',
`get_prize_tip`  varchar(255) NULL  COMMENT '中奖提示信息',
`no_prize_tip`  varchar(255) NULL  COMMENT '未中奖提示信息',
`lottery_number`  int(10) NULL  COMMENT '抽奖次数',
`get_prize_count`  int(10) NULL  COMMENT '中奖次数',
`comment_status`  char(10) NULL  COMMENT '评论是否需要审核',
`ctime`  int(10) NULL  COMMENT '活动创建时间',
`uid`  int(10) NULL  COMMENT 'uid',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('lzwg_activities','靓妆活动','0','','1','["title","remark","logo_img","start_time","end_time","get_prize_tip","no_prize_tip","lottery_number","get_prize_count","comment_status"]','1:基础','','','','','title:活动名称\r\nremark:活动描述\r\nlogo_img|get_img_html:活动LOGO\r\nactivitie_time:活动时间\r\nget_prize_tip:中将提示信息\r\nno_prize_tip:未中将提示信息\r\ncomment_list:评论列表\r\nset_vote:设置投票\r\nset_award:设置奖品\r\nget_prize_list:中奖列表\r\nids:操作:[EDIT]|编辑,[DELETE]|删除','20','','','1435306468','1436181872','1','MyISAM','draw');



CREATE TABLE IF NOT EXISTS `wp_lottery_prize_list` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`sports_id`  int(10) NULL  COMMENT '活动编号',
`award_id`  varchar(255) NULL  COMMENT '奖品编号',
`award_num`  int(10) NULL  COMMENT '奖品数量',
`uid`  int(10) NULL  COMMENT 'uid',
PRIMARY KEY (`id`),
KEY `sports_id` (`sports_id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('lottery_prize_list','抽奖奖品列表','0','','1','["sports_id","award_id","award_num"]','1:基础','','','','','sports_id:比赛场次\r\naward_id:奖品名称\r\naward_num:奖品数量\r\nid:编辑:[EDIT]|编辑,[DELETE]|删除,add?sports_id=[sports_id]|添加','20','','','1432613700','1432710817','1','MyISAM','draw');



CREATE TABLE IF NOT EXISTS `wp_event_prizes` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`event_id`  int(10) NULL  COMMENT '活动id',
`prize_list`  text NULL  COMMENT '奖品列表',
`prize_count`  int(10) NULL  COMMENT '奖品数量',
`start_num`  int(10) NULL  COMMENT '开始数字',
`end_num`  int(10) NULL  COMMENT '最后数字',
`sort`  int(10) NULL  DEFAULT 1 COMMENT '顺序',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('event_prizes','活动中奖奖品','0','','1','','1:基础','','','','','','20','','','0','0','0','MyISAM','draw');



