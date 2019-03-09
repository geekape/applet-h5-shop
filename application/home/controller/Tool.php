<?php
// +----------------------------------------------------------------------
// | WeiPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星
// +----------------------------------------------------------------------
namespace app\home\controller;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class Tool extends Home {
	var $db2 = '`w3`'; // 要更新的数据库
	var $db1 = '`weiphp3.0`'; // 源数据库
	function index() {
		$tables = array (
				'wp_auth_rule' => 'name',
				// 'wp_menu' => 'title',
				'wp_config' => 'name',
				'wp_addons' => 'name',
				'wp_action' => 'name',
				'wp_hooks' => 'name' ,
				'wp_plugin' => 'name',
		);
		
		foreach ( $tables as $t => $field ) {
			$this->adddel ( $t, $field );
		}
		
		$this->upateModel ();
	}
	function upateModel() {
		// 先判断模型
		$this->adddel ( 'wp_model', 'name' );
		
		// 属性更新
		$table = 'wp_attribute';
		$sql = "SELECT * FROM {$this->db1}.`$table`";
		$list1 = M()->query ( $sql );
		foreach ( $list1 as $vo1 ) {
			$arr1 [$vo1 ['model_name']] [$vo1 ['name']] = $vo1 ['id'];
		}
		// dump ( $arr1 );
		
		$sql = "SELECT * FROM {$this->db2}.`$table`";
		$list2 = M()->query ( $sql );
		foreach ( $list2 as $vo1 ) {
			$arr2 [$vo1 ['model_name']] [$vo1 ['name']] = $vo1 ['id'];
			$fields2 [$vo1 ['id']] = $vo1;
		}
		// dump ( $arr2 );exit;
		
		$has_insert = false;
		foreach ( $list1 as $vo ) {
			if (isset ( $arr2 [$vo ['model_name']] [$vo ['name']] )) {
				$field1 = $vo;
				$field2 = $fields2 [$arr2 [$vo ['model_name']] [$vo ['name']]];
				unset ( $field1 ['id'], $field1 ['update_time'], $field1 ['create_time'], $field1 ['model_id'] );
				unset ( $field2 ['id'], $field2 ['update_time'], $field2 ['create_time'], $field2 ['model_id'] );
				
				$diff = array_diff ( $field1, $field2 );
				
				if (! empty ( $diff )) {
					$set = '';
					foreach ( $diff as $f => $v ) {
						$v = str_replace ( "\r\n", '\r\n', $v );
						$set .= "`{$f}`='{$v}',";
					}
					$set = trim( $set, ',' );
					
					$sqlArr [] = "UPDATE $table SET {$set} WHERE `model_name`='{$vo [model_name]}' AND name='{$vo [name]}';<br/>";
				}
			} else {
				
				$vo ['model_id'] = 0;
				$model_name = $vo ['model_name'];
				unset ( $vo ['id'] );
				foreach ( $vo as $f => $v ) {
					$vo [$f] = str_replace ( "\r\n", '\r\n', $v );
				}
				$fields = array_keys ( $vo );
				$fields = '`' . implode ( '`,`', $fields ) . '`';
				$val = "'" . implode ( "','", $vo ) . "'";
				
				$sqlArr [] = "INSERT INTO $table ({$fields}) VALUES ({$val});<br/>";
				$has_insert = true;
			}
		}
		foreach ( $list2 as $vo ) {
			if (isset ( $arr1 [$vo ['model_name']] [$vo ['name']] ))
				continue;
			
			$sqlArr [] = "DELETE FROM wp_attribute WHERE `model_name`='{$vo [model_name]}' and `name`='{$vo [name]}';";
		}
		
		if ($has_insert) {
			$sqlArr [] = "UPDATE `wp_attribute` a, wp_model m SET a.model_id=m.`id` where m.`name`=a.model_name and a.model_id=0;";
		}
		
		echo implode ( '<br/>', $sqlArr );
	}
	function adddel($table, $field) {
		$sql = "SELECT * FROM {$this->db1}.`$table`";
		$list1 = M()->query ( $sql );
		$arr1 = getSubByKey ( $list1, $field );
		// dump ( $arr1 );
		
		$sql = "SELECT * FROM {$this->db2}.`$table`";
		$list2 = M()->query ( $sql );
		$arr2 = getSubByKey ( $list2, $field );
		// dump ( $arr2 );
		foreach ( $list2 as $v ) {
			unset ( $v ['id'], $v ['update_time'], $v ['create_time'] );
			$fields [$v ['name']] = $v;
		}
		
		$add_arr = array_diff ( $arr1, $arr2 );
		// dump ( $add_arr );
		
		$del_arr = array_diff ( $arr2, $arr1 );
		// dump ( $del_arr );
		
		foreach ( $list1 as $key => $value ) {
			unset ( $value ['id'] );
			if (in_array ( $value [$field], $add_arr )) {
				foreach ( $value as $f => $v ) {
					$value [$f] = str_replace ( "\r\n", '\r\n', $v );
				}
				$fields = array_keys ( $value );
				$fields = '`' . implode ( '`,`', $fields ) . '`';
				$val = "'" . implode ( "','", $value ) . "'";
				$sqlArr [] = "INSERT INTO $table ({$fields}) VALUES ({$val});<br/>";
			} elseif (in_array ( $value [$field], $del_arr )) {
				$sqlArr [] = "DELETE FROM $table WHERE `{$field}`='{$value [$field]}';<br/>";
			} else {
				unset ( $value ['id'], $value ['update_time'], $value ['create_time'] );
				$diff = array_diff ( $value, $fields [$value ['name']] );
				if (! empty ( $diff )) {
					$set = '';
					foreach ( $diff as $f => $v ) {
						$v = str_replace ( "\r\n", '\r\n', $v );
						$set .= "`{$f}`='{$v}',";
					}
					$set = trim( $set, ',' );
					
					$sqlArr [] = "UPDATE $table SET {$set} WHERE `{$field}`='{$value [$field]}';<br/>";
				}
			}
		}
		echo implode ( '<br/>', $sqlArr );
	}
	function updateFieldSort() {
		$list = M( 'model' )->select ();
		foreach ( $list as $vo ) {
			if (empty ( $vo ['field_sort'] ))
				continue;
			
			$field_sort = json_decode ( $vo ['field_sort'], true );
			foreach ( $field_sort as &$f ) {
				foreach ( $f as &$id ) {
					if (! is_numeric ( $id ))
						continue;
					
					$map ['model_id'] = $vo ['id'];
					$map ['id'] = $id;
					$id = M( 'attribute' )->where ( wp_where( $map ) )->value( 'name' );
				}
				$f = array_filter ( $f );
			}
			$field_sort = json_encode ( $field_sort );
			
			M( 'model' )->where( 'id=' . $vo ['id'] )->setField ( 'field_sort', $field_sort );
		}
	}
	function test_cache() {
		S ( 'test_cache', 112233 );
		$val = S ( 'test_cache' );
		if (! $val) {
			echo 'cache error';
			exit ();
		}
		
		// 清文件缓存
		rmdirr ( './runtime/' );
		@mkdir ( 'runtime', 0777, true );
		
		$val = S ( 'test_cache' );
		if ($val === false) {
			echo 'File Cache';
		} else {
			echo 'Memcahe';
		}
	}
	
