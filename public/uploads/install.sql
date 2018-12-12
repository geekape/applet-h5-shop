/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : update

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-11-24 16:51:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `wp_action`
-- ----------------------------
DROP TABLE IF EXISTS `wp_action`;
CREATE TABLE `wp_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '行为唯一标识',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '行为说明',
  `remark` char(140) NOT NULL DEFAULT '' COMMENT '行为描述',
  `rule` text COMMENT '行为规则',
  `log` text COMMENT '日志规则',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='系统行为表';

-- ----------------------------
-- Records of wp_action
-- ----------------------------
INSERT INTO `wp_action` VALUES ('1', 'user_login', '用户登录', '积分+10，每天一次', 'table:member|field:score|condition:uid={$self} AND status>-1|rule:score+10|cycle:24|max:1;', '[user|get_nickname]在[time|time_format]登录了管理中心', '1', '0', '1393685660');
INSERT INTO `wp_action` VALUES ('2', 'add_article', '发布文章', '积分+5，每天上限5次', 'table:member|field:score|condition:uid={$self}|rule:score+5|cycle:24|max:5', '', '2', '0', '1380173180');
INSERT INTO `wp_action` VALUES ('3', 'review', '评论', '评论积分+1，无限制', 'table:member|field:score|condition:uid={$self}|rule:score+1', '', '2', '0', '1383285646');
INSERT INTO `wp_action` VALUES ('4', 'add_document', '发表文档', '积分+10，每天上限5次', 'table:member|field:score|condition:uid={$self}|rule:score+10|cycle:24|max:5', '[user|get_nickname]在[time|time_format]发表了一篇文章。\r\n表[model]，记录编号[record]。', '2', '0', '1386139726');
INSERT INTO `wp_action` VALUES ('5', 'add_document_topic', '发表讨论', '积分+5，每天上限10次', 'table:member|field:score|condition:uid={$self}|rule:score+5|cycle:24|max:10', '', '2', '1', '1383285551');
INSERT INTO `wp_action` VALUES ('6', 'update_config', '更新配置', '新增或修改或删除配置', '', '', '1', '1', '1383294988');
INSERT INTO `wp_action` VALUES ('7', 'update_model', '更新模型', '新增或修改模型', '', '', '1', '1', '1383295057');
INSERT INTO `wp_action` VALUES ('8', 'update_attribute', '更新属性', '新增或更新或删除属性', '', '', '1', '1', '1383295963');
INSERT INTO `wp_action` VALUES ('9', 'update_channel', '更新导航', '新增或修改或删除导航', '', '', '1', '1', '1383296301');
INSERT INTO `wp_action` VALUES ('10', 'update_menu', '更新菜单', '新增或修改或删除菜单', '', '', '1', '1', '1383296392');
INSERT INTO `wp_action` VALUES ('11', 'update_category', '更新分类', '新增或修改或删除分类', '', '', '1', '1', '1383296765');
INSERT INTO `wp_action` VALUES ('12', 'admin_login', '登录后台', '管理员登录后台', '', '[user|get_nickname]在[time|time_format]登录了后台', '1', '1', '1393685618');
INSERT INTO `wp_action` VALUES ('13', 'set_menu', '设置菜单', '设置菜单', '', '', '1', '-1', '0');

-- ----------------------------
-- Table structure for `wp_action_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_action_log`;
CREATE TABLE `wp_action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `action_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '行为id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户id',
  `action_ip` bigint(20) NOT NULL COMMENT '执行行为者ip',
  `model` varchar(50) NOT NULL DEFAULT '' COMMENT '触发行为的表',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '触发行为的数据id',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  PRIMARY KEY (`id`),
  KEY `action_ip_ix` (`action_ip`) USING BTREE,
  KEY `action_id_ix` (`action_id`) USING BTREE,
  KEY `user_id_ix` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=770 DEFAULT CHARSET=utf8 COMMENT='行为日志表';

-- ----------------------------
-- Records of wp_action_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_admin_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_admin_log`;
CREATE TABLE `wp_admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL COMMENT '用户ID',
  `ip` varchar(30) DEFAULT NULL COMMENT '用户IP地址',
  `content` varchar(255) NOT NULL COMMENT '日志内容',
  `mod` varchar(50) NOT NULL COMMENT '应用名',
  `cTime` int(10) DEFAULT NULL COMMENT '记录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_admin_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_analysis`
-- ----------------------------
DROP TABLE IF EXISTS `wp_analysis`;
CREATE TABLE `wp_analysis` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sports_id` int(10) DEFAULT NULL COMMENT 'sports_id',
  `type` varchar(30) DEFAULT NULL COMMENT 'type',
  `time` varchar(50) DEFAULT NULL COMMENT 'time',
  `total_count` int(10) DEFAULT '0' COMMENT 'total_count',
  `follow_count` int(10) DEFAULT '0' COMMENT 'follow_count',
  `aver_count` int(10) DEFAULT '0' COMMENT 'aver_count',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_analysis
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_app_category`
-- ----------------------------
DROP TABLE IF EXISTS `wp_app_category`;
CREATE TABLE `wp_app_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `icon` int(10) unsigned DEFAULT NULL COMMENT '分类图标',
  `title` varchar(255) DEFAULT NULL COMMENT '分类名',
  `sort` int(10) DEFAULT '0' COMMENT '排序号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_app_category
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_apps`
-- ----------------------------
DROP TABLE IF EXISTS `wp_apps`;
CREATE TABLE `wp_apps` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text COMMENT '插件描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `config` text COMMENT '配置',
  `author` varchar(40) DEFAULT '' COMMENT '作者',
  `version` varchar(20) DEFAULT '' COMMENT '版本号',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `has_adminlist` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台列表',
  `type` tinyint(1) DEFAULT '0' COMMENT '插件类型 0 普通插件 1 微信插件 2 易信插件',
  `cate_id` int(11) DEFAULT NULL,
  `is_show` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`) USING BTREE,
  KEY `sti` (`status`,`is_show`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=268 DEFAULT CHARSET=utf8 COMMENT='微信插件表';

-- ----------------------------
-- Records of wp_apps
-- ----------------------------
INSERT INTO `wp_apps` VALUES ('173', 'apps', '小程序导航', '汇总网友的小程序，提供给更多人查看', '1', null, '凡星', '0.1', '1478318718', '1', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('200', 'weixin', '微信管理', null, '1', null, '凡星', '0.1', '0', '1', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('203', 'credit', '积分等级', '这是一个临时描述', '1', null, '无名', '0.1', '0', '1', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('208', 'qr_admin', '扫码管理', '在服务号的情况下，可以自主创建一个二维码，并可指定扫码后用户自动分配到哪个用户组，绑定哪些标签', '1', null, '凡星', '0.1', '0', '1', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('209', 'public_bind', '一键绑定公众号', '', '1', '{\"ComponentVerifyTicket\":\"ticket@@@IoJL0-7aKWxUPWtr9bTdIwtkNFsaDp7QwvyG5mrqj-bPopwpJ0kJ1zVdvESsUBZz-C9bjZ9QKGPiPw3deHZZbw\"}', '凡星', '0.1', '0', '0', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('211', 'real_prize', '实物奖励', '实物奖励设置', '1', null, 'aManx', '0.1', '0', '1', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('228', 'sms', '短信服务', '短信服务，短信验证，短信发送', '1', null, 'jacy', '0.1', '0', '0', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('240', 'servicer', '工作授权', '关注公众号后，扫描授权二维码，获取工作权限', '1', null, 'jacy', '0.1', '0', '1', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('241', 'database_dictionary', '数据库字典', '自动生成数据库字典', '1', null, '凡星', '0.1', '0', '1', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('245', 'draw', '抽奖游戏', '功能主要有奖品设置，抽奖配置和抽奖统计', '1', null, '凡星', '0.1', '0', '1', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('248', 'shop', '商城', '支持后台发布商品 banner管理 前端多模板选择 订单管理等', '1', null, '凡星', '0.1', '0', '1', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('249', 'user_center', '微信用户中心', '实现3G首页、微信登录，微信用户绑定，微信用户信息初始化等基本功能', '1', null, '凡星', '0.1', '0', '1', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('250', 'prize', '奖品库', '支持的奖品有优惠券，现金红包，实物，中奖码，积分，微信卡券', '1', null, '凡星', '0.1', '0', '0', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('251', 'wei_site', '微官网', '微3G网站、支持分类管理，文章管理、底部导航管理、微信引导信息配置，微网站统计代码部署。同时支持首页多模板切换、信息列表多模板切换、信息详情模板切换、底部导航多模板切换。并配置有详细的模板二次开发教程', '1', null, '凡星', '0.1', '0', '0', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('258', 'sing_in', '签到', '粉丝每天签到可以获得积分。', '1', '', '淡然', '1.11', '0', '1', '0', null, '1');
INSERT INTO `wp_apps` VALUES ('267', 'coupon', '优惠券', '配合粉丝圈子，打造粉丝互动的运营激励基础', '1', '[]', '凡星', '0.1', '0', '1', '0', null, '1');

-- ----------------------------
-- Table structure for `wp_area`
-- ----------------------------
DROP TABLE IF EXISTS `wp_area`;
CREATE TABLE `wp_area` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(50) DEFAULT NULL COMMENT '地区名',
  `pid` int(10) DEFAULT NULL COMMENT '上级编号',
  `sort` int(10) DEFAULT '0' COMMENT '排序号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=659 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_area
-- ----------------------------
INSERT INTO `wp_area` VALUES ('1', '中国', '0', '0');
INSERT INTO `wp_area` VALUES ('2', '四川', '1', '0');
INSERT INTO `wp_area` VALUES ('3', '重庆', '1', '1');
INSERT INTO `wp_area` VALUES ('4', '陕西', '1', '2');
INSERT INTO `wp_area` VALUES ('5', '甘肃', '1', '3');
INSERT INTO `wp_area` VALUES ('6', '青海', '1', '4');
INSERT INTO `wp_area` VALUES ('7', '宁夏', '1', '5');
INSERT INTO `wp_area` VALUES ('8', '云南', '1', '6');
INSERT INTO `wp_area` VALUES ('9', '澳门', '1', '7');
INSERT INTO `wp_area` VALUES ('10', '贵州', '1', '8');
INSERT INTO `wp_area` VALUES ('11', '香港', '1', '9');
INSERT INTO `wp_area` VALUES ('12', '辽宁', '1', '10');
INSERT INTO `wp_area` VALUES ('13', '吉林', '1', '11');
INSERT INTO `wp_area` VALUES ('14', '黑龙江', '1', '12');
INSERT INTO `wp_area` VALUES ('15', '海南', '1', '13');
INSERT INTO `wp_area` VALUES ('16', '广东', '1', '14');
INSERT INTO `wp_area` VALUES ('17', '广西', '1', '15');
INSERT INTO `wp_area` VALUES ('18', '湖北', '1', '16');
INSERT INTO `wp_area` VALUES ('19', '湖南', '1', '17');
INSERT INTO `wp_area` VALUES ('20', '河南', '1', '18');
INSERT INTO `wp_area` VALUES ('21', '台湾', '1', '19');
INSERT INTO `wp_area` VALUES ('22', '北京', '1', '20');
INSERT INTO `wp_area` VALUES ('23', '河北', '1', '21');
INSERT INTO `wp_area` VALUES ('24', '天津', '1', '22');
INSERT INTO `wp_area` VALUES ('25', '内蒙古', '1', '23');
INSERT INTO `wp_area` VALUES ('26', '山西', '1', '24');
INSERT INTO `wp_area` VALUES ('27', '浙江', '1', '25');
INSERT INTO `wp_area` VALUES ('28', '江苏', '1', '26');
INSERT INTO `wp_area` VALUES ('29', '上海', '1', '27');
INSERT INTO `wp_area` VALUES ('30', '山东', '1', '28');
INSERT INTO `wp_area` VALUES ('31', '江西', '1', '29');
INSERT INTO `wp_area` VALUES ('32', '福建', '1', '30');
INSERT INTO `wp_area` VALUES ('33', '安徽', '1', '31');
INSERT INTO `wp_area` VALUES ('34', '西藏', '1', '32');
INSERT INTO `wp_area` VALUES ('35', '新疆', '1', '33');
INSERT INTO `wp_area` VALUES ('178', '凉山', '2', '0');
INSERT INTO `wp_area` VALUES ('179', '资阳', '2', '1');
INSERT INTO `wp_area` VALUES ('180', '成都', '2', '2');
INSERT INTO `wp_area` VALUES ('181', '自贡', '2', '3');
INSERT INTO `wp_area` VALUES ('182', '泸州', '2', '4');
INSERT INTO `wp_area` VALUES ('183', '攀枝花', '2', '5');
INSERT INTO `wp_area` VALUES ('184', '绵阳', '2', '6');
INSERT INTO `wp_area` VALUES ('185', '德阳', '2', '7');
INSERT INTO `wp_area` VALUES ('186', '遂宁', '2', '8');
INSERT INTO `wp_area` VALUES ('187', '广元', '2', '9');
INSERT INTO `wp_area` VALUES ('188', '乐山', '2', '10');
INSERT INTO `wp_area` VALUES ('189', '内江', '2', '11');
INSERT INTO `wp_area` VALUES ('190', '南充', '2', '12');
INSERT INTO `wp_area` VALUES ('191', '宜宾', '2', '13');
INSERT INTO `wp_area` VALUES ('192', '眉山', '2', '14');
INSERT INTO `wp_area` VALUES ('193', '达州', '2', '15');
INSERT INTO `wp_area` VALUES ('194', '广安', '2', '16');
INSERT INTO `wp_area` VALUES ('195', '巴中', '2', '17');
INSERT INTO `wp_area` VALUES ('196', '雅安', '2', '18');
INSERT INTO `wp_area` VALUES ('197', '甘孜', '2', '19');
INSERT INTO `wp_area` VALUES ('198', '阿坝', '2', '20');
INSERT INTO `wp_area` VALUES ('199', '酉阳', '3', '21');
INSERT INTO `wp_area` VALUES ('200', '彭水', '3', '22');
INSERT INTO `wp_area` VALUES ('201', '合川', '3', '23');
INSERT INTO `wp_area` VALUES ('202', '永川', '3', '24');
INSERT INTO `wp_area` VALUES ('203', '江津', '3', '25');
INSERT INTO `wp_area` VALUES ('204', '南川', '3', '26');
INSERT INTO `wp_area` VALUES ('205', '铜梁', '3', '27');
INSERT INTO `wp_area` VALUES ('206', '大足', '3', '28');
INSERT INTO `wp_area` VALUES ('207', '荣昌', '3', '29');
INSERT INTO `wp_area` VALUES ('208', '璧山', '3', '30');
INSERT INTO `wp_area` VALUES ('209', '长寿', '3', '31');
INSERT INTO `wp_area` VALUES ('210', '綦江', '3', '32');
INSERT INTO `wp_area` VALUES ('211', '潼南', '3', '33');
INSERT INTO `wp_area` VALUES ('212', '梁平', '3', '34');
INSERT INTO `wp_area` VALUES ('213', '城口', '3', '35');
INSERT INTO `wp_area` VALUES ('214', '石柱', '3', '36');
INSERT INTO `wp_area` VALUES ('215', '秀山', '3', '37');
INSERT INTO `wp_area` VALUES ('216', '万州', '3', '38');
INSERT INTO `wp_area` VALUES ('217', '渝中', '3', '39');
INSERT INTO `wp_area` VALUES ('218', '涪陵', '3', '40');
INSERT INTO `wp_area` VALUES ('219', '江北', '3', '41');
INSERT INTO `wp_area` VALUES ('220', '大渡口', '3', '42');
INSERT INTO `wp_area` VALUES ('221', '九龙坡', '3', '43');
INSERT INTO `wp_area` VALUES ('222', '沙坪坝', '3', '44');
INSERT INTO `wp_area` VALUES ('223', '北碚', '3', '45');
INSERT INTO `wp_area` VALUES ('224', '南岸', '3', '46');
INSERT INTO `wp_area` VALUES ('225', '黔江', '3', '47');
INSERT INTO `wp_area` VALUES ('226', '巫溪', '3', '48');
INSERT INTO `wp_area` VALUES ('227', '双桥', '3', '49');
INSERT INTO `wp_area` VALUES ('228', '万盛', '3', '50');
INSERT INTO `wp_area` VALUES ('229', '巴南', '3', '51');
INSERT INTO `wp_area` VALUES ('230', '渝北', '3', '52');
INSERT INTO `wp_area` VALUES ('231', '忠县', '3', '53');
INSERT INTO `wp_area` VALUES ('232', '武隆', '3', '54');
INSERT INTO `wp_area` VALUES ('233', '垫江', '3', '55');
INSERT INTO `wp_area` VALUES ('234', '丰都', '3', '56');
INSERT INTO `wp_area` VALUES ('235', '巫山', '3', '57');
INSERT INTO `wp_area` VALUES ('236', '奉节', '3', '58');
INSERT INTO `wp_area` VALUES ('237', '云阳', '3', '59');
INSERT INTO `wp_area` VALUES ('238', '开县', '3', '60');
INSERT INTO `wp_area` VALUES ('239', '商洛', '4', '61');
INSERT INTO `wp_area` VALUES ('240', '西安', '4', '62');
INSERT INTO `wp_area` VALUES ('241', '宝鸡', '4', '63');
INSERT INTO `wp_area` VALUES ('242', '铜川', '4', '64');
INSERT INTO `wp_area` VALUES ('243', '渭南', '4', '65');
INSERT INTO `wp_area` VALUES ('244', '咸阳', '4', '66');
INSERT INTO `wp_area` VALUES ('245', '汉中', '4', '67');
INSERT INTO `wp_area` VALUES ('246', '延安', '4', '68');
INSERT INTO `wp_area` VALUES ('247', '安康', '4', '69');
INSERT INTO `wp_area` VALUES ('248', '榆林', '4', '70');
INSERT INTO `wp_area` VALUES ('249', '定西', '5', '71');
INSERT INTO `wp_area` VALUES ('250', '庆阳', '5', '72');
INSERT INTO `wp_area` VALUES ('251', '陇南', '5', '73');
INSERT INTO `wp_area` VALUES ('252', '甘南', '5', '74');
INSERT INTO `wp_area` VALUES ('253', '临夏', '5', '75');
INSERT INTO `wp_area` VALUES ('254', '兰州', '5', '76');
INSERT INTO `wp_area` VALUES ('255', '金昌', '5', '77');
INSERT INTO `wp_area` VALUES ('256', '嘉峪关', '5', '78');
INSERT INTO `wp_area` VALUES ('257', '天水', '5', '79');
INSERT INTO `wp_area` VALUES ('258', '白银', '5', '80');
INSERT INTO `wp_area` VALUES ('259', '张掖', '5', '81');
INSERT INTO `wp_area` VALUES ('260', '武威', '5', '82');
INSERT INTO `wp_area` VALUES ('261', '酒泉', '5', '83');
INSERT INTO `wp_area` VALUES ('262', '平凉', '5', '84');
INSERT INTO `wp_area` VALUES ('263', '海南', '6', '85');
INSERT INTO `wp_area` VALUES ('264', '果洛', '6', '86');
INSERT INTO `wp_area` VALUES ('265', '玉树', '6', '87');
INSERT INTO `wp_area` VALUES ('266', '海东', '6', '88');
INSERT INTO `wp_area` VALUES ('267', '海北', '6', '89');
INSERT INTO `wp_area` VALUES ('268', '黄南', '6', '90');
INSERT INTO `wp_area` VALUES ('269', '海西', '6', '91');
INSERT INTO `wp_area` VALUES ('270', '西宁', '6', '92');
INSERT INTO `wp_area` VALUES ('271', '银川', '7', '93');
INSERT INTO `wp_area` VALUES ('272', '吴忠', '7', '94');
INSERT INTO `wp_area` VALUES ('273', '石嘴山', '7', '95');
INSERT INTO `wp_area` VALUES ('274', '中卫', '7', '96');
INSERT INTO `wp_area` VALUES ('275', '固原', '7', '97');
INSERT INTO `wp_area` VALUES ('276', '红河', '8', '98');
INSERT INTO `wp_area` VALUES ('277', '文山', '8', '99');
INSERT INTO `wp_area` VALUES ('278', '楚雄', '8', '100');
INSERT INTO `wp_area` VALUES ('279', '怒江', '8', '101');
INSERT INTO `wp_area` VALUES ('280', '德宏', '8', '102');
INSERT INTO `wp_area` VALUES ('281', '西双版纳', '8', '103');
INSERT INTO `wp_area` VALUES ('282', '大理', '8', '104');
INSERT INTO `wp_area` VALUES ('283', '迪庆', '8', '105');
INSERT INTO `wp_area` VALUES ('284', '昆明', '8', '106');
INSERT INTO `wp_area` VALUES ('285', '曲靖', '8', '107');
INSERT INTO `wp_area` VALUES ('286', '保山', '8', '108');
INSERT INTO `wp_area` VALUES ('287', '玉溪', '8', '109');
INSERT INTO `wp_area` VALUES ('288', '丽江', '8', '110');
INSERT INTO `wp_area` VALUES ('289', '昭通', '8', '111');
INSERT INTO `wp_area` VALUES ('290', '临沧', '8', '112');
INSERT INTO `wp_area` VALUES ('291', '普洱', '8', '113');
INSERT INTO `wp_area` VALUES ('292', 'None', '9', '114');
INSERT INTO `wp_area` VALUES ('293', '毕节', '10', '115');
INSERT INTO `wp_area` VALUES ('294', '黔东南', '10', '116');
INSERT INTO `wp_area` VALUES ('295', '黔南', '10', '117');
INSERT INTO `wp_area` VALUES ('296', '铜仁', '10', '118');
INSERT INTO `wp_area` VALUES ('297', '黔西南', '10', '119');
INSERT INTO `wp_area` VALUES ('298', '贵阳', '10', '120');
INSERT INTO `wp_area` VALUES ('299', '遵义', '10', '121');
INSERT INTO `wp_area` VALUES ('300', '六盘水', '10', '122');
INSERT INTO `wp_area` VALUES ('301', '安顺', '10', '123');
INSERT INTO `wp_area` VALUES ('302', 'None', '11', '124');
INSERT INTO `wp_area` VALUES ('303', '盘锦', '12', '125');
INSERT INTO `wp_area` VALUES ('304', '辽阳', '12', '126');
INSERT INTO `wp_area` VALUES ('305', '朝阳', '12', '127');
INSERT INTO `wp_area` VALUES ('306', '铁岭', '12', '128');
INSERT INTO `wp_area` VALUES ('307', '葫芦岛', '12', '129');
INSERT INTO `wp_area` VALUES ('308', '沈阳', '12', '130');
INSERT INTO `wp_area` VALUES ('309', '鞍山', '12', '131');
INSERT INTO `wp_area` VALUES ('310', '大连', '12', '132');
INSERT INTO `wp_area` VALUES ('311', '本溪', '12', '133');
INSERT INTO `wp_area` VALUES ('312', '抚顺', '12', '134');
INSERT INTO `wp_area` VALUES ('313', '锦州', '12', '135');
INSERT INTO `wp_area` VALUES ('314', '丹东', '12', '136');
INSERT INTO `wp_area` VALUES ('315', '阜新', '12', '137');
INSERT INTO `wp_area` VALUES ('316', '营口', '12', '138');
INSERT INTO `wp_area` VALUES ('317', '延边', '13', '139');
INSERT INTO `wp_area` VALUES ('318', '长春', '13', '140');
INSERT INTO `wp_area` VALUES ('319', '四平', '13', '141');
INSERT INTO `wp_area` VALUES ('320', '吉林', '13', '142');
INSERT INTO `wp_area` VALUES ('321', '通化', '13', '143');
INSERT INTO `wp_area` VALUES ('322', '辽源', '13', '144');
INSERT INTO `wp_area` VALUES ('323', '松原', '13', '145');
INSERT INTO `wp_area` VALUES ('324', '白山', '13', '146');
INSERT INTO `wp_area` VALUES ('325', '白城', '13', '147');
INSERT INTO `wp_area` VALUES ('326', '黑河', '14', '148');
INSERT INTO `wp_area` VALUES ('327', '牡丹江', '14', '149');
INSERT INTO `wp_area` VALUES ('328', '绥化', '14', '150');
INSERT INTO `wp_area` VALUES ('329', '哈尔滨', '14', '151');
INSERT INTO `wp_area` VALUES ('330', '大兴安岭', '14', '152');
INSERT INTO `wp_area` VALUES ('331', '鸡西', '14', '153');
INSERT INTO `wp_area` VALUES ('332', '齐齐哈尔', '14', '154');
INSERT INTO `wp_area` VALUES ('333', '双鸭山', '14', '155');
INSERT INTO `wp_area` VALUES ('334', '鹤岗', '14', '156');
INSERT INTO `wp_area` VALUES ('335', '伊春', '14', '157');
INSERT INTO `wp_area` VALUES ('336', '大庆', '14', '158');
INSERT INTO `wp_area` VALUES ('337', '七台河', '14', '159');
INSERT INTO `wp_area` VALUES ('338', '佳木斯', '14', '160');
INSERT INTO `wp_area` VALUES ('339', '乐东', '15', '161');
INSERT INTO `wp_area` VALUES ('340', '昌江', '15', '162');
INSERT INTO `wp_area` VALUES ('341', '白沙', '15', '163');
INSERT INTO `wp_area` VALUES ('342', '西沙', '15', '164');
INSERT INTO `wp_area` VALUES ('343', '琼中', '15', '165');
INSERT INTO `wp_area` VALUES ('344', '保亭', '15', '166');
INSERT INTO `wp_area` VALUES ('345', '陵水', '15', '167');
INSERT INTO `wp_area` VALUES ('346', '中沙', '15', '168');
INSERT INTO `wp_area` VALUES ('347', '南沙', '15', '169');
INSERT INTO `wp_area` VALUES ('348', '海口', '15', '170');
INSERT INTO `wp_area` VALUES ('349', '三亚', '15', '171');
INSERT INTO `wp_area` VALUES ('350', '五指山', '15', '172');
INSERT INTO `wp_area` VALUES ('351', '儋州', '15', '173');
INSERT INTO `wp_area` VALUES ('352', '琼海', '15', '174');
INSERT INTO `wp_area` VALUES ('353', '文昌', '15', '175');
INSERT INTO `wp_area` VALUES ('354', '东方', '15', '176');
INSERT INTO `wp_area` VALUES ('355', '万宁', '15', '177');
INSERT INTO `wp_area` VALUES ('356', '定安', '15', '178');
INSERT INTO `wp_area` VALUES ('357', '屯昌', '15', '179');
INSERT INTO `wp_area` VALUES ('358', '澄迈', '15', '180');
INSERT INTO `wp_area` VALUES ('359', '临高', '15', '181');
INSERT INTO `wp_area` VALUES ('360', '揭阳', '16', '182');
INSERT INTO `wp_area` VALUES ('361', '中山', '16', '183');
INSERT INTO `wp_area` VALUES ('362', '广州', '16', '184');
INSERT INTO `wp_area` VALUES ('363', '深圳', '16', '185');
INSERT INTO `wp_area` VALUES ('364', '韶关', '16', '186');
INSERT INTO `wp_area` VALUES ('365', '汕头', '16', '187');
INSERT INTO `wp_area` VALUES ('366', '珠海', '16', '188');
INSERT INTO `wp_area` VALUES ('367', '江门', '16', '189');
INSERT INTO `wp_area` VALUES ('368', '佛山', '16', '190');
INSERT INTO `wp_area` VALUES ('369', '茂名', '16', '191');
INSERT INTO `wp_area` VALUES ('370', '湛江', '16', '192');
INSERT INTO `wp_area` VALUES ('371', '惠州', '16', '193');
INSERT INTO `wp_area` VALUES ('372', '肇庆', '16', '194');
INSERT INTO `wp_area` VALUES ('373', '汕尾', '16', '195');
INSERT INTO `wp_area` VALUES ('374', '梅州', '16', '196');
INSERT INTO `wp_area` VALUES ('375', '阳江', '16', '197');
INSERT INTO `wp_area` VALUES ('376', '河源', '16', '198');
INSERT INTO `wp_area` VALUES ('377', '东莞', '16', '199');
INSERT INTO `wp_area` VALUES ('378', '清远', '16', '200');
INSERT INTO `wp_area` VALUES ('379', '潮州', '16', '201');
INSERT INTO `wp_area` VALUES ('380', '云浮', '16', '202');
INSERT INTO `wp_area` VALUES ('381', '贺州', '17', '203');
INSERT INTO `wp_area` VALUES ('382', '百色', '17', '204');
INSERT INTO `wp_area` VALUES ('383', '来宾', '17', '205');
INSERT INTO `wp_area` VALUES ('384', '河池', '17', '206');
INSERT INTO `wp_area` VALUES ('385', '崇左', '17', '207');
INSERT INTO `wp_area` VALUES ('386', '南宁', '17', '208');
INSERT INTO `wp_area` VALUES ('387', '桂林', '17', '209');
INSERT INTO `wp_area` VALUES ('388', '柳州', '17', '210');
INSERT INTO `wp_area` VALUES ('389', '北海', '17', '211');
INSERT INTO `wp_area` VALUES ('390', '梧州', '17', '212');
INSERT INTO `wp_area` VALUES ('391', '钦州', '17', '213');
INSERT INTO `wp_area` VALUES ('392', '防城港', '17', '214');
INSERT INTO `wp_area` VALUES ('393', '玉林', '17', '215');
INSERT INTO `wp_area` VALUES ('394', '贵港', '17', '216');
INSERT INTO `wp_area` VALUES ('395', '黄冈', '18', '217');
INSERT INTO `wp_area` VALUES ('396', '荆州', '18', '218');
INSERT INTO `wp_area` VALUES ('397', '随州', '18', '219');
INSERT INTO `wp_area` VALUES ('398', '咸宁', '18', '220');
INSERT INTO `wp_area` VALUES ('399', '神农架', '18', '221');
INSERT INTO `wp_area` VALUES ('400', '恩施', '18', '222');
INSERT INTO `wp_area` VALUES ('401', '武汉', '18', '223');
INSERT INTO `wp_area` VALUES ('402', '十堰', '18', '224');
INSERT INTO `wp_area` VALUES ('403', '黄石', '18', '225');
INSERT INTO `wp_area` VALUES ('404', '宜昌', '18', '226');
INSERT INTO `wp_area` VALUES ('405', '鄂州', '18', '227');
INSERT INTO `wp_area` VALUES ('406', '襄樊', '18', '228');
INSERT INTO `wp_area` VALUES ('407', '孝感', '18', '229');
INSERT INTO `wp_area` VALUES ('408', '荆门', '18', '230');
INSERT INTO `wp_area` VALUES ('409', '潜江', '18', '231');
INSERT INTO `wp_area` VALUES ('410', '仙桃', '18', '232');
INSERT INTO `wp_area` VALUES ('411', '天门', '18', '233');
INSERT INTO `wp_area` VALUES ('412', '永州', '19', '234');
INSERT INTO `wp_area` VALUES ('413', '郴州', '19', '235');
INSERT INTO `wp_area` VALUES ('414', '娄底', '19', '236');
INSERT INTO `wp_area` VALUES ('415', '怀化', '19', '237');
INSERT INTO `wp_area` VALUES ('416', '湘西', '19', '238');
INSERT INTO `wp_area` VALUES ('417', '长沙', '19', '239');
INSERT INTO `wp_area` VALUES ('418', '湘潭', '19', '240');
INSERT INTO `wp_area` VALUES ('419', '株洲', '19', '241');
INSERT INTO `wp_area` VALUES ('420', '邵阳', '19', '242');
INSERT INTO `wp_area` VALUES ('421', '衡阳', '19', '243');
INSERT INTO `wp_area` VALUES ('422', '常德', '19', '244');
INSERT INTO `wp_area` VALUES ('423', '岳阳', '19', '245');
INSERT INTO `wp_area` VALUES ('424', '益阳', '19', '246');
INSERT INTO `wp_area` VALUES ('425', '张家界', '19', '247');
INSERT INTO `wp_area` VALUES ('426', '漯河', '20', '248');
INSERT INTO `wp_area` VALUES ('427', '许昌', '20', '249');
INSERT INTO `wp_area` VALUES ('428', '南阳', '20', '250');
INSERT INTO `wp_area` VALUES ('429', '三门峡', '20', '251');
INSERT INTO `wp_area` VALUES ('430', '信阳', '20', '252');
INSERT INTO `wp_area` VALUES ('431', '商丘', '20', '253');
INSERT INTO `wp_area` VALUES ('432', '驻马店', '20', '254');
INSERT INTO `wp_area` VALUES ('433', '周口', '20', '255');
INSERT INTO `wp_area` VALUES ('434', '济源', '20', '256');
INSERT INTO `wp_area` VALUES ('435', '郑州', '20', '257');
INSERT INTO `wp_area` VALUES ('436', '洛阳', '20', '258');
INSERT INTO `wp_area` VALUES ('437', '开封', '20', '259');
INSERT INTO `wp_area` VALUES ('438', '安阳', '20', '260');
INSERT INTO `wp_area` VALUES ('439', '平顶山', '20', '261');
INSERT INTO `wp_area` VALUES ('440', '新乡', '20', '262');
INSERT INTO `wp_area` VALUES ('441', '鹤壁', '20', '263');
INSERT INTO `wp_area` VALUES ('442', '濮阳', '20', '264');
INSERT INTO `wp_area` VALUES ('443', '焦作', '20', '265');
INSERT INTO `wp_area` VALUES ('444', '屏东县', '21', '266');
INSERT INTO `wp_area` VALUES ('445', '澎湖县', '21', '267');
INSERT INTO `wp_area` VALUES ('446', '台东县', '21', '268');
INSERT INTO `wp_area` VALUES ('447', '花莲县', '21', '269');
INSERT INTO `wp_area` VALUES ('448', '台北市', '21', '270');
INSERT INTO `wp_area` VALUES ('449', '基隆市', '21', '271');
INSERT INTO `wp_area` VALUES ('450', '高雄市', '21', '272');
INSERT INTO `wp_area` VALUES ('451', '台南市', '21', '273');
INSERT INTO `wp_area` VALUES ('452', '台中市', '21', '274');
INSERT INTO `wp_area` VALUES ('453', '嘉义市', '21', '275');
INSERT INTO `wp_area` VALUES ('454', '新竹市', '21', '276');
INSERT INTO `wp_area` VALUES ('455', '宜兰县', '21', '277');
INSERT INTO `wp_area` VALUES ('456', '台北县', '21', '278');
INSERT INTO `wp_area` VALUES ('457', '新竹县', '21', '279');
INSERT INTO `wp_area` VALUES ('458', '桃园县', '21', '280');
INSERT INTO `wp_area` VALUES ('459', '台中县', '21', '281');
INSERT INTO `wp_area` VALUES ('460', '苗栗县', '21', '282');
INSERT INTO `wp_area` VALUES ('461', '南投县', '21', '283');
INSERT INTO `wp_area` VALUES ('462', '彰化县', '21', '284');
INSERT INTO `wp_area` VALUES ('463', '嘉义县', '21', '285');
INSERT INTO `wp_area` VALUES ('464', '云林县', '21', '286');
INSERT INTO `wp_area` VALUES ('465', '高雄县', '21', '287');
INSERT INTO `wp_area` VALUES ('466', '台南县', '21', '288');
INSERT INTO `wp_area` VALUES ('467', '房山', '22', '289');
INSERT INTO `wp_area` VALUES ('468', '大兴', '22', '290');
INSERT INTO `wp_area` VALUES ('469', '顺义', '22', '291');
INSERT INTO `wp_area` VALUES ('470', '通州', '22', '292');
INSERT INTO `wp_area` VALUES ('471', '昌平', '22', '293');
INSERT INTO `wp_area` VALUES ('472', '密云', '22', '294');
INSERT INTO `wp_area` VALUES ('473', '平谷', '22', '295');
INSERT INTO `wp_area` VALUES ('474', '延庆', '22', '296');
INSERT INTO `wp_area` VALUES ('475', '东城', '22', '297');
INSERT INTO `wp_area` VALUES ('476', '怀柔', '22', '298');
INSERT INTO `wp_area` VALUES ('477', '崇文', '22', '299');
INSERT INTO `wp_area` VALUES ('478', '西城', '22', '300');
INSERT INTO `wp_area` VALUES ('479', '朝阳', '22', '301');
INSERT INTO `wp_area` VALUES ('480', '宣武', '22', '302');
INSERT INTO `wp_area` VALUES ('481', '石景山', '22', '303');
INSERT INTO `wp_area` VALUES ('482', '丰台', '22', '304');
INSERT INTO `wp_area` VALUES ('483', '门头沟', '22', '305');
INSERT INTO `wp_area` VALUES ('484', '海淀', '22', '306');
INSERT INTO `wp_area` VALUES ('485', '衡水', '23', '307');
INSERT INTO `wp_area` VALUES ('486', '廊坊', '23', '308');
INSERT INTO `wp_area` VALUES ('487', '石家庄', '23', '309');
INSERT INTO `wp_area` VALUES ('488', '秦皇岛', '23', '310');
INSERT INTO `wp_area` VALUES ('489', '唐山', '23', '311');
INSERT INTO `wp_area` VALUES ('490', '邢台', '23', '312');
INSERT INTO `wp_area` VALUES ('491', '邯郸', '23', '313');
INSERT INTO `wp_area` VALUES ('492', '张家口', '23', '314');
INSERT INTO `wp_area` VALUES ('493', '保定', '23', '315');
INSERT INTO `wp_area` VALUES ('494', '沧州', '23', '316');
INSERT INTO `wp_area` VALUES ('495', '承德', '23', '317');
INSERT INTO `wp_area` VALUES ('496', '西青', '24', '318');
INSERT INTO `wp_area` VALUES ('497', '东丽', '24', '319');
INSERT INTO `wp_area` VALUES ('498', '北辰', '24', '320');
INSERT INTO `wp_area` VALUES ('499', '津南', '24', '321');
INSERT INTO `wp_area` VALUES ('500', '宁河', '24', '322');
INSERT INTO `wp_area` VALUES ('501', '武清', '24', '323');
INSERT INTO `wp_area` VALUES ('502', '静海', '24', '324');
INSERT INTO `wp_area` VALUES ('503', '宝坻', '24', '325');
INSERT INTO `wp_area` VALUES ('504', '和平', '24', '326');
INSERT INTO `wp_area` VALUES ('505', '河西', '24', '327');
INSERT INTO `wp_area` VALUES ('506', '河东', '24', '328');
INSERT INTO `wp_area` VALUES ('507', '河北', '24', '329');
INSERT INTO `wp_area` VALUES ('508', '南开', '24', '330');
INSERT INTO `wp_area` VALUES ('509', '塘沽', '24', '331');
INSERT INTO `wp_area` VALUES ('510', '红桥', '24', '332');
INSERT INTO `wp_area` VALUES ('511', '大港', '24', '333');
INSERT INTO `wp_area` VALUES ('512', '汉沽', '24', '334');
INSERT INTO `wp_area` VALUES ('513', '蓟县', '24', '335');
INSERT INTO `wp_area` VALUES ('514', '锡林郭勒', '25', '336');
INSERT INTO `wp_area` VALUES ('515', '兴安', '25', '337');
INSERT INTO `wp_area` VALUES ('516', '阿拉善', '25', '338');
INSERT INTO `wp_area` VALUES ('517', '呼和浩特', '25', '339');
INSERT INTO `wp_area` VALUES ('518', '乌海', '25', '340');
INSERT INTO `wp_area` VALUES ('519', '包头', '25', '341');
INSERT INTO `wp_area` VALUES ('520', '通辽', '25', '342');
INSERT INTO `wp_area` VALUES ('521', '赤峰', '25', '343');
INSERT INTO `wp_area` VALUES ('522', '呼伦贝尔', '25', '344');
INSERT INTO `wp_area` VALUES ('523', '鄂尔多斯', '25', '345');
INSERT INTO `wp_area` VALUES ('524', '乌兰察布', '25', '346');
INSERT INTO `wp_area` VALUES ('525', '巴彦淖尔', '25', '347');
INSERT INTO `wp_area` VALUES ('526', '吕梁', '26', '348');
INSERT INTO `wp_area` VALUES ('527', '临汾', '26', '349');
INSERT INTO `wp_area` VALUES ('528', '太原', '26', '350');
INSERT INTO `wp_area` VALUES ('529', '阳泉', '26', '351');
INSERT INTO `wp_area` VALUES ('530', '大同', '26', '352');
INSERT INTO `wp_area` VALUES ('531', '晋城', '26', '353');
INSERT INTO `wp_area` VALUES ('532', '长治', '26', '354');
INSERT INTO `wp_area` VALUES ('533', '晋中', '26', '355');
INSERT INTO `wp_area` VALUES ('534', '朔州', '26', '356');
INSERT INTO `wp_area` VALUES ('535', '忻州', '26', '357');
INSERT INTO `wp_area` VALUES ('536', '运城', '26', '358');
INSERT INTO `wp_area` VALUES ('537', '丽水', '27', '359');
INSERT INTO `wp_area` VALUES ('538', '台州', '27', '360');
INSERT INTO `wp_area` VALUES ('539', '杭州', '27', '361');
INSERT INTO `wp_area` VALUES ('540', '温州', '27', '362');
INSERT INTO `wp_area` VALUES ('541', '宁波', '27', '363');
INSERT INTO `wp_area` VALUES ('542', '湖州', '27', '364');
INSERT INTO `wp_area` VALUES ('543', '嘉兴', '27', '365');
INSERT INTO `wp_area` VALUES ('544', '金华', '27', '366');
INSERT INTO `wp_area` VALUES ('545', '绍兴', '27', '367');
INSERT INTO `wp_area` VALUES ('546', '舟山', '27', '368');
INSERT INTO `wp_area` VALUES ('547', '衢州', '27', '369');
INSERT INTO `wp_area` VALUES ('548', '镇江', '28', '370');
INSERT INTO `wp_area` VALUES ('549', '扬州', '28', '371');
INSERT INTO `wp_area` VALUES ('550', '宿迁', '28', '372');
INSERT INTO `wp_area` VALUES ('551', '泰州', '28', '373');
INSERT INTO `wp_area` VALUES ('552', '南京', '28', '374');
INSERT INTO `wp_area` VALUES ('553', '徐州', '28', '375');
INSERT INTO `wp_area` VALUES ('554', '无锡', '28', '376');
INSERT INTO `wp_area` VALUES ('555', '苏州', '28', '377');
INSERT INTO `wp_area` VALUES ('556', '常州', '28', '378');
INSERT INTO `wp_area` VALUES ('557', '连云港', '28', '379');
INSERT INTO `wp_area` VALUES ('558', '南通', '28', '380');
INSERT INTO `wp_area` VALUES ('559', '盐城', '28', '381');
INSERT INTO `wp_area` VALUES ('560', '淮安', '28', '382');
INSERT INTO `wp_area` VALUES ('561', '杨浦', '29', '383');
INSERT INTO `wp_area` VALUES ('562', '南汇', '29', '384');
INSERT INTO `wp_area` VALUES ('563', '宝山', '29', '385');
INSERT INTO `wp_area` VALUES ('564', '闵行', '29', '386');
INSERT INTO `wp_area` VALUES ('565', '浦东新', '29', '387');
INSERT INTO `wp_area` VALUES ('566', '嘉定', '29', '388');
INSERT INTO `wp_area` VALUES ('567', '松江', '29', '389');
INSERT INTO `wp_area` VALUES ('568', '金山', '29', '390');
INSERT INTO `wp_area` VALUES ('569', '崇明', '29', '391');
INSERT INTO `wp_area` VALUES ('570', '奉贤', '29', '392');
INSERT INTO `wp_area` VALUES ('571', '青浦', '29', '393');
INSERT INTO `wp_area` VALUES ('572', '黄浦', '29', '394');
INSERT INTO `wp_area` VALUES ('573', '卢湾', '29', '395');
INSERT INTO `wp_area` VALUES ('574', '长宁', '29', '396');
INSERT INTO `wp_area` VALUES ('575', '徐汇', '29', '397');
INSERT INTO `wp_area` VALUES ('576', '普陀', '29', '398');
INSERT INTO `wp_area` VALUES ('577', '静安', '29', '399');
INSERT INTO `wp_area` VALUES ('578', '虹口', '29', '400');
INSERT INTO `wp_area` VALUES ('579', '闸北', '29', '401');
INSERT INTO `wp_area` VALUES ('580', '日照', '30', '402');
INSERT INTO `wp_area` VALUES ('581', '威海', '30', '403');
INSERT INTO `wp_area` VALUES ('582', '临沂', '30', '404');
INSERT INTO `wp_area` VALUES ('583', '莱芜', '30', '405');
INSERT INTO `wp_area` VALUES ('584', '聊城', '30', '406');
INSERT INTO `wp_area` VALUES ('585', '德州', '30', '407');
INSERT INTO `wp_area` VALUES ('586', '菏泽', '30', '408');
INSERT INTO `wp_area` VALUES ('587', '滨州', '30', '409');
INSERT INTO `wp_area` VALUES ('588', '济南', '30', '410');
INSERT INTO `wp_area` VALUES ('589', '淄博', '30', '411');
INSERT INTO `wp_area` VALUES ('590', '青岛', '30', '412');
INSERT INTO `wp_area` VALUES ('591', '东营', '30', '413');
INSERT INTO `wp_area` VALUES ('592', '枣庄', '30', '414');
INSERT INTO `wp_area` VALUES ('593', '潍坊', '30', '415');
INSERT INTO `wp_area` VALUES ('594', '烟台', '30', '416');
INSERT INTO `wp_area` VALUES ('595', '泰安', '30', '417');
INSERT INTO `wp_area` VALUES ('596', '济宁', '30', '418');
INSERT INTO `wp_area` VALUES ('597', '上饶', '31', '419');
INSERT INTO `wp_area` VALUES ('598', '抚州', '31', '420');
INSERT INTO `wp_area` VALUES ('599', '南昌', '31', '421');
INSERT INTO `wp_area` VALUES ('600', '萍乡', '31', '422');
INSERT INTO `wp_area` VALUES ('601', '景德镇', '31', '423');
INSERT INTO `wp_area` VALUES ('602', '新余', '31', '424');
INSERT INTO `wp_area` VALUES ('603', '九江', '31', '425');
INSERT INTO `wp_area` VALUES ('604', '赣州', '31', '426');
INSERT INTO `wp_area` VALUES ('605', '鹰潭', '31', '427');
INSERT INTO `wp_area` VALUES ('606', '宜春', '31', '428');
INSERT INTO `wp_area` VALUES ('607', '吉安', '31', '429');
INSERT INTO `wp_area` VALUES ('608', '福州', '32', '430');
INSERT INTO `wp_area` VALUES ('609', '莆田', '32', '431');
INSERT INTO `wp_area` VALUES ('610', '厦门', '32', '432');
INSERT INTO `wp_area` VALUES ('611', '泉州', '32', '433');
INSERT INTO `wp_area` VALUES ('612', '三明', '32', '434');
INSERT INTO `wp_area` VALUES ('613', '南平', '32', '435');
INSERT INTO `wp_area` VALUES ('614', '漳州', '32', '436');
INSERT INTO `wp_area` VALUES ('615', '宁德', '32', '437');
INSERT INTO `wp_area` VALUES ('616', '龙岩', '32', '438');
INSERT INTO `wp_area` VALUES ('617', '滁州', '33', '439');
INSERT INTO `wp_area` VALUES ('618', '黄山', '33', '440');
INSERT INTO `wp_area` VALUES ('619', '宿州', '33', '441');
INSERT INTO `wp_area` VALUES ('620', '阜阳', '33', '442');
INSERT INTO `wp_area` VALUES ('621', '六安', '33', '443');
INSERT INTO `wp_area` VALUES ('622', '巢湖', '33', '444');
INSERT INTO `wp_area` VALUES ('623', '池州', '33', '445');
INSERT INTO `wp_area` VALUES ('624', '亳州', '33', '446');
INSERT INTO `wp_area` VALUES ('625', '宣城', '33', '447');
INSERT INTO `wp_area` VALUES ('626', '合肥', '33', '448');
INSERT INTO `wp_area` VALUES ('627', '蚌埠', '33', '449');
INSERT INTO `wp_area` VALUES ('628', '芜湖', '33', '450');
INSERT INTO `wp_area` VALUES ('629', '马鞍山', '33', '451');
INSERT INTO `wp_area` VALUES ('630', '淮南', '33', '452');
INSERT INTO `wp_area` VALUES ('631', '铜陵', '33', '453');
INSERT INTO `wp_area` VALUES ('632', '淮北', '33', '454');
INSERT INTO `wp_area` VALUES ('633', '安庆', '33', '455');
INSERT INTO `wp_area` VALUES ('634', '那曲', '34', '456');
INSERT INTO `wp_area` VALUES ('635', '阿里', '34', '457');
INSERT INTO `wp_area` VALUES ('636', '林芝', '34', '458');
INSERT INTO `wp_area` VALUES ('637', '昌都', '34', '459');
INSERT INTO `wp_area` VALUES ('638', '山南', '34', '460');
INSERT INTO `wp_area` VALUES ('639', '日喀则', '34', '461');
INSERT INTO `wp_area` VALUES ('640', '拉萨', '34', '462');
INSERT INTO `wp_area` VALUES ('641', '博尔塔拉', '35', '463');
INSERT INTO `wp_area` VALUES ('642', '吐鲁番', '35', '464');
INSERT INTO `wp_area` VALUES ('643', '哈密', '35', '465');
INSERT INTO `wp_area` VALUES ('644', '昌吉', '35', '466');
INSERT INTO `wp_area` VALUES ('645', '和田', '35', '467');
INSERT INTO `wp_area` VALUES ('646', '喀什', '35', '468');
INSERT INTO `wp_area` VALUES ('647', '克孜勒苏', '35', '469');
INSERT INTO `wp_area` VALUES ('648', '巴音郭楞', '35', '470');
INSERT INTO `wp_area` VALUES ('649', '阿克苏', '35', '471');
INSERT INTO `wp_area` VALUES ('650', '伊犁', '35', '472');
INSERT INTO `wp_area` VALUES ('651', '塔城', '35', '473');
INSERT INTO `wp_area` VALUES ('652', '乌鲁木齐', '35', '474');
INSERT INTO `wp_area` VALUES ('653', '阿勒泰', '35', '475');
INSERT INTO `wp_area` VALUES ('654', '克拉玛依', '35', '476');
INSERT INTO `wp_area` VALUES ('655', '石河子', '35', '477');
INSERT INTO `wp_area` VALUES ('656', '图木舒克', '35', '478');
INSERT INTO `wp_area` VALUES ('657', '阿拉尔', '35', '479');
INSERT INTO `wp_area` VALUES ('658', '五家渠', '35', '480');

-- ----------------------------
-- Table structure for `wp_attachment`
-- ----------------------------
DROP TABLE IF EXISTS `wp_attachment`;
CREATE TABLE `wp_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) DEFAULT '0' COMMENT '用户ID',
  `title` char(30) NOT NULL DEFAULT '' COMMENT '附件显示名',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '附件类型',
  `source` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资源ID',
  `record_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '关联记录ID',
  `download` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '附件大小',
  `dir` int(12) unsigned NOT NULL DEFAULT '0' COMMENT '上级目录ID',
  `sort` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `idx_record_status` (`record_id`,`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='附件表';

