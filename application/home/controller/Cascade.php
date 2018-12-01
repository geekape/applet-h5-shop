<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\home\controller;

/**
 * 通用的级联数据管理
 */
class Cascade extends Home {
	var $model;
	function initialize() {
		parent::initialize();
		
		$act = strtolower ( ACTION_NAME );
		$nav = [];
		$res ['title'] = '级联分组';
		$res ['url'] = U ( 'lists' );
		$res ['class'] = $act == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
		$this->model = $this->getModel ( 'common_category_group' );
	}
	public function lists() {
		$this->assign ( 'search_url', U ( 'lists' ) );
		$this->assign ( 'check_all', false );
		
		$map['wpid'] = get_wpid();
		session ( 'common_condition', $map );
		
		return parent::common_lists ( $this->model, 'common@base/lists' );
	}
	public function del() {
		return parent::common_del ( $this->model );
	}
	public function edit() {
		$id = I ( 'id' );
		return parent::common_edit ( $this->model, $id, 'common@base/edit' );
	}
	public function add() {
		return parent::common_add ( $this->model, 'common@base/add' );
	}
	function cascade() {
		$module = I ( 'module' );
		session ( 'common_category_module', $module );
		return redirect( U ( 'home/Category/lists', array (
				'module' => $module 
		) ) );
	}
}