<?php

namespace app\common\model;

use app\common\model\Base;
 

/**
 * 插件配置操作集成
 */
class Prize extends Base {
	/**
	 * 保存配置
	 */
	function set($target_id, $addon = 'Scratch') {
		$opt_data ['addon'] = $addon;
		$opt_data ['target_id'] = $target_id;
		
		foreach ( input('post.prize_title') as $key => $opt ) {
			if (empty ( $opt ))
				continue;
			
			$opt_data ['prize_title'] = $opt;
			$opt_data ['prize_name'] = input('post.prize_name') [$key];
			$opt_data ['prize_num'] = input('post.prize_num') [$key];
			if ($key > 0) {
				$optIds [] = $key;
				$map ['id'] = $key;
				M( 'prize' )->where ( wp_where( $map ) )->update ( $opt_data );
			} else {
				$optIds [] = M( 'prize' )->insertGetId( $opt_data );
			}
		}
		
		// 删除旧数据
		$map2 ['id'] = array (
				'not in',
				$optIds 
		);
		$map2 ['target_id'] = $opt_data ['target_id'];
		$flag = M( 'prize' )->where ( wp_where( $map2 ) )->delete ();
		
		return $flag;
	}
}
?>
