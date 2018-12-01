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
 * 插件模型
 *
 * @author yangweijie <yangweijiester@gmail.com>
 */
class Plugins extends Base {
	protected $table = DB_PREFIX. 'plugin';

	/**
	 * 获取应用插件列表
	 *
	 * @param string $addon_dir
	 */
	public function getWeixinList($isAll = false, $wpid_status = [], $is_admin = false, $is_show = false) {
		$where ['status'] = 1;
		$is_show && $where ['is_show'] = 1;
		$list = $this->where ( wp_where( $where ) )->select ();
		$isAll || $wpid_status = D('common/AddonStatus' )->getList ( $is_admin );
		foreach ( $list as $addon ) {
			if (! $isAll && isset ( $wpid_status [$addon ['name']] ) && $wpid_status [$addon ['name']] < 1)
				continue;

			if ($addon ['has_adminlist']) {
				$addon ['addons_url'] = U($addon['name'] . '/' . $addon ['name'] . '/lists' );
			} elseif (file_exists ( ONETHINK_PLUGIN_PATH . $addon ['name'] . '/config.php' )) {
				$addon ['addons_url'] = U($addon['name'] . '/' . $addon ['name'] . '/config' );
			} else {
				$addon ['addons_url'] = U($addon['name'] . '/' . $addon ['name'] . '/nulldeal' );
			}

			$addons [$addon ['name']] = $addon;
		}

		return $addons;
	}
	function getList($update = false) {
		$map ['status'] = 1;
		$key = cache_key($map, $this->table);
		$list = S ( $key );
		if ($list === false || $update) {			
			$list_res = $this->where ( wp_where( $map ) )->select ();
			foreach ( $list_res as $vo ) {
				$list [$vo ['name']] = $vo;
			}
			S ( $key, $list );
		}

		return $list;
	}
	function getInfoByName($name, $update = false) {
		$list = $this->getList ( $update );
		return $list [$name];
	}
}
