<?php

namespace app\draw\model;

use app\common\model\Base;


/**
 * DrawFollowLog模型
 */
class DrawFollowLog extends Base {
    function initialize()
    {
        parent::initialize();
        $this->openCache = true;
    }	
	function getInfo($id, $update = false, $data = []) {
		$key = cache_key('id:'.$id, $this->name);
		$info = S ( $key );
		if ($info === false || $update) {
            if (empty($data)) {
                $info = $this->findById($id);
            } else {
                $info = $data;
            }
			if(!empty($info)) {
				$sports = D ( 'Sports/Sports' )->getInfo ( $info ['sports_id'] );
				$info ['sportsarr'] = $sports;
				$follow = get_followinfo ( $info ['follow_id'] );
				$info ['followarr'] = $follow;
			}
		}
		S ( $key, $info, 86400 );
		return $info;
	}
	//每人总共抽奖次数
	//$time 有值时：每人每天抽奖次数
	function get_user_attend_count($gamesId,$uid=0,$time=0){
	    $map['sports_id']=$gamesId;
	    $map['wpid']=get_wpid();
	    if ($uid !=0){
	        $map['follow_id']=$uid;
	    }
	    
	    if ($time !=0){
	        $map['cTime']=array('egt',strtotime(time_format($time,'Y-m-d')));
	    }
	    $data=$this->where( wp_where($map) )->field('sum( count ) totals')->select();
	    return intval($data[0]['totals']);
	}
	
	function hasDraw($sports_id, $follow_id, $update = false) {
		$map ['sports_id'] = $sports_id;
		$map ['follow_id'] = $follow_id;
		$map ['uid'] = session ( 'manager_id' );
		if (empty ( $map ['uid'] )) {
			$map ['uid'] = get_mid();
		}
		$key = cache_key($map, $this->name, 'count');
		$info = S ( $key );
		if ($info === false || $update) {
			$info = intval ( $this->where ( wp_where( $map ) )->value( 'count' ) );
			
			S ( $key, $info, 86400 );
		}
		
		return $info;
	}
	// 获取靓妆用户当日抽奖数
	function hasDrawByDay($sports_id, $follow_id, $update = false) {

			$user_id = session ( 'manager_id' );
			if (empty ( $user_id )) {
				$user_id = get_mid();
			}
			$cur_date = strtotime (date ( 'Y-m-d', NOW_TIME ) );
			$info = $this->field ( 'suM( `count` ) as num' )->where("uid=$user_id and sports_id=$sports_id and follow_id=$follow_id and cTime >= '$cur_date'" )->select ();
		
		return $info [0] ['num'];
	}
	function delayAddData($data, $delay = 10) {
		$res = $this->delayAdd ( $data, $delay );
		$this->hasDraw ( $data ['sports_id'], $data ['follow_id'], true );
// 		$this->hasDrawByDay ( $data ['sports_id'], $data ['follow_id'], true );
		return $res;
	}
	function updateCount($sports_id, $follow_id, $count) {
		$count ++;
		$map ['sports_id'] = $sports_id;
		$map ['follow_id'] = $follow_id;
		$map ['uid'] = session ( 'manager_id' );
		if (empty ( $map ['uid'] )) {
			$map ['uid'] = get_mid();
		}
		$res = $this->where ( wp_where( $map ) )->setField ( 'count', $count );
		if ($res) {
			
			$this->hasDraw ( $sports_id, $follow_id, true );
		}
		return $res;
	}
}
