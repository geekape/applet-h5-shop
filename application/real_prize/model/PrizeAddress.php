<?php

namespace app\real_prize\model;

use app\common\model\Base;


/**
 * PrizeAddress模型
 */
class PrizeAddress extends Base {	
	//判断用户是否填写地址 领取奖励
	function getAddressInfo($uid,$prizeid, $update = false) {
		$map['uid']=$uid;
		$map['prizeid']=$prizeid;
		$key = cache_key($map, DB_PREFIX.'prize_address');
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array )  $this->where ( wp_where( $map ) )->find ();
			S ( $key, $info, 86400 );
		}
		return $info;
	}
	
	
}
