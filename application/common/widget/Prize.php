<?php

namespace app\common\widget;

use app\common\controller\base;

/**
 * 动态多选菜单插件
 *
 * @author 凡星
 */
class Prize extends base {
	public $info = array (
			'name' => 'Prize',
			'title' => '奖品选择',
			'description' => '支持多种奖品选择',
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
	 * table=addons&type=1&value_field=name&title_field=title&order=id desc
	 */
	public function prize($data) {
		$key = 'prize_' . $data ['name'] . '_' . get_wpid ();
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

			unset ( $arr ['table'], $arr ['value_field'], $arr ['title_field'], $arr ['order'] );
			// dump($arr);
			$arr ['wpid'] = get_wpid ();
			$list = M( $table )->where ( wp_where( $arr ) )->field ( $value_field . ',' . $title_field )->order ( $order )->select ();

			$res = [];
			foreach ( $list as $v ) {
				$res [$v [$value_field]] = $v [$title_field];
			}

			S ( $key, $res, 86400 );
		}
		// dump ( $json );
		$this->assign ( 'list', $res );

// 		$data ['default_value'] = $data ['value'] = is_array ( $data ['value'] ) ? $data ['value'] : explode ( ',', $data ['value'] );
		$data ['default_value'] = $data ['value'] ;
		$data ['prize_detail'] = get_prize_detail($data['value']);
		$this->assign ( $data );
		return $this->fetch( 'common@widget/prize' );
	}
}