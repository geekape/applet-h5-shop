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
class QrCode extends Home {
	function initialize() {
		parent::initialize();
		
		$act = strtolower ( ACTION_NAME );
		$nav = [];
		$res ['title'] = '二维码维护';
		$res ['url'] = U ( 'lists' );
		$res ['class'] = $act == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	public function lists() {
		$this->assign ( 'add_button', false );
		$this->assign( 'search_url', U ( 'lists' ));
		
		$model = $this->getModel ( 'qr_code' );
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		$fields = $list_data['fields'];
// 		foreach ( $list_data ['list_grids'] as &$vo ) {
// 			if (isset ( $vo ['href'] )) {
// 				$vo ['href'] = '[DELETE]|删除';
// 			}
// 		}		
		
		// 搜索条件
		$map = $this->_search_map($model, $list_data['db_fields']);
		$map['pbid'] = get_pbid();

		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];

		empty ( $fields ) || in_array ( 'id', $fields ) || array_push ( $fields, 'id' );
		$name = parse_name ( $model ['name'], true );
		
		$page_data = M( $name )->field ( empty ( $fields ) ? true : $fields )->where ( wp_where( $map ) )->order ( 'id DESC' )->paginate($row);
		$list_data = dealPage($page_data);	
		
		$addons = M( 'apps' )->where("type=1")->field('name,title')->select();
		foreach($addons as $a){
			$addonsArr[$a['name']] = $a['title'];
		}
		
		foreach($list_data ['list_data'] as &$vo){
			$vo['addon'] = $addonsArr[$vo['addon']];
		}
		
		$this->assign ( $list_data );
		// dump($list_data);
		
		return $this->fetch( 'common@base/lists' );
	}
	public function del(){
		$model = $this->getModel ( 'qr_code' );
		return parent::common_del ( $model);
	}
}