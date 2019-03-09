<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星
// +----------------------------------------------------------------------
namespace app\admin\controller;

/**
 * 模型数据管理控制器
 *
 * @author 凡星
 */
class Think extends Admin {
	
	/**
	 * 显示指定模型列表数据
	 *
	 * @param String $model
	 *        	模型标识
	 * @author 凡星
	 */
	public function lists() {
        $model = I('model');	
		is_array ( $model ) || $model = $this->getModel ( $model );
		
		$list_data = $this->_get_model_list ( $model );
		$this->assign ( $list_data );
		
		$this->meta_title = $model ['title'] . '列表';
		
		return $this->fetch();
	}
	public function edit() {
        $model = I('model');
        $id = I('id');
		$this->meta_title = '编辑' . $model ['title'];
		return parent::common_edit ( $model, $id );
	}
	public function add() {
        $model = I('model');
        $model = $this->getModel($model);		

		$this->meta_title = '新增' . $model ['title'];
		return parent::common_add ( $model );
	}
	public function del() {
		$model = I('model');
		$ids = I('ids');
		return parent::common_del ( $model, $ids );
	}
}