-- ----------------------------
-- Records of wp_attachment
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_auth_extend`
-- ----------------------------
DROP TABLE IF EXISTS `wp_auth_extend`;
CREATE TABLE `wp_auth_extend` (
  `group_id` mediumint(10) unsigned NOT NULL COMMENT '用户id',
  `extend_id` mediumint(8) unsigned NOT NULL COMMENT '扩展表中数据的id',
  `type` tinyint(1) unsigned NOT NULL COMMENT '扩展类型标识 1:栏目分类权限;2:模型权限',
  UNIQUE KEY `group_extend_type` (`group_id`,`extend_id`,`type`) USING BTREE,
  KEY `uid` (`group_id`) USING BTREE,
  KEY `group_id` (`extend_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户组与分类的对应关系表';

-- ----------------------------
-- Records of wp_auth_extend
-- ----------------------------
INSERT INTO `wp_auth_extend` VALUES ('1', '1', '1');
INSERT INTO `wp_auth_extend` VALUES ('1', '1', '2');
INSERT INTO `wp_auth_extend` VALUES ('1', '2', '1');
INSERT INTO `wp_auth_extend` VALUES ('1', '2', '2');
INSERT INTO `wp_auth_extend` VALUES ('1', '3', '1');
INSERT INTO `wp_auth_extend` VALUES ('1', '3', '2');
INSERT INTO `wp_auth_extend` VALUES ('1', '4', '1');
INSERT INTO `wp_auth_extend` VALUES ('1', '37', '1');

-- ----------------------------
-- Table structure for `wp_auth_group`
-- ----------------------------
DROP TABLE IF EXISTS `wp_auth_group`;
CREATE TABLE `wp_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(30) DEFAULT NULL COMMENT '分组名称',
  `icon` int(10) unsigned DEFAULT NULL COMMENT '图标',
  `description` text COMMENT '描述信息',
  `status` tinyint(2) DEFAULT '1' COMMENT '状态',
  `type` tinyint(2) DEFAULT '1' COMMENT '类型',
  `rules` text COMMENT '权限',
  `manager_id` int(10) DEFAULT '0' COMMENT '管理员ID',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '是否默认自动加入',
  `qr_code` varchar(255) DEFAULT NULL COMMENT '微信二维码',
  `wechat_group_id` int(10) DEFAULT '-1' COMMENT '微信端的分组ID',
  `wechat_group_name` varchar(100) DEFAULT NULL COMMENT '微信端的分组名',
  `wechat_group_count` int(10) DEFAULT NULL COMMENT '微信端用户数',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否已删除',
  `pbid` varchar(100) DEFAULT NULL COMMENT '公众号id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=369 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_auth_group
-- ----------------------------
INSERT INTO `wp_auth_group` VALUES ('1', '默认用户组', null, '通用的用户组', '1', '0', '1,2,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,79,80,81,82,83,84,86,87,88,89,90,91,92,93,94,95,96,97,100,102,103,105,106', '0', '0', null, null, null, null, '0', '73');
INSERT INTO `wp_auth_group` VALUES ('2', '公众号粉丝组', null, '所有从公众号自动注册的粉丝用户都会自动加入这个用户组', '1', '0', '1,2,5,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,79,80,82,83,84,88,89,90,91,92,93,96,97,100,102,103,195', '0', '0', null, null, null, null, '0', '73');
INSERT INTO `wp_auth_group` VALUES ('3', '公众号管理组', null, '公众号管理员注册时会自动加入这个用户组', '1', '0', '', '0', '0', null, null, null, null, '0', '73');

-- ----------------------------
-- Table structure for `wp_auth_group_access`
-- ----------------------------
DROP TABLE IF EXISTS `wp_auth_group_access`;
CREATE TABLE `wp_auth_group_access` (
  `uid` int(10) DEFAULT NULL COMMENT '用户id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `group_id` (`group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_auth_group_access
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `wp_auth_rule`;
CREATE TABLE `wp_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `title` char(100) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识',
  `mod` char(50) NOT NULL DEFAULT 'common' COMMENT ' 所属应用/模块，核心系统的规则使用common',
  `type` char(30) NOT NULL DEFAULT 'custom_app' COMMENT '规则类型，可能的值有： common_app custom_app wap public_mod public_interface',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`mod`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=422 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_auth_rule
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_auto_reply`
-- ----------------------------
DROP TABLE IF EXISTS `wp_auto_reply`;
CREATE TABLE `wp_auto_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `video_id` int(10) DEFAULT NULL COMMENT '视频素材id',
  `voice_id` int(10) DEFAULT NULL COMMENT '语音素材id',
  `image_material` int(10) DEFAULT NULL COMMENT '素材图片id',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员ID',
  `img_id` int(10) unsigned DEFAULT NULL COMMENT '上传图片',
  `news_id` int(10) DEFAULT NULL COMMENT '图文',
  `msg_type` char(50) DEFAULT 'text' COMMENT '消息类型',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `text_id` int(10) DEFAULT NULL COMMENT '文本素材id',
  `pbid` varchar(50) DEFAULT NULL COMMENT '公众号id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_auto_reply
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_award`
-- ----------------------------
DROP TABLE IF EXISTS `wp_award`;
CREATE TABLE `wp_award` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `img` int(10) DEFAULT NULL COMMENT '奖品图片',
  `name` varchar(255) NOT NULL COMMENT '奖项名称',
  `score` int(10) DEFAULT '0' COMMENT '积分数',
  `award_type` varchar(30) DEFAULT '1' COMMENT '奖品类型',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '商品价格',
  `explain` text COMMENT '奖品说明',
  `count` int(10) DEFAULT '0' COMMENT '奖品数量',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `coupon_id` char(50) DEFAULT NULL COMMENT '选择赠送券',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '现金',
  `aim_table` varchar(255) DEFAULT NULL COMMENT '活动标识',
  `cTime` int(10) DEFAULT '0' COMMENT '创建时间',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_award
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_buy_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_buy_log`;
CREATE TABLE `wp_buy_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pay` float DEFAULT NULL COMMENT '消费金额',
  `sn_id` int(10) DEFAULT NULL COMMENT '优惠卷',
  `pay_type` char(10) DEFAULT NULL COMMENT '支付方式',
  `branch_id` int(10) DEFAULT '0' COMMENT '消费门店',
  `member_id` int(10) DEFAULT NULL COMMENT '会员卡id',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员ID',
  `username` varchar(255) DEFAULT NULL COMMENT '姓名',
  `phone` varchar(255) DEFAULT NULL COMMENT '手机号',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_buy_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_cache_keys`
