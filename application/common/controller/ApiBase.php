<?php

// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\common\controller;

use app\common\controller\Base;

/**
 * 手机H5版的控制器基类，实现手机端的初始化，权限控制和一些通用方法
 *
 * @author 凡星
 *        
 */
class ApiBase extends Base {
	var $expires_in = 86400;
	public function __construct() {
		parent::__construct ();
	}
	public function initialize() {
		parent::initialize ();
		
		$this->initFollow ();
	}
	
	// 初始化用户信息
	private function initFollow() {
		$uid = intval ( session ( 'mid_' . get_pbid () ) );
		
		// 当前登录者
		$GLOBALS ['mid'] = $this->mid = $uid;
		$myinfo = get_userinfo ( $this->mid );
		$GLOBALS ['myinfo'] = $myinfo;
		
		// 当前访问对象的uid
		$_REQUEST = input ( 'param.' );
		$GLOBALS ['uid'] = $this->uid = isset ( $_REQUEST ['uid'] ) && intval ( $_REQUEST ['uid'] == 0 ? $this->mid : $_REQUEST ['uid'] );
	}
	
	// 获取ACCESS_TOKEN
	function access_token() {
		$map ['appid'] = I ( 'appid' );
		$map ['secret'] = I ( 'secret' );
		if (empty ( $map ['appid'] ) || empty ( $map ['secret'] )) {
			$this->error ( '115000:appid或secret参数不能为空' );
		}
		
		$cache_key = 'api_access_token_' . $map ['appid'];
		$access_token = S ( $cache_key );
		if ($access_token === false) {
			// 先从数据库中找找2小时内有效的access_token
			$map ['cTime'] = [ 
					'gt',
					NOW_TIME - $this->expires_in 
			];
			
			$info = M ( 'api_access_token' )->where ( $map )->find ();
			if (! empty ( $info )) {
				$access_token = $info ['access_token'];
				$time = $this->expires_in - (NOW_TIME - $info ['cTime']);
				S ( $cache_key, $access_token, $time );
				S ( 'check_' . $access_token, 1, $time );
			}
			unset ( $map ['cTime'] );
		}
		// 重新分配access_token
		if (empty ( $access_token )) {
			// 先判断appid和secret是否正确
			$id = M ( 'publics' )->where ( $map )->value ( 'id' );
			if (! $id) {
				$this->error ( '115001:appid或secret参数不对' );
			}
			
			// 每天限制2000次
			$map ['cTime'] = [ 
					'gt',
					strtotime ( date ( 'Y-m-d' ) ) 
			];
			$count = M ( 'api_access_token' )->where ( $map )->count ();
			if ($count > 2000) {
				$this->error ( '115005:access_token每天最多只取发放2000个' );
			}
			
			$rand = rand ( 10, 99 );
			$access_token = md5 ( $id . NOW_TIME . $rand . $map ['appid'] );
			S ( $cache_key, $access_token, $this->expires_in );
			S ( 'check_' . $access_token, 1, $this->expires_in );
			
			$map ['access_token'] = $access_token;
			$map ['cTime'] = NOW_TIME;
			$res = M ( 'api_access_token' )->insert ( $map );
			if (! $res) {
				$this->error ( '115002:保存access_token失败' );
			}
		}
		
		$this->del_old_data ();
		
		$this->ajaxReturn ( [ 
				'access_token' => $access_token,
				'expires_in' => $this->expires_in 
		] );
	}
	function check_access_token() {
		$access_token = I ( 'access_token' );
		if (empty ( $access_token )) {
			return $this->api_error ( '115003:缺少access_token参数' );
		}
		
		// 优先从缓存中查检
		$check = S ( 'check_' . $access_token );
		if ($check === false) {
			// 从数据库中检查
			$map ['access_token'] = $access_token;
			$map ['cTime'] = [ 
					'gt',
					NOW_TIME - $this->expires_in 
			];
			
			$check = M ( 'api_access_token' )->where ( $map )->find ();
		}
		
		if (! $check) {
			return $this->api_error ( '115004:access_token不正确或已过期' );
		}
		
		return true;
	}
	function del_old_data() {
		// 每天只清一次
		$key = 'del_old_data_lock_' . date ( 'Y_m_d' );
		$lock = S ( $key );
		
		if ($lock !== false)
			return false;
		
		S ( $key, 1, 86400 );
		
		// 清除48小时之前的数据
		$map ['cTime'] = [ 
				'lt',
				NOW_TIME - 172800 
		];
		M ( 'api_access_token' )->where ( $map )->delete ();
	}
	function api_error($msg, $data = []) {
		$this->result ( $data, 0, $msg, 'json' );
	}
	function api_success($data = []) {
		$debug = input ( 'debug' );
		if ($debug) {
			dump ( $data );
			exit ();
		}
		$this->result ( $data, 1, '', 'json' );
	}
	public function _empty($name) {
		$mid = session ( 'mid_' . get_pbid () );
		
		$this->apiModel->setMid ( $mid );
		$data = $this->apiModel->$name ();
		
		$debug = input ( 'debug' );
		if ($debug == 1) {
			dump ( IS_AJAX );
			dump ( $data );
		}
		return is_numeric ( $data ) || is_string ( $data ) ? $data : json ( $data );
	}
}