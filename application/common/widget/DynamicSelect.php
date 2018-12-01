<?php

namespace app\common\widget;

use app\common\controller\base;

/**
 * 级联菜单插件
 *
 * @author 凡星
 */
class DynamicSelect extends base {
	public $info = array (
			'name' => 'DynamicSelect',
			'title' => '动态下拉菜单',
			'description' => '支持动态从数据库里取值显示',
			'status' => 1,
			'author' => '凡星',
			'version' => '0.1',
			'has_adminlist' => 0,
			'type' => 0
	);
	public function install() {
		return true;
	}
	public function uninstall() {
		return true;
	}

	/**
	 * 编辑器挂载的后台文档模型文章内容钩子
	 *
	 * table=addons&type=1&value_field=name&title_field=title&order=id desc&first_option=请选择
	 */
	public function dynamic_select($data) {
		$key = 'select_' . $data ['name'] . '_' . get_wpid ();
		$res = S ( $key );

		if ($res === false || true) {
			$manager_id = $GLOBALS ['uid'];
			$wpid = get_wpid ();
			$data ['extra'] = str_replace ( array (
					'[manager_id]',
					'[wpid]'
			), array (
					$manager_id,
					$wpid
			), $data ['extra'] );

			parse_str ( $data ['extra'], $arr );

			$table = isset ( $arr ['table'] ) ? $arr ['table'] : 'common_category';
			$value_field = isset ( $arr ['value_field'] ) ? $arr ['value_field'] : 'id';
			$title_field = isset ( $arr ['title_field'] ) ? $arr ['title_field'] : 'title';
			$order = isset ( $arr ['order'] ) ? $arr ['order'] : $value_field . ' asc';
			$first_option = isset ( $arr ['first_option'] ) ? $arr ['first_option'] : '请选择';

			unset ( $arr ['table'], $arr ['value_field'], $arr ['title_field'], $arr ['order'], $arr ['first_option'] );
			// dump($arr);
			//$arr ['wpid'] = get_wpid ();
			$list = M( $table )->where ( wp_where( $arr ) )->field ( $value_field . ',' . $title_field )->order ( $order )->select ();

			$res = [];
			foreach ( $list as $v ) {
				$res [$v [$value_field]] = $v [$title_field];
			}

			S ( $key, $res, 86400 );
		}
		// dump ( $json );
		$this->assign ( 'list', $res );

		$data ['default_value'] = $data ['value'];
		$this->assign ( $data );
		$this->assign ( 'first_option', $first_option );

		return $this->fetch( 'common@/widget/dynamic_select' );
	}
}