-- ----------------------------
DROP TABLE IF EXISTS `wp_cache_keys`;
CREATE TABLE `wp_cache_keys` (
  `table_name` varchar(50) DEFAULT NULL,
  `key_rule` varchar(255) DEFAULT NULL,
  `map_field` varchar(255) DEFAULT NULL,
  `data_field` varchar(255) DEFAULT NULL,
  `extra` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wp_cache_keys
-- ----------------------------
INSERT INTO `wp_cache_keys` VALUES ('wp_public_follow', 'wpcache_wp_public_follow_uid-[uid]_wpid-[wpid]', '{\"uid\":6000,\"wpid\":\"37\"}', 'openid', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_apps', 'wpcache_wp_apps_status-[status]', '{\"status\":1}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_picture', 'wpcache_wp_picture_status-[status]_id-[id]', '{\"status\":1,\"id\":2151}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_shop_page', 'wpcache_wp_shop_page_id-[id]', '{\"id\":\"71\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_shop_goods', 'wpcache_wp_shop_goods_id-[id]', '{\"id\":\"99\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_shop_goods_comment', 'wpcache_wp_shop_goods_comment_is_show-[is_show]_goods_id-[goods_id]_wpid-[wpid]', '{\"is_show\":1,\"goods_id\":99,\"wpid\":37}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_shop_collect', 'wpcache_wp_shop_collect_uid-[uid]', '{\"uid\":\"109\"}', 'goods_id,cTime', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_shop_order', 'wpcache_wp_shop_order_id-[id]', '{\"id\":\"261\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_seckill', 'wpcache_wp_seckill_id-[id]', '{\"id\":\"26\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_manager_menu', 'wpcache_wp_manager_menu_is_hide-[is_hide]', '{\"is_hide\":\"0\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('Publics', 'wpcache_Publics_id-[id]', '{\"id\":\"23\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_public_config', 'wpcache_wp_public_config_pkey-[pkey]_wpid-[wpid]', '{\"pkey\":\"shop_shop\",\"wpid\":41}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_model', 'wpcache_wp_model_name-[name]', '{\"name\":\"servicer\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('User', 'wpcache_User_uid-[uid]', '{\"uid\":\"104\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('Shop', 'wpcache_Shop_id-[id]', '{\"id\":\"46\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_file', 'wpcache_wp_file_id-[id]', '{\"id\":\"32\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_publics', 'wpcache_wp_publics_public_id-[public_id]', '{\"public_id\":\"gh_fd7d36352d19\"}', 'id', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_publics', 'wpcache_wp_publics', '[]', 'id,appid', '');
INSERT INTO `wp_cache_keys` VALUES ('Coupon', 'wpcache_Coupon_id-[id]', '{\"id\":\"1\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_shop', 'wpcache_wp_shop_wpid-[wpid]', '{\"wpid\":\"46\"}', 'id', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_sn_code', 'wpcache_wp_sn_code_uid-[uid]_target_id-[target_id]_addon-[addon]', '{\"uid\":104,\"target_id\":\"37\",\"addon\":\"ShopCoupon\"}', 'id', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_sn_code', 'wpcache_wp_sn_code_target_id-[target_id]_addon-[addon]', '{\"target_id\":37,\"addon\":\"ShopCoupon\"}', 'id', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_sn_code', 'wpcache_wp_sn_code_id-[id]', '{\"id\":\"140\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_sn_code', 'wpcache_wp_sn_code_uid-[uid]_addon-[addon]', '{\"uid\":104,\"addon\":\"ShopCoupon\"}', 'id', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_publics', 'wpcache_wp_publics_id-[id]', '{\"id\":\"23\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_user', 'wpcache_wp_user_uid-[uid]', '{\"uid\":\"104\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_shop', 'wpcache_wp_shop_id-[id]', '{\"id\":\"46\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_coupon', 'wpcache_wp_coupon_id-[id]', '{\"id\":\"1\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_credit_config', 'wpcache_wp_credit_config_wpid-[wpid]', '{\"wpid\":\"0|25\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_sn_code', 'wpcache_wp_sn_code_uid-[uid]_addon-[addon]_can_use-[can_use]', '{\"uid\":101,\"addon\":\"Coupon\",\"can_use\":1}', 'id', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_collage', 'wpcache_wp_collage_id-[id]', '{\"id\":\"20\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_haggle', 'wpcache_wp_haggle_id-[id]', '{\"id\":\"28\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_collage_goods', 'wpcache_wp_collage_goods_id-[id]', '{\"id\":\"28\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_seckill_goods', 'wpcache_wp_seckill_goods_id-[id]', '{\"id\":\"45\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_lottery_games_award_link', 'wpcache_wp_lottery_games_award_link_games_id-[games_id]_wpid-[wpid]', '{\"games_id\":\"25\",\"wpid\":37}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_award', 'wpcache_wp_award_id-[id]', '{\"id\":\"51\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_seckill_goods', 'wpcache_wp_seckill_goods_seckill_id-[seckill_id]', '{\"seckill_id\":29}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_games', 'wpcache_wp_games_id-[id]', '{\"id\":\"25\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_event_prizes', 'wpcache_wp_event_prizes_event_id-[event_id]', '{\"event_id\":\"25\"}', 'start_num,end_num,prize_list,sort', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_lucky_follow', 'wpcache_wp_lucky_follow_id-[id]', '{\"id\":\"141\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_shop_statistics_follow', 'wpcache_wp_shop_statistics_follow_wpid-[wpid]_uid-[uid]', '{\"wpid\":37,\"uid\":73}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_sn_code', 'wpcache_wp_sn_code_uid-[uid]', '{\"uid\":1}', 'id', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_sn_code', 'wpcache_wp_sn_code_uid-[uid]_can_use-[can_use]', '{\"uid\":1,\"can_use\":1}', 'id', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_sn_code', 'wpcache_wp_sn_code_uid-[uid]_target_id-[target_id]', '{\"uid\":1,\"target_id\":\"1\"}', 'id', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_menu', 'wpcache_wp_menu_is_hide-[is_hide]', '{\"is_hide\":\"0\"}', '', '');
INSERT INTO `wp_cache_keys` VALUES ('wp_sn_code', 'wpcache_wp_sn_code_target_id-[target_id]', '{\"target_id\":\"2\"}', 'id', '');

-- ----------------------------
-- Table structure for `wp_chat`
-- ----------------------------
DROP TABLE IF EXISTS `wp_chat`;
CREATE TABLE `wp_chat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `to_uid` int(10) DEFAULT NULL COMMENT 'to_uid',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `content` text COMMENT '内容',
  `create_at` int(10) DEFAULT NULL COMMENT '时间',
  `is_read` tinyint(2) DEFAULT '0' COMMENT '已读',
  `come_from` varchar(100) DEFAULT NULL,
  `referer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=634 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_chat
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_collage`
-- ----------------------------
DROP TABLE IF EXISTS `wp_collage`;
CREATE TABLE `wp_collage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) NOT NULL COMMENT '活动名称',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '活动宣传图',
  `start_time` int(10) NOT NULL COMMENT '活动开始时间',
  `end_time` int(10) NOT NULL COMMENT '活动结束时间',
  `is_subscribe` tinyint(2) DEFAULT NULL COMMENT '是否需要关注公众号才能参加',
  `is_member` tinyint(2) DEFAULT NULL COMMENT '是否需要成为会员才能参加',
  `type` tinyint(2) DEFAULT NULL COMMENT '显示类型',
  `content` text COMMENT '活动描述',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `status` tinyint(2) DEFAULT NULL COMMENT '状态',
  `member_limit` int(10) NOT NULL DEFAULT '2' COMMENT '参团人数',
  `goods_limit` int(10) NOT NULL DEFAULT '1' COMMENT '商品限购',
  `is_open` tinyint(2) DEFAULT '1' COMMENT '拼团设置',
  `robot_open` tinyint(2) DEFAULT '0' COMMENT '模拟成团',
  `intro` text COMMENT '玩法说明',
  `group_limit_day` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_collage
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_collage_goods`
-- ----------------------------
DROP TABLE IF EXISTS `wp_collage_goods`;
CREATE TABLE `wp_collage_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `shop_goods_id` int(10) DEFAULT NULL COMMENT '商品来源',
  `title` varchar(255) DEFAULT NULL COMMENT '商品名称',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '商品封面图',
  `express` decimal(10,2) DEFAULT NULL COMMENT '邮费',
  `collage_id` int(10) DEFAULT NULL COMMENT '活动Id',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `send_type` varchar(30) NOT NULL DEFAULT '1' COMMENT '收货方式',
  `stores_ids` varchar(100) DEFAULT NULL COMMENT '自提门店',
  `is_all_store` tinyint(2) DEFAULT '0' COMMENT '店门类型',
  `visit_count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_collage_goods
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_collage_group`
-- ----------------------------
DROP TABLE IF EXISTS `wp_collage_group`;
CREATE TABLE `wp_collage_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '开团用户ID',
  `create_at` int(10) DEFAULT NULL COMMENT '参与时间',
  `goods_id` int(10) DEFAULT NULL COMMENT '拼团商品ID',
  `order_count` int(10) DEFAULT NULL COMMENT '拼团人数',
  `status` tinyint(2) DEFAULT '0' COMMENT '状态',
  `collage_id` int(10) DEFAULT NULL COMMENT '活动ID',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_collage_group
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_collage_order`
-- ----------------------------
DROP TABLE IF EXISTS `wp_collage_order`;
CREATE TABLE `wp_collage_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `collage_id` int(10) DEFAULT NULL COMMENT '活动Id',
  `order_id` int(10) DEFAULT NULL COMMENT '订单Id',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `create_at` int(10) DEFAULT NULL COMMENT '下单时间',
  `sale_price` decimal(10,2) DEFAULT NULL COMMENT '拼团价格',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品ID',
  `shop_goods_id` int(10) DEFAULT NULL COMMENT '库存ID',
  `is_pay` tinyint(2) DEFAULT '0' COMMENT '状态',
  `collage_group_id` int(10) DEFAULT NULL,
  `is_robot` tinyint(2) DEFAULT '0' COMMENT '凑团订单',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_collage_order
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_collage_robot`
-- ----------------------------
DROP TABLE IF EXISTS `wp_collage_robot`;
CREATE TABLE `wp_collage_robot` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `headimgurl` int(10) unsigned NOT NULL COMMENT '头像',
  `nickname` varchar(100) NOT NULL COMMENT '昵称',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `delete_time` int(10) DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_collage_robot
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_comment`
-- ----------------------------
DROP TABLE IF EXISTS `wp_comment`;
CREATE TABLE `wp_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `aim_table` varchar(30) DEFAULT NULL COMMENT '评论关联数据表',
  `aim_id` int(10) DEFAULT NULL COMMENT '评论关联ID',
  `cTime` int(10) DEFAULT NULL COMMENT '评论时间',
  `follow_id` int(10) DEFAULT NULL COMMENT 'follow_id',
  `content` text COMMENT '评论内容',
  `is_audit` tinyint(2) DEFAULT '0' COMMENT '是否审核',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_comment
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_common_category`
-- ----------------------------
DROP TABLE IF EXISTS `wp_common_category`;
CREATE TABLE `wp_common_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) DEFAULT NULL COMMENT '分类标识',
  `title` varchar(255) NOT NULL COMMENT '分类标题',
  `icon` int(10) unsigned DEFAULT NULL COMMENT '分类图标',
  `pid` int(10) unsigned DEFAULT '0' COMMENT '上一级分类',
  `path` varchar(255) DEFAULT NULL COMMENT '分类路径',
  `module` varchar(255) DEFAULT NULL COMMENT '分类所属功能',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `is_show` tinyint(2) DEFAULT '1' COMMENT '是否显示',
  `intro` varchar(255) DEFAULT NULL COMMENT '分类描述',
  `code` varchar(255) DEFAULT NULL COMMENT '分类扩展编号',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3419 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_common_category
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_common_category_group`
-- ----------------------------
DROP TABLE IF EXISTS `wp_common_category_group`;
CREATE TABLE `wp_common_category_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(100) NOT NULL COMMENT '分组标识',
  `title` varchar(255) NOT NULL COMMENT '分组标题',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `level` tinyint(1) unsigned DEFAULT '3' COMMENT '最多级数',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_common_category_group
-- ----------------------------
INSERT INTO `wp_common_category_group` VALUES ('1', 'area', '区域', '1461319103', '2', '73');

-- ----------------------------
-- Table structure for `wp_config`
-- ----------------------------
DROP TABLE IF EXISTS `wp_config`;
CREATE TABLE `wp_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置说明',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '配置值',
  `remark` varchar(100) NOT NULL COMMENT '配置说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `value` text NOT NULL COMMENT '配置值',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8 COMMENT='系统配置表';

-- ----------------------------
-- Records of wp_config
-- ----------------------------
INSERT INTO `wp_config` VALUES ('1', 'WEB_SITE_TITLE', '1', '网站标题', '1', '', '网站标题前台显示标题', '1378898976', '1430825115', '1', 'WeiPHP5.0', '0');
INSERT INTO `wp_config` VALUES ('2', 'WEB_SITE_DESCRIPTION', '2', '网站描述', '1', '', '网站搜索引擎描述', '1378898976', '1379235841', '1', 'weiphp微信小程序版是一套完整的有前端和后端的CMS框架', '9');
INSERT INTO `wp_config` VALUES ('3', 'WEB_SITE_KEYWORD', '2', '网站关键字', '1', '', '网站搜索引擎关键字', '1378898976', '1381390100', '1', 'weiphp,互联网+,微信开源开发框架，微信小程序，小程序开发', '8');
INSERT INTO `wp_config` VALUES ('4', 'WEB_SITE_CLOSE', '4', '关闭站点', '1', '0:关闭\r\n1:开启', '站点关闭后其他用户不能访问，管理员可以正常访问', '1378898976', '1406859591', '1', '1', '1');
INSERT INTO `wp_config` VALUES ('9', 'CONFIG_TYPE_LIST', '3', '配置类型列表', '6', '', '主要用于数据解析和页面表单的生成', '1378898976', '1379235348', '1', '0:数字\r\n1:字符\r\n2:文本\r\n3:数组\r\n4:枚举', '2');
INSERT INTO `wp_config` VALUES ('10', 'WEB_SITE_ICP', '1', '网站备案号', '1', '', '设置在网站底部显示的备案号，如“沪ICP备12007941号-2', '1378900335', '1379235859', '1', '', '9');
INSERT INTO `wp_config` VALUES ('13', 'COLOR_STYLE', '4', '后台色系', '1', 'default_color:默认\r\nblue_color:紫罗兰', '后台颜色风格', '1379122533', '1379235904', '1', 'default_color', '10');
INSERT INTO `wp_config` VALUES ('20', 'CONFIG_GROUP_LIST', '3', '配置分组', '6', '', '配置分组', '1379228036', '1384418383', '1', '1:基本\r\n3:用户\r\n6:开发\r\n99:高级', '4');
INSERT INTO `wp_config` VALUES ('21', 'HOOKS_TYPE', '3', '钩子的类型', '0', '', '类型 1-用于扩展显示内容，2-用于扩展业务处理', '1379313397', '1379313407', '1', '1:视图\r\n2:控制器', '6');
INSERT INTO `wp_config` VALUES ('22', 'AUTH_CONFIG', '3', 'Auth配置', '0', '', '自定义Auth.class.php类配置', '1379409310', '1379409564', '1', 'AUTH_ON:1\r\nAUTH_TYPE:2', '8');
INSERT INTO `wp_config` VALUES ('25', 'LIST_ROWS', '0', '后台每页记录数', '0', '', '后台数据每页显示记录数', '1379503896', '1391938052', '1', '20', '10');
INSERT INTO `wp_config` VALUES ('28', 'DATA_BACKUP_PATH', '1', '数据库备份根路径', '0', '', '路径必须以 / 结尾', '1381482411', '1381482411', '1', './Data/', '5');
INSERT INTO `wp_config` VALUES ('29', 'DATA_BACKUP_PART_SIZE', '0', '数据库备份卷大小', '0', '', '该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M', '1381482488', '1381729564', '1', '20971520', '7');
INSERT INTO `wp_config` VALUES ('30', 'DATA_BACKUP_COMPRESS', '4', '数据库备份文件是否启用压缩', '0', '0:不压缩\r\n1:启用压缩', '压缩备份文件需要PHP环境支持gzopen,gzwrite函数', '1381713345', '1381729544', '1', '1', '9');
INSERT INTO `wp_config` VALUES ('31', 'DATA_BACKUP_COMPRESS_LEVEL', '4', '数据库备份文件压缩级别', '0', '1:普通\r\n4:一般\r\n9:最高', '数据库备份文件的压缩级别，该配置在开启压缩时生效', '1381713408', '1381713408', '1', '9', '10');
INSERT INTO `wp_config` VALUES ('32', 'DEVELOP_MODE', '4', '开启开发者模式', '6', '0:关闭\r\n1:开启', '是否开启开发者模式', '1383105995', '1383291877', '1', '0', '0');
INSERT INTO `wp_config` VALUES ('35', 'REPLY_LIST_ROWS', '0', '回复列表每页条数', '0', '', '', '1386645376', '1387178083', '1', '20', '0');
INSERT INTO `wp_config` VALUES ('36', 'ADMIN_ALLOW_IP', '2', '后台允许访问IP', '99', '', '多个用逗号分隔，如果不配置表示不限制IP访问', '1387165454', '1387165553', '1', '', '12');
INSERT INTO `wp_config` VALUES ('37', 'SHOW_PAGE_TRACE', '4', '是否显示页面Trace', '6', '0:关闭\r\n1:开启', '是否显示页面Trace信息', '1387165685', '1387165685', '1', '0', '1');
INSERT INTO `wp_config` VALUES ('38', 'WEB_SITE_VERIFY', '4', '登录验证码', '3', '0:关闭\r\n1:开启', '登录时是否需要验证码', '1378898976', '1406859544', '1', '1', '2');
INSERT INTO `wp_config` VALUES ('42', 'ACCESS', '2', '未登录时可访问的页面', '6', '', '不区分大小写', '1390656601', '1390664079', '1', 'Home/User/*\r\nHome/Index/*\r\nHome/Help/*\r\nhome/weixin/*\r\nadmin/File/*\r\nhome/File/*\r\nhome/Forum/*\r\nHome/Material/detail\r\npublic_bind/public_bind/setTicket\r\npublic_bind/public_bind/test', '0');
INSERT INTO `wp_config` VALUES ('45', 'SYSTEM_UPDATE_REMIND', '4', '系统升级提醒', '0', '0:关闭\r\n1:开启', '开启后官方有新升级信息会及时在后台的网站设置页面头部显示升级提醒', '1393764263', '1393764263', '1', '0', '5');
INSERT INTO `wp_config` VALUES ('46', 'SYSTEM_UPDATRE_VERSION', '0', '系统升级最新版本号', '6', '', '记录当前系统的版本号，这是与官方比较是否有升级包的唯一标识，不熟悉者只勿改变其数值', '1393764702', '1394337646', '1', '20181213', '0');
INSERT INTO `wp_config` VALUES ('47', 'FOLLOW_YOUKE_UID', '0', '粉丝游客ID', '0', '', '', '1398927704', '1398927704', '1', '-9013', '0');
INSERT INTO `wp_config` VALUES ('50', 'COPYRIGHT', '1', '版权信息', '1', '', '', '1401018910', '1401018910', '1', '版本由圆梦云科技有限公司所有', '3');
INSERT INTO `wp_config` VALUES ('52', 'SYSTEM_LOGO', '1', '网站LOGO的URL', '1', '', '填写LOGO的网址，为空时默认显示weiphp的logo', '1403566699', '1403566746', '1', '', '0');
INSERT INTO `wp_config` VALUES ('60', 'TONGJI_CODE', '2', '第三方统计JS代码', '99', '', '', '1428634717', '1428634717', '1', '', '0');
INSERT INTO `wp_config` VALUES ('61', 'SENSITIVE_WORDS', '1', '敏感词', '1', '', '当出现有敏感词的地方，会用*号代替, (多个敏感词用 , 隔开 )', '1433125977', '1463195869', '1', 'bitch,shit', '11');
INSERT INTO `wp_config` VALUES ('62', 'REG_AUDIT', '4', '注册审核', '3', '0:需要审核\r\n1:不需要审核', '', '1439811099', '1439811099', '1', '0', '1');
INSERT INTO `wp_config` VALUES ('63', 'PUBLIC_BIND', '4', '公众号第三方平台', '5', '0:关闭\r\n1:开启', '申请审核通过微信开放平台里的公众号第三方平台账号后，就可以开启体验了', '1434542818', '1434542818', '1', '0', '0');
INSERT INTO `wp_config` VALUES ('64', 'COMPONENT_APPID', '1', '公众号开放平台的AppID', '5', '', '公众号第三方平台开启后必填的参数', '1434542891', '1434542975', '1', 'wxedd687dfab20466a', '0');
INSERT INTO `wp_config` VALUES ('65', 'COMPONENT_APPSECRET', '1', '公众号开放平台的AppSecret', '5', '', '公众号第三方平台开启后必填的参数', '1434542936', '1434542984', '1', 'd159ffea0012654a0cefaf91991ff6ed', '0');
INSERT INTO `wp_config` VALUES ('67', 'APPID', '2', '小程序AppID', '0', '', '', '1477122750', '1477122750', '1', '', '0');
INSERT INTO `wp_config` VALUES ('68', 'APPSECRET', '1', '小程序AppSecret', '0', '', '', '1477122812', '1477122812', '1', '', '0');
INSERT INTO `wp_config` VALUES ('69', 'USER_ALLOW_REGISTER', '4', '是否允许用户注册', '0', '0:关闭注册\r\n1:允许注册', '是否开放用户注册', '0', '0', '1', '0', '0');
INSERT INTO `wp_config` VALUES ('72', 'IS_QRCODE_LOGIN', '4', '是否开启扫码登录', '10', '0:否\r\n1:是', '是否开启扫码登录', '0', '0', '1', '0', '0');
INSERT INTO `wp_config` VALUES ('73', 'DEFAULT_PUBLICS', '4', '扫码登录绑定的公众号', '10', '', '', '0', '0', '1', '', '3');
INSERT INTO `wp_config` VALUES ('74', 'REQUEST_LOG', '4', '接口日志是否开启', '0', '0:否\r\n1:是', '', '0', '0', '1', '1', '0');
INSERT INTO `wp_config` VALUES ('75', 'SCAN_LOGIN', '4', '是否开启扫码登录', '10', '0:关闭\r\n1:开启', '', '0', '0', '0', '0', '0');
INSERT INTO `wp_config` VALUES ('76', 'ENCODING_AES_KEY', '1', '公众号消息加解密Key', '5', '', '', '0', '0', '1', 'DfEqNBRvzbg8MJdRQCSGyaMp6iLcGOldKFT0r8I6Tnp', '0');

-- ----------------------------
-- Table structure for `wp_coupon`
-- ----------------------------
DROP TABLE IF EXISTS `wp_coupon`;
CREATE TABLE `wp_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `background` int(10) unsigned DEFAULT NULL COMMENT '素材背景图',
  `use_tips` text COMMENT '使用说明',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text COMMENT '封面简介',
  `end_time` int(10) DEFAULT NULL COMMENT '领取结束时间',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '优惠券图片',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `start_time` int(10) DEFAULT NULL COMMENT '开始时间',
  `end_tips` text COMMENT '领取结束说明',
  `end_img` int(10) unsigned DEFAULT NULL COMMENT '领取结束提示图片',
  `num` int(10) unsigned DEFAULT '0' COMMENT '优惠券数量',
  `max_num` int(10) unsigned DEFAULT '1' COMMENT '每人最多允许获取次数',
  `follower_condtion` char(50) DEFAULT '1' COMMENT '粉丝状态',
  `credit_conditon` int(10) unsigned DEFAULT '0' COMMENT '积分限制',
  `credit_bug` int(10) unsigned DEFAULT '0' COMMENT '积分消费',
  `addon_condition` varchar(255) DEFAULT NULL COMMENT '插件场景限制',
  `collect_count` int(10) unsigned DEFAULT '0' COMMENT '已领取数',
  `view_count` int(10) unsigned DEFAULT '0' COMMENT '浏览人数',
  `addon` char(50) DEFAULT 'public' COMMENT '插件',
  `shop_uid` varchar(255) DEFAULT NULL COMMENT '商家管理员ID',
  `use_count` int(10) DEFAULT '0' COMMENT '已使用数',
  `empty_prize_tips` varchar(255) DEFAULT NULL COMMENT '奖品抽完后的提示',
  `start_tips` varchar(255) DEFAULT NULL COMMENT '活动还没开始时的提示语',
  `over_time` int(10) DEFAULT NULL COMMENT '使用的截止时间',
  `use_start_time` int(10) DEFAULT NULL COMMENT '使用开始时间',
  `shop_name` varchar(255) DEFAULT '优惠商家' COMMENT '商家名称',
  `shop_logo` int(10) unsigned DEFAULT NULL COMMENT '商家LOGO',
  `member` varchar(100) DEFAULT '0' COMMENT '选择人群',
  `is_del` int(10) DEFAULT '0' COMMENT '是否删除',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  `money` decimal(10,2) DEFAULT NULL,
  `goods_category` text,
  `only_goods` tinyint(1) DEFAULT '0',
  `order_money` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_coupon
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_credit_cash`
-- ----------------------------
DROP TABLE IF EXISTS `wp_credit_cash`;
CREATE TABLE `wp_credit_cash` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` text COMMENT '商品名称',
  `describe` text COMMENT '商品描述',
  `num` int(10) DEFAULT NULL COMMENT '商品个数',
  `img` int(10) unsigned DEFAULT NULL COMMENT '商品图片',
  `explain` text COMMENT '使用说明',
  `fail` text COMMENT '兑换失败提示',
  `score` int(10) DEFAULT NULL COMMENT '兑换所需积分',
  `surplus` int(10) DEFAULT NULL COMMENT '剩余',
  `status` char(50) DEFAULT '0' COMMENT '状态',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_credit_cash
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_credit_config`
-- ----------------------------
DROP TABLE IF EXISTS `wp_credit_config`;
CREATE TABLE `wp_credit_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) NOT NULL COMMENT '规则名称',
  `name` varchar(50) NOT NULL COMMENT '规则标识名',
  `mod` varchar(50) NOT NULL DEFAULT 'common' COMMENT '应用英文名，核心功能默认为common',
  `mTime` int(10) DEFAULT NULL COMMENT '更新时间',
  `score` int(10) DEFAULT '0' COMMENT '积分值',
  `type` tinyint(1) DEFAULT '0' COMMENT '规则类型 0是公众号积分规则 1是非公众号积分规则 2是可变积分规则',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_credit_config
-- ----------------------------
INSERT INTO `wp_credit_config` VALUES ('1', '关注公众号', 'subscribe', '', '1490711733', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('2', '取消关注公众号', 'unsubscribe', '', '1438596459', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('3', '参与投票', 'vote', '', '1398565597', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('4', '参与调研', 'survey', '', '1398565640', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('5', '参与考试', 'exam', '', '1398565659', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('6', '参与测试', 'test', '', '1398565681', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('7', '微信聊天', 'chat', '', '1398565740', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('8', '建议意见反馈', 'suggestions', '', '1398565798', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('9', '会员卡绑定', 'card_bind', '', '1438596438', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('10', '获取优惠卷', 'coupons', '', '1398565926', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('11', '访问微网站', 'weisite', '', '1398565973', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('12', '查看自定义回复内容', 'custom_reply', '', '1398566068', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('13', '填写通用表单', 'forms', '', '1398566118', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('14', '访问微商店', 'shop', '', '1398566206', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('32', '程序自由增加', 'auto_add', '', '1442659667', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('45', '合同签订', 'test', 'common', '1489135999', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('46', '关注公众号', 'subscribe', 'common', '1490711918', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('47', '关注公众号', 'subscribe', 'common', '1490712244', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('48', '关注公众号', 'subscribe', 'common', '1490712695', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('49', '取消关注公众号', 'unsubscribe', 'common', '1490712716', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('50', '关注公众号', 'subscribe', 'common', '1529743515', '0', '0', '73');
INSERT INTO `wp_credit_config` VALUES ('51', '关注公众号', 'subscribe', 'common', '1529981804', '0', '0', '73');

-- ----------------------------
-- Table structure for `wp_credit_data`
-- ----------------------------
DROP TABLE IF EXISTS `wp_credit_data`;
CREATE TABLE `wp_credit_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `credit_name` varchar(50) NOT NULL COMMENT '规则标识名',
  `credit_title` varchar(100) DEFAULT NULL COMMENT '积分描述',
  `score` int(10) DEFAULT '0' COMMENT '积分值',
  `cTime` int(10) NOT NULL COMMENT '记录时间',
  `admin_uid` int(10) DEFAULT '0' COMMENT '操作者UID，0表示系统自动增加',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1581 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_credit_data
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_credit_grade`
-- ----------------------------
DROP TABLE IF EXISTS `wp_credit_grade`;
CREATE TABLE `wp_credit_grade` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL COMMENT '等级名称',
  `icon` int(10) DEFAULT NULL COMMENT '等级图标',
  `mTime` int(10) DEFAULT NULL COMMENT '更新时间',
  `score` int(10) unsigned DEFAULT '0' COMMENT '累计积分要求的值',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_credit_grade
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_custom_menu`
-- ----------------------------
DROP TABLE IF EXISTS `wp_custom_menu`;
CREATE TABLE `wp_custom_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pid` int(10) DEFAULT '0' COMMENT '一级菜单',
  `title` varchar(50) NOT NULL COMMENT '菜单名',
  `from` char(50) NOT NULL DEFAULT '0' COMMENT '来源 0一级菜单，1素材 2URL 3自定义',
  `type` varchar(30) NOT NULL DEFAULT 'click' COMMENT '内容类型：\r\ntext:素材文本\r\nimg:素材图片\r\nnews:素材图文\r\nvideo:素材视频\r\nvoice：素材语音\r\nurl:URL地址\r\nclick：点击推事件\r\nscancode_push：扫码推事件 \r\nscancode_waitmsg：扫码带提示\r\npic_sysphoto：弹出系统拍照发图  \r\npic_photo_or_album： 弹出拍照或者相册发图  \r\npic_weixin：弹出微信相册发图器  \r\nlocation_select：弹出地理位置选择器',
  `sort` tinyint(4) DEFAULT '0' COMMENT '排序号',
  `rule_id` int(11) DEFAULT '0' COMMENT '个性化菜单ID，0表示默认菜单',
  `material` varchar(50) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `keyword` varchar(100) DEFAULT NULL,
  `appid` varchar(50) DEFAULT NULL,
  `pagepath` varchar(100) DEFAULT NULL,
  `appurl` varchar(255) DEFAULT NULL,
  `pbid` int(10) DEFAULT NULL COMMENT '公众号id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_custom_menu
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_custom_menu_rule`
-- ----------------------------
DROP TABLE IF EXISTS `wp_custom_menu_rule`;
CREATE TABLE `wp_custom_menu_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `tag_id` int(11) DEFAULT '0',
  `sex` tinyint(1) DEFAULT '0' COMMENT '0 不限 1 男 2女',
  `os` tinyint(4) DEFAULT '0' COMMENT '0不限 1ios 2android 3other',
  `city` int(11) DEFAULT NULL,
  `province` int(11) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `lang` char(30) DEFAULT NULL,
  `menuid` varchar(50) DEFAULT NULL COMMENT '微信返回的ID,用于后续删除菜单接口',
  `pbid` int(10) NOT NULL DEFAULT '0' COMMENT '公众号id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_custom_menu_rule
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_custom_reply_mult`
-- ----------------------------
DROP TABLE IF EXISTS `wp_custom_reply_mult`;
CREATE TABLE `wp_custom_reply_mult` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `keyword_type` tinyint(2) DEFAULT '0' COMMENT '关键词类型',
  `mult_ids` varchar(255) DEFAULT NULL COMMENT '多图文ID',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_custom_reply_mult
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_custom_reply_news`
-- ----------------------------
DROP TABLE IF EXISTS `wp_custom_reply_news`;
CREATE TABLE `wp_custom_reply_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `keyword_type` tinyint(2) DEFAULT NULL COMMENT '关键词类型',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text COMMENT '简介',
  `cate_id` int(10) unsigned DEFAULT '0' COMMENT '所属类别',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '封面图片',
  `content` text COMMENT '内容',
  `cTime` int(10) DEFAULT NULL COMMENT '发布时间',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `view_count` int(10) unsigned DEFAULT '0' COMMENT '浏览数',
  `jump_url` varchar(255) DEFAULT NULL COMMENT '外链',
  `author` varchar(50) DEFAULT NULL COMMENT '作者',
  `show_type` varchar(100) DEFAULT '0' COMMENT '显示方式',
  `is_show` char(10) DEFAULT '1' COMMENT '图片是否显示在内容页',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_custom_reply_news
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_custom_reply_text`
-- ----------------------------
DROP TABLE IF EXISTS `wp_custom_reply_text`;
CREATE TABLE `wp_custom_reply_text` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键词',
  `keyword_type` tinyint(2) DEFAULT '0' COMMENT '关键词类型',
  `content` text COMMENT '回复内容',
  `view_count` int(10) unsigned DEFAULT '0' COMMENT '浏览数',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_custom_reply_text
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_custom_sendall`
-- ----------------------------
DROP TABLE IF EXISTS `wp_custom_sendall`;
CREATE TABLE `wp_custom_sendall` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `ToUserName` varchar(255) DEFAULT NULL COMMENT 'token',
  `FromUserName` varchar(255) DEFAULT NULL COMMENT 'openid',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `msgType` varchar(255) DEFAULT NULL COMMENT '消息类型',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员id',
  `content` text COMMENT '内容',
  `media_id` varchar(255) DEFAULT NULL COMMENT '多媒体文件id',
  `is_send` int(10) DEFAULT NULL COMMENT '是否已经发送',
  `uid` int(10) DEFAULT NULL COMMENT '粉丝uid',
  `news_group_id` varchar(10) DEFAULT NULL COMMENT '图文组id',
  `video_title` varchar(255) DEFAULT NULL COMMENT '视频标题',
  `video_description` text COMMENT '视频描述',
  `video_thumb` varchar(255) DEFAULT NULL COMMENT '视频缩略图',
  `voice_id` int(10) DEFAULT NULL COMMENT '语音id',
  `image_id` int(10) DEFAULT NULL COMMENT '图片id',
  `video_id` int(10) DEFAULT NULL COMMENT '视频id',
  `send_type` int(10) DEFAULT NULL COMMENT '发送方式',
  `send_openids` text COMMENT '指定用户',
  `group_id` int(10) DEFAULT NULL COMMENT '分组id',
  `diff` int(10) DEFAULT '0' COMMENT '区分消息标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_custom_sendall
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_debug_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_debug_log`;
CREATE TABLE `wp_debug_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data` text,
  `data_post` text,
  `cTime_format` varchar(30) DEFAULT NULL,
  `cTime` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45532 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_debug_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_draw_follow_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_draw_follow_log`;
CREATE TABLE `wp_draw_follow_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `follow_id` int(10) DEFAULT NULL COMMENT '粉丝id',
  `sports_id` int(10) DEFAULT NULL COMMENT '场次id',
  `count` int(10) DEFAULT '0' COMMENT '抽奖次数',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `cTime` int(10) DEFAULT NULL COMMENT '支持时间',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`),
  KEY `sports_id` (`sports_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1229 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_draw_follow_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_draw_pv_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_draw_pv_log`;
CREATE TABLE `wp_draw_pv_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cTime` int(10) DEFAULT NULL COMMENT '访问时间',
  `draw_id` int(10) DEFAULT '0' COMMENT '游戏ID',
  `uid` int(10) DEFAULT '0' COMMENT '用户id',
  `openid` varchar(255) DEFAULT NULL COMMENT 'openid',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2617 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_draw_pv_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_duty_everyday`
-- ----------------------------
DROP TABLE IF EXISTS `wp_duty_everyday`;
CREATE TABLE `wp_duty_everyday` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `duty` text COMMENT '每日任务设置',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_duty_everyday
-- ----------------------------
INSERT INTO `wp_duty_everyday` VALUES ('2', '<p>66666<br/></p>', '73');
INSERT INTO `wp_duty_everyday` VALUES ('3', '<p>13sdhrkoljergmhw</p><p>和围观和起哄</p>', '73');

-- ----------------------------
-- Table structure for `wp_event_prizes`
-- ----------------------------
DROP TABLE IF EXISTS `wp_event_prizes`;
CREATE TABLE `wp_event_prizes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `event_id` int(10) DEFAULT '0' COMMENT '活动id',
  `prize_list` text COMMENT '奖品列表',
  `prize_count` int(10) DEFAULT NULL COMMENT '奖品数量',
  `start_num` int(10) DEFAULT '0' COMMENT '开始数字',
  `end_num` int(10) DEFAULT '0' COMMENT '最后数字',
  `sort` int(10) DEFAULT '1' COMMENT '顺序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_event_prizes
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_exchange`
-- ----------------------------
DROP TABLE IF EXISTS `wp_exchange`;
CREATE TABLE `wp_exchange` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品id',
  `uid` int(10) DEFAULT NULL COMMENT '兑换用户',
  `cTime` varchar(255) DEFAULT NULL COMMENT '兑换时间',
  `mobile` varchar(255) DEFAULT NULL COMMENT '联系方式',
  `address` varchar(255) DEFAULT NULL COMMENT '联系方式',
  `status` char(50) DEFAULT '0' COMMENT '状态',
  `name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_exchange
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_file`
-- ----------------------------
DROP TABLE IF EXISTS `wp_file`;
CREATE TABLE `wp_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文件ID',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '原始文件名',
  `savename` char(30) NOT NULL DEFAULT '' COMMENT '保存名称',
  `savepath` char(30) NOT NULL DEFAULT '' COMMENT '文件保存路径',
  `ext` char(5) NOT NULL DEFAULT '' COMMENT '文件后缀',
  `mime` char(40) NOT NULL DEFAULT '' COMMENT '文件mime类型',
  `size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `md5` char(32) DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `location` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '文件保存位置',
  `create_time` int(10) unsigned NOT NULL COMMENT '上传时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_md5` (`md5`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8 COMMENT='文件表';

-- ----------------------------
-- Records of wp_file
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_goods_category_link`
-- ----------------------------
DROP TABLE IF EXISTS `wp_goods_category_link`;
CREATE TABLE `wp_goods_category_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品编号',
  `sort` int(10) DEFAULT '0' COMMENT '排序',
  `category_first` int(10) DEFAULT NULL COMMENT '一级分类',
  `category_second` int(10) DEFAULT NULL COMMENT '二级分类',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=435 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_goods_category_link
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_goods_count`
-- ----------------------------
DROP TABLE IF EXISTS `wp_goods_count`;
CREATE TABLE `wp_goods_count` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `good_id` int(10) DEFAULT NULL COMMENT '商品id',
  `day_time` int(10) DEFAULT NULL COMMENT '格式化时间',
  `time` int(10) DEFAULT NULL COMMENT '时间戳',
  `uid` int(10) DEFAULT NULL COMMENT '访问用户uid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_goods_count
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_goods_param_link`
-- ----------------------------
DROP TABLE IF EXISTS `wp_goods_param_link`;
CREATE TABLE `wp_goods_param_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL COMMENT '参数名',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品编号',
  `param_value` varchar(255) DEFAULT NULL COMMENT '参数值',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  `param_name` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_goods_param_link
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_goods_param_temp`
-- ----------------------------
DROP TABLE IF EXISTS `wp_goods_param_temp`;
CREATE TABLE `wp_goods_param_temp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(30) NOT NULL COMMENT '模板名称',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `param` text COMMENT 'param',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_goods_param_temp
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_goods_store_link`
-- ----------------------------
DROP TABLE IF EXISTS `wp_goods_store_link`;
CREATE TABLE `wp_goods_store_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品编号',
  `store_id` int(10) DEFAULT NULL COMMENT '门店编号',
  `store_num` int(10) DEFAULT '0' COMMENT '门店库存,备用',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `event_type` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `goods` (`goods_id`,`event_type`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=139 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_goods_store_link
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_haggle`
-- ----------------------------
DROP TABLE IF EXISTS `wp_haggle`;
CREATE TABLE `wp_haggle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) NOT NULL COMMENT '活动名称',
  `share_cover` int(10) unsigned DEFAULT NULL COMMENT '分享的宣传图',
  `start_time` int(10) NOT NULL COMMENT '活动开始时间',
  `end_time` int(10) NOT NULL COMMENT '活动结束时间',
  `is_subscribe` tinyint(2) DEFAULT NULL COMMENT '是否需要关注公众号才能参加',
  `is_member` tinyint(2) DEFAULT NULL COMMENT '是否需要成为会员才能参加',
  `shop_goods_id` int(10) DEFAULT NULL COMMENT '商品来源',
  `express` decimal(10,2) DEFAULT NULL COMMENT '邮费',
  `random_content` text COMMENT '砍价后文字描述',
  `content` text COMMENT '活动描述',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `min_price` decimal(10,2) DEFAULT '0.00' COMMENT '砍一次的最小价格',
  `max_price` decimal(10,2) DEFAULT '0.00' COMMENT '砍一次的最大价格',
  `send_type` varchar(30) NOT NULL DEFAULT '1' COMMENT '收货方式',
  `status` tinyint(1) DEFAULT '0',
  `is_all_store` tinyint(1) DEFAULT '0',
  `visit_count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_haggle
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_haggle_order`
-- ----------------------------
DROP TABLE IF EXISTS `wp_haggle_order`;
CREATE TABLE `wp_haggle_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `haggle_id` int(10) DEFAULT NULL COMMENT '活动Id',
  `uid` int(10) DEFAULT NULL COMMENT '用户Id',
  `create_at` int(10) DEFAULT NULL COMMENT '参与时间',
  `market_price` decimal(10,2) DEFAULT NULL COMMENT '商品价格',
  `sale_price` decimal(10,2) DEFAULT NULL COMMENT '当前价格',
  `member_count` int(10) DEFAULT '0' COMMENT '砍价人数',
  `is_pay` tinyint(2) DEFAULT '0' COMMENT '是否支付',
  `order_id` int(10) DEFAULT NULL COMMENT '订单Id',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_haggle_order
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_haggle_user`
-- ----------------------------
DROP TABLE IF EXISTS `wp_haggle_user`;
CREATE TABLE `wp_haggle_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `haggle_id` int(10) DEFAULT NULL COMMENT 'haggle_id',
  `invite_uid` int(10) DEFAULT NULL COMMENT '请求帮砍价的用户ID',
  `uid` int(10) DEFAULT NULL COMMENT '砍价人的UID',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '砍价价格',
  `create_at` int(10) DEFAULT NULL COMMENT '砍价时间',
  `temp_index` tinyint(2) DEFAULT '0' COMMENT '随机模板',
  `order_id` int(10) DEFAULT '0' COMMENT '订单ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_haggle_user
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_hooks`
-- ----------------------------
DROP TABLE IF EXISTS `wp_hooks`;
CREATE TABLE `wp_hooks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `description` text NOT NULL COMMENT '描述',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `addons` text COMMENT '钩子挂载的插件 ''，''分割',
  PRIMARY KEY (`id`),
  UNIQUE KEY `搜索索引` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='插件钩子表';

-- ----------------------------
-- Records of wp_hooks
-- ----------------------------
INSERT INTO `wp_hooks` VALUES ('1', 'pageHeader', '页面header钩子，一般用于加载插件CSS文件和代码', '1', '0', '');
INSERT INTO `wp_hooks` VALUES ('2', 'pageFooter', '页面footer钩子，一般用于加载插件JS文件和JS代码', '1', '0', 'ReturnTop');
INSERT INTO `wp_hooks` VALUES ('3', 'documentEditForm', '添加编辑表单的 扩展内容钩子', '1', '0', '');
INSERT INTO `wp_hooks` VALUES ('4', 'documentDetailAfter', '文档末尾显示', '1', '0', 'SocialComment');
INSERT INTO `wp_hooks` VALUES ('5', 'documentDetailBefore', '页面内容前显示用钩子', '1', '0', '');
INSERT INTO `wp_hooks` VALUES ('6', 'documentSaveComplete', '保存文档数据后的扩展钩子', '2', '0', '');
INSERT INTO `wp_hooks` VALUES ('7', 'documentEditFormContent', '添加编辑表单的内容显示钩子', '1', '0', 'Editor');
INSERT INTO `wp_hooks` VALUES ('8', 'adminArticleEdit', '后台内容编辑页编辑器', '1', '1378982734', 'EditorForAdmin');
INSERT INTO `wp_hooks` VALUES ('13', 'AdminIndex', '首页小格子个性化显示', '1', '1382596073', 'SiteStat,SystemInfo,DevTeam');
INSERT INTO `wp_hooks` VALUES ('14', 'topicComment', '评论提交方式扩展钩子。', '1', '1380163518', 'Editor');
INSERT INTO `wp_hooks` VALUES ('16', 'app_begin', '应用开始', '2', '1384481614', '');
INSERT INTO `wp_hooks` VALUES ('17', 'weixin', '微信插件必须加载的钩子', '1', '1388810858', 'Hitegg,Diy,ShopCoupon,Wuguai,YaoTV,Analysis,Cms,Feedback,TopMenu,Docs,WebSocket,Apps');
INSERT INTO `wp_hooks` VALUES ('18', 'cascade', '级联菜单', '1', '1398694587', 'Cascade');
INSERT INTO `wp_hooks` VALUES ('19', 'page_diy', '万能页面的钩子', '1', '1399040364', 'Diy');
INSERT INTO `wp_hooks` VALUES ('20', 'dynamic_select', '动态下拉菜单', '1', '1435223189', 'DynamicSelect');
INSERT INTO `wp_hooks` VALUES ('21', 'news', '图文素材选择', '1', '1439196828', 'News');
INSERT INTO `wp_hooks` VALUES ('22', 'dynamic_checkbox', '动态多选菜单', '1', '1464002882', 'DynamicCheckbox');
INSERT INTO `wp_hooks` VALUES ('23', 'material', '素材选择', '1', '1464060023', 'Material');
INSERT INTO `wp_hooks` VALUES ('24', 'prize', '奖品选择', '1', '1464060044', 'Prize');

-- ----------------------------
-- Table structure for `wp_import`
-- ----------------------------
DROP TABLE IF EXISTS `wp_import`;
CREATE TABLE `wp_import` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `attach` int(10) unsigned NOT NULL COMMENT '上传文件',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_import
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_join_count`
-- ----------------------------
DROP TABLE IF EXISTS `wp_join_count`;
CREATE TABLE `wp_join_count` (
  `follow_id` int(10) DEFAULT NULL,
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aim_id` int(10) DEFAULT NULL,
  `count` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fid_aim` (`follow_id`,`aim_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_join_count
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_keyword`
-- ----------------------------
DROP TABLE IF EXISTS `wp_keyword`;
CREATE TABLE `wp_keyword` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `addon` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '关键词所属插件',
  `aim_id` int(10) unsigned NOT NULL COMMENT '插件表里的ID值',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `keyword_length` int(10) unsigned DEFAULT '0' COMMENT '关键词长度',
  `keyword_type` tinyint(2) DEFAULT '0' COMMENT '匹配类型',
  `extra_text` text CHARACTER SET utf8 COMMENT '文本扩展',
  `extra_int` int(10) DEFAULT NULL COMMENT '数字扩展',
  `request_count` int(10) DEFAULT '0' COMMENT '请求数',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`),
  KEY `keyword_token` (`keyword`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wp_keyword
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lottery_prize_list`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lottery_prize_list`;
CREATE TABLE `wp_lottery_prize_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sports_id` int(10) DEFAULT NULL COMMENT '活动编号',
  `award_id` varchar(255) DEFAULT NULL COMMENT '奖品编号',
  `award_num` int(10) DEFAULT NULL COMMENT '奖品数量',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  PRIMARY KEY (`id`),
  KEY `sports_id` (`sports_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lottery_prize_list
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lucky_follow`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lucky_follow`;
CREATE TABLE `wp_lucky_follow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `draw_id` int(10) DEFAULT NULL COMMENT '活动编号',
  `sport_id` int(10) DEFAULT NULL COMMENT '场次编号',
  `award_id` int(10) DEFAULT NULL COMMENT '奖品编号',
  `follow_id` int(10) DEFAULT NULL COMMENT '粉丝id',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  `num` int(10) DEFAULT '0' COMMENT '获奖数',
  `state` tinyint(2) DEFAULT '0' COMMENT '兑奖状态',
  `zjtime` int(10) DEFAULT NULL COMMENT '中奖时间',
  `djtime` int(10) DEFAULT NULL COMMENT '兑奖时间',
  `aim_table` varchar(255) DEFAULT NULL COMMENT '活动标识',
  `remark` text COMMENT '备注',
  `scan_code` varchar(255) DEFAULT NULL COMMENT '核销码',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  `error_remark` text COMMENT '发放失败备注',
  `send_aim_id` int(10) DEFAULT '0' COMMENT '发送奖品对应id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=511 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lucky_follow
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lzwg_activities`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lzwg_activities`;
CREATE TABLE `wp_lzwg_activities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL COMMENT '活动名称',
  `remark` text COMMENT '活动描述',
  `logo_img` int(10) unsigned DEFAULT NULL COMMENT '活动LOGO',
  `start_time` int(10) DEFAULT NULL COMMENT '开始时间',
  `end_time` int(10) DEFAULT NULL COMMENT '结束时间',
  `get_prize_tip` varchar(255) DEFAULT NULL COMMENT '中奖提示信息',
  `no_prize_tip` varchar(255) DEFAULT NULL COMMENT '未中奖提示信息',
  `ctime` int(10) DEFAULT NULL COMMENT '活动创建时间',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `lottery_number` int(10) DEFAULT '1' COMMENT '抽奖次数',
  `comment_status` char(10) DEFAULT '0' COMMENT '评论是否需要审核',
  `get_prize_count` int(10) DEFAULT '1' COMMENT '中奖次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lzwg_activities
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lzwg_activities_vote`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lzwg_activities_vote`;
CREATE TABLE `wp_lzwg_activities_vote` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `lzwg_id` int(10) DEFAULT NULL COMMENT '活动编号',
  `lzwg_type` char(10) DEFAULT '0' COMMENT '活动类型',
  `vote_id` int(10) DEFAULT NULL COMMENT '题目编号',
  `vote_type` char(10) DEFAULT '1' COMMENT '问题类型',
  `vote_limit` int(10) DEFAULT NULL COMMENT '最多选择几项',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lzwg_activities_vote
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lzwg_coupon`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lzwg_coupon`;
CREATE TABLE `wp_lzwg_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) DEFAULT NULL COMMENT '名称',
  `money` decimal(10,2) DEFAULT NULL COMMENT '减免金额',
  `name` varchar(255) DEFAULT NULL COMMENT '代金券 标题',
  `condition` decimal(10,2) DEFAULT NULL COMMENT '抵押条件',
  `intro` varchar(255) DEFAULT NULL COMMENT '优惠券简述',
  `img` int(10) unsigned DEFAULT NULL COMMENT '优惠卷图标',
  `sn_str` text COMMENT '序列号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lzwg_coupon
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lzwg_coupon_receive`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lzwg_coupon_receive`;
CREATE TABLE `wp_lzwg_coupon_receive` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `follow_id` int(10) DEFAULT NULL COMMENT '用户ID',
  `coupon_id` int(10) DEFAULT NULL COMMENT '优惠券ID',
  `sn_id` varchar(100) DEFAULT NULL COMMENT '序列号',
  `cTime` int(10) DEFAULT NULL COMMENT '领取时间',
  `aim_id` int(10) DEFAULT NULL COMMENT '活动编号',
  `aim_table` varchar(255) DEFAULT NULL COMMENT '活动表名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lzwg_coupon_receive
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lzwg_coupon_sn`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lzwg_coupon_sn`;
CREATE TABLE `wp_lzwg_coupon_sn` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `coupon_id` int(10) DEFAULT NULL COMMENT '优惠券Id',
  `sn` varchar(255) DEFAULT NULL COMMENT '优惠券sn',
  `is_use` int(10) DEFAULT '0' COMMENT '是否已领取',
  `is_get` int(10) DEFAULT '0' COMMENT '是否已经被领取',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lzwg_coupon_sn
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lzwg_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lzwg_log`;
CREATE TABLE `wp_lzwg_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `lzwg_id` int(10) DEFAULT NULL COMMENT '活动ID',
  `follow_id` int(10) DEFAULT NULL COMMENT '用户ID',
  `count` int(10) DEFAULT '0' COMMENT '参与次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lzwg_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lzwg_vote`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lzwg_vote`;
CREATE TABLE `wp_lzwg_vote` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(50) DEFAULT NULL COMMENT '关键词',
  `title` varchar(100) DEFAULT NULL COMMENT '投票标题',
  `description` text COMMENT '投票描述',
  `picurl` int(10) unsigned DEFAULT NULL COMMENT '封面图片',
  `type` char(10) DEFAULT '0' COMMENT '选择类型',
  `start_date` int(10) DEFAULT NULL COMMENT '开始日期',
  `end_date` int(10) DEFAULT NULL COMMENT '结束日期',
  `is_img` tinyint(2) DEFAULT '0' COMMENT '文字/图片投票',
  `vote_count` int(10) unsigned DEFAULT '0' COMMENT '投票数',
  `cTime` int(10) DEFAULT NULL COMMENT '投票创建时间',
  `mTime` int(10) DEFAULT NULL COMMENT '更新时间',
  `template` varchar(255) DEFAULT 'default' COMMENT '素材模板',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `vote_type` char(10) DEFAULT '0' COMMENT '题目类型',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lzwg_vote
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lzwg_vote_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lzwg_vote_log`;
CREATE TABLE `wp_lzwg_vote_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `vote_id` int(10) unsigned DEFAULT NULL COMMENT '投票ID',
  `user_id` int(10) DEFAULT NULL COMMENT '用户ID',
  `options` varchar(255) DEFAULT NULL COMMENT '选择选项',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `activity_id` int(10) DEFAULT NULL COMMENT '活动编号',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lzwg_vote_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_lzwg_vote_option`
-- ----------------------------
DROP TABLE IF EXISTS `wp_lzwg_vote_option`;
CREATE TABLE `wp_lzwg_vote_option` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `vote_id` int(10) unsigned NOT NULL COMMENT '投票ID',
  `name` varchar(255) NOT NULL COMMENT '选项标题',
  `image` int(10) unsigned DEFAULT NULL COMMENT '图片选项',
  `opt_count` int(10) unsigned DEFAULT '0' COMMENT '当前选项投票数',
  `order` int(10) unsigned DEFAULT '0' COMMENT '选项排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_lzwg_vote_option
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_manager`
-- ----------------------------
DROP TABLE IF EXISTS `wp_manager`;
CREATE TABLE `wp_manager` (
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `has_public` tinyint(2) DEFAULT '0' COMMENT '是否配置公众号',
  `headface_url` int(10) unsigned DEFAULT NULL COMMENT '管理员头像',
  `GammaAppId` varchar(30) DEFAULT NULL COMMENT '摇电视的AppId',
  `GammaSecret` varchar(100) DEFAULT NULL COMMENT '摇电视的Secret',
  `copy_right` varchar(255) DEFAULT NULL COMMENT '授权信息',
  `tongji_code` text COMMENT '统计代码',
  `website_logo` int(10) unsigned DEFAULT NULL COMMENT '网站LOGO',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_manager
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_material_file`
-- ----------------------------
DROP TABLE IF EXISTS `wp_material_file`;
CREATE TABLE `wp_material_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `file_id` int(10) DEFAULT NULL COMMENT '上传文件',
  `cover_url` varchar(255) DEFAULT NULL COMMENT '本地URL',
  `media_id` varchar(100) DEFAULT '0' COMMENT '微信端图文消息素材的media_id',
  `wechat_url` varchar(255) DEFAULT NULL COMMENT '微信端的文件地址',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员ID',
  `title` varchar(100) DEFAULT NULL COMMENT '素材名称',
  `type` int(10) DEFAULT NULL COMMENT '类型',
  `introduction` text COMMENT '描述',
  `is_use` int(10) DEFAULT '1' COMMENT '可否使用',
  `aim_id` int(10) DEFAULT NULL COMMENT '添加来源标识id',
  `aim_table` varchar(255) DEFAULT NULL COMMENT '来源表名',
  `pbid` int(10) NOT NULL DEFAULT '0' COMMENT 'pbid',
  `admin_uid` int(11) DEFAULT NULL,
  `pub_id` int(11) DEFAULT NULL COMMENT '共享素材ID，用于去重',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_material_file
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_material_image`
-- ----------------------------
DROP TABLE IF EXISTS `wp_material_image`;
CREATE TABLE `wp_material_image` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cover_id` int(10) DEFAULT NULL COMMENT '图片在本地的ID',
  `cover_url` varchar(255) DEFAULT NULL COMMENT '本地URL',
  `media_id` varchar(100) DEFAULT '0' COMMENT '微信端图文消息素材的media_id',
  `wechat_url` varchar(255) DEFAULT NULL COMMENT '微信端的图片地址',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员ID',
  `is_use` int(10) DEFAULT '1' COMMENT '可否使用',
  `aim_id` int(10) DEFAULT NULL COMMENT '添加来源标识id',
  `aim_table` varchar(255) DEFAULT NULL COMMENT '来源表名',
  `pbid` int(10) NOT NULL DEFAULT '0' COMMENT 'pbid',
  `admin_uid` int(11) DEFAULT NULL,
  `pub_id` int(11) DEFAULT NULL COMMENT '共享素材ID，用于去重',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1301 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_material_image
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_material_news`
-- ----------------------------
DROP TABLE IF EXISTS `wp_material_news`;
CREATE TABLE `wp_material_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `author` varchar(30) DEFAULT NULL COMMENT '作者',
  `cover_id` int(10) unsigned DEFAULT NULL COMMENT '封面',
  `intro` varchar(255) DEFAULT NULL COMMENT '摘要',
  `content` longtext COMMENT '内容',
  `link` varchar(255) DEFAULT NULL COMMENT '外链',
  `group_id` int(10) DEFAULT '0' COMMENT '多图文组的ID',
  `thumb_media_id` varchar(100) DEFAULT NULL COMMENT '图文消息的封面图片素材id（必须是永久mediaID）',
  `media_id` varchar(100) DEFAULT '0' COMMENT '微信端图文消息素材的media_id',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员ID',
  `cTime` int(10) DEFAULT NULL COMMENT '发布时间',
  `url` varchar(255) DEFAULT NULL COMMENT '图文页url',
  `is_use` int(10) DEFAULT '1' COMMENT '可否使用',
  `aim_id` int(10) DEFAULT NULL COMMENT '添加来源标识id',
  `aim_table` varchar(255) DEFAULT NULL COMMENT '来源表名',
  `update_time` int(10) DEFAULT '0' COMMENT 'update_time',
  `pbid` int(10) NOT NULL DEFAULT '0' COMMENT 'pbid',
  `admin_uid` int(11) DEFAULT NULL,
  `pub_id` int(11) DEFAULT NULL COMMENT '共享素材ID，用于去重',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=190 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_material_news
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_material_text`
-- ----------------------------
DROP TABLE IF EXISTS `wp_material_text`;
CREATE TABLE `wp_material_text` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `content` text COMMENT '文本内容',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `is_use` int(10) DEFAULT '1' COMMENT '可否使用',
  `aim_id` int(10) DEFAULT NULL COMMENT '添加来源标识id',
  `aim_table` varchar(255) DEFAULT NULL COMMENT '来源表名',
  `pbid` int(10) NOT NULL DEFAULT '0' COMMENT 'pbid',
  `admin_uid` int(11) DEFAULT NULL,
  `pub_id` int(11) DEFAULT NULL COMMENT '共享素材ID，用于去重',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_material_text
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_member_erp_buying`
-- ----------------------------
DROP TABLE IF EXISTS `wp_member_erp_buying`;
CREATE TABLE `wp_member_erp_buying` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `invoicedate` varchar(255) DEFAULT NULL COMMENT ' 销售日期',
  `tag` varchar(100) DEFAULT NULL COMMENT '销售状态',
  `invoiceno` varchar(255) DEFAULT NULL COMMENT '销售单号',
  `barcode` varchar(255) DEFAULT NULL COMMENT '条码号',
  `partdesc` varchar(255) DEFAULT NULL COMMENT '货品描述',
  `price` varchar(255) DEFAULT NULL COMMENT '金额',
  `cTime` int(10) DEFAULT NULL COMMENT '时间',
  `openid` varchar(255) DEFAULT NULL COMMENT 'openid',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_member_erp_buying
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_member_follow_link`
-- ----------------------------
DROP TABLE IF EXISTS `wp_member_follow_link`;
CREATE TABLE `wp_member_follow_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '粉丝用户',
  `member_id` int(10) DEFAULT NULL COMMENT '会员',
  `wpid` int(10) DEFAULT NULL COMMENT '公众号id',
  `cTime` int(10) DEFAULT NULL COMMENT '关注时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_member_follow_link
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_menu`
-- ----------------------------
DROP TABLE IF EXISTS `wp_menu`;
CREATE TABLE `wp_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `menu_type` tinyint(2) DEFAULT '0' COMMENT '菜单类型',
  `pid` varchar(50) DEFAULT '0' COMMENT '上级菜单',
  `title` varchar(50) DEFAULT NULL COMMENT '菜单名',
  `url_type` tinyint(2) DEFAULT '0' COMMENT '链接类型',
  `addon_name` varchar(30) DEFAULT NULL COMMENT '插件名',
  `url` varchar(255) DEFAULT NULL COMMENT '外链',
  `target` char(50) DEFAULT '_self' COMMENT '打开方式',
  `is_hide` tinyint(2) DEFAULT '0' COMMENT '是否隐藏',
  `sort` int(10) DEFAULT '0' COMMENT '排序号',
  `place` tinyint(1) DEFAULT '0' COMMENT '0：运营端，1：开发端',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=804 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_menu
-- ----------------------------
INSERT INTO `wp_menu` VALUES ('366', '0', '0', '数据模型', '1', '', 'Admin/Model/index', '_self', '0', '4', '1');
INSERT INTO `wp_menu` VALUES ('367', '0', '0', '系统', '1', '', 'Admin/Config/group', '_self', '0', '0', '1');
INSERT INTO `wp_menu` VALUES ('368', '0', '0', '菜单管理', '1', '', 'Admin/Menu/lists', '_self', '0', '5', '1');
INSERT INTO `wp_menu` VALUES ('369', '1', '368', '运营者菜单', '1', '', 'Admin/Menu/lists', '_self', '0', '0', '1');
INSERT INTO `wp_menu` VALUES ('370', '1', '368', '开发者菜单', '1', '', 'Admin/Menu/lists?place=1', '_self', '0', '2', '1');
INSERT INTO `wp_menu` VALUES ('371', '1', '367', '网站设置', '1', '', 'Admin/Config/group', '_self', '0', '0', '1');
INSERT INTO `wp_menu` VALUES ('372', '1', '367', '配置管理', '1', '', 'Admin/Config/index', '_self', '0', '1', '1');
INSERT INTO `wp_menu` VALUES ('374', '1', '367', '在线升级', '1', '', 'Admin/Update/index', '_self', '0', '3', '1');
INSERT INTO `wp_menu` VALUES ('375', '1', '367', '清除缓存', '1', '', 'Admin/Update/delcache', '_self', '0', '4', '1');
INSERT INTO `wp_menu` VALUES ('376', '0', '0', '运营者', '1', '', 'Admin/User/index', '_self', '1', '1', '1');
INSERT INTO `wp_menu` VALUES ('377', '1', '376', '运营者列表', '1', '', 'Admin/User/index', '_self', '0', '0', '1');
INSERT INTO `wp_menu` VALUES ('378', '1', '376', '微信接口节点', '1', '', 'Admin/Rule/wechat', '_self', '0', '1', '1');
INSERT INTO `wp_menu` VALUES ('379', '1', '376', '公众号组管理', '1', '', 'Admin/AuthManager/wechat', '_self', '0', '2', '1');
INSERT INTO `wp_menu` VALUES ('380', '0', '0', '插件管理', '1', '', 'Admin/Apps/index', '_self', '0', '3', '1');
INSERT INTO `wp_menu` VALUES ('394', '1', '380', '应用插件', '1', '', 'Admin/Apps/index', '_self', '0', '0', '1');
INSERT INTO `wp_menu` VALUES ('405', '1', '409', '用户分组', '1', '', 'home/AuthGroup/lists', '_self', '0', '8', '0');
INSERT INTO `wp_menu` VALUES ('409', '0', '0', '微信基础功能', '1', '', 'weixin/wecome/config', '_self', '1', '4', '0');
INSERT INTO `wp_menu` VALUES ('414', '1', '409', '群发消息', '1', '', 'weixin/Message/add', '_self', '0', '11', '0');
INSERT INTO `wp_menu` VALUES ('424', '1', '409', '素材管理', '1', '', 'material/material/material_lists', '_self', '0', '5', '0');
INSERT INTO `wp_menu` VALUES ('702', '1', '761', '工作授权', '1', '', 'Servicer/Servicer/lists', '_self', '0', '12', '0');
INSERT INTO `wp_menu` VALUES ('750', '1', '409', '自定义菜单', '1', '', 'weixin/CustomMenu/lists', '_self', '0', '2', '0');
INSERT INTO `wp_menu` VALUES ('751', '1', '409', '欢迎语设置', '1', null, 'weixin/Wecome/config', '_self', '0', '1', '0');
INSERT INTO `wp_menu` VALUES ('752', '1', '409', '自定义回复', '1', null, 'weixin/AutoReply/lists', '_self', '0', '3', '0');
INSERT INTO `wp_menu` VALUES ('753', '1', '409', '未识别回复', '1', null, 'weixin/NoAnswer/config', '_self', '0', '4', '0');
INSERT INTO `wp_menu` VALUES ('758', '0', '0', '粉丝管理', '1', '', 'weixin/user_center/lists', '_self', '0', '6', '0');
INSERT INTO `wp_menu` VALUES ('761', '0', '0', '微商城', '0', 'shop', null, '_self', '0', '2', '0');
INSERT INTO `wp_menu` VALUES ('762', '0', '0', '微官网', '0', 'wei_site', '', '_self', '1', '6', '0');
INSERT INTO `wp_menu` VALUES ('770', '1', '409', '模板消息群发', '1', '', 'weixin/TemplateMessage/config', '_self', '0', '12', '0');
INSERT INTO `wp_menu` VALUES ('772', '1', '762', '微信回复', '0', 'wei_site', '', '_self', '0', '0', '0');
INSERT INTO `wp_menu` VALUES ('773', '1', '762', '首页设置', '1', '', 'wei_site/Template/index', '_self', '0', '1', '0');
INSERT INTO `wp_menu` VALUES ('774', '1', '762', '内容页配置', '1', '', 'wei_site/Template/lists', '_self', '0', '2', '0');
INSERT INTO `wp_menu` VALUES ('775', '1', '762', '导航栏配置', '1', '', 'wei_site/Template/footer', '_self', '0', '3', '0');
INSERT INTO `wp_menu` VALUES ('776', '1', '761', '基本信息', '1', '', 'shop/shop/edit', '_self', '0', '1', '0');
INSERT INTO `wp_menu` VALUES ('777', '1', '761', '商品管理', '1', '', 'shop/goods/lists', '_self', '0', '2', '0');
INSERT INTO `wp_menu` VALUES ('778', '1', '761', '订单管理', '1', '', 'shop/order/lists', '_self', '0', '3', '0');
INSERT INTO `wp_menu` VALUES ('779', '1', '761', '商品分类', '1', '', 'shop/category/lists', '_self', '0', '4', '0');
INSERT INTO `wp_menu` VALUES ('781', '1', '761', '首页幻灯片', '1', '', 'shop/slideshow/lists', '_self', '0', '6', '0');
INSERT INTO `wp_menu` VALUES ('782', '1', '761', '页面管理', '1', '', 'shop/diy_page/lists', '_self', '0', '7', '0');
INSERT INTO `wp_menu` VALUES ('786', '1', '761', '统计中心', '1', '', 'Shop/Count/lists', '_self', '0', '13', '0');
INSERT INTO `wp_menu` VALUES ('787', '1', '761', '门店管理', '1', '', 'shop/stores/lists', '_self', '0', '6', '0');
INSERT INTO `wp_menu` VALUES ('791', '1', '761', '商城入口', '1', '', 'Shop/shop/summary', '_self', '0', '0', '0');
INSERT INTO `wp_menu` VALUES ('792', '0', '0', '客户端', '1', '', 'weixin/publics/lists', '_self', '0', '0', '0');
INSERT INTO `wp_menu` VALUES ('793', '1', '792', '客户端管理', '1', '', 'weixin/publics/lists', '_self', '0', '0', '0');
INSERT INTO `wp_menu` VALUES ('794', '1', '758', '粉丝管理', '1', null, 'weixin/user_center/lists', '_self', '0', '0', '0');
INSERT INTO `wp_menu` VALUES ('798', '1', '792', '短信配置', '0', 'sms', '', '_self', '0', '2', '0');
INSERT INTO `wp_menu` VALUES ('802', '1', '380', '插件库', '1', null, 'https://www.weiphp.cn/index.php/home/index/apps', '_blank', '0', '2', '1');
INSERT INTO `wp_menu` VALUES ('803', '0', '0', '数据库字典', '0', 'database_dictionary', '', '_blank', '0', '5', '1');

-- ----------------------------
-- Table structure for `wp_message`
-- ----------------------------
DROP TABLE IF EXISTS `wp_message`;
CREATE TABLE `wp_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `bind_keyword` varchar(50) DEFAULT NULL COMMENT '关联关键词',
  `preview_openids` text COMMENT '预览人OPENID',
  `group_id` int(10) DEFAULT '0' COMMENT '群发对象',
  `type` tinyint(2) DEFAULT '0' COMMENT '素材来源',
  `media_id` varchar(100) DEFAULT NULL COMMENT '微信素材ID',
  `send_type` tinyint(1) DEFAULT '0' COMMENT '发送方式',
  `send_openids` text COMMENT '要发送的OpenID',
  `msg_id` varchar(255) DEFAULT NULL COMMENT 'msg_id',
  `content` text COMMENT '文本消息内容',
  `msgtype` varchar(255) DEFAULT NULL COMMENT '消息类型',
  `appmsg_id` int(10) DEFAULT NULL COMMENT '图文id',
  `voice_id` int(10) DEFAULT NULL COMMENT '语音id',
  `video_id` int(10) DEFAULT NULL COMMENT '视频id',
  `cTime` int(10) DEFAULT NULL COMMENT '群发时间',
  `pbid` int(10) NOT NULL DEFAULT '0' COMMENT 'pbid',
  `image_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_message
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_model`
-- ----------------------------
DROP TABLE IF EXISTS `wp_model`;
CREATE TABLE `wp_model` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '模型ID',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '模型标识',
  `title` char(30) NOT NULL DEFAULT '' COMMENT '模型名称',
  `extend` int(10) unsigned DEFAULT '0' COMMENT '继承的模型',
  `relation` varchar(30) DEFAULT '' COMMENT '继承与被继承模型的关联字段',
  `need_pk` tinyint(1) unsigned DEFAULT '1' COMMENT '新建表时是否需要主键字段',
  `field_sort` text COMMENT '表单字段排序',
  `field_group` varchar(255) DEFAULT '1:基础' COMMENT '字段分组',
  `attribute_list` text COMMENT '属性列表（表的字段）',
  `template_list` varchar(100) DEFAULT '' COMMENT '列表模板',
  `template_add` varchar(100) DEFAULT '' COMMENT '新增模板',
  `template_edit` varchar(100) DEFAULT '' COMMENT '编辑模板',
  `list_grid` text COMMENT '列表定义',
  `list_row` smallint(2) unsigned DEFAULT '10' COMMENT '列表数据长度',
  `search_key` varchar(50) DEFAULT '' COMMENT '默认搜索字段',
  `search_list` varchar(255) DEFAULT '' COMMENT '高级搜索的字段',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(3) unsigned DEFAULT '0' COMMENT '状态',
  `engine_type` varchar(25) DEFAULT 'MyISAM' COMMENT '数据库引擎',
  `addon` varchar(50) DEFAULT NULL COMMENT '所属插件',
  PRIMARY KEY (`id`),
  KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1478 DEFAULT CHARSET=utf8 COMMENT='系统模型表';

-- ----------------------------
-- Records of wp_model
-- ----------------------------
INSERT INTO `wp_model` VALUES ('1', 'user', '用户信息表', '0', '', '0', '[\"come_from\",\"nickname\",\"password\",\"truename\",\"mobile\",\"email\",\"sex\",\"headimgurl\",\"city\",\"province\",\"country\",\"language\",\"score\",\"unionid\",\"login_count\",\"reg_ip\",\"reg_time\",\"last_login_ip\",\"last_login_time\",\"status\",\"is_init\",\"is_audit\"]', '1:基础', '', '', '', '', 'headimgurl|url_img_html:头像\r\nlogin_name:登录账号\r\nlogin_password:登录密码\r\nnickname|deal_emoji:用户昵称\r\nsex|get_name_by_status:性别\r\ngroup:分组\r\nscore:金币值\r\nids:操作:set_login?uid=[uid]|设置登录账号,detail?uid=[uid]|详细资料,[EDIT]|编辑', '20', '', '', '1436929111', '1441187405', '1', 'MyISAM', 'core');
INSERT INTO `wp_model` VALUES ('2', 'manager', '公众号管理员配置', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1436932532', '1436942362', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('3', 'menu', '公众号管理员菜单', '0', '', '1', '[\"menu_type\",\"pid\",\"title\",\"url_type\",\"addon_name\",\"url\",\"target\",\"is_hide\",\"sort\"]', '1:基础', '', '', '', '', 'title:菜单名\r\nmenu_type|get_name_by_status:菜单类型\r\naddon_name:插件名\r\nurl:外链\r\ntarget|get_name_by_status:打开方式\r\nis_hide|get_name_by_status:隐藏\r\nsort:排序号\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', '', '', '1435215960', '1437623073', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('4', 'keyword', '关键词表', '0', '', '1', '[\"keyword\",\"keyword_type\",\"addon\",\"aim_id\",\"keyword_length\",\"cTime\",\"extra_text\",\"extra_int\"]', '1:基础', '', '', '', '', 'id:编号\r\nkeyword:关键词\r\naddon:所属插件\r\naim_id:插件数据ID\r\ncTime|time_format:增加时间\r\nrequest_count|intval:请求数\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'keyword', '', '1388815871', '1407251192', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('5', 'qr_code', '二维码表', '0', '', '1', '[\"qr_code\",\"addon\",\"aim_id\",\"cTime\",\"extra_text\",\"extra_int\",\"scene_id\",\"action_name\"]', '1:基础', '', '', '', '', 'scene_id:事件KEY值\r\nqr_code|get_code_img:二维码\r\naction_name|get_name_by_status: 	二维码类型\r\naddon:所属插件\r\naim_id:插件数据ID\r\ncTime|time_format:增加时间\r\nrequest_count|intval:请求数\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'qr_code', '', '1388815871', '1406130247', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('6', 'publics', '公众号管理', '0', '', '1', '[\"public_name\",\"public_id\",\"wechat\",\"headface_url\",\"type\",\"appid\",\"secret\",\"encodingaeskey\",\"tips_url\",\"GammaAppId\",\"GammaSecret\",\"public_copy_right\"]', '1:基础', '', '', '', '', 'id:公众号ID\r\npublic_name:公众号名称\r\ntoken:Token\r\ncount:管理员数\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,main&public_id=[id]|进入管理', '20', 'public_name', '', '1391575109', '1447231672', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('9', 'import', '导入数据', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1407554076', '1407554076', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('12', 'common_category', '通用分类', '0', '', '1', '[\"pid\",\"title\",\"icon\",\"intro\",\"sort\",\"is_show\"]', '1:基础', '', '', '', '', 'code:编号\r\ntitle:标题\r\nicon|get_img_html:图标\r\nsort:排序号\r\nis_show|get_name_by_status:显示\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1397529095', '1404182789', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('13', 'common_category_group', '通用分类分组', '0', '', '1', '[\"name\",\"title\"]', '1:基础', '', '', '', '', 'name:分组标识\r\ntitle:分组标题\r\nids:操作:cascade?target=_blank&module=[name]|数据管理,[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1396061373', '1403664378', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('14', 'credit_config', '积分配置', '0', '', '1', '[\"name\",\"title\",\"score\"]', '1:基础', '', '', '', '', 'title:积分描述\r\nname:积分标识\r\nscore:金币值\r\nids:操作:[EDIT]|配置', '20', 'title', '', '1396061373', '1438591151', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('15', 'credit_data', '用户积分记录', '0', '', '1', '[\"uid\",\"score\",\"credit_name\"]', '1:基础', '', '', '', '', 'uid:用户名\r\ncredit_title:积分来源\r\nscore:积分\r\ncTime|time_format:时间\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'uid', '', '1398564291', '1447250833', '1', 'MyISAM', 'core');
INSERT INTO `wp_model` VALUES ('16', 'material_image', '图片素材', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1438684613', '1438684613', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('17', 'material_news', '图文素材', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1438670890', '1438670890', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('18', 'message', '群发消息', '0', '', '1', '[\"type\",\"bind_keyword\",\"media_id\",\"openid\",\"send_type\",\"group_id\",\"send_openids\"]', '1:基础', '', '', '', '', '', '20', '', '', '1437984111', '1438049406', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('19', 'visit_log', '网站访问日志', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1439448351', '1439448351', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('20', 'auth_group', '用户组', '0', '', '1', '[\"title\",\"description\"]', '1:基础', '', '', '', '', 'title:分组名称\r\ndescription:描述\r\nqr_code:二维码\r\nids:操作:export?id=[id]|导出用户,[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1437633503', '1447660681', '1', 'MyISAM', 'core');
INSERT INTO `wp_model` VALUES ('21', 'analysis', '统计分析', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1432806941', '1432806941', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('81', 'sn_code', 'SN码', '0', '', '1', '[\"prize_title\"]', '1:基础', '', '', '', '', 'sn:SN码\r\nuid|get_nickname|deal_emoji:昵称\r\nprize_title:奖项\r\ncTime|time_format:创建时间\r\nis_use|get_name_by_status:是否已使用\r\nuse_time|time_format:使用时间\r\nids:操作:[DELETE]|删除,set_use?id=[id]|改变使用状态', '20', 'sn', '', '1399272054', '1401013099', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('93', 'system_notice', '系统公告表', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1431141043', '1431141043', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('94', 'update_version', '系统版本升级', '0', '', '1', '[\"version\",\"title\",\"description\",\"create_date\",\"package\"]', '1:基础', '', '', '', '', 'version:版本号\r\ntitle:升级包名\r\ndescription:描述\r\ncreate_date|time_format:创建时间\r\ndownload_count:下载统计数\r\nids:操作:[EDIT]&id=[id]|编辑,[DELETE]&id=[id]|删除', '20', '', '', '1393770420', '1393771807', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('103', 'weixin_message', '微信消息管理', '0', '', '1', '', '1:基础', '', '', '', '', 'FromUserName:用户\r\ncontent:内容\r\nCreateTime:时间', '20', '', '', '1438142999', '1438151555', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('148', 'material_text', '文本素材', '0', '', '1', '[\"content\"]', '1:基础', '', '', '', '', 'id:编号\r\ncontent:文本内容\r\nids:操作:text_edit?id=[id]|编辑,text_del?id=[id]|删除', '10', 'content:请输入文本内容搜索', '', '1442976119', '1442977453', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('149', 'material_file', '文件素材', '0', '', '1', '[\"title\",\"file_id\"]', '1:基础', '', '', '', '', '', '10', '', '', '1438684613', '1442982212', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('176', 'update_score_log', '修改积分记录', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1444302325', '1444302325', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('201', 'custom_sendall', '客服群发消息', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1447241925', '1447241925', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('1150', 'user_tag', '用户标签', '0', '', '1', '[\"title\"]', '1:基础', null, '', '', '', 'id:标签编号\r\ntitle:标签名称\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '10', 'title:请输入标签名称搜索', '', '1463990100', '1463993574', '1', 'MyISAM', 'UserCenter');
INSERT INTO `wp_model` VALUES ('1151', 'user_tag_link', '用户标签关系表', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1463992911', '1463992911', '1', 'MyISAM', 'UserCenter');
INSERT INTO `wp_model` VALUES ('1159', 'apps', '小程序导航', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1478318763', '1478318763', '1', 'MyISAM', 'Apps');
INSERT INTO `wp_model` VALUES ('1160', 'weisite_category', '微官网分类', '0', '', '1', '[\"title\",\"icon\",\"url\",\"is_show\",\"sort\",\"pid\"]', '1:基础', '', '', '', '', 'title:15%分类标题\r\nicon|get_img_html:分类图片\r\nurl:30%外链\r\nsort:10%排序号\r\npid:10%一级目录\r\nis_show|get_name_by_status:10%显示\r\nid:10%操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1395987942', '1439522869', '1', 'MyISAM', 'WeiSite');
INSERT INTO `wp_model` VALUES ('1161', 'weisite_footer', '底部导航', '0', '', '1', '[\"pid\",\"title\",\"url\",\"sort\",\"icon\"]', '1:基础', '', '', '', '', 'title:15%菜单名\r\nicon:10%图标\r\nurl:50%关联URL\r\nsort:8%排序号\r\nids:20%操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1394518309', '1464705675', '1', 'MyISAM', 'WeiSite');
INSERT INTO `wp_model` VALUES ('1162', 'stores', '门店', '0', '', '1', '[\"name\",\"address\",\"gps\",\"phone\"]', '1:基础', '', '', '', '', 'name:店名\r\nphone:联系电话\r\naddress:详细地址\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'name:店名搜索', '', '1427164604', '1439465222', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1163', 'coupon', '优惠券', '0', '', '1', '[\"title\",\"use_tips\",\"start_time\",\"end_time\",\"num\",\"max_num\",\"over_time\",\"background\",\"use_start_time\",\"member\"]', '1:基础', '', '', '', '', 'id:优惠券编号\r\ntitle:标题\r\nnum:计划发送数\r\ncollect_count:已领取数\r\nuse_count:已使用数\r\nstart_time|time_format:开始时间\r\nend_time|time_format:结束时间\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,Sn/lists?target_id=[id]&target=_blank|成员管理,preview?id=[id]&target=_blank|预览', '20', 'title', '', '1396061373', '1445567658', '1', 'MyISAM', 'Coupon');
INSERT INTO `wp_model` VALUES ('1165', 'real_prize', '实物奖励', '0', '', '1', '[\"prize_title\",\"prize_name\",\"prize_conditions\",\"prize_count\",\"prize_image\",\"prize_type\",\"use_content\",\"fail_content\",\"template\"]', '1:基础', '', '', '', '', 'prize_name:20%奖品名称\r\nprize_conditions:20%活动说明\r\nprize_count:10%奖品个数\r\nprize_type|get_name_by_status:10%奖品类型\r\nuse_content:20%使用说明\r\nid:20%操作:[EDIT]|编辑,[DELETE]|删除,address_lists?target_id=[id]|查看数据,preview?id=[id]&target=_blank|预览', '20', '', '', '1429515376', '1437452269', '1', 'MyISAM', 'RealPrize');
INSERT INTO `wp_model` VALUES ('1168', 'visit_log', '网站访问日志', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1439448351', '1439448351', '1', 'MyISAM', 'Core');
INSERT INTO `wp_model` VALUES ('1172', 'shop_user_level_link', '分销用户级别关系', '0', '', '1', '[\"uid\",\"upper_user\",\"level\"]', '1:基础', '', '', '', '', '', '10', '', '', '1459826468', '1459838196', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1197', 'stores_link', '门店关联', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1427356350', '1427356350', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1211', 'custom_menu', '自定义菜单', '0', '', '1', '[\"pid\",\"title\",\"from_type\",\"type\",\"jump_type\",\"addon\",\"sucai_type\",\"keyword\",\"url\",\"sort\"]', '1:基础', '', '', '', '', 'title:10%菜单名\r\nkeyword:10%关联关键词\r\nurl:50%关联URL\r\nsort:5%排序号\r\nid:10%操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1394518309', '1446533816', '1', 'MyISAM', 'weixin');
INSERT INTO `wp_model` VALUES ('1213', 'prize_address', '奖品收货地址', '0', '', '1', '[\"address\",\"mobile\",\"turename\",\"remark\"]', '1:基础', '', '', '', '', 'prizeid:奖品名称\r\nturename:收货人\r\nmobile:联系方式\r\naddress:收货地址\r\nremark:备注\r\nids:操作:address_edit&id=[id]&_controller=RealPrize&_addons=RealPrize|编辑,[DELETE]|删除', '20', '', '', '1429521514', '1447831599', '1', 'MyISAM', 'RealPrize');
INSERT INTO `wp_model` VALUES ('1225', 'public_config', '公共配置信息', '0', '', '1', null, '1:基础', null, '', '', '', null, '10', '', '', '0', '0', '0', 'MyISAM', '');
INSERT INTO `wp_model` VALUES ('1229', 'area', '地区数据', '0', '', '1', null, '1:基础', null, '', '', '', null, '10', '', '', '0', '0', '0', 'MyISAM', '');
INSERT INTO `wp_model` VALUES ('1231', 'user_tag', '用户标签', '0', '', '1', '[\"title\"]', '1:基础', '', '', '', '', 'id:标签编号\r\ntitle:标签名称\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '10', 'title:请输入标签名称搜索', '', '1463990100', '1463993574', '1', 'MyISAM', 'UserCenter');
INSERT INTO `wp_model` VALUES ('1232', 'user_tag_link', '用户标签关系表', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1463992911', '1463992911', '1', 'MyISAM', 'UserCenter');
INSERT INTO `wp_model` VALUES ('1235', 'auto_reply', '自动回复', '0', '', '1', '[\"img_id\",\"group_id\",\"content\",\"keyword\"]', '1:基础', null, '', '', '', 'keyword:关键词\r\ntext_id:文本\r\ngroup_id:图文\r\nimg_id:图片\r\nvoice_id:语音\r\nvideo_id:视频\r\nids:操作:[EDIT]&type=[msg_type]|编辑,[DELETE]|删除', '10', '', '', '0', '0', '0', 'MyISAM', '');
INSERT INTO `wp_model` VALUES ('1280', 'draw_follow_log', '粉丝抽奖记录', '0', '', '1', '[\"follow_id\",\"sports_id\",\"count\",\"cTime\"]', '1:基础', '', '', '', '', 'follow_id:微信名称\r\nopenid:openID\r\narea:地区\r\nsex:性别\r\nhas_prize:是否中奖\r\ncTime:参与时间\r\ntruename:真实姓名\r\nmobile:电话', '20', '', '', '1432619171', '1491386963', '1', 'MyISAM', 'draw');
INSERT INTO `wp_model` VALUES ('1282', 'lucky_follow', '中奖者信息', '0', '', '1', '[\"draw_id\",\"sport_id\",\"award_id\",\"follow_id\",\"address\",\"num\",\"state\",\"zjtime\",\"djtime\",\"remark\",\"scan_code\"]', '1:基础', '', '', '', '', 'draw_id:活动名称\r\ndraw_time:活动时间\r\nfollow_id|deal_emoji:8%微信昵称\r\nopenid:中奖人OPENID\r\nzjtime|time_format:中奖时间\r\ntruename:姓名\r\nmobile:手机号\r\naward_id:奖项\r\naward_name:奖品名称\r\nstate|get_name_by_status:发奖状态\r\nids:8%操作:do_fafang?id=[id]|发放奖品', '20', 'award_name:输入奖品名称', '', '1432618091', '1491373747', '1', 'MyISAM', 'draw');
INSERT INTO `wp_model` VALUES ('1285', 'award', '奖品库奖品', '0', '', '1', '[\"award_type\",\"name\",\"img\",\"virtual_type\",\"score\",\"money\",\"explain\"]', '1:基础', '', '', '', '', 'id:6%编号\r\nname:23%奖项名称\r\nimg|get_img_html:10%商品图片\r\naward_type|get_name_by_status:10%奖品类型\r\nexplain:30%奖品说明\r\nids:20%操作:[EDIT]|编辑,[DELETE]|删除', '20', 'name:请输入奖品名称', '', '1432607100', '1462358042', '1', 'MyISAM', 'draw');
INSERT INTO `wp_model` VALUES ('1292', 'qr_admin', '扫码管理', '0', '', '1', '[\"action_name\",\"group_id\",\"tag_ids\"]', '1:基础', '', '', '', '', 'qr_code:二维码\r\naction_name:类型\r\ngroup_id:用户组\r\ntag_ids:标签\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '10', '', '', '1463999052', '1464002422', '1', 'MyISAM', 'QrAdmin');
INSERT INTO `wp_model` VALUES ('1293', 'servicer', '授权用户', '0', '', '1', '[\"uid\",\"truename\",\"mobile\",\"role\",\"enable\"]', '1:基础', '', '', '', '', 'truename:姓名\r\nrole:权限列表\r\nnickname:微信名称\r\nenable|get_name_by_status:是否启用\r\nids:操作:set_enable?id=[id]&enable=[enable]|改变启用状态,[EDIT]|编辑,[DELETE]|删除', '10', 'truename', '', '1443066649', '1490713267', '1', 'MyISAM', 'Servicer');
INSERT INTO `wp_model` VALUES ('1297', 'credit_cash', '兑换商品', '0', '', '1', '[\"name\",\"describe\",\"num\",\"img\",\"fail\",\"score\"]', '1:基础', '', '', '', '', 'id:商品ID\r\nname:商品名\r\nscore:兑换积分\r\nnum:总数\r\nsurplus:剩余数量\r\nstatus:兑换记录\r\nids:操作:[EDIT]|编辑,[DEL]|删除', '10', '', '', '1479455694', '1491038710', '1', 'MyISAM', '');
INSERT INTO `wp_model` VALUES ('1300', 'duty_everyday', '每日任务', '0', '', '1', '', '1:基础', null, '', '', '', '', '10', '', '', '1490860235', '1490860235', '0', 'MyISAM', '');
INSERT INTO `wp_model` VALUES ('1302', 'exchange', '积分兑换', '0', '', '1', '[\"goods_id\",\"uid\",\"cTime\",\"address\",\"mobile\"]', '1:基础', '', '', '', '', 'goods_id:商品id\r\nuid:兑换用户\r\ncTime:兑换时间\r\nmobile:联系方式\r\naddress:联系地址\r\nstatus:操作', '20', '', '', '1480478185', '1480585316', '1', 'MyISAM', '');
INSERT INTO `wp_model` VALUES ('1305', 'draw_pv_log', '抽奖游戏浏览记录', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1491379489', '0', '0', 'MyISAM', 'draw');
INSERT INTO `wp_model` VALUES ('1309', 'comment', '评论互动', '0', '', '1', '[\"is_audit\"]', '1:基础', '', '', '', '', 'headimgurl|url_img_html:用户头像\r\nnickname|deal_emoji:用户姓名\r\ncontent:评论内容\r\ncTime|time_format:评论时间\r\nis_audit|get_name_by_status:审核状态\r\nids:操作:[DELETE]|删除', '20', 'content:请输入评论内容', '', '1432602310', '1435310857', '1', 'MyISAM', 'Comment');
INSERT INTO `wp_model` VALUES ('1310', 'custom_reply_mult', '多图文配置', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1396602475', '1396602475', '1', 'MyISAM', 'CustomReply');
INSERT INTO `wp_model` VALUES ('1311', 'custom_reply_news', '图文回复', '0', '', '1', '[\"keyword\",\"keyword_type\",\"title\",\"intro\",\"cate_id\",\"cover\",\"content\",\"sort\",\"jump_url\",\"author\"]', '1:基础', '', '', '', '', 'id:5%ID\r\nkeyword:10%关键词\r\nkeyword_type|get_name_by_status:20%关键词类型\r\ntitle:30%标题\r\ncate_id:10%所属分类\r\nsort:7%排序号\r\nview_count:8%浏览数\r\nids:10%操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1396061373', '1466505161', '1', 'MyISAM', 'CustomReply');
INSERT INTO `wp_model` VALUES ('1312', 'custom_reply_text', '文本回复', '0', '', '1', '[\"keyword\",\"keyword_type\",\"content\",\"sort\"]', '1:基础', '', '', '', '', 'id:ID\r\nkeyword:关键词\r\nkeyword_type|get_name_by_status:关键词类型\r\nsort:排序号\r\nview_count:浏览数\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'keyword', '', '1396578172', '1401017369', '1', 'MyISAM', 'CustomReply');
INSERT INTO `wp_model` VALUES ('1313', 'payment_order', '订单支付记录', '0', '', '1', '[\"from\",\"orderName\",\"single_orderid\",\"price\",\"token\",\"wecha_id\",\"paytype\",\"showwxpaytitle\",\"status\"]', '1:基础', '', '', '', '', '', '20', '', '', '1420596259', '1423534012', '1', 'MyISAM', 'Payment');
INSERT INTO `wp_model` VALUES ('1314', 'payment_set', '支付配置', '0', '', '1', '[\"wxappid\",\"wxappsecret\",\"wxpaysignkey\",\"zfbname\",\"pid\",\"key\",\"partnerid\",\"partnerkey\",\"wappartnerid\",\"wappartnerkey\",\"quick_security_key\",\"quick_merid\",\"quick_merabbr\",\"wxmchid\"]', '1:基础', '', '', '', '', '', '10', '', '', '1406958084', '1439364636', '1', 'MyISAM', 'Payment');
INSERT INTO `wp_model` VALUES ('1315', 'weisite_cms', '文章管理', '0', '', '1', '[\"keyword\",\"keyword_type\",\"title\",\"intro\",\"cate_id\",\"cover\",\"content\",\"sort\"]', '1:基础', '', '', '', '', 'keyword:关键词\r\nkeyword_type|get_name_by_status:关键词类型\r\ntitle:标题\r\ncate_id:所属分类\r\nsort:排序号\r\nview_count:浏览数\r\nids:操作:[EDIT]&module_id=[pid]|编辑,[DELETE]|删除', '20', 'title', '', '1396061373', '1408326292', '1', 'MyISAM', 'WeiSite');
INSERT INTO `wp_model` VALUES ('1316', 'weisite_slideshow', '幻灯片', '0', '', '1', '[\"title\",\"img\",\"url\",\"is_show\",\"sort\"]', '1:基础', '', '', '', '', 'title:标题\r\nimg:图片\r\nurl:链接地址\r\nis_show|get_name_by_status:显示\r\nsort:排序\r\nids:操作:[EDIT]&module_id=[pid]|编辑,[DELETE]|删除', '20', 'title', '', '1396098264', '1408323347', '1', 'MyISAM', 'WeiSite');
INSERT INTO `wp_model` VALUES ('1317', 'sms', '短信记录', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1446107661', '1446107661', '1', 'MyISAM', 'sms');
INSERT INTO `wp_model` VALUES ('1323', 'buy_log', '会员消费记录', '0', '', '1', '[\"pay\",\"pay_type\",\"branch_id\",\"cTime\",\"token\",\"manager_id\",\"sn_id\"]', '1:基础', null, '', '', '', 'member_id:会员名称\r\nphone:电话\r\ncTime|time_format:消费时间\r\nbranch_id:消费门店\r\npay:消费金额\r\nsn_id:优惠金额\r\npay_type|get_name_by_status:消费方式', '10', 'member:请输入会员名称或手机号', '', '1444289843', '1444392724', '1', 'MyISAM', 'Card');
INSERT INTO `wp_model` VALUES ('1324', 'recharge_log', '会员充值记录', '0', '', '1', '[\"recharge\",\"branch_id\",\"operator\",\"cTime\",\"token\",\"manager_id\"]', '1:基础', null, '', '', '', 'member_id:会员卡号\r\ntruename:姓名\r\nphone:手机号\r\nrecharge:充值金额\r\ncTime|time_format:充值时间\r\nbranch_id:充值门店\r\noperator:操作员', '10', 'operator:请输入姓名或手机号或操作员', '', '1444275985', '1444387901', '1', 'MyISAM', 'Card');
INSERT INTO `wp_model` VALUES ('1334', 'score_exchange_log', '兑换记录', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1444731340', '1444731340', '1', 'MyISAM', 'card');
INSERT INTO `wp_model` VALUES ('1335', 'share_log', '分享记录', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1444789662', '1444789662', '1', 'MyISAM', 'Card');
INSERT INTO `wp_model` VALUES ('1341', 'shop_address', '收货地址', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1423477477', '1423477477', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1347', 'goods_category_link', '商品所属分类', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1457933153', '1457933153', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1348', 'goods_param_link', '商品参数表', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1457941322', '1457941322', '1', 'MyISAM', 'Shop');
INSERT INTO `wp_model` VALUES ('1349', 'goods_store_link', '商品所属门店', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1458551555', '1458551555', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1350', 'shop_goods_comment', '商品评论信息', '0', '', '1', '[\"goods_id\",\"score\",\"content\",\"order_id\",\"uid\",\"cTime\",\"shop_id\",\"is_show\"]', '1:基础', null, '', '', '', 'id:10%编号\r\nuid|get_username:15%用户昵称\r\ncTime|time_format:15%评论时间\r\nscore:15%星星数\r\ncontent:25%评论内容\r\nis_show|get_name_by_status:10%是否显示\r\nids:编辑:changeShow?id=[id]&is_show=[is_show]&goods_id=[goods_id]|设置显示状态', '10', '', '', '1457430858', '1458901414', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1373', 'shop_statistics_follow', '分销粉丝统计表', '0', '', '1', '[\"uid\",\"duid\",\"ctime\",\"token\"]', '1:基础', null, '', '', '', '', '10', '', '', '1443001407', '1443002218', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1374', 'shop_distribution_user', '分销用户', '0', '', '1', '[\"uid\",\"qr_code\",\"wechat\",\"inviter\",\"level\",\"is_audit\"]', '1:基础', null, '', '', '', 'id:序号\r\ntruename:姓名\r\nmobile:手机号\r\nuid:微信昵称\r\nwechat:微信号\r\ninviter:邀请人\r\nctime|time_format:创建时间\r\nlevel:分销级别\r\nis_audit:审核\r\nids:操作:[EDIT]|编辑', '10', 'truename:请输入姓名', '', '1442922612', '1460357351', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1375', 'shop_goods_sku_config', '商品规格配置', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1442309511', '1442309511', '1', 'MyISAM', 'Shop');
INSERT INTO `wp_model` VALUES ('1376', 'shop_cashout_log', '提现记录表', '0', '', '1', '[\"cashout_amount\",\"remark\",\"cashout_status\",\"ctime\",\"token\",\"cashout_account\"]', '1:基础', null, '', '', '', 'ctime|time_format:申请日期\r\ncashout_amount:申请金额（￥）\r\ntype:提现方式\r\ncashout_account:提现账号\r\nname:账号名称\r\ncashout_status|get_name_by_status:审核状态\r\nremark:详细', '10', '', '', '1442315168', '1442478119', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1377', 'shop_goods_sku_data', '商品规格表', '0', '', '1', '[\"cost_price\"]', '1:基础', null, '', '', '', '', '10', '', '', '1442221199', '1442309479', '1', 'MyISAM', 'Shop');
INSERT INTO `wp_model` VALUES ('1378', 'shop_value', '分类扩展属性数据表', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1396687959', '1396687959', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1379', 'shop_page', '自定义页面', '0', '', '1', '[\"title\",\"ctime\",\"config\",\"desc\",\"shop_id\",\"token\",\"manager_id\",\"use\"]', '1:基础', null, '', '', '', 'title:页面标题\r\nctime|time_format:创建时间\r\nids:操作:preview?id=[id]&target=_blank|预览,[EDIT]|编辑,[DELETE]|删除\r\ncopy:复制链接', '10', '', '', '1442202619', '1442821956', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1380', 'shop_distribution_profit', '分销用户返利表', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1441957173', '1441957173', '1', 'MyISAM', 'Shop');
INSERT INTO `wp_model` VALUES ('1381', 'shop_attribute', '分类属性', '0', '', '1', '[\"title\",\"attr_type\",\"extra\",\"value\",\"sort\"]', '1:基础', '', '', '', '', 'title:字段标题\r\ntype|get_name_by_status:字段类型\r\nextra:参数\r\nsort:排序\r\nids:操作:[EDIT]&cate_id=[cate_id]|编辑,[DELETE]|删除', '20', 'title', '', '1396061373', '1442368516', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1382', 'shop_spec_option', '商品规格选项', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1441942503', '1441942503', '1', 'MyISAM', 'Shop');
INSERT INTO `wp_model` VALUES ('1383', 'shop_spec', '商品规格', '0', '', '1', '[\"title\",\"remark\",\"sort\"]', '1:基础', null, '', '', '', 'title:规格名称\r\nremark:规格属性\r\nid:操作:[EDIT]|编辑,[DELETE]|删除', '10', 'title:请输入规格名称', '', '1441942151', '1441943264', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1387', 'shop_virtual', '虚拟物品信息', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1441006502', '1441006502', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1406', 'sport_award', '抽奖奖品', '0', '', '1', '[\"award_type\",\"name\",\"count\",\"img\",\"price\",\"score\",\"explain\",\"coupon_id\",\"money\"]', '1:基础', '', '', '', '', 'id:6%编号\r\nname:23%奖项名称\r\nimg|get_img_html:8%商品图片\r\nprice:8%商品价格\r\nexplain:24%奖品说明\r\ncount:8%奖品数量\r\nids:20%操作:[EDIT]|编辑,[DELETE]|删除,getlistByAwardId?awardId=[id]&_controller=LuckyFollow|中奖者列表', '20', 'name:请输入抽奖名称', '', '1432607100', '1444901269', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1407', 'shop_slideshow', '幻灯片', '0', '', '1', '[\"title\",\"img\",\"url\",\"is_show\",\"sort\"]', '1:基础', '', '', '', '', 'title:标题\r\nimg:图片\r\nurl:链接地址\r\nis_show|get_name_by_status:显示\r\nsort:排序\r\nids:操作:[EDIT]&module_id=[pid]|编辑,[DELETE]|删除', '20', 'title', '', '1396098264', '1408323347', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1408', 'shop_order', '订单记录', '0', '', '1', '[\"uid\",\"goods_datas\",\"remark\",\"order_number\",\"cTime\",\"total_price\",\"address_id\",\"is_send\",\"send_code\",\"send_number\",\"send_type\",\"shop_id\"]', '1:基础', '', '', '', '', 'order_number:15%订单编号\r\ngoods:20%下单商品\r\nuid:10%客户\r\ntotal_price:7%总价\r\ncTime|time_format:17%下单时间\r\ncommon|get_name_by_status:10%支付类型\r\nstatus_code|get_name_by_status:10%订单跟踪\r\naction:11%操作', '20', 'key:请输入订单编号 或 客户昵称', '', '1420269240', '1440147136', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1409', 'shop_order_log', '订单跟踪', '0', '', '1', '', '1:基础', '', '', '', '', '', '10', '', '', '1439525562', '1439525562', '1', 'MyISAM', 'Shop');
INSERT INTO `wp_model` VALUES ('1410', 'shop_goods_score', '商品评分记录', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1422930901', '1422930901', '1', 'MyISAM', 'Shop');
INSERT INTO `wp_model` VALUES ('1411', 'shop_goods_category', '商品分类', '0', '', '1', '[\"pid\",\"title\",\"icon\",\"sort\",\"is_recommend\",\"is_show\"]', '1:基础', '', '', '', '', 'title:30%分组\r\nicon|get_img_html:20%图标\r\nsort:10%排序号\r\nis_show|get_name_by_status:20%显示\r\nids:20%操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1397529095', '1467365556', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1412', 'shop_collect', '商品收藏', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1423471275', '1423471275', '1', 'MyISAM', 'Shop');
INSERT INTO `wp_model` VALUES ('1413', 'shop_goods', '商品列表', '0', '', '1', '[\"title\",\"category_id\",\"imgs\",\"content\",\"cover\",\"type\",\"is_recommend\",\"auto_send\",\"virtual_textarea\",\"is_show\",\"market_price\",\"stock_num\",\"cost_price\",\"sale_price\",\"weight\",\"sn_code\",\"is_delete\",\"is_new\",\"can_deposit\"]', '1:基础', '', '', '', '', 'cover|get_img_html:封面图\r\ntitle:商品名称\r\nmarket_price:价格\r\nstock_num:库存量\r\nsale_count:销售量\r\nis_show|get_name_by_status:是否上架\r\nids:操作:set_show?id=[id]&is_show=[is_show]|改变上架状态,[EDIT]|编辑,[DELETE]|删除,goodsCommentLists?goods_id=[id]&target=_blank|评论列表', '20', 'title:请输入商品名称', '', '1422672084', '1458898390', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1414', 'shop_cart', '购物车', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1419577864', '1419577864', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1415', 'shop', '微商城', '0', '', '1', '[\"title\",\"logo\",\"intro\",\"mobile\",\"qq\",\"wechat\",\"api_key\",\"custom_tip\",\"content\",\"address\",\"gps\"]', '1:基础', '', '', '', '', 'title:商店名称\r\nlogo|get_img_html:商店LOGO\r\nmobile:联系电话\r\nqq:QQ号\r\nwechat:微信号\r\nids:操作:[EDIT]&id=[id]|编辑,lists&_controller=Category&target=_blank&shop_id=[id]|商品分类,lists&_controller=Slideshow&target=_blank&shop_id=[id]|幻灯片,lists&_controller=Goods&target=_blank&shop_id=[id]|商品管理,lists&_controller=Order&target=_blank&shop_id=[id]|订单管理,lists&_addons=Payment&_controller=Payment&target=_blank&shop_id=[id]|支付配置,lists&_controller=Template&target=_blank&shop_id=[id]|模板选择,[DELETE]|删除,index&_controller=Wap&target=_blank&shop_id=[id]|预览', '20', 'title:请输入商店名称', '', '1422670956', '1458268970', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1416', 'scratch', '刮刮卡', '0', '', '1', '[\"keyword\",\"title\",\"intro\",\"cover\",\"use_tips\",\"start_time\",\"end_time\",\"end_tips\",\"end_img\",\"predict_num\",\"max_num\",\"follower_condtion\",\"credit_conditon\",\"credit_bug\",\"addon_condition\",\"collect_count\",\"view_count\",\"template\"]', '1:基础', '', '', '', '', 'id:刮刮卡ID\r\nkeyword:关键词\r\ntitle:标题\r\ncollect_count:获取人数\r\ncTime|time_format:发布时间\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,lists?target_id=[id]&target=_blank&_controller=Sn|中奖管理,lists?target_id=[id]&target=_blank&_controller=Prize|奖品管理,preview?id=[id]&target=_blank|预览', '20', 'title', '', '1396061373', '1437035669', '1', 'MyISAM', 'Scratch');
INSERT INTO `wp_model` VALUES ('1417', 'reservation_number', '预约号', '0', '', '1', '[\"reservation_date\",\"type\",\"is_use\",\"phone\",\"name\",\"desc\",\"use_time\"]', '1:基础', '', '', '', '', 'type|get_name_by_status:10%上午/下午\r\nreservation_num:15%预约号\r\nis_use|get_name_by_status:10%是否预约\r\nname:11%姓名\r\nphone:12%手机号\r\ndesc:20%病症描述\r\nuse_time:12%提交预约时间\r\nids:10%操作:[EDIT]|编辑,[DELETE]|删除', '20', '', '', '1436237723', '1436256435', '1', 'MyISAM', 'Reservation');
INSERT INTO `wp_model` VALUES ('1418', 'reservation', '每天放号', '0', '', '1', '[\"reservation_date\",\"morning_count\",\"afternoon_count\"]', '1:基础', '', '', '', '', 'reservation_date|day_format:30%日期\r\nmorning_count:15%上午放号数\r\nafternoon_count:15%下午放号数\r\nids:40%操作:lists?reservation_id=[id]&_controller=ReservationNumber&_addons=Reservation|查看预约情况,[DELETE]|删除', '20', '', '', '1436236287', '1436255291', '1', 'MyISAM', 'Reservation');
INSERT INTO `wp_model` VALUES ('1419', 'lzwg_vote_log', '投票记录', '0', '', '1', '[\"vote_id\",\"user_id\",\"options\"]', '1:基础', '', '', '', '', 'vote_id:25%投票标题\r\nuser_id:25%用户\r\noptions:25%投票选项\r\ncTime|time_format:25%创建时间\r\n\r\n\r\n\r\n', '20', '', '', '1388934136', '1430101786', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1420', 'lzwg_vote_option', '投票选项', '0', '', '1', '[\"name\",\"opt_count\",\"order\"]', '1:基础', '', '', '', '', 'name:选项标题\r\nopt_count:投票数', '20', '', '', '1388933346', '1429861449', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1421', 'prize', '奖项设置', '0', '', '1', '[\"title\",\"name\",\"num\",\"img\",\"sort\"]', '1:基础', '', '', '', '', 'title:奖项标题\r\nname:奖项\r\nnum:名额数量\r\nimg|get_img_html:奖品图片\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', 'title', '', '1399348610', '1399702991', '1', 'MyISAM', 'Scratch');
INSERT INTO `wp_model` VALUES ('1422', 'lzwg_coupon_sn', '优惠卷序列号', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1435896982', '1435896982', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1423', 'lzwg_log', '活动参与记录', '0', '', '1', '', '1:基础', '', '', '', '', '', '20', '', '', '1435892409', '1435892409', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1424', 'lzwg_vote', '投票', '0', '', '1', '[\"title\",\"description\",\"picurl\",\"start_date\",\"end_date\",\"template\",\"vote_type\"]', '1:基础', '', '', '', '', 'id:题目编号\r\ntitle:题目名称\r\nvote_option:题目选项\r\ntype|get_name_by_status:类型\r\nvote_count:投票数\r\nids:操作:[EDIT]&id=[id]|编辑,showLog&id=[id]|投票记录,showCount&id=[id]|选项票数,[DELETE]|删除', '20', 'title', 'description', '1388930292', '1435732110', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1425', 'lzwg_coupon_receive', '优惠券领取', '0', '', '1', '[\"sn_id\"]', '1:基础', '', '', '', '', 'follow_id|get_nickname|deal_emoji:领取人\r\ncTime|time_format:领取时间\r\ncoupon_title:优惠券名称\r\nsn:序列号\r\nis_use:是否使用', '20', '', '', '1435316411', '1435925060', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1426', 'lzwg_coupon', '优惠券', '0', '', '1', '[\"title\",\"money\",\"name\",\"condition\",\"intro\",\"sn_str\",\"img\"]', '1:基础', '', '', '', '', 'title:名称\r\nmoney:减免金额\r\nname:代金券标题\r\ncondition:抵押条件\r\nintro:优惠券简述\r\nsn_str:数量\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', '', '', '1435312925', '1435909008', '1', 'MyISAM', null);
INSERT INTO `wp_model` VALUES ('1427', 'lzwg_activities_vote', '投票答题活动', '0', '', '1', '[\"lzwg_id\",\"vote_type\",\"vote_limit\",\"lzwg_type\",\"vote_id\"]', '1:基础', '', '', '', '', 'lzwg_name:活动名称\r\nstart_time|time_format:活动开始时间\r\nend_time|time_format:活动结束时间\r\nlzwg_type|get_name_by_status:活动类型\r\nvote_title:题目\r\nids:操作:[EDIT]|编辑,[DELETE]|删除,tongji&id=[id]|用户参与分析\r\n', '20', 'lzwg_id:活动名称', '', '1435734819', '1435825972', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1428', 'lzwg_activities', '靓妆活动', '0', '', '1', '[\"title\",\"remark\",\"logo_img\",\"start_time\",\"end_time\",\"get_prize_tip\",\"no_prize_tip\",\"lottery_number\",\"get_prize_count\",\"comment_status\"]', '1:基础', '', '', '', '', 'title:活动名称\r\nremark:活动描述\r\nlogo_img|get_img_html:活动LOGO\r\nactivitie_time:活动时间\r\nget_prize_tip:中将提示信息\r\nno_prize_tip:未中将提示信息\r\ncomment_list:评论列表\r\nset_vote:设置投票\r\nset_award:设置奖品\r\nget_prize_list:中奖列表\r\nids:操作:[EDIT]|编辑,[DELETE]|删除', '20', '', '', '1435306468', '1436181872', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1429', 'lottery_prize_list', '抽奖奖品列表', '0', '', '1', '[\"sports_id\",\"award_id\",\"award_num\"]', '1:基础', '', '', '', '', 'sports_id:比赛场次\r\naward_id:奖品名称\r\naward_num:奖品数量\r\nid:编辑:[EDIT]|编辑,[DELETE]|删除,add?sports_id=[sports_id]|添加', '20', '', '', '1432613700', '1432710817', '1', 'MyISAM', 'Draw');
INSERT INTO `wp_model` VALUES ('1438', 'shop_reward_condition', '优惠条件', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1442458767', '1442458767', '1', 'MyISAM', 'ShopReward');
INSERT INTO `wp_model` VALUES ('1439', 'shop_cashout_account', '提现账号', '0', '', '1', '', '1:基础', null, '', '', '', null, '10', '', '', '1442396922', '1442396922', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1440', 'shop_reward', '促销活动', '0', '', '1', '[\"title\",\"start_time\",\"end_time\",\"is_mult\",\"is_all_goods\"]', '1:基础', null, '', '', '', 'title:活动名称\r\nstart_time:有效期\r\nstatus:活动状态\r\nid:操作:[EDIT]|编辑,[DELETE]|删除', '10', 'title:请输入活动名称搜索', '', '1442457808', '1442544407', '1', 'MyISAM', 'shop_reward');
INSERT INTO `wp_model` VALUES ('1441', 'shop_membership', '商城会员设置', '0', '', '1', '[\"membership\",\"img\",\"condition\"]', '1:基础', null, '', '', '', 'img|get_img_html:20%会员图标\r\nmembership:25%会员名\r\ncondition:20%条件（经历值）\r\nid:30%操作:[EDIT]|编辑,[DELETE]|删除', '10', 'membership:请输入会员名', '', '1441787383', '1441857253', '1', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1446', 'seckill', '秒杀', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'seckill');
INSERT INTO `wp_model` VALUES ('1451', 'seckill_goods', '秒杀商品', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'seckill');
INSERT INTO `wp_model` VALUES ('1452', 'seckill_order', '秒杀订单', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'seckill');
INSERT INTO `wp_model` VALUES ('1453', 'collage', '拼团', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'collage');
INSERT INTO `wp_model` VALUES ('1454', 'collage_goods', '拼团商品', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'collage');
INSERT INTO `wp_model` VALUES ('1455', 'collage_order', '拼团订单', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'collage');
INSERT INTO `wp_model` VALUES ('1456', 'collage_group', '开团信息', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'collage');
INSERT INTO `wp_model` VALUES ('1457', 'collage_robot', '凑团名单', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', 'nickname:搜索昵称', '', '0', '0', '0', 'MyISAM', 'collage');
INSERT INTO `wp_model` VALUES ('1458', 'haggle', '砍价', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'haggle');
INSERT INTO `wp_model` VALUES ('1459', 'haggle_order', '砍价订单', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'haggle');
INSERT INTO `wp_model` VALUES ('1460', 'haggle_user', '帮砍价的人', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'haggle');
INSERT INTO `wp_model` VALUES ('1461', 'chat', '客户记录', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'core');
INSERT INTO `wp_model` VALUES ('1462', 'stores_user', '用户默认选择的门店ID', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1463', 'shop_track', '足迹', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1465', 'event_prizes', '活动中奖奖品', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'draw');
INSERT INTO `wp_model` VALUES ('1467', 'staff_follow_link', '员工粉丝关系', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'card');
INSERT INTO `wp_model` VALUES ('1468', 'template_messages', '模板消息群发记录', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', 'title:根据消息标题搜索', '', '0', '0', '0', 'MyISAM', 'core');
INSERT INTO `wp_model` VALUES ('1469', 'goods_param_temp', '商品参数模板', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1470', 'member_erp_buying', 'ERP购买记录', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'card');
INSERT INTO `wp_model` VALUES ('1471', 'member_follow_link', '会员粉丝关系', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'card');
INSERT INTO `wp_model` VALUES ('1472', 'shop_goods_stock', '商品库存', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1474', 'shop_goods_content', '商品详情', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1475', 'shop_order_goods', '订单商品关联表', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'shop');
INSERT INTO `wp_model` VALUES ('1476', 'news', '官网新闻', '0', '', '1', null, '1:基础', null, '', '', '', null, '20', '', '', '0', '0', '0', 'MyISAM', 'solution');

-- ----------------------------
-- Table structure for `wp_online_count`
-- ----------------------------
DROP TABLE IF EXISTS `wp_online_count`;
CREATE TABLE `wp_online_count` (
  `publicid` int(11) DEFAULT NULL,
  `addon` varchar(30) DEFAULT NULL,
  `aim_id` int(11) DEFAULT NULL,
  `time` bigint(12) DEFAULT NULL,
  `count` int(11) DEFAULT NULL,
  KEY `tc` (`time`,`count`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_online_count
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_payment`
-- ----------------------------
DROP TABLE IF EXISTS `wp_payment`;
CREATE TABLE `wp_payment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `out_trade_no` char(32) NOT NULL,
  `total_fee` int(11) NOT NULL,
  `appid` char(32) NOT NULL,
  `openid` char(128) DEFAULT NULL,
  `callback` varchar(200) NOT NULL,
  `prepay_id` char(64) DEFAULT NULL,
  `code_url` char(64) DEFAULT NULL,
  `return_code` char(16) DEFAULT NULL,
  `return_msg` char(128) DEFAULT NULL,
  `result_code` char(16) DEFAULT NULL,
  `err_code_des` char(200) DEFAULT NULL,
  `cTime` int(10) NOT NULL,
  `param` text,
  `res_data` text,
  `is_pay` tinyint(1) DEFAULT '0',
  `after_pay_res` text,
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`),
  KEY `out_trade_no` (`out_trade_no`,`appid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=684 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_payment
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_payment_config`
-- ----------------------------
DROP TABLE IF EXISTS `wp_payment_config`;
CREATE TABLE `wp_payment_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `appid` char(32) NOT NULL,
  `mch_id` char(32) NOT NULL,
  `appsecret` char(100) DEFAULT NULL,
  `partner_key` char(100) DEFAULT NULL,
  `cert_pem` int(11) DEFAULT NULL,
  `key_pem` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_payment_config
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_payment_order`
-- ----------------------------
DROP TABLE IF EXISTS `wp_payment_order`;
CREATE TABLE `wp_payment_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `from` varchar(50) NOT NULL COMMENT '回调地址',
  `orderName` varchar(255) DEFAULT NULL COMMENT '订单名称',
  `single_orderid` varchar(100) NOT NULL COMMENT '订单号',
  `price` decimal(10,2) DEFAULT NULL COMMENT '价格',
  `wecha_id` varchar(200) NOT NULL COMMENT 'OpenID',
  `paytype` varchar(30) NOT NULL COMMENT '支付方式',
  `showwxpaytitle` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否显示标题',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '支付状态',
  `aim_id` int(10) DEFAULT NULL COMMENT 'aim_id',
  `uid` int(10) DEFAULT NULL COMMENT '用户uid',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_payment_order
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_payment_scan`
-- ----------------------------
DROP TABLE IF EXISTS `wp_payment_scan`;
CREATE TABLE `wp_payment_scan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `appid` char(32) NOT NULL,
  `callback` char(100) NOT NULL,
  `product_id` char(32) NOT NULL,
  `out_trade_no` char(32) DEFAULT NULL,
  `total_fee` int(11) DEFAULT NULL,
  `cTime` int(10) NOT NULL,
  `product` varchar(255) DEFAULT NULL,
  `shorturl_res` text,
  `order_param` varchar(255) DEFAULT NULL,
  `order_data` text,
  `order_res` text,
  PRIMARY KEY (`id`),
  KEY `appid_product_id` (`appid`,`product_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_payment_scan
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_payment_set`
-- ----------------------------
DROP TABLE IF EXISTS `wp_payment_set`;
CREATE TABLE `wp_payment_set` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `ctime` int(10) DEFAULT NULL COMMENT '创建时间',
  `wxappid` varchar(255) DEFAULT NULL COMMENT 'AppID',
  `wxpaysignkey` varchar(255) DEFAULT NULL COMMENT '支付密钥',
  `wxappsecret` varchar(255) DEFAULT NULL COMMENT 'AppSecret',
  `zfbname` varchar(255) DEFAULT NULL COMMENT '帐号',
  `pid` varchar(255) DEFAULT NULL COMMENT 'PID',
  `key` varchar(255) DEFAULT NULL COMMENT 'KEY',
  `partnerid` varchar(255) DEFAULT NULL COMMENT '财付通标识',
  `partnerkey` varchar(255) DEFAULT NULL COMMENT '财付通Key',
  `wappartnerid` varchar(255) DEFAULT NULL COMMENT '财付通标识WAP',
  `wappartnerkey` varchar(255) DEFAULT NULL COMMENT 'WAP财付通Key',
  `wxpartnerkey` varchar(255) DEFAULT NULL COMMENT '微信partnerkey',
  `wxpartnerid` varchar(255) DEFAULT NULL COMMENT '微信partnerid',
  `quick_security_key` varchar(255) DEFAULT NULL COMMENT '银联在线Key',
  `quick_merid` varchar(255) DEFAULT NULL COMMENT '银联在线merid',
  `quick_merabbr` varchar(255) DEFAULT NULL COMMENT '商户名称',
  `wpid` int(10) DEFAULT '0' COMMENT '商店ID',
  `wxmchid` varchar(255) DEFAULT NULL COMMENT '微信支付商户号',
  `wx_cert_pem` int(10) unsigned DEFAULT NULL COMMENT '上传证书',
  `wx_key_pem` int(10) unsigned DEFAULT NULL COMMENT '上传密匙',
  `shop_pay_score` int(10) DEFAULT '0' COMMENT '支付返积分',
  `deposit` int(10) DEFAULT '10' COMMENT '支付定金百分比',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_payment_set
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_picture`
-- ----------------------------
DROP TABLE IF EXISTS `wp_picture`;
CREATE TABLE `wp_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id自增',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片链接',
  `category_id` int(255) DEFAULT '0',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `system` tinyint(10) DEFAULT '0',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`),
  KEY `status` (`id`,`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3116 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_picture
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_picture_category`
-- ----------------------------
DROP TABLE IF EXISTS `wp_picture_category`;
CREATE TABLE `wp_picture_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `ctime` int(11) DEFAULT NULL,
  `system` tinyint(2) DEFAULT '0',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_picture_category
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_plugin`
-- ----------------------------
DROP TABLE IF EXISTS `wp_plugin`;
CREATE TABLE `wp_plugin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '插件名或标识',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '中文名',
  `description` text COMMENT '插件描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `config` text COMMENT '配置',
  `author` varchar(40) DEFAULT '' COMMENT '作者',
  `version` varchar(20) DEFAULT '' COMMENT '版本号',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `has_adminlist` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台列表',
  `cate_id` int(11) DEFAULT NULL,
  `is_show` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `name` (`name`) USING BTREE,
  KEY `sti` (`status`,`is_show`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=130 DEFAULT CHARSET=utf8 COMMENT='系统插件表';

-- ----------------------------
-- Records of wp_plugin
-- ----------------------------
INSERT INTO `wp_plugin` VALUES ('5', 'Editor', '前台编辑器', '用于增强整站长文本的输入和显示', '1', '{\"editor_type\":\"2\",\"editor_wysiwyg\":\"1\",\"editor_height\":\"300px\",\"editor_resize_type\":\"1\"}', 'thinkphp', '0.1', '1379830910', '0', null, '1');
INSERT INTO `wp_plugin` VALUES ('58', 'Cascade', '级联菜单', '支持无级级联菜单，用于地区选择、多层分类选择等场景。菜单的数据来源支持查询数据库和直接用户按格式输入两种方式', '1', 'null', '凡星', '0.1', '1398694996', '0', null, '1');
INSERT INTO `wp_plugin` VALUES ('120', 'DynamicSelect', '动态下拉菜单', '支持动态从数据库里取值显示', '1', 'null', '凡星', '0.1', '1435223177', '0', null, '1');
INSERT INTO `wp_plugin` VALUES ('125', 'News', '图文素材选择器', '', '1', 'null', '凡星', '0.1', '1439198046', '0', null, '1');
INSERT INTO `wp_plugin` VALUES ('127', 'DynamicCheckbox', '动态多选菜单', '支持动态从数据库里取值显示', '1', 'null', '凡星', '0.1', '1464002908', '0', null, '1');
INSERT INTO `wp_plugin` VALUES ('128', 'Prize', '奖品选择', '支持多种奖品选择', '1', 'null', '凡星', '0.1', '1464060178', '0', null, '1');
INSERT INTO `wp_plugin` VALUES ('129', 'Material', '素材选择', '支持动态从素材库里选择素材', '1', 'null', '凡星', '0.1', '1464060381', '0', null, '1');

-- ----------------------------
-- Table structure for `wp_prize`
-- ----------------------------
DROP TABLE IF EXISTS `wp_prize`;
CREATE TABLE `wp_prize` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `addon` varchar(255) DEFAULT 'Scratch' COMMENT '来源插件',
  `target_id` int(10) unsigned DEFAULT NULL COMMENT '来源ID',
  `title` varchar(255) DEFAULT NULL COMMENT '奖项标题',
  `name` varchar(255) DEFAULT NULL COMMENT '奖项',
  `num` int(10) unsigned DEFAULT NULL COMMENT '名额数量',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `img` int(10) unsigned DEFAULT NULL COMMENT '奖品图片',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_prize
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_prize_address`
-- ----------------------------
DROP TABLE IF EXISTS `wp_prize_address`;
CREATE TABLE `wp_prize_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `address` varchar(255) DEFAULT NULL COMMENT '奖品收货地址',
  `mobile` varchar(50) DEFAULT NULL COMMENT '手机',
  `turename` varchar(255) DEFAULT NULL COMMENT '收货人姓名',
  `uid` int(10) DEFAULT NULL COMMENT '用户id',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `prizeid` int(10) DEFAULT NULL COMMENT '奖品编号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_prize_address
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_public_auth`
-- ----------------------------
DROP TABLE IF EXISTS `wp_public_auth`;
CREATE TABLE `wp_public_auth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(50) NOT NULL,
  `title` varchar(100) NOT NULL,
  `type_0` tinyint(1) DEFAULT '0' COMMENT '普通订阅号的开关',
  `type_1` tinyint(1) DEFAULT '0' COMMENT '微信认证订阅号的开关',
  `type_2` tinyint(1) DEFAULT '0' COMMENT '普通服务号的开关',
  `type_3` tinyint(1) DEFAULT '0' COMMENT '微信认证服务号的开关',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_public_auth
-- ----------------------------
INSERT INTO `wp_public_auth` VALUES ('1', 'GET_ACCESS_TOKEN', '基础支持-获取access_token', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('2', 'GET_WECHAT_IP', '基础支持-获取微信服务器IP地址', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('3', 'GET_MSG', '接收消息-验证消息真实性、接收普通消息、接收事件推送、接收语音识别结果', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('4', 'SEND_REPLY_MSG', '发送消息-被动回复消息', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('5', 'SEND_CUSTOM_MSG', '发送消息-客服接口', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('6', 'SEND_GROUP_MSG', '发送消息-群发接口', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('7', 'SEND_NOTICE', '发送消息-模板消息接口（发送业务通知）', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('8', 'USER_GROUP', '用户管理-用户分组管理', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('9', 'USER_REMARK', '用户管理-设置用户备注名', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('10', 'USER_BASE_INFO', '用户管理-获取用户基本信息', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('11', 'USER_LIST', '用户管理-获取用户列表', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('12', 'USER_LOCATION', '用户管理-获取用户地理位置', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('13', 'USER_OAUTH', '用户管理-网页授权获取用户openid/用户基本信息', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('14', 'QRCODE', '推广支持-生成带参数二维码', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('15', 'LONG_URL', '推广支持-长链接转短链接口', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('16', 'MENU', '界面丰富-自定义菜单', '0', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('17', 'MATERIAL', '素材管理-素材管理接口', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('18', 'SEMANTIC', '智能接口-语义理解接口', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('19', 'CUSTOM_SERVICE', '多客服-获取多客服消息记录、客服管理', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('20', 'PAYMENT', '微信支付接口', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('21', 'SHOP', '微信小店接口', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('22', 'CARD', '微信卡券接口', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('23', 'DEVICE', '微信设备功能接口', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('24', 'JSSKD_BASE', '微信JS-SDK-基础接口', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('25', 'JSSKD_SHARE', '微信JS-SDK-分享接口', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('26', 'JSSKD_IMG', '微信JS-SDK-图像接口', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('27', 'JSSKD_AUDIO', '微信JS-SDK-音频接口', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('28', 'JSSKD_SEMANTIC', '微信JS-SDK-智能接口（网页语音识别）', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('29', 'JSSKD_DEVICE', '微信JS-SDK-设备信息', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('30', 'JSSKD_LOCATION', '微信JS-SDK-地理位置', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('31', 'JSSKD_MENU', '微信JS-SDK-界面操作', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('32', 'JSSKD_SCAN', '微信JS-SDK-微信扫一扫', '1', '1', '1', '1');
INSERT INTO `wp_public_auth` VALUES ('33', 'JSSKD_SHOP', '微信JS-SDK-微信小店', '0', '0', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('34', 'JSSKD_CARD', '微信JS-SDK-微信卡券', '0', '1', '0', '1');
INSERT INTO `wp_public_auth` VALUES ('35', 'JSSKD_PAYMENT', '微信JS-SDK-微信支付', '0', '0', '0', '1');

-- ----------------------------
-- Table structure for `wp_public_check`
-- ----------------------------
DROP TABLE IF EXISTS `wp_public_check`;
CREATE TABLE `wp_public_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `na` varchar(50) NOT NULL,
  `msg` varchar(255) DEFAULT NULL,
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=968 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_public_check
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_public_config`
-- ----------------------------
DROP TABLE IF EXISTS `wp_public_config`;
CREATE TABLE `wp_public_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pbid` int(11) DEFAULT '0' COMMENT 'pbid',
  `pkey` varchar(30) DEFAULT NULL COMMENT '配置规则名',
  `pvalue` text COMMENT '配置值',
  `mtime` int(10) DEFAULT NULL COMMENT '设置时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_public_config
-- ----------------------------
INSERT INTO `wp_public_config` VALUES ('5', '0', 'wecome', '{\"stype\":\"news:22\",\"material_stype_type\":\"text\",\"material_stype_text_id\":\"6\",\"material_stype_news_id\":\"\",\"material_stype_img_id\":\"\",\"material_stype_voice_id\":\"\",\"material_stype_video_id\":\"\",\"config\":{\"stype\":\"text:6\"}}', '1490264196');
INSERT INTO `wp_public_config` VALUES ('6', '0', 'no_answer', '{\"stype\":\"img:9\",\"material_stype_type\":\"img\",\"material_stype_text_id\":\"\",\"material_stype_news_id\":\"\",\"material_stype_img_id\":\"9\",\"material_stype_voice_id\":\"\",\"material_stype_video_id\":\"\",\"config\":{\"stype\":\"img:9\"}}', '1490267837');
INSERT INTO `wp_public_config` VALUES ('7', '0', 'user_center', '{\"score\":\"50\"}', '1490712694');
INSERT INTO `wp_public_config` VALUES ('8', '0', 'card', '{\"title\":\"\\u65f6\\u5c1a\\u7f8e\\u5bb9\\u7f8e\\u53d1\\u5e97VIP\\u4f1a\\u5458\\u5361\",\"title_color\":\"#000000\",\"number_color\":\"#000000\",\"background\":\"4\",\"bg\":\"\",\"logo\":\"\",\"show_logo\":\"0\",\"length\":\"40001\",\"startNumber\":\"4\",\"need_verify\":\"0\",\"mobile\":\"5555\",\"address\":\"66666\",\"background_custom\":\"\",\"bg_id\":\"\",\"back_background\":\"3\",\"backbg\":\"\",\"back_color\":\"#000000\",\"back_background_custom\":\"\",\"backbg_id\":\"\",\"instruction\":\"1\\u3001\\u606d\\u559c\\u60a8\\u6210\\u4e3a\\u65f6\\u5c1a\\u7f8e\\u5bb9\\u7f8e\\u53d1\\u5e97VIP\\u4f1a\\u5458;\\r\\n2\\u3001\\u7ed3\\u5e10\\u65f6\\u8bf7\\u51fa\\u793a\\u6b64\\u5361\\uff0c\\u51ed\\u6b64\\u5361\\u53ef\\u4eab\\u53d7\\u4f1a\\u5458\\u4f18\\u60e0;\\r\\n3\\u3001\\u6b64\\u5361\\u6700\\u7ec8\\u89e3\\u91ca\\u6743\\u5f52\\u65f6\\u5c1a\\u7f8e\\u5bb9\\u7f8e\\u53d1\\u5e97\\u6240\\u6709\"}', '1490756065');
INSERT INTO `wp_public_config` VALUES ('9', '0', 'draw', '{\"need_attention\":\"1\",\"need_writeinfo\":\"0\"}', '1490928134');
INSERT INTO `wp_public_config` VALUES ('10', '0', 'no_answer', '{\"stype\":\"img:12\"}', '1490952252');
INSERT INTO `wp_public_config` VALUES ('11', '0', 'wecome', '{\"stype\":\"news:0\"}', '1491028905');
INSERT INTO `wp_public_config` VALUES ('12', '0', 'weixin_wecome', '{\"stype\":\"news:7\",\"material_stype_type\":\"news\",\"material_stype_text_id\":\"\",\"material_stype_news_id\":\"7\",\"material_stype_img_id\":\"\",\"material_stype_voice_id\":\"\",\"material_stype_video_id\":\"\"}', '1528786777');
INSERT INTO `wp_public_config` VALUES ('15', '0', 'wei_site', '{\"title\":\"\\u76db\\u8baf\",\"cover\":\"1745\",\"info\":\"\\u76db\\u8baf\\u73e0\\u5b9d\",\"background\":null,\"template_\":\"pop_v1\",\"template_lists\":\"v2\",\"template_index\":\"color_v1\",\"template_footer\":\"pop_v1\"}', '1529230746');
INSERT INTO `wp_public_config` VALUES ('20', '0', 'draw_draw', '{\"need_attention\":\"1\",\"need_writeinfo\":\"1\"}', '1529394534');
INSERT INTO `wp_public_config` VALUES ('21', '0', 'weixin_no_answer', '{\"data_type\":\"0\",\"stype\":\"text:13\",\"material_stype_type\":\"text\",\"material_stype_text_id\":\"13\",\"material_stype_news_id\":\"\",\"material_stype_img_id\":\"\",\"material_stype_voice_id\":\"\",\"material_stype_video_id\":\"\",\"id\":\"\"}', '1529404292');
INSERT INTO `wp_public_config` VALUES ('22', '0', 'sing_in', '{\"random\":\"1\",\"score\":\"0\",\"score1\":\"1\",\"score2\":\"2\",\"hour\":\"0\",\"minute\":\"0\",\"continue_day\":\"1\",\"continue_score\":\"2\",\"share_score\":\"3\",\"share_limit\":\"2\",\"notstart\":\"\\u4eb2\\uff0c\\u4f60\\u8d77\\u5f97\\u592a\\u65e9\\u4e86,\\u7b7e\\u5230\\u4ece[\\u5f00\\u59cb\\u65f6\\u95f4]\\u5f00\\u59cb,\\u73b0\\u5728\\u624d[\\u5f53\\u524d\\u65f6\\u95f4]\\uff01\",\"done\":\"\\u4eb2\\uff0c\\u4eca\\u5929\\u5df2\\u7ecf\\u7b7e\\u5230\\u8fc7\\u4e86\\uff0c\\u8bf7\\u660e\\u5929\\u518d\\u6765\\u54e6\\uff0c\\u8c22\\u8c22\\uff01\",\"reply\":\"\\u606d\\u559c\\u60a8,\\u7b7e\\u5230\\u6210\\u529f\\r\\n\\r\\n\\u672c\\u6b21\\u7b7e\\u5230\\u83b7\\u5f97[\\u672c\\u6b21\\u79ef\\u5206]\\u79ef\\u5206\\r\\n\\r\\n\\u5f53\\u524d\\u603b\\u79ef\\u5206[\\u79ef\\u5206\\u4f59\\u989d]\\r\\n\\r\\n[\\u7b7e\\u5230\\u65f6\\u95f4]\\r\\n\\r\\n\\u60a8\\u4eca\\u5929\\u662f\\u7b2c[\\u6392\\u540d]\\u4f4d\\u7b7e\\u5230\\r\\n\\r\\n\\u7b7e\\u5230\\u6392\\u884c\\u699c\\uff1a\\r\\n\\r\\n[\\u6392\\u884c\\u699c]\",\"content\":\"\"}', '1529478724');
INSERT INTO `wp_public_config` VALUES ('23', '0', 'shop_shop_distribution_user', '{\"lock_num_time\":\"12\",\"is_mail\":\"1\",\"mail_money\":\"15\"}', '1529565666');
INSERT INTO `wp_public_config` VALUES ('26', '0', 'shop_shop', '{\"lock_num_time\":\"12\",\"is_mail\":\"1\",\"mail_money\":\"10\",\"id\":\"\"}', '1529629402');
INSERT INTO `wp_public_config` VALUES ('27', '0', 'card', '{\"bg\":\"\\/\",\"backbg\":\"\\/\"}', '1529633156');

-- ----------------------------
-- Table structure for `wp_public_follow`
-- ----------------------------
DROP TABLE IF EXISTS `wp_public_follow`;
CREATE TABLE `wp_public_follow` (
  `openid` varchar(100) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `has_subscribe` tinyint(1) DEFAULT '0',
  `syc_status` tinyint(1) DEFAULT '2' COMMENT '0 开始同步中 1 更新用户信息中 2 完成同步',
  `remark` varchar(100) DEFAULT NULL,
  `unionid` varchar(50) DEFAULT '' COMMENT '微信第三方ID',
  `pbid` int(10) NOT NULL DEFAULT '0' COMMENT 'pbid',
  UNIQUE KEY `openid` (`openid`,`pbid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_public_follow
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_publics`
-- ----------------------------
DROP TABLE IF EXISTS `wp_publics`;
CREATE TABLE `wp_publics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `public_name` varchar(50) DEFAULT NULL COMMENT '公众号名称',
  `public_id` varchar(100) DEFAULT NULL COMMENT '公众号原始id',
  `wechat` varchar(100) DEFAULT NULL COMMENT '微信号',
  `interface_url` varchar(255) DEFAULT NULL COMMENT '接口地址',
  `headface_url` varchar(255) DEFAULT NULL COMMENT '公众号头像',
  `area` varchar(50) DEFAULT NULL COMMENT '地区',
  `addon_status` text COMMENT '插件状态',
  `is_use` tinyint(2) DEFAULT '0' COMMENT '是否为当前公众号',
  `type` char(10) DEFAULT '0' COMMENT '公众号类型',
  `appid` char(32) DEFAULT NULL COMMENT 'AppID',
  `secret` varchar(255) DEFAULT NULL COMMENT 'AppSecret',
  `encodingaeskey` varchar(255) DEFAULT NULL COMMENT 'EncodingAESKey',
  `tips_url` varchar(255) DEFAULT NULL COMMENT '提示关注公众号的文章地址',
  `domain` varchar(30) DEFAULT NULL COMMENT '自定义域名',
  `is_bind` tinyint(2) DEFAULT '0' COMMENT '是否为微信开放平台绑定账号',
  `mch_id` char(32) DEFAULT NULL,
  `partner_key` char(100) DEFAULT NULL,
  `cert_pem` int(11) DEFAULT NULL,
  `key_pem` int(11) DEFAULT NULL,
  `authorizer_refresh_token` varchar(255) DEFAULT NULL,
  `check_file` int(10) unsigned DEFAULT NULL COMMENT '微信验证文件',
  `app_type` tinyint(2) DEFAULT '0',
  `wpid` int(11) DEFAULT NULL,
  `order_payok_messageid` varchar(255) DEFAULT NULL COMMENT '交易完成通知的模板ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_publics
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_qr_admin`
-- ----------------------------
DROP TABLE IF EXISTS `wp_qr_admin`;
CREATE TABLE `wp_qr_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `action_name` varchar(30) NOT NULL DEFAULT 'QR_SCENE' COMMENT '类型',
  `group_id` int(10) DEFAULT '0' COMMENT '用户组',
  `tag_ids` varchar(255) DEFAULT NULL COMMENT '用户标签',
  `qr_code` varchar(255) DEFAULT NULL COMMENT '二维码',
  `material` varchar(50) DEFAULT NULL COMMENT '扫码后的回复内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_qr_admin
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_qr_code`
-- ----------------------------
DROP TABLE IF EXISTS `wp_qr_code`;
CREATE TABLE `wp_qr_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `qr_code` varchar(255) NOT NULL COMMENT '二维码',
  `addon` varchar(255) NOT NULL COMMENT '二维码所属插件',
  `aim_id` int(10) unsigned NOT NULL COMMENT '插件表里的ID值',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `action_name` char(30) DEFAULT 'QR_SCENE' COMMENT '二维码类型',
  `extra_text` text COMMENT '文本扩展',
  `extra_int` int(10) DEFAULT NULL COMMENT '数字扩展',
  `request_count` int(10) DEFAULT '0' COMMENT '请求数',
  `scene_id` int(10) DEFAULT '0' COMMENT '场景ID',
  `expire_seconds` int(11) DEFAULT '2592000' COMMENT '有效期',
  `pbid` int(10) NOT NULL DEFAULT '0' COMMENT 'pbid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=841 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_qr_code
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_real_prize`
-- ----------------------------
DROP TABLE IF EXISTS `wp_real_prize`;
CREATE TABLE `wp_real_prize` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `prize_name` varchar(255) DEFAULT NULL COMMENT '奖品名称',
  `prize_conditions` text COMMENT '活动说明',
  `prize_count` int(10) DEFAULT NULL COMMENT '奖品个数',
  `prize_image` varchar(255) DEFAULT '上传奖品图片' COMMENT '奖品图片',
  `fail_content` text COMMENT '领取失败提示',
  `prize_type` tinyint(2) DEFAULT '1' COMMENT '奖品类型',
  `use_content` text COMMENT '使用说明',
  `prize_title` varchar(255) DEFAULT NULL COMMENT '活动标题',
  `template` varchar(255) DEFAULT 'default' COMMENT '素材模板',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_real_prize
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_recharge_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_recharge_log`;
CREATE TABLE `wp_recharge_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `recharge` float DEFAULT NULL COMMENT '充值金额',
  `branch_id` int(10) DEFAULT '0' COMMENT '充值门店',
  `operator` varchar(255) DEFAULT NULL COMMENT '操作员',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `member_id` int(10) DEFAULT NULL COMMENT '会员id',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员id',
  `type` tinyint(2) DEFAULT '1' COMMENT '充值方式',
  `remark` text COMMENT '备注',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `username` varchar(255) DEFAULT NULL COMMENT '姓名',
  `phone` varchar(255) DEFAULT NULL COMMENT '手机号',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_recharge_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_request_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_request_log`;
CREATE TABLE `wp_request_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `md5` char(32) NOT NULL COMMENT '接口名称',
  `url` varchar(255) NOT NULL COMMENT '序列化后的参数',
  `param` text,
  `res` text,
  `error_code` char(30) DEFAULT '0',
  `msg` varchar(255) DEFAULT NULL,
  `server_ip` varchar(30) DEFAULT NULL COMMENT '服务器IP地址',
  `cTime` int(10) NOT NULL COMMENT '记录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_request_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_reservation`
-- ----------------------------
DROP TABLE IF EXISTS `wp_reservation`;
CREATE TABLE `wp_reservation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `reservation_date` int(10) DEFAULT NULL COMMENT '日期',
  `morning_count` char(50) DEFAULT '30' COMMENT '上午放号数',
  `afternoon_count` char(50) DEFAULT '30' COMMENT '下午放号数',
  `c_time` int(10) DEFAULT NULL COMMENT '创建日期',
  `manager_id` int(10) DEFAULT '0' COMMENT '管理员ID',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_reservation
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_reservation_number`
-- ----------------------------
DROP TABLE IF EXISTS `wp_reservation_number`;
CREATE TABLE `wp_reservation_number` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `reservation_date` int(10) DEFAULT NULL COMMENT '预约日期',
  `type` int(10) DEFAULT NULL COMMENT '上午/下午',
  `is_use` int(10) DEFAULT '0' COMMENT '是否预约',
  `phone` varchar(255) DEFAULT NULL COMMENT '手机号',
  `name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `desc` text COMMENT '病症描述',
  `use_time` int(10) DEFAULT NULL COMMENT '提交预约的时间',
  `reservation_id` int(10) DEFAULT NULL COMMENT '日期ID',
  `reservation_num` varchar(255) DEFAULT NULL COMMENT '预约号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_reservation_number
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_score_exchange_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_score_exchange_log`;
CREATE TABLE `wp_score_exchange_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `card_score_id` int(10) DEFAULT NULL COMMENT '兑换活动id',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `ctime` int(10) DEFAULT NULL COMMENT 'ctime',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  `score` int(10) DEFAULT '0' COMMENT '兑换积分',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_score_exchange_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_scratch`
-- ----------------------------
DROP TABLE IF EXISTS `wp_scratch`;
CREATE TABLE `wp_scratch` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `use_tips` varchar(255) NOT NULL COMMENT '使用说明',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text COMMENT '封面简介',
  `end_time` int(10) DEFAULT NULL COMMENT '结束时间',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '封面图片',
  `cTime` int(10) unsigned DEFAULT NULL COMMENT '发布时间',
  `start_time` int(10) DEFAULT NULL COMMENT '开始时间',
  `end_tips` text COMMENT '过期说明',
  `predict_num` int(10) unsigned NOT NULL COMMENT '预计参与人数',
  `max_num` int(10) unsigned DEFAULT '1' COMMENT '每人最多允许抽奖次数',
  `follower_condtion` char(50) DEFAULT '1' COMMENT '粉丝状态',
  `credit_conditon` int(10) unsigned DEFAULT '0' COMMENT '积分限制',
  `credit_bug` int(10) unsigned DEFAULT '0' COMMENT '积分消费',
  `addon_condition` varchar(255) DEFAULT NULL COMMENT '插件场景限制',
  `collect_count` int(10) unsigned DEFAULT '0' COMMENT '已领取人数',
  `view_count` int(10) unsigned DEFAULT '0' COMMENT '浏览人数',
  `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
  `end_img` int(10) unsigned DEFAULT NULL COMMENT '过期提示图片',
  `template` varchar(255) DEFAULT 'default' COMMENT '素材模板',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_scratch
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_seckill`
-- ----------------------------
DROP TABLE IF EXISTS `wp_seckill`;
CREATE TABLE `wp_seckill` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) NOT NULL COMMENT '活动名称',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '活动宣传图',
  `start_time` int(10) NOT NULL COMMENT '活动开始时间',
  `end_time` int(10) NOT NULL COMMENT '活动结束时间',
  `is_subscribe` tinyint(2) DEFAULT '0' COMMENT '是否需要关注公众号才能参加',
  `is_member` tinyint(2) DEFAULT '0' COMMENT '是否需要成为会员才能参加',
  `content` text COMMENT '活动描述',
  `type` tinyint(2) DEFAULT '0' COMMENT '显示类型',
  `status` tinyint(2) DEFAULT '0' COMMENT '状态',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `order_limit` smallint(4) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_seckill
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_seckill_goods`
-- ----------------------------
DROP TABLE IF EXISTS `wp_seckill_goods`;
CREATE TABLE `wp_seckill_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL COMMENT '商品名称',
  `shop_goods_id` int(10) DEFAULT NULL COMMENT '商品来源',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '商品封面图',
  `express` decimal(10,2) DEFAULT '0.00' COMMENT '邮费',
  `seckill_id` int(10) DEFAULT NULL COMMENT '活动Id',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `send_type` varchar(30) DEFAULT NULL,
  `is_all_store` tinyint(1) DEFAULT '0',
  `visit_count` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_seckill_goods
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_seckill_order`
-- ----------------------------
DROP TABLE IF EXISTS `wp_seckill_order`;
CREATE TABLE `wp_seckill_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `seckill_id` int(10) DEFAULT NULL COMMENT '活动Id',
  `order_id` int(10) DEFAULT NULL COMMENT '订单Id',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `create_at` int(10) DEFAULT NULL COMMENT '秒杀时间',
  `sale_price` decimal(10,2) DEFAULT '0.00' COMMENT '秒杀价格',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品ID',
  `shop_goods_id` int(10) DEFAULT NULL COMMENT '库存ID',
  `is_pay` tinyint(2) DEFAULT '0' COMMENT ' 状态',
  `market_price` decimal(10,2) DEFAULT NULL COMMENT '原价',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=374 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_seckill_order
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_servicer`
-- ----------------------------
DROP TABLE IF EXISTS `wp_servicer`;
CREATE TABLE `wp_servicer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '用户选择',
  `truename` varchar(255) DEFAULT NULL COMMENT '真实姓名',
  `mobile` varchar(255) DEFAULT NULL COMMENT '手机号',
  `role` varchar(100) DEFAULT '0' COMMENT '授权列表',
  `enable` int(10) DEFAULT '1' COMMENT '是否启用',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  `update_at` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_servicer
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_share_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_share_log`;
CREATE TABLE `wp_share_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '用户id',
  `sTime` int(10) DEFAULT NULL COMMENT '分享时间',
  `score` int(10) DEFAULT NULL COMMENT '积分',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_share_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop`;
CREATE TABLE `wp_shop` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '商店名称',
  `logo` int(10) DEFAULT NULL COMMENT '商店LOGO',
  `intro` text COMMENT '店铺简介',
  `mobile` varchar(30) DEFAULT NULL COMMENT '联系电话',
  `qq` int(10) DEFAULT NULL COMMENT 'QQ',
  `wechat` varchar(50) DEFAULT NULL COMMENT '微信',
  `template` varchar(30) DEFAULT NULL COMMENT '模板',
  `content` text COMMENT '店铺介绍',
  `wpid` int(11) DEFAULT NULL,
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员ID',
  `api_key` varchar(100) DEFAULT NULL COMMENT '快递接口的APPKEY',
  `parent_shop` varchar(255) DEFAULT NULL COMMENT '分销上级商店',
  `address` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `gps` varchar(55) DEFAULT NULL COMMENT 'GPS经纬度',
  `custom_tip` text COMMENT '联系客服提示内容',
  `tcp` text COMMENT '客户协议',
  `order_payok_messageid` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_address`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_address`;
CREATE TABLE `wp_shop_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `truename` varchar(100) DEFAULT NULL COMMENT '收货人姓名',
  `mobile` varchar(50) DEFAULT NULL COMMENT '手机号码',
  `city` varchar(255) DEFAULT NULL COMMENT '城市',
  `address` varchar(255) DEFAULT NULL COMMENT '具体地址',
  `is_use` tinyint(2) DEFAULT '0' COMMENT '是否设置为默认',
  `address_detail` varchar(255) DEFAULT NULL,
  `is_del` tinyint(2) DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_address
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_attribute`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_attribute`;
CREATE TABLE `wp_shop_attribute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `is_show` tinyint(2) DEFAULT '1' COMMENT '是否显示',
  `cate_id` int(10) unsigned DEFAULT NULL COMMENT '所属分类ID',
  `error_info` varchar(255) DEFAULT NULL COMMENT '出错提示',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `validate_rule` varchar(255) DEFAULT NULL COMMENT '正则验证',
  `is_must` tinyint(2) DEFAULT NULL COMMENT '是否必填',
  `remark` varchar(255) DEFAULT NULL COMMENT '字段备注',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `value` varchar(255) DEFAULT NULL COMMENT '默认值',
  `title` varchar(255) NOT NULL COMMENT '字段标题',
  `mTime` int(10) DEFAULT NULL COMMENT '修改时间',
  `extra` text COMMENT '参数',
  `attr_type` char(50) NOT NULL DEFAULT 'string' COMMENT '字段类型',
  `type` tinyint(2) DEFAULT '0' COMMENT '属性类型',
  `name` varchar(30) DEFAULT NULL COMMENT '属性标识',
  `goods_field` varchar(50) DEFAULT NULL COMMENT '商品表中所占用的字段名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_attribute
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_cart`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_cart`;
CREATE TABLE `wp_shop_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `goods_id` varchar(255) NOT NULL COMMENT '商品id',
  `num` int(10) unsigned DEFAULT NULL COMMENT '数量',
  `price` decimal(10,2) DEFAULT NULL COMMENT '单价',
  `goods_type` tinyint(2) DEFAULT '0' COMMENT '商品类型',
  `openid` varchar(255) DEFAULT NULL COMMENT 'openid',
  `spec_option_ids` varchar(50) DEFAULT NULL COMMENT '商品SKU',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `lock_rid_num` int(10) DEFAULT '0' COMMENT '释放库存数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=425 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_cart
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_cashout_account`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_cashout_account`;
CREATE TABLE `wp_shop_cashout_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `account` varchar(255) DEFAULT NULL COMMENT '提现账号',
  `type` char(50) DEFAULT NULL COMMENT '提现方式',
  `name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_cashout_account
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_cashout_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_cashout_log`;
CREATE TABLE `wp_shop_cashout_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cashout_amount` float DEFAULT NULL COMMENT '提现金额',
  `remark` text COMMENT '备注',
  `cashout_account` varchar(300) DEFAULT NULL COMMENT '提现账号',
  `cashout_status` int(10) DEFAULT '0' COMMENT '提现处理状态',
  `ctime` int(10) DEFAULT NULL COMMENT '提现时间',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_cashout_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_collect`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_collect`;
CREATE TABLE `wp_shop_collect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '使用UID',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品ID',
  `cTime` int(10) DEFAULT NULL COMMENT '收藏时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=238 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_collect
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_distribution_profit`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_distribution_profit`;
CREATE TABLE `wp_shop_distribution_profit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT 'Uid',
  `ctime` int(10) DEFAULT NULL COMMENT '返利时间',
  `profit` float DEFAULT '0' COMMENT '拥金',
  `profit_shop` int(10) DEFAULT NULL COMMENT '获得佣金的店铺',
  `distribution_percent` varchar(255) DEFAULT NULL COMMENT '分销比例',
  `order_id` int(10) DEFAULT NULL COMMENT '订单id',
  `upper_user` int(10) DEFAULT NULL COMMENT '分销用户',
  `upper_level` int(10) DEFAULT NULL COMMENT '分销用户级别',
  `duser` int(10) DEFAULT NULL COMMENT '该用户带来的消费用户',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_distribution_profit
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_distribution_user`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_distribution_user`;
CREATE TABLE `wp_shop_distribution_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '用户id',
  `enable` char(10) DEFAULT '1' COMMENT '是否启用',
  `branch_id` varchar(255) DEFAULT NULL COMMENT '授权门店',
  `ctime` int(10) DEFAULT NULL COMMENT '创建时间',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `qr_code` varchar(255) DEFAULT NULL COMMENT '二维码',
  `fans_gift_money` decimal(10,2) DEFAULT '0.00' COMMENT '转发增粉奖励金额',
  `fans_gift_score` int(10) DEFAULT '0' COMMENT '转发增粉奖励积分',
  `fans_gift_coupon` int(10) DEFAULT '0' COMMENT '转发增粉奖励优惠券',
  `wechat` varchar(255) DEFAULT NULL COMMENT '微信号',
  `inviter` varchar(255) DEFAULT NULL COMMENT '邀请人',
  `level` int(10) DEFAULT NULL COMMENT '分佣级别',
  `is_audit` int(10) DEFAULT '0' COMMENT '是否审核',
  `is_delete` int(10) DEFAULT '0' COMMENT '是否删除',
  `shop_name` varchar(255) DEFAULT NULL COMMENT '商城店名字',
  `shop_logo` varchar(300) DEFAULT NULL COMMENT '商城图标',
  `profit_money` float DEFAULT '0' COMMENT '盈利金额',
  `zfb_name` varchar(255) DEFAULT NULL COMMENT '支付宝名称',
  `zfb_account` varchar(255) DEFAULT NULL COMMENT '支付宝账号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_distribution_user
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_goods`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_goods`;
CREATE TABLE `wp_shop_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '商品名称',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '商品封面图',
  `imgs` varchar(255) DEFAULT NULL COMMENT '商品图片',
  `is_show` tinyint(2) DEFAULT '0' COMMENT '是否上架',
  `is_recommend` tinyint(2) DEFAULT '0' COMMENT '是否推荐',
  `rank` int(10) DEFAULT '0' COMMENT '热销度',
  `show_time` int(10) DEFAULT '0' COMMENT '上架时间',
  `cost_price` decimal(10,2) DEFAULT '0.00' COMMENT '成本价',
  `category_id` char(50) DEFAULT NULL COMMENT '商品分类',
  `auto_send` char(10) DEFAULT '0' COMMENT '自动发货',
  `virtual_textarea` text COMMENT '虚拟物品信息',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `weight` float DEFAULT NULL COMMENT '重量',
  `sn_code` text COMMENT '商品编号',
  `diy_id` int(10) DEFAULT '0' COMMENT '详情页面DidId',
  `is_delete` int(10) DEFAULT '0' COMMENT '是否删除',
  `is_new` varchar(100) DEFAULT NULL COMMENT '新品类型',
  `can_deposit` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否支持定金支付',
  `reduce_score` int(10) DEFAULT '0' COMMENT '可抵扣积分',
  `distribution_price` decimal(10,2) DEFAULT NULL COMMENT '分销返佣金额',
  `is_spec` int(10) DEFAULT '0' COMMENT '是否有规格',
  `file_url` varchar(255) DEFAULT NULL COMMENT '文件下载链接',
  `express` decimal(10,2) DEFAULT '0.00' COMMENT '邮费',
  `send_type` varchar(30) DEFAULT '1' COMMENT '收货方式',
  `stores_ids` varchar(100) DEFAULT NULL COMMENT '自提门店',
  `is_all_store` tinyint(2) DEFAULT '0' COMMENT '店门类型',
  `tab` varchar(100) DEFAULT NULL COMMENT '同款标签',
  `param_temp_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_goods
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_goods_category`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_goods_category`;
CREATE TABLE `wp_shop_goods_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) NOT NULL COMMENT '分类标题',
  `icon` int(10) unsigned DEFAULT NULL COMMENT '分类图标',
  `pid` int(10) unsigned DEFAULT '0' COMMENT '上一级分类',
  `path` varchar(255) DEFAULT NULL COMMENT '分类路径',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `is_show` tinyint(2) DEFAULT '1' COMMENT '是否显示',
  `is_recommend` tinyint(2) DEFAULT '0' COMMENT '是否推荐',
  `wpid` int(10) DEFAULT NULL COMMENT 'Token',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_goods_category
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_goods_comment`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_goods_comment`;
CREATE TABLE `wp_shop_goods_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品编号',
  `score` int(10) DEFAULT '0' COMMENT '评价',
  `content` text COMMENT '内容',
  `order_id` int(10) DEFAULT NULL COMMENT '订单编号',
  `uid` int(10) DEFAULT NULL COMMENT '用户uid',
  `pic` varchar(255) DEFAULT NULL COMMENT '图片',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `is_show` int(10) DEFAULT '1' COMMENT '是否显示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_goods_comment
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_goods_content`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_goods_content`;
CREATE TABLE `wp_shop_goods_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `goods_id` int(10) NOT NULL COMMENT '商品ID',
  `content` text COMMENT '内容',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_goods_content
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_goods_score`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_goods_score`;
CREATE TABLE `wp_shop_goods_score` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品ID',
  `score` int(10) DEFAULT '0' COMMENT '得分',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_goods_score
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_goods_sku_config`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_goods_sku_config`;
CREATE TABLE `wp_shop_goods_sku_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `goods_id` int(10) DEFAULT '0' COMMENT '商品ID',
  `spec_id` int(10) DEFAULT '0' COMMENT '规格ID',
  `option_id` int(10) DEFAULT '0' COMMENT '属性ID',
  `img` int(10) unsigned DEFAULT NULL COMMENT '属性加图',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_goods_sku_config
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_goods_sku_data`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_goods_sku_data`;
CREATE TABLE `wp_shop_goods_sku_data` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品ID',
  `cost_price` decimal(11,2) DEFAULT '0.00' COMMENT '成本价',
  `market_price` decimal(11,2) DEFAULT '0.00' COMMENT '市场价',
  `sale_price` decimal(11,2) DEFAULT '0.00' COMMENT '促销价',
  `stock_num` int(10) DEFAULT '0' COMMENT '库存量',
  `sn_code` varchar(50) DEFAULT NULL COMMENT '商品编号',
  `sku_int_0` int(10) DEFAULT '0' COMMENT '数量规格0',
  `sku_int_1` int(10) DEFAULT '0' COMMENT '数量规格1',
  `sku_int_2` int(10) DEFAULT '0' COMMENT '数量规格2',
  `sku_varchar_0` varchar(255) DEFAULT NULL COMMENT '文本规格0',
  `sku_varchar_1` varchar(255) DEFAULT NULL COMMENT '文本规格1',
  `sku_varchar_2` varchar(255) DEFAULT NULL COMMENT '文本规格2',
  `spec_option_ids` varchar(100) DEFAULT NULL COMMENT '规格属性ID串',
  `lock_num` int(10) DEFAULT '0' COMMENT '商品锁定库存',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_goods_sku_data
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_goods_stock`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_goods_stock`;
CREATE TABLE `wp_shop_goods_stock` (
  `stock_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键，不使用id主要是方便与goods表联系查询不产生id覆盖报错',
  `stock` int(10) unsigned DEFAULT '0' COMMENT '物理库存',
  `stock_active` int(10) unsigned DEFAULT '0' COMMENT '销售库存',
  `lock_count` int(10) unsigned DEFAULT '0' COMMENT '锁定库存',
  `sale_count` int(10) unsigned DEFAULT '0' COMMENT '销售量',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品ID',
  `event_type` tinyint(2) DEFAULT '0' COMMENT '商品来源',
  `market_price` decimal(10,2) DEFAULT '0.00' COMMENT '原价',
  `sale_price` decimal(10,2) DEFAULT '0.00' COMMENT '销售价',
  `shop_goods_id` int(10) DEFAULT NULL,
  `del_at` int(10) DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`stock_id`),
  KEY `goods_id` (`goods_id`,`event_type`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=275 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_goods_stock
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_membership`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_membership`;
CREATE TABLE `wp_shop_membership` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `membership` varchar(255) DEFAULT NULL COMMENT '会员名',
  `condition` int(10) DEFAULT '0' COMMENT '升级会员条件',
  `uid` int(10) DEFAULT NULL COMMENT '企业用户id',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `img` int(10) unsigned DEFAULT NULL COMMENT '图标',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_membership
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_order`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_order`;
CREATE TABLE `wp_shop_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `goods_datas` text NOT NULL COMMENT '商品序列化数据',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `remark` text NOT NULL COMMENT '备注',
  `order_number` varchar(255) NOT NULL COMMENT '订单编号',
  `cTime` int(10) NOT NULL COMMENT '订单时间',
  `total_price` decimal(10,2) DEFAULT NULL COMMENT '总价',
  `openid` varchar(255) NOT NULL COMMENT 'OpenID',
  `pay_status` int(10) DEFAULT NULL COMMENT '支付状态',
  `pay_type` tinyint(2) DEFAULT '0' COMMENT '支付类型',
  `address_id` int(10) DEFAULT NULL COMMENT '配送信息',
  `is_send` int(10) DEFAULT '0' COMMENT '是否发货',
  `send_code` varchar(255) DEFAULT NULL COMMENT '快递公司编号',
  `send_number` varchar(255) DEFAULT NULL COMMENT '快递单号',
  `send_type` char(10) DEFAULT NULL COMMENT '发货类型',
  `is_new` tinyint(2) DEFAULT '1' COMMENT '是否为新订单',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `status_code` char(50) DEFAULT '0' COMMENT '订单跟踪状态码',
  `event_type` tinyint(2) DEFAULT '0' COMMENT '订单来源',
  `is_lock` int(10) DEFAULT '1' COMMENT '数量是否锁定',
  `erp_lock_code` text COMMENT 'ERP锁定商品编号',
  `mail_money` float DEFAULT '0' COMMENT '邮费金额',
  `stores_id` int(10) DEFAULT NULL COMMENT '门店编号',
  `pay_time` int(10) DEFAULT NULL COMMENT '支付时间',
  `send_time` int(10) DEFAULT NULL COMMENT '发货时间',
  `extra` text COMMENT '扩展参数',
  `order_state` int(10) DEFAULT '1' COMMENT '订单状态',
  `out_trade_no` varchar(100) DEFAULT NULL COMMENT '支付的订单号',
  `event_id` int(10) DEFAULT NULL COMMENT '活动ID',
  `is_original` tinyint(2) DEFAULT '0' COMMENT '活动中是否原价购买',
  `update_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `notice_erp` int(11) DEFAULT '0' COMMENT '为0时不需要推送，大于0时需要推送',
  `refund` tinyint(1) DEFAULT '0',
  `refund_content` varchar(255) DEFAULT NULL,
  `pay_money` decimal(10,2) DEFAULT NULL COMMENT '实付价格',
  `dec_money` decimal(10,2) DEFAULT NULL COMMENT '优惠价格',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2298 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_order
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_order_goods`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_order_goods`;
CREATE TABLE `wp_shop_order_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` int(10) NOT NULL COMMENT '订单ID',
  `goods_id` int(10) NOT NULL COMMENT '商品ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=485 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_order_goods
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_order_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_order_log`;
CREATE TABLE `wp_shop_order_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` int(10) DEFAULT NULL COMMENT '订单ID',
  `status_code` char(50) DEFAULT '0' COMMENT '状态码',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注内容',
  `cTime` int(10) DEFAULT NULL COMMENT '时间',
  `extend` varchar(255) DEFAULT NULL COMMENT '扩展信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1183 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_order_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_page`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_page`;
CREATE TABLE `wp_shop_page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL COMMENT '页面名称',
  `ctime` int(15) DEFAULT NULL COMMENT '创建时间',
  `config` text COMMENT '配置参数',
  `desc` text COMMENT '描述',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `manager_id` int(10) DEFAULT NULL COMMENT '创建者ID',
  `use` varchar(255) DEFAULT 'page' COMMENT '哪里使用',
  `is_show` tinyint(2) DEFAULT '0' COMMENT '是否显示底部导航',
  `is_index` int(10) DEFAULT '0' COMMENT '设为首页',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=145 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_page
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_reward`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_reward`;
CREATE TABLE `wp_shop_reward` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) DEFAULT NULL COMMENT '活动名称',
  `start_time` int(10) DEFAULT NULL COMMENT '开始时间',
  `end_time` int(10) DEFAULT NULL COMMENT '过期时间',
  `is_mult` tinyint(2) DEFAULT '0' COMMENT '多级优惠',
  `is_all_goods` tinyint(2) DEFAULT '0' COMMENT '适用的活动商品',
  `goods_ids` text COMMENT '指定商品ID串',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_reward
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_reward_condition`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_reward_condition`;
CREATE TABLE `wp_shop_reward_condition` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `condition` decimal(11,2) DEFAULT NULL COMMENT '条件',
  `reward_id` int(10) DEFAULT NULL COMMENT '活动ID',
  `money` decimal(10,2) DEFAULT NULL COMMENT '现在开关',
  `money_param` decimal(10,2) DEFAULT NULL COMMENT '现金参数',
  `postage` tinyint(2) DEFAULT '0' COMMENT '免邮',
  `score` tinyint(2) DEFAULT '0' COMMENT '积分开关',
  `score_param` int(10) DEFAULT NULL COMMENT '积分参数',
  `coupon` tinyint(2) DEFAULT '0' COMMENT '优惠券开关',
  `coupon_param` int(10) DEFAULT NULL COMMENT '优惠券ID',
  `sort` int(10) DEFAULT '0' COMMENT '排序号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_reward_condition
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_service`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_service`;
CREATE TABLE `wp_shop_service` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `token` varchar(255) DEFAULT NULL COMMENT 'Token',
  `openid` varchar(255) DEFAULT NULL COMMENT 'OpenID',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `content` text COMMENT '文本消息内容',
  `state` int(10) DEFAULT '0' COMMENT '状态',
  `uid` int(10) DEFAULT '0' COMMENT '用户',
  `is_manager` int(10) DEFAULT '0' COMMENT '是否管理员回复',
  `from_type` varchar(255) DEFAULT '0_导航栏' COMMENT '入口类型',
  `to_id` int(10) DEFAULT '0' COMMENT '回复对象',
  `customer` int(10) DEFAULT '0' COMMENT '客服人员',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=466 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_service
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_slideshow`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_slideshow`;
CREATE TABLE `wp_shop_slideshow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `img` int(10) unsigned NOT NULL COMMENT '图片',
  `url` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `is_show` tinyint(2) DEFAULT '1' COMMENT '是否显示',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `uid` int(10) DEFAULT NULL COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_slideshow
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_spec`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_spec`;
CREATE TABLE `wp_shop_spec` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(30) DEFAULT NULL COMMENT '规格名称',
  `remark` varchar(100) DEFAULT NULL COMMENT '备注',
  `spec_sort` int(10) DEFAULT '0' COMMENT '排序',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_spec
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_spec_option`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_spec_option`;
CREATE TABLE `wp_shop_spec_option` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `spec_id` int(10) DEFAULT NULL COMMENT '规格ID',
  `name` varchar(100) DEFAULT NULL COMMENT '规格属性名称',
  `sort` int(10) DEFAULT '0' COMMENT '排序号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_spec_option
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_statistics_follow`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_statistics_follow`;
CREATE TABLE `wp_shop_statistics_follow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT '粉丝id',
  `duid` int(10) DEFAULT NULL COMMENT '分销用户id',
  `ctime` int(10) DEFAULT NULL COMMENT '关注时间',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `openid` varchar(255) DEFAULT NULL COMMENT '粉丝openid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_statistics_follow
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_track`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_track`;
CREATE TABLE `wp_shop_track` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `goods_id` int(10) DEFAULT NULL COMMENT 'goods_id',
  `create_at` int(10) DEFAULT NULL COMMENT 'create_at',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5708 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_track
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_user_level_link`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_user_level_link`;
CREATE TABLE `wp_shop_user_level_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `uid` int(10) DEFAULT NULL COMMENT '分销用户',
  `upper_user` int(10) DEFAULT NULL COMMENT '上级分销用户',
  `level` int(10) DEFAULT NULL COMMENT '分销级别',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_user_level_link
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_value`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_value`;
CREATE TABLE `wp_shop_value` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `cate_id` int(10) unsigned DEFAULT NULL COMMENT '所属分类ID',
  `value` text COMMENT '表单值',
  `cTime` int(10) DEFAULT NULL COMMENT '增加时间',
  `openid` varchar(255) DEFAULT NULL COMMENT 'OpenId',
  `uid` int(10) DEFAULT NULL COMMENT '用户ID',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_value
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_shop_virtual`
-- ----------------------------
DROP TABLE IF EXISTS `wp_shop_virtual`;
CREATE TABLE `wp_shop_virtual` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品ID',
  `account` varchar(255) DEFAULT NULL COMMENT '账号',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `is_use` char(10) DEFAULT '0' COMMENT '是否已经使用',
  `order_id` int(10) DEFAULT NULL COMMENT '订单号',
  `card_codes` varchar(255) DEFAULT NULL COMMENT '点卡序列号',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `uid` int(10) DEFAULT NULL COMMENT '购买用户uid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_shop_virtual
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_sign_in_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_sign_in_log`;
CREATE TABLE `wp_sign_in_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `score` int(10) NOT NULL COMMENT '积分',
  `sTime` int(10) unsigned NOT NULL COMMENT '签到时间',
  `uid` varchar(255) NOT NULL COMMENT '用户ID',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_sign_in_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_signin_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_signin_log`;
CREATE TABLE `wp_signin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `score` int(10) NOT NULL COMMENT '积分',
  `sTime` int(10) unsigned NOT NULL COMMENT '签到时间',
  `uid` varchar(255) NOT NULL COMMENT '用户ID',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=868 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_signin_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_sms`
-- ----------------------------
DROP TABLE IF EXISTS `wp_sms`;
CREATE TABLE `wp_sms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `from_type` varchar(255) DEFAULT NULL COMMENT '用途',
  `code` varchar(255) DEFAULT NULL COMMENT '验证码',
  `smsId` varchar(255) DEFAULT NULL COMMENT '短信唯一标识',
  `phone` varchar(255) DEFAULT NULL COMMENT '手机号',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `status` int(10) DEFAULT NULL COMMENT '使用状态',
  `plat_type` int(10) DEFAULT NULL COMMENT '平台标识',
  `wpid` int(10) DEFAULT '0' COMMENT '公众号id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_sms
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_sn_code`
-- ----------------------------
DROP TABLE IF EXISTS `wp_sn_code`;
CREATE TABLE `wp_sn_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sn` varchar(255) DEFAULT NULL COMMENT 'SN码',
  `uid` int(10) DEFAULT NULL COMMENT '粉丝UID',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `is_use` tinyint(2) DEFAULT '0' COMMENT '是否已使用',
  `use_time` int(10) DEFAULT NULL COMMENT '使用时间',
  `target_id` int(10) unsigned DEFAULT NULL COMMENT '来源ID',
  `prize_id` int(10) unsigned DEFAULT NULL COMMENT '奖项ID',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否可用',
  `prize_title` varchar(255) DEFAULT NULL COMMENT '奖项',
  `can_use` tinyint(2) DEFAULT '1' COMMENT '是否可用',
  `server_addr` varchar(50) DEFAULT NULL COMMENT '服务器IP',
  `admin_uid` int(10) DEFAULT NULL COMMENT '核销管理员ID',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  `openid` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`uid`,`target_id`) USING BTREE,
  KEY `addon` (`target_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=249 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_sn_code
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_sport_award`
-- ----------------------------
DROP TABLE IF EXISTS `wp_sport_award`;
CREATE TABLE `wp_sport_award` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(255) NOT NULL COMMENT '奖项名称',
  `img` int(10) NOT NULL COMMENT '奖品图片',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '商品价格',
  `explain` text COMMENT '奖品说明',
  `award_type` varchar(30) DEFAULT '1' COMMENT '奖品类型',
  `count` int(10) DEFAULT '0' COMMENT '奖品数量',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `score` int(10) DEFAULT '0' COMMENT '积分数',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `coupon_id` char(50) DEFAULT NULL COMMENT '选择赠送券',
  `money` decimal(10,2) DEFAULT NULL COMMENT '返现金额',
  `aim_table` varchar(255) DEFAULT NULL COMMENT '活动标识',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_sport_award
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_staff_follow_link`
-- ----------------------------
DROP TABLE IF EXISTS `wp_staff_follow_link`;
CREATE TABLE `wp_staff_follow_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) DEFAULT '0' COMMENT '粉丝用户',
  `staff_id` int(10) DEFAULT '0' COMMENT '员工',
  `wpid` int(10) DEFAULT '0' COMMENT '公众号id',
  `cTime` int(10) DEFAULT NULL COMMENT '关注时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_staff_follow_link
-- ----------------------------
INSERT INTO `wp_staff_follow_link` VALUES ('4', '1758', '209', '73', '1533867608');

-- ----------------------------
-- Table structure for `wp_stores`
-- ----------------------------
DROP TABLE IF EXISTS `wp_stores`;
CREATE TABLE `wp_stores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(100) DEFAULT NULL COMMENT '店名',
  `address` varchar(255) DEFAULT NULL COMMENT '详细地址',
  `phone` varchar(30) DEFAULT NULL COMMENT '联系电话',
  `gps` varchar(50) DEFAULT NULL COMMENT 'GPS经纬度',
  `coupon_id` int(10) DEFAULT NULL COMMENT '所属优惠券编号',
  `wpid` int(10) DEFAULT NULL COMMENT 'token',
  `open_time` varchar(50) DEFAULT NULL COMMENT '营业时间',
  `img` int(10) unsigned DEFAULT NULL COMMENT '门店展示图',
  `auth_group` int(10) DEFAULT NULL COMMENT '门店用户组',
  `shop_code` varchar(255) DEFAULT NULL COMMENT '地点编码',
  `password` varchar(255) DEFAULT NULL COMMENT '确认收款密码',
  `img_url` varchar(255) DEFAULT NULL COMMENT 'erp门店图片链接',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_stores
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_stores_link`
-- ----------------------------
DROP TABLE IF EXISTS `wp_stores_link`;
CREATE TABLE `wp_stores_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `coupon_id` int(10) DEFAULT NULL COMMENT 'coupon_id',
  `wpid` int(10) DEFAULT NULL COMMENT 'shop_id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_stores_link
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_stores_user`
-- ----------------------------
DROP TABLE IF EXISTS `wp_stores_user`;
CREATE TABLE `wp_stores_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `wpid` int(10) DEFAULT NULL COMMENT 'wpid',
  `uid` int(10) DEFAULT NULL COMMENT 'uid',
  `store_id` int(10) DEFAULT NULL COMMENT 'store_id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_stores_user
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_system_notice`
-- ----------------------------
DROP TABLE IF EXISTS `wp_system_notice`;
CREATE TABLE `wp_system_notice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL COMMENT '公告标题',
  `content` text COMMENT '公告内容',
  `create_time` int(10) DEFAULT NULL COMMENT '发布时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_system_notice
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_template_messages`
-- ----------------------------
DROP TABLE IF EXISTS `wp_template_messages`;
CREATE TABLE `wp_template_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pbid` int(10) DEFAULT NULL COMMENT '公众号id',
  `cTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `title` varchar(255) DEFAULT NULL COMMENT '消息标题',
  `content` text COMMENT '消息内容',
  `sender` varchar(255) DEFAULT NULL COMMENT '发起人',
  `jamp_url` varchar(555) DEFAULT NULL COMMENT '跳转url',
  `send_type` char(10) DEFAULT '0' COMMENT '发送方式',
  `send_openids` varchar(255) DEFAULT NULL COMMENT '发送openid',
  `group_id` int(10) DEFAULT NULL COMMENT '发送分组id',
  `send_count` int(10) DEFAULT '0' COMMENT '发送人数',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_template_messages
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_transfers_recode`
-- ----------------------------
DROP TABLE IF EXISTS `wp_transfers_recode`;
CREATE TABLE `wp_transfers_recode` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  ` mch_appid` char(32) NOT NULL,
  `openid` char(32) NOT NULL,
  `amount` int(11) NOT NULL,
  `partner_trade_no` varchar(100) DEFAULT NULL,
  `cTime` int(10) NOT NULL,
  `status` tinyint(2) DEFAULT '1' COMMENT '0 表示已下发 1 表示待下发 2 延时下发 3 下发失败 4 自定义失败原因',
  `wait_time` int(10) DEFAULT NULL,
  `more_param` text,
  `log_md5` char(32) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `act_id` int(11) DEFAULT NULL,
  `act_mod` char(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_transfers_recode
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_update_score_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_update_score_log`;
CREATE TABLE `wp_update_score_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `score` int(10) DEFAULT NULL COMMENT '修改积分',
  `branch_id` int(10) DEFAULT NULL COMMENT '修改门店',
  `operator` varchar(255) DEFAULT NULL COMMENT '操作员',
  `cTime` int(10) DEFAULT NULL COMMENT '修改时间',
  `member_id` int(10) DEFAULT NULL COMMENT '会员卡id',
  `manager_id` int(10) DEFAULT NULL COMMENT '管理员id',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_update_score_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_update_version`
-- ----------------------------
DROP TABLE IF EXISTS `wp_update_version`;
CREATE TABLE `wp_update_version` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `version` int(10) unsigned NOT NULL COMMENT '版本号',
  `title` varchar(50) NOT NULL COMMENT '升级包名',
  `description` text COMMENT '描述',
  `create_date` int(10) DEFAULT NULL COMMENT '创建时间',
  `download_count` int(10) unsigned DEFAULT '0' COMMENT '下载统计',
  `package` varchar(255) NOT NULL COMMENT '升级包地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_update_version
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_user`
-- ----------------------------
DROP TABLE IF EXISTS `wp_user`;
CREATE TABLE `wp_user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` text COMMENT '用户名',
  `password` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '登录密码',
  `truename` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '真实姓名',
  `mobile` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '联系电话',
  `email` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '邮箱地址',
  `sex` tinyint(2) DEFAULT NULL COMMENT '性别',
  `headimgurl` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '头像地址',
  `city` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '城市',
  `province` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '省份',
  `country` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '国家',
  `language` varchar(20) CHARACTER SET utf8 DEFAULT 'zh-cn' COMMENT '语言',
  `score` float DEFAULT '0' COMMENT '积分值',
  `unionid` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '微信第三方ID',
  `login_count` int(10) DEFAULT '0' COMMENT '登录次数',
  `reg_ip` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '注册IP',
  `reg_time` int(10) DEFAULT NULL COMMENT '注册时间',
  `last_login_ip` varchar(30) CHARACTER SET utf8 DEFAULT NULL COMMENT '最近登录IP',
  `last_login_time` int(10) DEFAULT NULL COMMENT '最近登录时间',
  `status` tinyint(2) DEFAULT '1' COMMENT '状态',
  `is_init` tinyint(2) DEFAULT '0' COMMENT '初始化状态',
  `is_audit` tinyint(2) DEFAULT '0' COMMENT '审核状态',
  `subscribe_time` int(10) DEFAULT NULL COMMENT '用户关注公众号时间',
  `remark` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '微信用户备注',
  `groupid` int(10) DEFAULT NULL COMMENT '微信端的分组ID',
  `come_from` tinyint(1) DEFAULT '0' COMMENT '来源',
  `login_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT 'login_name',
  `login_password` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '登录密码',
  `manager_id` int(10) DEFAULT '0' COMMENT '公众号管理员ID',
  `level` tinyint(2) DEFAULT '0' COMMENT '-1:机器人0:粉丝1:超级管理员2:A级管理员\r\n3:B级管理员\r\n4:C级管理员',
  `membership` char(50) CHARACTER SET utf8 DEFAULT '0' COMMENT '会员等级',
  `bind_openid` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '绑定的openid',
  `audit_time` int(10) DEFAULT NULL COMMENT '审核通过时间',
  `grade` int(10) DEFAULT '0' COMMENT '当前用户的等级',
  `wpid` int(11) DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=11697 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wp_user
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_user_follow`
-- ----------------------------
DROP TABLE IF EXISTS `wp_user_follow`;
CREATE TABLE `wp_user_follow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `publicid` int(11) DEFAULT NULL,
  `follow_id` int(11) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_user_follow
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_user_tag`
-- ----------------------------
DROP TABLE IF EXISTS `wp_user_tag`;
CREATE TABLE `wp_user_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(50) NOT NULL COMMENT '标签名称',
  `mTime` int(10) DEFAULT NULL COMMENT '更新时间',
  `rule` varchar(255) DEFAULT NULL COMMENT '所拥有的权限ID，多个用英文逗号隔开',
  `type` tinyint(1) DEFAULT '0' COMMENT '标签（角色）类型，0 粉丝角色 1 运营人员角色',
  `wid` int(10) DEFAULT NULL COMMENT '微信标签id',
  `wtype` char(1) DEFAULT '' COMMENT '公众号类型0.1.2.3',
  `pbid` int(10) NOT NULL DEFAULT '0' COMMENT '公众号id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_user_tag
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_user_tag_link`
-- ----------------------------
DROP TABLE IF EXISTS `wp_user_tag_link`;
CREATE TABLE `wp_user_tag_link` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) NOT NULL COMMENT 'uid',
  `tag_id` int(10) NOT NULL COMMENT 'tag_id',
  `cTime` int(10) NOT NULL COMMENT '记录时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_user_tag_link
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_visit_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_visit_log`;
CREATE TABLE `wp_visit_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `wpid` int(10) DEFAULT '0' COMMENT 'publicid',
  `module_name` varchar(30) DEFAULT NULL COMMENT 'module_name',
  `controller_name` varchar(30) DEFAULT NULL COMMENT 'controller_name',
  `action_name` varchar(30) DEFAULT NULL COMMENT 'action_name',
  `uid` varchar(255) DEFAULT '0' COMMENT 'uid',
  `ip` varchar(30) DEFAULT NULL COMMENT 'ip',
  `brower` varchar(30) DEFAULT NULL COMMENT 'brower',
  `param` text COMMENT 'param',
  `referer` varchar(255) DEFAULT NULL COMMENT 'referer',
  `cTime` int(10) DEFAULT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60058 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_visit_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_weisite_category`
-- ----------------------------
DROP TABLE IF EXISTS `wp_weisite_category`;
CREATE TABLE `wp_weisite_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(100) NOT NULL COMMENT '分类标题',
  `icon` int(10) unsigned DEFAULT NULL COMMENT '分类图片',
  `url` varchar(255) DEFAULT NULL COMMENT '外链',
  `is_show` tinyint(2) DEFAULT '1' COMMENT '显示',
  `sort` int(10) DEFAULT '0' COMMENT '排序号',
  `pid` int(10) DEFAULT '0' COMMENT '一级目录',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_weisite_category
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_weisite_cms`
-- ----------------------------
DROP TABLE IF EXISTS `wp_weisite_cms`;
CREATE TABLE `wp_weisite_cms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `keyword_type` tinyint(2) DEFAULT NULL COMMENT '关键词类型',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `intro` text COMMENT '简介',
  `cate_id` int(10) unsigned DEFAULT '0' COMMENT '所属类别',
  `cover` int(10) unsigned DEFAULT NULL COMMENT '封面图片',
  `content` text COMMENT '内容',
  `cTime` int(10) DEFAULT NULL COMMENT '发布时间',
  `sort` int(10) unsigned DEFAULT '0' COMMENT '排序号',
  `view_count` int(10) unsigned DEFAULT '0' COMMENT '浏览数',
  `show_type` varchar(100) DEFAULT '0' COMMENT '显示方式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_weisite_cms
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_weisite_footer`
-- ----------------------------
DROP TABLE IF EXISTS `wp_weisite_footer`;
CREATE TABLE `wp_weisite_footer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `url` varchar(255) DEFAULT NULL COMMENT '关联URL',
  `title` varchar(50) NOT NULL COMMENT '菜单名',
  `pid` tinyint(2) DEFAULT '0' COMMENT '一级菜单',
  `sort` tinyint(4) DEFAULT '0' COMMENT '排序号',
  `icon` int(10) unsigned DEFAULT NULL COMMENT '图标',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`),
  KEY `token` (`pid`,`sort`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_weisite_footer
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_weisite_slideshow`
-- ----------------------------
DROP TABLE IF EXISTS `wp_weisite_slideshow`;
CREATE TABLE `wp_weisite_slideshow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `img` int(10) unsigned NOT NULL COMMENT '图片',
  `url` varchar(255) DEFAULT NULL COMMENT '链接地址',
  `is_show` tinyint(2) DEFAULT '1' COMMENT '是否显示',
  `sort` int(10) DEFAULT '0' COMMENT '排序',
  `wpid` int(10) NOT NULL DEFAULT '0' COMMENT 'wpid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_weisite_slideshow
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_weixin_log`
-- ----------------------------
DROP TABLE IF EXISTS `wp_weixin_log`;
CREATE TABLE `wp_weixin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cTime` int(11) DEFAULT NULL,
  `cTime_format` varchar(30) DEFAULT NULL,
  `data` text,
  `data_post` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wp_weixin_log
-- ----------------------------

-- ----------------------------
-- Table structure for `wp_weixin_message`
-- ----------------------------
DROP TABLE IF EXISTS `wp_weixin_message`;
CREATE TABLE `wp_weixin_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `ToUserName` varchar(100) DEFAULT NULL COMMENT 'Token',
  `FromUserName` varchar(100) DEFAULT NULL COMMENT 'OpenID',
  `CreateTime` int(10) DEFAULT NULL COMMENT '创建时间',
  `MsgType` varchar(30) DEFAULT NULL COMMENT '消息类型',
  `MsgId` varchar(100) DEFAULT NULL COMMENT '消息ID',
  `Content` text COMMENT '文本消息内容',
  `PicUrl` varchar(255) DEFAULT NULL COMMENT '图片链接',
  `MediaId` varchar(100) DEFAULT NULL COMMENT '多媒体文件ID',
  `Format` varchar(30) DEFAULT NULL COMMENT '语音格式',
  `ThumbMediaId` varchar(30) DEFAULT NULL COMMENT '缩略图的媒体id',
  `Title` varchar(100) DEFAULT NULL COMMENT '消息标题',
  `Description` text COMMENT '消息描述',
  `Url` varchar(255) DEFAULT NULL COMMENT 'Url',
  `collect` tinyint(1) DEFAULT '0' COMMENT '收藏状态',
  `deal` tinyint(1) DEFAULT '0' COMMENT '处理状态',
  `is_read` tinyint(1) DEFAULT '0' COMMENT '是否已读',
  `type` tinyint(1) DEFAULT '0' COMMENT '消息分类',
  `is_material` int(10) DEFAULT '0' COMMENT '设置为文本素材',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1153 DEFAULT CHARSET=utf8;