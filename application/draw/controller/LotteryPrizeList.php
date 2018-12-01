<?php

namespace app\draw\controller;

use app\common\controller\WebBase;

class LotteryPrizeList extends WebBase{
	public function lists() {
		$model = $this->getModel ( 'lottery_prize_list' );
		$list_data = $this->_get_model_list ( $model, 'id desc', true );
		$dao = D ( 'LotteryPrizeList' );
		foreach ( $list_data ['list_data'] as &$vo ) {
			$info = $dao->getInfo ( $vo ['id'] );
			$info ['award_id'] = $info ['awardarr'] ['name'];
			$info ['sports_id'] = $info ['sports'] ['vs_team'];
			$vo = array_merge($vo, $info);
		}
		$this->assign ( $list_data );
		
		return $this->fetch();
	}
	public function add() {
		$sports_id = I ( 'sports_id/d', 0 );
		if ($sports_id) {
			$this->sports_add ( $sports_id );
		}
		$lzwgid = I ( 'lzwg_id/d', 0 );
		if ($lzwgid) {
			$this->lzwg_add ( $lzwgid );
		}
	}
	function sports_add($sports_id) {
		// $sports_id = I ( 'sports_id/d', 0 );
		$uid = $map1 ['uid'] = $this->mid;
		// $award = M( 'award' )->where( wp_where($map1) )->select ();
		$award = M( 'award' )->select ();
		$model = getModelByName ( 'lottery_prize_list' );
		
		$fafangjp = D ( 'LuckyFollow' )->getAwardNum( $sports_id );
		
		$dao = D ( 'LotteryPrizeList' );
		$lotteryPrizedata = $dao->getList ( $sports_id );
		foreach ( $lotteryPrizedata as $l ) {
			$lp [$l ['award_id']] = intval ( $l ['award_num'] );
		}
		
		foreach ( $award as $v ) {
			$v ['has_pay'] = intval ( $fafangjp [$v ['id']] );
			$v ['set_num'] = intval ( $lp [$v ['id']] );
			
			$awardArr [$v ['id']] = $v;
		}
		if (IS_POST) {
			$awardNumArr = I ( 'post.award_num' );
			$prizeNum = 0;
			foreach ( $awardNumArr as $id => $num ) {
				if ($num < 0) {
					$this->error ( '奖品数量不能小于0' );
				}
				if ($num > 0) {
					$prizeNum ++;
				}
				if ($awardArr [$id] ['award_type'] == 1) {
					if ($lp [$id] + $awardArr [$id] ['count'] < $num) {
						$this->error ( $awardArr [$id] ['name'] . '数量不能超过奖品库剩余的数量！' );
					} else {
						$awardArr [$id] ['count'] = $awardArr [$id] ['count'] + $lp [$id] - $num;
					}
				}
			}
			// if ($prizeNum>9){
			// $this->error('一场比赛只能设置 9 种奖品');
			// }
			foreach ( $awardNumArr as $id => $num ) {
				
				if (isset ( $lp [$id] )) { // 更新数据
					$map ['sports_id'] = $sports_id;
					$map ['award_id'] = $id;
					$data ['award_num'] = $num;
					$res = M( 'lottery_prize_list' )->where ( wp_where( $map ) )->update ( $data );
					$dao->getInfo ( $id, true );
					$dao->getList ( $sports_id, true );
				} else { // 增加数据
					$data ['sports_id'] = $sports_id;
					$data ['award_id'] = $id;
					$data ['award_num'] = $num;
					$data ['uid'] = $this->mid;
					$res = M( 'lottery_prize_list' )->insertGetId( $data );
				}
			}
			
			$awardDao = D ( 'Award' );
			foreach ( $awardArr as $id => $vo ) {
				$res = $awardDao->updateInfo ( $id, $vo );
			}
			$prizelist = $dao->getList ( $sports_id, true );
			foreach ( $prizelist as &$v ) {
				if ($fafangjp [$v ['award_id']]) {
					$v ['award_num'] = $v ['award_num'] - $fafangjp [$v ['award_id']];
				}
				if ($v ['award_num'] > 0) {
					if ($v ['awardarr'] ['award_type'] == 1) {
						$shiwu [] = $v;
					} else {
						// $xuni[]=$v;
					}
					// $d [] = $v;
				}
			}
			foreach ( $shiwu as $v ) {
				$shiwuArr [] = array (
						'prize_id' => $v ['award_id'],
						'prize_num' => $v ['award_num'] 
				);
			}
			// foreach ( $xuni as $v ) {
			// $xuniArr [] = array (
			// 'prize_id' => $v ['award_id'],
			// 'prize_num' => $v ['award_num']
			// );
			// }
			$prizeArr ['shiwu'] = $shiwuArr;
			// $prizeArr['xuni']=$xuniArr;
			
			// foreach ( $d as $v ) {
			// $prizeArr [] = array (
			// 'prize_id' => $v ['award_id'],
			// 'prize_num' => $v ['award_num']
			// );
			// }
			$sports = D ( 'Sports/Sports' )->getInfo ( $sports_id );
			$start_time = $sports ['start_time'];
			$end_time = $start_time + 120 * 60;
			
			// if ($end_time < NOW_TIME) {
			// $prizeid = - 1;
			// } else {
			get_lottery ( $prizeArr, $start_time, $end_time, $sports_id, true );
			
			$this->success ( '添加' . $model ['title'] . '成功！' );
		}
		
		$this->assign ( 'sports_id', $sports_id );
		// $this->assign ( 'prizes', $prizes );
		$this->assign ( 'awards', $awardArr );
		return $this->fetch();
	}
	public function lzwg_add($lzwgid) {
		// $lzwgid = I ( 'lzwg_id/d', 0 );
		$uid = $map1 ['uid'] = $this->mid;
		$award = M( 'award' )->where ( wp_where( $map1 ) )->select ();
		$model = getModelByName ( 'lottery_prize_list' );
		
		$fafangjp = D ( 'LuckyFollow' )->getLzwgAwardNum( $lzwgid );
		
		$dao = D ( 'LotteryPrizeList' );
		$lotteryPrizedata = $dao->getList ( $lzwgid );
		foreach ( $lotteryPrizedata as $l ) {
			$lp [$l ['award_id']] = intval ( $l ['award_num'] );
		}
		
		foreach ( $award as $v ) {
			$v ['has_pay'] = intval ( $fafangjp [$v ['id']] );
			$v ['set_num'] = intval ( $lp [$v ['id']] );
			
			$awardArr [$v ['id']] = $v;
		}
		if (IS_POST) {
			$awardNumArr = I ( 'post.award_num' );
			
			// $prizeNum=0;
			foreach ( $awardNumArr as $id => $num ) {
				if ($num < 0) {
					$this->error ( '奖品数量不能小于0' );
				}
				// if ($num>0){
				// $prizeNum++;
				// }
				if ($lp [$id] + $awardArr [$id] ['count'] < $num) {
					$this->error ( $awardArr [$id] ['name'] . '数量不能超过奖品库剩余的数量！' );
				} else {
					$awardArr [$id] ['count'] = $awardArr [$id] ['count'] + $lp [$id] - $num;
				}
			}
			// if ($prizeNum>9){
			// $this->error('一场活动只能设置 9 种奖品');
			// }
			foreach ( $awardNumArr as $id => $num ) {
				
				if (isset ( $lp [$id] )) { // 更新数据
					$map ['sports_id'] = $lzwgid;
					$map ['award_id'] = $id;
					$data ['award_num'] = $num;
					$res = M( 'lottery_prize_list' )->where ( wp_where( $map ) )->update ( $data );
					$dao->getInfo ( $id, true );
				} else { // 增加数据
					$data ['sports_id'] = $lzwgid;
					$data ['award_id'] = $id;
					$data ['award_num'] = $num;
					$data ['uid'] = $this->mid;
					$res = M( 'lottery_prize_list' )->insertGetId( $data );
				}
			}
			
			$awardDao = D ( 'Award' );
			foreach ( $awardArr as $id => $vo ) {
				$res = $awardDao->updateInfo ( $id, $vo );
			}
			$prizelist = $dao->getList ( $lzwgid, true );
			foreach ( $prizelist as &$v ) {
				if ($fafangjp [$v ['award_id']]) {
					$v ['award_num'] = $v ['award_num'] - $fafangjp [$v ['award_id']];
				}
				if ($v ['award_num'] > 0) {
					
					$d [] = $v;
				}
			}
			
			foreach ( $d as $v ) {
				$prizeArr [] = array (
						'prize_id' => $v ['award_id'],
						'prize_num' => $v ['award_num'] 
				);
			}
			$lzwg_activities = D ( 'Draw/Draw' )->getInfo ( $lzwgid );
			$start_time = $lzwg_activities ['start_time'];
			$end_time = $lzwg_activities ['end_time'];
			
			// if ($end_time < NOW_TIME) {
			// $prizeid = - 1;
			// } else {
			get_lottery ( $prizeArr, $start_time, $end_time, $uid, $lzwgid, true );
			
			$this->success ( '添加' . $model ['title'] . '成功！' );
		}
		
		$this->assign ( 'sports_id', $lzwgid );
		// $this->assign ( 'prizes', $prizes );
		$this->assign ( 'awards', $awardArr );
		$this->display ();
	}
}
