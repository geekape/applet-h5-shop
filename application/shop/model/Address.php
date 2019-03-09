<?php

namespace app\shop\model;

use app\common\model\Base;

/**
 * Shop模型
 */
class Address extends Base {
	protected $table = DB_PREFIX . 'shop_address';
	function initialize() {
		parent::initialize ();
		$this->openCache = true;
	}
	public function getInfo($id, $update = false, $data = []) {
		$info = $this->findById ( $id );
		return $info;
	}
	public function getCityName($city) {
		$ids = array_filter ( explode ( ',', $city ) );
		
		$list = M ( 'common_category' )->whereIn ( 'id', $ids )->select ();
		foreach ( $list as $v ) {
			$arr [$v ['id']] = $v ['title'];
		}
		foreach ( $ids as $id ) {
			isset ( $arr [$id] ) && $title [] = $arr [$id];
		}
		return implode ( ' ', $title );
	}
	public function deal($data, $info) {
		if (isset ( $info ['id'] ) && $info ['id'] > 0) {
			// 更新
			$map ['id'] = $info ['id'];
			$res = $this->allowField ( true )->save ( $data, $map );
		} else {
			// 插入
			$res = $this->allowField ( true )->save ( $data );
		}
		
		return $res;
	}
	public function getMyAddress($uid) {
		$info = $this->where ( 'uid', $uid )->find ();
		if (! isset ( $info ['id'] )) {
			$info = getUserInfo ( $uid );
		}
		
		return $info;
	}
	public function delAddress($id) {
		$map ['uid'] = session ( 'mid_' . get_pbid () );
		$map ['id'] = $id;
		$data ['is_del'] = 1;
		$res = $this->allowField ( true )->save ( $data, $map );
		return $res;
		// return $this->where(wp_where($map))->delete();
	}
	public function setDefault($id) {
		$map ['uid'] = session ( 'mid_' . get_pbid () );
		$this->save ( [ 
				'is_use' => 0 
		], $map );
		$map ['id'] = $id;
		$res = $this->save ( [ 
				'is_use' => 1 
		], $map );
		return $res;
	}
	public function getAddress($uid = 0) {
		// 获取默认地址
		$map ['uid'] = empty ( $uid ) ? session ( 'mid_' . get_pbid () ) : $uid;
		$map ['is_use'] = 1;
		$map ['is_del'] = 0;
		
		$address = $this->where ( wp_where ( $map ) )->find ();
		
		$address = isset ( $address ) ? $address->toArray () : [ ];
		
		return $address;
	}
	public function addAddress($param) {
		$data ['uid'] = session ( 'mid_' . get_pbid () );
		$data ['truename'] = $param ['truename'];
		$data ['mobile'] = $param ['mobile'];
		$data ['address'] = $param ['address'];
		$data ['is_use'] = 1;
		$data ['is_del'] = 0;
		$res = $this->insertGetId ( $data );
		return $res;
	}
	function getTrueName($order_id) {
		$id = D ( 'shop/Order' )->where ( 'id', $order_id )->value ( 'address_id' );
		return $this->where ( 'id', $id )->value ( 'truename' );
	}
}
