<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
		// 数据库配置
        'DB_TYPE'   => '[DB_TYPE]', // 数据库类型
        'DB_HOST'   => '[DB_HOST]', // 服务器地址
        'DB_NAME'   => '[DB_NAME]', // 数据库名
        'DB_USER'   => '[DB_USER]', // 用户名
        'DB_PWD'    => '[DB_PWD]',  // 密码
        'DB_PORT'   => '[DB_PORT]', // 端口
        'DB_PREFIX' => '[DB_PREFIX]', // 数据库表前缀
		'DB_CHARSET' => 'utf8mb4',
		'DB_PARAMS' => array (
				\PDO::ATTR_CASE => \PDO::CASE_NATURAL 
		),
		// SESSION 和 COOKIE 配置
		'SESSION_PREFIX' => SITE_DIR_NAME . '_home', // session前缀
		'COOKIE_PREFIX' => SITE_DIR_NAME . '_home',
		
		// 模板相关配置
		'TAGLIB_PRE_LOAD' => 'Weiphp',
		'TMPL_PARSE_STRING' => array (
				'__STATIC__' => __ROOT__ . '/Public/static',
				'__ADDONS__' => __ROOT__ . '/Public/Home/Addons',
				'__IMG__' => __ROOT__ . '/Public/Home/images',
				'__CSS__' => __ROOT__ . '/Public/Home/css',
				'__JS__' => __ROOT__ . '/Public/Home/js' 
		),
		// 系统数据加密设置
		'DATA_AUTH_KEY' => '[AUTH_KEY]', // 默认数据加密KEY
		                                                               
		// 调试配置
		'SHOW_PAGE_TRACE' => false,
		'LOG_RECORD' => true, // 开启日志记录
		
		// 用户相关设置数
		'USER_ADMINISTRATOR' => 1, // 管理员用户ID
		                           
		// URL配置
		'URL_CASE_INSENSITIVE' => false, // 默认false 表示URL区分大小写 true则表示不区分大小写
		'URL_MODEL' => 3, // URL模式
		'DIV_DOMAIN' => false, // 泛域名支持,注：在localhost 或者IP地址下访问下无效
		                      
		// 全局过滤配置
		'DEFAULT_FILTER' => 'safe', // 全局过滤函数
		                            
		// 数据缓存设置
		'DATA_CACHE_PREFIX' => SITE_DIR_NAME . '_', // 缓存前缀
		'DATA_CACHE_TYPE' => 'File', // 数据缓存类型
		'MEMCACHE_HOST' => '127.0.0.1',
		'MEMCACHE_PORT' => 11211,
		'DATA_CACHE_TIMEOUT' => 86400,
		
		'PICTURE_UPLOAD_DRIVER' => 'Local',
		
		// 本地上传文件驱动配置
		'UPLOAD_LOCAL_CONFIG' => array (),
		
		// 七牛上传文件驱动配置
		'UPLOAD_QINIU_CONFIG' => array (
				'accessKey' => '',
				'secrectKey' => '',
				'bucket' => '',
				'domain' => '',
				'timeout' => 3600 
		),
		
		// 百度云上传文件驱动配置
		'UPLOAD_BCS_CONFIG' => array (
				'AccessKey' => '',
				'SecretKey' => '',
				'bucket' => '',
				'rename' => false 
		),
		
		// 图片上传相关配置
		'PICTURE_UPLOAD' => array (
				'maxSize' => 2097152, // 2M 上传的文件大小限制 (0-不做限制)
				'exts' => 'jpg,gif,png,jpeg,bmp', // 允许上传的文件后缀
				'rootPath' => './Uploads/Picture/' 
		),
		
		// 编辑器图片上传相关配置
		'EDITOR_UPLOAD' => array (
				'maxSize' => 2097152, // 2M 上传的文件大小限制 (0-不做限制)
				'exts' => 'jpg,gif,png,jpeg,bmp', // 允许上传的文件后缀
				'rootPath' => './Uploads/Editor/' 
		),
		//编辑器上传服务器
		// Local--本地  Qiniu --七牛
		'EDITOR_PICTURE_UPLOAD_DRIVER' => 'Local',		
		
		// 文件上传相关配置
		'DOWNLOAD_UPLOAD' => array (
				'maxSize' => 10485760, // 10M 上传的文件大小限制 (0-不做限制)
				'exts' => 'jpg,gif,png,jpeg,zip,rar,tar,gz,7z,doc,docx,txt,xml,xls,xlsx,csv,pem,amr,mp3,mp4,bmp,wma,wav', // 允许上传的文件后缀
				'rootPath' => './Uploads/Download/' 
		) 
);
