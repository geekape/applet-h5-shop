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
 * 扩展后台管理页面
 *
 * @author yangweijie <yangweijiester@gmail.com>
 */
class AppCategory extends Admin {
	protected $model;
	function initialize() {
		parent::initialize();
		
		$this->model = $this->getModel ( 'app_category' );
	}
	// 通用应用的列表模型
	public function lists() {
		return parent::common_lists ( $this->model, 'Think/lists', 'sort asc,id desc' );
	}
	
	// 通用应用的编辑模型
	public function edit() {
		return parent::common_edit ( $this->model, 0,  'Think/edit' );
	}
	
	// 通用应用的增加模型
	public function add() {
		return parent::common_add ( $this->model,  'Think/add' );
	}
	
	// 通用应用的删除模型
	public function del() {
		return parent::common_del ( $this->model );
	}
	
	// 应用分类编辑
	function category() {
		$map ['id'] = I ( 'id' );
		//dump($map);exit;
		if (request()->isPost()) {
		    $cate_id =I ( 'cate_id',0,"intval" );
		    
			$res =D('home/Addons' )->where ( wp_where( $map ) )->setField ( 'cate_id', $cate_id );
			
			//D('home/Addons' )->clearCache(0);
			
			$this->success ( '设置成功', U ( 'Admin/apps/index' ) );
			//exit ();
		}
		$data = M( 'apps' )->where ( wp_where( $map ) )->find ();
		$this->assign ( 'data', $data );
		// dump ( $data );
		
		$categorys = M( 'app_category' )->order ( 'sort asc, id desc' )->select ();
		$this->assign ( 'categorys', $categorys );
		
		return $this->fetch();
	}
}
