<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\home\model;

use app\common\model\Base;
 

/**
 * 分类模型
 */
class Category extends Base {
	var $table = DB_PREFIX. 'common_category';
	
	
	/**
	 * 获取分类详细信息
	 *
	 * @param milit $id
	 *        	分类ID或标识
	 * @param boolean $field
	 *        	查询字段
	 * @return array 分类信息
	 */
	public function info($id, $field = true) {
		/* 获取分类信息 */
		$map = [];
		if (is_numeric ( $id )) { // 通过ID查询
			$map ['id'] = $id;
		} else { // 通过标识查询
			$map ['name'] = $id;
		}
		return $this->field ( $field )->where ( wp_where( $map ) )->find ();
	}
	
	/**
	 * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
	 *
	 * @param integer $id
	 *        	分类ID
	 * @param boolean $field
	 *        	查询字段
	 * @return array 分类树
	 */
	public function getTree($id = 0, $field = true, $module='') {
		/* 获取当前分类信息 */
		if ($id) {
			$info = $this->info ( $id );
			$id = $info ['id'];
		}
		$map ['module'] = empty($module) ? I ( 'module' ) : $module;
		$map ['wpid'] = get_wpid ();
		
		/* 获取所有分类 */
		$list = $this->field ( $field )->where ( wp_where( $map ) )->order ( 'sort asc, id asc' )->select ();
		foreach ($list as &$vo){
		    $vo=$vo->getData();
		}
		$list = list_to_tree ( $list, $pk = 'id', $pid = 'pid', $child = '_', $root = $id );
		/* 获取返回数据 */
		if (isset ( $info )) { // 指定分类则返回当前分类极其子分类
			$info ['_'] = $list;
		} else { // 否则返回所有分类
			$info = $list;
		}
		return $info;
	}
	function getSubUidByMid($tree, $is_sub = false) {
		static $_uids = [];
	
		foreach ( $tree as $v ) {
			$uidArr = explode ( ',', $v ['admin_uids'] );
			if (empty ( $uidArr ))
				continue;
				
			if ($is_sub) {
				$_uids = array_merge ( $_uids, $uidArr );
			}
				
			if (empty ( $v ['_'] ))
				continue;
				
			if ($is_sub || in_array ( $GLOBALS ['mid'], $uidArr )) {
				$this->getSubUidByMid ( $v ['_'], true );
			} else {
				$this->getSubUidByMid ( $v ['_'], false );
			}
		}
		return $_uids;
	}	
	
	/**
	 * 获取指定分类的同级分类
	 *
	 * @param integer $id
	 *        	分类ID
	 * @param boolean $field
	 *        	查询字段
	 * @return array
	 */
	public function getSameLevel($id, $field = true) {
		$info = $this->info ( $id, 'pid' );
		$map ['pid'] = $info ['pid'];
		$map ['module'] = I ( 'module' );
		$map ['wpid'] = get_wpid ();
		return $this->field ( $field )->where ( wp_where( $map ) )->order ( 'sort' )->select ();
	}
	
	/**
	 * 更新分类信息
	 *
	 * @return boolean 更新状态
	 */
	public function updateInfo($data=[]) {
	    empty($data) && $data = input('post.');
		if (! $data) { // 数据对象创建错误
			return false;
		}
		
		/* 添加或更新数据 */
		return empty ( $data ['id'] ) ? $this->save($data) : $this->isUpdate(true)->save ($data);
	}
	
	/**
	 * 获取指定分类子分类ID
	 *
	 * @param string $cate
	 *        	分类ID
	 * @return string id列表
	 */
	public function getChildrenId($cate) {
		$field = 'id,name,pid,title';
		$category = D ( 'Category' )->getTree ( $cate, $field );
		$ids = [];
		foreach ( $category ['_'] as $key => $value ) {
			$ids [] = $value ['id'];
		}
		return implode ( ',', $ids );
	}
	
	/**
	 * 查询后解析扩展信息
	 *
	 * @param array $data
	 *        	分类数据
	 */
	protected function _after_find(&$data, $options) {
		/* 分割模型 */
		if (! empty ( $data ['model'] )) {
			$data ['model'] = explode ( ',', $data ['model'] );
		}
		
		/* 分割文档类型 */
		if (! empty ( $data ['type'] )) {
			$data ['type'] = explode ( ',', $data ['type'] );
		}
		
		/* 分割模型 */
		if (! empty ( $data ['reply_model'] )) {
			$data ['reply_model'] = explode ( ',', $data ['reply_model'] );
		}
		
		/* 分割文档类型 */
		if (! empty ( $data ['reply_type'] )) {
			$data ['reply_type'] = explode ( ',', $data ['reply_type'] );
		}
		
		/* 还原扩展数据 */
		if (! empty ( $data ['extend'] )) {
			$data ['extend'] = json_decode ( $data ['extend'], true );
		}
	}
}