	// ********************************* 代码打包工具 ***********************************************//
	// 打包入口文件
	function main() {
		$this->del_data ();
	}
	// 删除测试的业务数据
	function del_data() {
		$res = M()->execute ( 'DELETE FROM wp_user WHERE uid!=1' );
		dump ( $res );
		lastsql ();
		$res = M()->execute ( 'DELETE FROM wp_menu WHERE uid!=1' );
		dump ( $res );
		lastsql ();
		$res = M()->execute ( 'DELETE FROM wp_auth_group WHERE manager_id!=0' );
		dump ( $res );
		lastsql ();
		$res = M()->execute ( 'DELETE FROM wp_auth_group_access WHERE uid!=1' );
		dump ( $res );
		lastsql ();
		
		$arr = [];
		
		foreach ( $arr as $t ) {
			$res = M()->execute ( 'DELETE FROM ' . $t );
			dump ( $res );
			lastsql ();
			
			$res = M()->execute ( 'ALTER TABLE ' . $t . ' AUTO_INCREMENT=1' );
			dump ( $res );
			lastsql ();
		}
		
		$tables = "'wp_user','wp_menu','wp_auth_group','wp_credit_config','wp_auth_group_access'";
		
		$sql = "SELECT TABLE_NAME as t,COLUMN_NAME as f FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA='weiphp3.0' AND COLUMN_NAME in ('uid','manager_id','wpid') AND TABLE_NAME not in ($tables)";
		$list = M()->query ( $sql );
		foreach ( $list as $vo ) {
			$res = M()->execute ( 'DELETE FROM ' . $vo ['t'] );
			dump ( $res );
			lastsql ();
			
			$res = M()->execute ( 'ALTER TABLE ' . $vo ['t'] . ' AUTO_INCREMENT=1' );
			dump ( $res );
			lastsql ();
		}
		
		$res = M()->execute ( 'update wp_user set is_init=0 where uid=1' );
		dump ( $res );
		lastsql ();
	}
}