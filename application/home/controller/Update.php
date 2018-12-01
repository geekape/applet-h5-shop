<?php

namespace app\home\controller;

class Update extends Home {
	
	function follow_merge() {
		if(function_exists('set_time_limit')){
			set_time_limit(0);
		}
		$id = I ( 'id/d', 0 );
		$map ['id'] = array (
				'gt',
				$id 
		);
		$follow = M( 'follow' )->where ( wp_where( $map ) )->limit ( 10 )->order ( 'id asc' )->select ();
		if (! $follow) {
			die ( 'It is Over!' );
		}
		foreach ( $follow as $vo ) {
			$user = array (
					'uid' => $vo ['id'],
					'nickname' => $vo ['nickname'],
					'password' => $vo ['password'],
					'truename' => $vo ['truename'],
					'mobile' => $vo ['mobile'],
					'email' => $vo ['email'],
					'sex' => $vo ['sex'],
					'headimgurl' => $vo ['headimgurl'],
					'city' => $vo ['city'],
					'province' => $vo ['province'],
					'country' => $vo ['country'],
					'language' => $vo ['language'],
					'score' => intval ( $vo ['score'] ),
					'unionid' => $vo ['unionid'],
					
					'status' => intval ( $vo ['status'] ),
					'is_init' => 1,
					'is_audit' => 1 
			);
			
			$map ['uid'] = $param ['id'] = $vo ['id'];
			if (M( 'user' )->where ( wp_where( $map ) )->find ()) {
				$res = M( 'user' )->where ( wp_where( $map ) )->update ( $user );
			} else {
				$res = M( 'user' )->insertGetId( $user );
			}
			lastsql ();
			dump ( $res );
		}
		
		$url = U ( 'follow_merge', $param );
		echo '<script>window.location.href="' . $url . '"</script> ';
	}
	
	// 处理model表的field_sort字段
	function field_sort() {
		$list = M( 'model' )->select ();
		// dump ( $list );
		foreach ( $list as $v ) {
			if (empty ( $v ['field_sort'] ))
				continue;
			
			$field_sort = json_decode ( $v ['field_sort'], true );
			if (! is_array ( $field_sort [1] ))
				continue;
			
			$field_sort = json_encode ( $field_sort [1] );
			// dump ( $field_sort );
			$map ['id'] = $v ['id'];
			$res = M( 'model' )->where ( wp_where( $map ) )->setField ( 'field_sort', $field_sort );
			dump ( $res );
			lastsql ();
		}
		dump ( 'It is over' );
	}
}
