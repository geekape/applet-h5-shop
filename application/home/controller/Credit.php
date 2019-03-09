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
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class Credit extends Home {
	function initialize() {
		parent::initialize();
		
		$act = strtolower ( ACTION_NAME );
		$nav = [];
		$res ['title'] = '积分配置';
		$res ['url'] = U ( 'lists' );
		$res ['class'] = $act == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	public function lists() {
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'search_button', false );
		$this->assign ( 'check_all', false );
		
		$model = $this->getModel ( 'credit_config' );
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		foreach ( $list_data ['list_grids'] as &$vo ) {
			if (isset ( $vo ['href'] )) {
				$vo ['href'] = str_replace ( ',[DELETE]|删除', '', $vo ['href'] ); // 去掉删除功能
			}
		}
		// dump ( $list_data );
		
		$list_data ['list_data'] = D('common/Credit' )->getCreditByName ();
		
		$this->assign ( $list_data );
		// dump($list_data);
		
		return $this->fetch( 'common@base/lists' );
	}
	public function edit($id = 0) {
		$model = $this->getModel ( 'credit_config' );
		$id || $id = I ( 'id' );
		
		// 获取数据
		$data = M( $model ['name'] )->where('id', $id)->find();
		$data || $this->error ( '数据不存在！' );
		
		if (request()->isPost()) {
			$post = input('post.');
			$act = 'save';
			if ($data ['wpid'] == 0) {
				$post['wpid'] = get_wpid ();
				unset ( $post['id'] );
				$act = 'add';
			}
			$Model = D ($model ['name']);
			// 获取模型的字段信息
			
			$post = $this->checkData ( $post, $model );
			if ($Model->$act ($post)!==false) {
				// dump($Model->getLastSql());
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				// dump($Model->getLastSql());
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model );
			
			$this->assign ( 'fields', $fields );
			$this->assign ( 'data', $data );
			$this->meta_title = '编辑' . $model ['title'];
			
			return $this->fetch( 'common@base/edit' );
		}
	}
	function credit_data() {
		$model = $this->getModel ( 'credit_data' );
		
		$map ['wpid'] = get_wpid ();
		session ( 'common_condition', $map );
		
		return parent::common_lists ( $model, 'common@base/lists' );
	}
}