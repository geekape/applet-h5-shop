<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\admin\model;

use app\common\model\Base;


/**
 * 应用模型
 *
 * @author yangweijie <yangweijiester@gmail.com>
 */
class Plugin extends Base {
	protected $table = DB_PREFIX. 'plugin';

	/**
	 * 查找后置操作
	 */
	protected function _after_find(&$result, $options) {
	}
	protected function _after_select(&$result, $options) {
		foreach ( $result as &$record ) {
			$this->_after_find ( $record, $options );
		}
	}
	/* 自动验证规则 */
	//protected $_validate = array (
//			array (
//					'name',
//					'require',
//					'应用标识不能为空',
//					self::MUST_VALIDATE,
//					'regex',
//					MODEL_BOTH
//			),
//			array (
//					'name',
//					'/^[a-zA-Z][\w_]{1,29}$/',
//					'应用标识不合法',
//					self::MUST_VALIDATE,
//					'regex',
//					MODEL_BOTH
//			),
//			array (
//					'name',
//					'',
//					'应用已安装，请勿重复安装。或者请先卸载后再安装',
//					self::VALUE_VALIDATE,
//					'unique',
//					MODEL_BOTH
//			)
//	);
	/**
	 * 文件模型自动完成
	 *
	 * @var array
	 */
//	protected $_auto = array (
//			array (
//					'create_time',
//					NOW_TIME,
//					MODEL_INSERT
//			)
//	);

	/**
	 * 获取应用列表
	 *
	 * @param string $addon_dir
	 */
	public function getList($addon_dir = '', $type = 0) {
		if (! $addon_dir)
			$addon_dir = ONETHINK_PLUGIN_PATH;
		$dirs = array_map ( 'basename', glob ( $addon_dir . '*', GLOB_ONLYDIR ) );
		if ($dirs === FALSE || ! file_exists ( $addon_dir )) {
			$this->error = '应用目录不可读或者不存在';
			return FALSE;
		}
		$addons = [];
		$where ['name'] = array (
				'in',
				$dirs
		);
		$list = $this->where ( wp_where( $where ) )->field ( true )->select ();
		foreach ( $list as $addon ) {
			$addon ['uninstall'] = 0;
			$addon ['is_show_text'] = $addon ['is_show'] == 1 ? '是' : '否';
			$addons [$addon ['name']] = $addon;
		}
		foreach ( $dirs as $value ) {
			if (! isset ( $addons [$value] )) {
				$class = get_addon_class ( $value );
				if (! class_exists ( $class )) { // 实例化应用失败忽略执行
					\think\facade\Log::record ( '应用' . $value . '的入口文件不存在！' );
					continue;
				}
				$obj = new $class ();
				$addons [$value] = $obj->info;
				if ($addons [$value]) {
					$addons [$value] ['uninstall'] = 1;
					unset ( $addons [$value] ['status'] );
				}
			}
		}

		int_to_string ( $addons, array (
				'status' => array (
						- 1 => '损坏',
						0 => '禁用',
						1 => '启用',
						null => '未安装'
				)
		) );
		$addons = list_sort_by ( $addons, 'uninstall', 'desc' );
		return $addons;
	}

	/**
	 * 获取应用的后台列表
	 */
	public function getAdminList() {
		$admin = [];
		$db_addons = $this->where("status=1 AND has_adminlist=1" )->field ( 'title,name' )->select ();
		if ($db_addons) {
			foreach ( $db_addons as $value ) {
				$admin [] = array (
						'title' => $value ['title'],
						'url' => "application/adminList?name={$value['name']}"
				);
			}
		}
		return $admin;
	}
	function set_show($id, $val) {
		$map ['id'] = $id;
		return $this->where ( wp_where( $map ) )->setField ( 'is_show', $val );
	}
}
