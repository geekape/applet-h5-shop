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
class Keyword extends Home {
	function initialize() {
		parent::initialize();
		
		$act = strtolower ( ACTION_NAME );
		$nav = [];
		$res ['title'] = '关键词维护';
		$res ['url'] = U ( 'lists' );
		$res ['class'] = $act == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
		
	}
	public function lists() {
		if (! is_administrator ( $this->mid )) {
			return redirect( U ( 'weixin/UserCenter/lists' ) );
		}
		// $this->assign ( 'add_button', false );
		$this->assign ( 'search_url', U ( 'lists' ) );
		
		$model = $this->getModel ( 'keyword' );
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		$fields = $list_data ['fields'];
		
// 		foreach ( $list_data ['list_grids'] as &$vo ) {
// 			if (isset ( $vo ['href'] )) {
// 				$vo ['href'] = '[DELETE]|删除';
// 			}
// 		}
		
		// 搜索条件
		$map = $this->_search_map($model, $list_data['db_fields']);
		$map ['wpid'] = get_wpid ();
		
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		
		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
		$name = parse_name ( $model ['name'], true );
		$data = M( $name )->field ( empty ( $fields ) ? true : $fields )->where ( wp_where( $map ) )->order ( 'id DESC' )->paginate($row);
		$list_data = $this->parsePageData($data, $model, $list_data, false);
		
		$addons = M( 'apps' )->where("type=1" )->field ( 'name,title' )->select ();
		foreach ( $addons as $a ) {
			$addonsArr [$a ['name']] = $a ['title'];
		}
		
		foreach ( $list_data ['list_data'] as &$vo ) {
			$vo ['addon'] = isset($addonsArr [$vo ['addon']]) ? $addonsArr [$vo ['addon']]:$vo ['addon'];
		}
		
		$this->assign ( $list_data );
		// dump($list_data);
		
		return $this->fetch( 'common@base/lists' );
	}
	public function del() {
		$model = $this->getModel ( 'keyword' );
		return parent::common_del ( $model );
	}
	public function edit() {
		$model = $this->getModel ( 'keyword' );
		return parent::common_edit ( $model, 0, 'common@base/edit' );
	}
	public function add() {
		$model = $this->getModel ( 'keyword' );
		
		return parent::common_add ( $model, 'common@base/add' );
	}
}