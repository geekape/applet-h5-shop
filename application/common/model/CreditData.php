<?php

namespace app\common\model;

use app\common\model\Base;
 

/**
 * 积分操作
 */
class CreditData extends Base {
	protected $table = DB_PREFIX. 'credit_data';
	// 增加积分
	function addCredit($data) {
		if (empty ( $data ) || empty ( $data ['credit_name'] ))
			return false;
		
		$credit = D ( 'common/Credit' )->getCreditByName ( $data ['credit_name'] );
		// if (! $credit)
		// return false;
		empty ( $data ['uid'] ) && $data ['uid'] = intval(session('mid_'.get_pbid()));
		empty ( $data ['cTime'] ) && $data ['cTime'] = time ();
		$data ['wpid'] = get_wpid ();
		
		isset ( $data ['score'] ) || $data ['score'] = $credit ['score'];
		$data ['credit_title'] = isset ( $data ['title'] ) ? $data ['title'] : $credit ['title'];
		$data1 =$data;
		
		$res = $this->save( $data1 );
		if ($res) {
			$info = get_followinfo ( $data ['uid'] );
			$save ['score'] = $info ['score'] + $data ['score'];
						
			D('common/Follow' )->updateInfo ( $data ['uid'], $save );
			// $this->updateFollowTotalCredit ( $data ['uid'] );
		}
		
		return $res;
	}
	// 更新个人总积分
	function updateFollowTotalCredit($uid) {
		$map ['uid'] = $map2 ['id'] = $uid;
		$map ['wpid'] = get_wpid ();
		$info = $this->where ( wp_where( $map ) )->field ( 'sum( score ) as score' )->find ();
		
		D('common/Follow' )->updateInfo ( $uid, $info );
	}
	function getAllCreditInfo($uid) {
		$map ['uid'] = $uid;
		$info = $this->where ( wp_where( $map ) )->field ( 'sum( score ) as score' )->find ();
		return $info;
	}
	function clearCache($id, $act_type = '', $uid = 0, $more_param = []) {
	}
	function updateSubscribeCredit($wpid, $credit, $type = 0) {
		if ($type == 0) {
			$config = getAddonConfig ( 'UserCenter', $wpid );
			$config ['score'] = $credit ['score'];
			D('common/PublicConfig' )->setConfig ( 'UserCenter', $config );
		} else {
			$data ['wpid'] = $wpid;
			$data ['name'] = 'subscribe';
			
			$info = M( 'credit_config' )->where ( wp_where( $data ) )->find ();
			if ($info) {
				$res = M( 'credit_config' )->where ( wp_where( $data ) )->update ( $credit );
			} else {
				$data ['score'] = $credit ['score'];
				
				$data ['title'] = '关注公众号';
				$data ['mTime'] = NOW_TIME;
				
				M( 'credit_config' )->insert( $data );
			}
			$this->clearCache(0);
		}
	}
}
?>
