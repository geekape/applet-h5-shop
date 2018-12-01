<?php

namespace app\shop\controller;

use app\shop\controller\Base;

class Attribute extends Base{
	var $model;
	var $cate_id;
	var $field_type;
	function initialize() {
		parent::initialize();
		
		$this->model = $this->getModel ( 'shop_attribute' );
		$param['mdm']=input('mdm');
		$param ['cate_id'] = $this->cate_id = intval ( $_REQUEST ['cate_id'] );
		
		$type = I ( 'type/d', 0 );
		
		$param ['type'] = 0;
		$res ['title'] = '筛选属性';
		$res ['url'] = U ( 'Shop/Attribute/lists', $param );
		$res ['class'] = ACTION_NAME == 'lists' && $type == 0 ? 'current' : '';
		$nav [] = $res;
		
		$param ['type'] = 1;
		$res ['title'] = '普通属性';
		$res ['url'] = U ( 'Shop/Attribute/lists', $param );
		$res ['class'] = ACTION_NAME == 'lists' && $type == 1 ? 'current' : '';
		$nav [] = $res;
		
		if (ACTION_NAME == 'edit') {
			$res ['title'] = $type ? '编辑普通属性' : '编辑筛选属性';
			$res ['url'] = '#';
			$res ['class'] = 'current';
			$nav [] = $res;
		} else {
			$res ['title'] = $type ? '增加普通属性' : '增加筛选属性';
			$param['type']=$type;
			$res ['url'] = U ( 'Shop/Attribute/add', $param );
			$res ['class'] = ACTION_NAME == 'add' ? 'current' : '';
			$nav [] = $res;
		}
		
		$this->assign ( 'nav', $nav );
		
		$this->field_type = array (
				'string' => 'varchar',
				'textarea' => 'varchar',
				'radio' => 'varchar',
				'checkbox' => 'varchar',
				'select' => 'varchar',
				'picture' => 'int',
				'datetime' => 'int' 
		);
	}
	// 通用插件的列表模型
	public function lists() {
		$model = $this->model;
		$order = 'cate_id asc,sort asc, id asc';
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		$fields = $list_data ['fields'];
		
		// 搜索条件
		$map = $this->_search_map($model, $list_data['db_fields']);
		
		$ids = [];
		D ( 'Category' )->get_parent_ids ( $this->cate_id, $ids );
		
		$this->assign ( 'top_more_button', array (
				array (
						'title' => '返回分类列表',
						'url' => U ( 'Shop/Category/lists' ) 
				) 
		) );
		$param ['cate_id'] = $this->cate_id;
		$param ['model'] = $this->model ['id'];
		$add_url = U ( 'add', $param );
		$this->assign ( 'add_url', $add_url );
		
		$map ['cate_id'] = array (
				'in',
				$ids 
		);
		$map ['type'] = I ( 'type/d', 0 );
		
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		
		// 读取模型数据列表
		
		$name = parse_name ( $model['name'], true );
		$data = M( $name )->field ( true )->where ( wp_where( $map ) )->order ( $order )->paginate($row);
		$list_data = $this->parsePageData($data, $model, $list_data);

		$this->assign ( 'cate_id', $this->cate_id );
		
		return $this->fetch ();
	}
	
