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
class CreditFollow extends Home {
	function initialize() {
		parent::initialize();
		
		$act = strtolower ( CONTROLLER_NAME );
		$nav = [];
		$res ['title'] = '积分配置';
		$res ['url'] = U ( 'CreditConfig/lists' );
		$res ['class'] = $act == 'creditconfig' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '积分流水帐';
		$res ['url'] = U ( 'CreditData/lists' );
		$res ['class'] = $act == 'creditdata' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '粉丝积分';
		$res ['url'] = U ( 'CreditFollow/lists' );
		$res ['class'] = $act == 'creditfollow' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
		
		$_GET['sidenav'] = 'home_creditconfig_lists';
	}
	public function lists() {
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		$this->assign ( 'search_button', false );
		
		$model = $this->getModel ( 'user' );
		
		$map ['wpid'] = get_wpid ();
		if (! empty ( $_REQUEST ['nickname'] )) {
			$map['uid'] = array('in', D('common/User' )->searchUser ( $_REQUEST ['nickname'] ));
		}
		
		$page_data = M( 'user' )->where ( wp_where( $map ) )->order ( 'uid DESC' )->paginate ();
		$list = dealPage($page_data);			
		
		$grid ['field'] [0] = 'uid';
		$grid ['title'] = '粉丝编号';
		$list ["list_grids"] [] = $grid;
		
		//$grid ['field'] [0] = 'openid';
		//$grid ['title'] = 'Openid';
		//$list_data ["list_grids"] [] = $grid;
		
		$grid ['field'] [0] = 'nickname';
		$grid ['title'] = '昵称';
		$list ["list_grids"] [] = $grid;
		
		$grid ['field'] [0] = 'score';
		$grid ['title'] = '金币值';
		$list ["list_grids"] [] = $grid;
			
		$grid ['field'] [0] = 'uid';
		$grid ['title'] = '详情';
		
		$varController = config( 'VAR_CONTROLLER' );
		
		$grid ['href'] = 'CreditData/lists?uid=[uid]&target=_blank|详情';
		$list ["list_grids"] [] = $grid;
		
		$this->assign ( $list );
		
		return $this->fetch( 'common@base/lists' );
	}
	public function add() {
		$model = $this->getModel ( 'credit_data' );
		if (request()->isPost()) {
			$Model = D ($model ['name']);
			// 获取模型的字段信息
			$data = input('post.');
			$data = $this->checkData($data, $model);
			if (false!==($id = $Model->save($data))) {
				$this->_saveKeyword ( $model, $id );
				
				// 清空缓存
				method_exists ( $Model, 'clearCache') && $Model->clearCache( $id, 'edit' );
				
				$this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'] ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$fields = get_model_attribute ( $model );
			
			$this->assign ( 'fields', $fields );
			$this->meta_title = '新增' . $model ['title'];
			
			return $this->fetch( 'common@base/add' );
		}
	}
	public function edit($id = 0) {
		$model = $this->getModel ( 'credit_data' );
		$id || $id = I ( 'id' );
		
		// 获取数据
		$data = M( $model ['name'] )->where('id', $id)->find();
		$data || $this->error ( '数据不存在！' );
		
		$wpid = get_wpid ();
		if (isset ( $data ['wpid'] ) && $wpid != $data ['wpid']) {
			$this->error ( '非法访问！' );
		}		
		
		if (request()->isPost()) {
			$post = input('post.');
			$act = 'save';
			if ($data ['wpid'] == 0) {
				$post['wpid'] = get_wpid ();
				unset ( $post['id'] );
				$act = 'add';
			}
			$Model = D ($model ['name']);			
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
	function del() {
		$model = $this->getModel ( 'credit_data' );
		return parent::common_del ( $model );
	}
	function credit_data() {
		$model = $this->getModel ( 'credit_data' );
		
		$map ['wpid'] = get_wpid ();
		session ( 'common_condition', $map );
		
		return parent::common_lists ( $model, 'common@base/lists' );
	}
}