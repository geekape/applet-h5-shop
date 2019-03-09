<?php

namespace app\common\model;

use app\common\model\Base;
 

class Tag extends Base {
	var $table = DB_PREFIX. 'user_tag';
	function addTags($uid, $tag_ids) {
		$ids = array_filter ( explode ( ',', $tag_ids ) );
		
		$data ['uid'] = $uid;
		foreach ( $ids as $id ) {
			$data ['tag_id'] = $id;
			
			$has = M( 'user_tag_link' )->where ( wp_where( $data ) )->value( 'id' );
			if (! $has) {
				M( 'user_tag_link' )->insert( $data );
			}
		}
	}
}
