<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\admin\controller;

/**
 * 后台首页控制器
 *
 */
class Index extends Admin {
	
	/**
	 * 后台首页
	 *
	 */
	public function index() {
		return redirect( U ( 'Admin/Config/group' ) );
		
		$this->meta_title = '管理首页';
		return $this->fetch();
	}
}