	// 通用插件的编辑模型
	public function edit() {
		$id = I ( 'id' );
		// 获取数据
		$data = M( 'shop_attribute' )->where('id', $id)->find();
		$data || $this->error ( '数据不存在！' );
		
		$wpid = get_wpid ();
		if (isset ( $data ['wpid'] ) && $wpid != $data ['wpid']) {
			$this->error ( '非法访问！' );
		}
		
		if (IS_POST) {
			$post = input('post.');
			if (empty ( $data ['goods_field'] ) || $this->field_type [$data ['attr_type']] != $this->field_type [input('post.attr_type')]) {
				$post['name'] = $post['goods_field'] = $this->_goods_field ( input('post.cate_id'), input('post.attr_type') );
				
				// 数据转移到新字段中
				if (! empty ( $data ['goods_field'] ) && $data ['goods_field'] != input('post.goods_field')) {
					$sql = "UPDATE wp_shop_goods SET `{input('post.goods_field')}`=`{$data ['goods_field']}` WHERE id>0";
					M()->execute ( $sql );
				}
			}
			$post['extra'] = $this->_deal_extra ( input('post.extra') );
			
			$Model = D ( $this->model ['name'] );
			// 获取模型的字段信息
            
            $post = $this->checkData($post, $this->model);
            $res  = $Model->isUpdate(true)->save($post);
			if ($res!==false) {
				
				// 清空缓存
				method_exists ( $Model, 'clearCache') && $Model->clearCache( $id, 'edit' );
				
				$param ['cate_id'] = $this->cate_id;
				$param ['model'] = $this->model ['id'];
				$param ['type'] = input('post.type');
				
				$url = U ( 'lists', $param );
				$this->success ( '保存' . $this->model ['title'] . '成功！', $url );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$this->_deal_fields ();
			$this->assign ( 'data', $data );
			return $this->fetch ();
		}
	}
	
	// 通用插件的增加模型
	public function add() {
		if (IS_POST) {
			$data = input('post.');
			$data['goods_field'] = $this->_goods_field ( input('post.cate_id'), input('post.attr_type') );
			$data['extra'] = $this->_deal_extra ( input('post.extra') );
			$data['name'] =input('post.goods_field');
			$Model = D ( $this->model ['name'] );            
            $data = $this->checkData($data, $this->model);

            $id = $Model->insertGetId($data);
			if ($id) {
				
				// 清空缓存
				method_exists ( $Model, 'clearCache') && $Model->clearCache( $id, 'edit' );
				$param['mdm']=input('mdm');
				$param ['cate_id'] = $this->cate_id;
				$param ['model'] = $this->model ['id'];
				$param ['type'] = input('post.type');
				$url = U ( 'lists', $param );
				$this->success ( '添加' . $this->model ['title'] . '成功！', $url );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$this->_deal_fields ();
			return $this->fetch ();
		}
	}
	function _deal_fields() {
		$fields = get_model_attribute ( $this->model );
		$type = I ( 'type/d', 0 );
		if ($type == 0) { // 筛选属性只有单选和多选，下拉菜单三种类型
			$fields ['attr_type'] ['extra'] = "radio:单选|extra@show
checkbox:多选|extra@show
select:下拉选择|extra@show";
		}
		
		$this->assign ( 'fields', $fields );
	}
	// 去掉参数里的首尾空格
	function _deal_extra($text) {
		if (empty ( $text ))
			return '';
		
		$arr = wp_explode ( $text );
		return implode ( "\n", $arr );
	}
	// 自动分配shop_goods表里的扩展字段
	function _goods_field($cid, $attr_type) {
		// 获取分类包括父级的所有ID
		$cate_ids = [];
		D ( 'Category' )->get_parent_ids ( $cid, $cate_ids );
		
		// 获取已经占用的扩展字段
		$map ['wpid'] = get_wpid ();
		$map ['cate_id'] = array (
				'in',
				$cate_ids 
		);
		
		$goods_field = M( 'shop_attribute' )->where ( wp_where( $map ) )->column ( 'goods_field' );
		
		$type = isset ( $this->field_type [$attr_type] ) ? $this->field_type [$attr_type] : 'varchar';
		for($i = 0; $i < 20; $i ++) {
			$field = 'extra_' . $type . '_' . $i;
			if (! in_array ( $field, $goods_field )) {
				return $field;
			}
		}
		$this->error ( '字段已经用完' );
	}
	// 通用插件的删除模型
	public function del() {
		parent::common_del ( $this->model );
	}
}
