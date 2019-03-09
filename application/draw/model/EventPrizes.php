<?php

namespace app\draw\model;

use app\common\model\Base;
use think\cache\driver\Redis;

class EventPrizes extends Base {
	/**
	 *
	 * @param unknown $eventId
	 *        	活动id
	 * @param unknown $currentNum
	 *        	当前第几位进来
	 */
	function getPrize($eventId, $currentNum) {
		if ($currentNum<=0){
			return 0;
		}
		$all = $this->getEventPrize($eventId);
		$pArr = [];
		$sort = 0;
		foreach ($all as $vo){
			//当前次数是在哪批奖品中
			if ($vo['start_num'] <= $currentNum && $vo['end_num']>= $currentNum){
				$pArr = $vo['prize_list'];
				$sort = $vo['sort'];
				break;
			}
		}
		$win = 0;
		$winList=[];
		if (isset ( $pArr [$currentNum] )) {
			//有中奖的
			//判断该奖品是否已经被抽走了
			$redis = new Redis ();
			// 获取当前已经抽中的奖品列表
			$rkey = 'rdraw_winlists_' . $eventId ;
			$winList = $redis->get ( $rkey );
// 			if (!isset($winList[$currentNum])){
				//未被抽过的奖品
				$winList [$currentNum] = $currentNum;
				$res = $redis->set ( $rkey, $winList );
				if ($res){
					$win = $pArr [$currentNum];
				}
// 			}
		}
		$data['prize_id']=$win;
		$data['win_list']=$winList;
		return $data;
	}
	// 获取活动抽奖奖品列表
	function getEventPrize($eventId, $update = false) {
		$key = cache_key('event_id:'.$eventId, $this->name, 'start_num,end_num,prize_list,sort');
		$data = S ( $key );
		if (empty ( $data ) || $update) {
			// 用于查看
			$data = $this->where ( 'event_id', $eventId )->order ( 'sort asc' )->field ( 'start_num,end_num,prize_list,sort' )->select ();
			foreach ( $data as &$vo ) {
				$vo ['prize_list'] = $this->prizeStrToArr ( $vo ['prize_list'] );
			}
			S ( $key, $data );
		}
		return $data;
	}
	
	/**
	 * 获取奖品列表(没有用到，改成用上面的有缓存的方法)
	 *
	 * @param unknown $eventId
	 *        	活动id
	 * @param number $currentNum
	 *        	当前第几位进来，0：获取整个奖品列表
	 * @param number $isList
	 *        	1：返回进来数字的奖品列表 0：返回进来数字对应的奖品，没有则返回0（不中奖）
	 */
	function getPrize111($eventId, $currentNum = 0, $isList = 0) {
		if ($currentNum > 0) {
			$data = $this->where ( 'event_id', $eventId )->where ( 'start_num', 'elt', $currentNum )->where ( 'end_num', 'egt', $currentNum )->order ( 'sort asc' )->value ( 'prize_list' );
			$pArr = $this->prizeStrToArr ( $data );
		} else {
			// 用于查看
			$data = $this->where ( 'event_id', $eventId )->order ( 'sort asc' )->field ( 'prize_list' )->select ();
			$pArr = [ ];
			foreach ( $data as $vo ) {
				$pp = $this->prizeStrToArr ( $vo ['prize_list'] );
				$pArr += $pp;
			}
		}
		// echo $this->getLastSql();
		if ($isList == 1) {
			return $pArr;
		} else {
			return isset ( $pArr [$currentNum] ) ? $pArr [$currentNum] : 0;
		}
	}
	
	// 将字符串的奖品转为数组
	function prizeStrToArr($pStr) {
		$arr = explode ( ',', $pStr );
		$pArr = [ ];
		foreach ( $arr as $vv ) {
			$dd = explode ( ':', $vv );
			if (isset ( $dd [1] ) && isset ( $dd [0] )) {
				$pArr [$dd [1]] = $dd [0];
			}
		}
		return $pArr;
	}
	//清缓存
	function clearCache($eventId, $act_type = '', $uid = 0, $more_param = []) {
		$this->getEventPrize ( $eventId, true );
	}
	/**
	 * 保存抽奖时奖品排位列表
	 *
	 * @param unknown $eventId
	 *        	活动id
	 * @param unknown $prizeArr
	 *        	活动的奖品 [[奖品1,数量1],[奖品2,数量2],[奖品3,数量3]]
	 *        	如：[['prize_id'=>'A','prize_num'=>10],['prize_id'=>'B','prize_num'=>20]];
	 * @param unknown $drawCount
	 *        	抽奖次数
	 * @param unknown $isFixed 是否一定中奖
	 * 
	 */
	function setPrizeList($eventId, $prizeArr, $drawCount,$isFixed=0) {
		if ($drawCount<= 0){
			//总抽奖次数小于等于0，则不中奖
			return [];
		}
		if(function_exists('set_time_limit')){
			set_time_limit(0);
		}
		ini_set('memory_limit','500M');
		
		// 删除之前的奖品列表记录
		$this->where ( 'event_id', '=', $eventId )->delete ();
		// 总奖品数
		$totalNum = 0;
		// 奖品数组
		$rand = [ ];
		foreach ( $prizeArr as $p ) {
			$totalNum += $p ['prize_num'];
		}
		if ($totalNum <= 0) {
			return [ ];
		}
		
		// 将奖品分批排位，每1000个奖品一批
		$maxBatch = 1000;
		// 总共分多少批
		$batchCount = ceil ( $totalNum / $maxBatch );
		//抽奖次数分批
		$space = intval(($drawCount-$totalNum)/$batchCount);
		
// 		$space = intval ( $drawCount / $batchCount );

		// 每批第一个位置
		$redis = new Redis();
		//抽奖已经开始了，获取当前第几位抽奖
		$rediesKey = 'rdraw_lottery_'.$eventId;
		$cSeat =$redis->get($rediesKey);
		if ($cSeat > 0){
			$sSeat=$cSeat+1;
			$drawCount=$cSeat+$drawCount+1;
		}else {
			$sSeat = 1;
		}
		$eSeat = $space;
		// 获取每批的奖品
		for($i = 1; $i <= $batchCount; $i ++) {
			$plist = [ ];
			// 每批第一个位置
			if ($i != 1) {
				$sSeat = $eSeat + 1;
			}
			/* // 每批最后一个位置
			if ($i == $batchCount) {
				$eSeat = $drawCount;
			} else {
				$eSeat = $space * $i;
			} */
			
			if ($i != $batchCount) {
				// 抽取每种奖品个数比例
				$percent = $maxBatch / $totalNum;
				// dump($percent);
				// 当前奖品数组
				// $pArr = [ ];
				// 当前奖品数量
				$tNum = 0;
				foreach ( $prizeArr as &$pp ) {
					// 抽取的个数
					$pnum = floor ( $pp ['prize_num'] * $percent );
					if ($pnum <= 0 && $pp ['prize_num'] > 0) {
						// 确保每批奖品列表中每种奖品有一个
						$pnum = 1;
					}
					$tNum += $pnum;
					$pp ['prize_num'] -= $pnum;
					// $pArr [] = array (
					// 'prize_id' => $pp ['prize_id'],
					// 'prize_num' => $pnum
					// );
					
					for($j = 0; $j < $pnum; $j ++) {
						$plist [] = $pp ['prize_id'];
					}
				}
				// dump('---剩余总数 ---');
				$totalNum -= $tNum;
				// dump($totalNum);
				
				if ($i == $batchCount) {
					$eSeat = $drawCount;
				} else {
					//抽奖次数的每批最后一个数
					$eSeat = $sSeat + $tNum + $space - 1;
				}

				// 奖品ID排序随机
				$this->savePrizes ( $eventId, $plist, $tNum, $sSeat, $eSeat, $i,$isFixed );
				// dump('---每批---');
				// $pbArr [$i] = $pArr;
				// dump($pbArr);
			} else {
				// 剩余的奖品都在最后一批
				// dump('--------');
				// dump($totalNum);
				// dump($prizeArr);
				// $pbArr [$i] = $prizeArr;
				foreach ( $prizeArr as $pa ) {
					for($j = 0; $j < $pa ['prize_num']; $j ++) {
						$plist [] = $pa ['prize_id'];
					}
				}
				if ($i == $batchCount) {
					$eSeat = $drawCount;
				} else {
					//抽奖次数的每批最后一个数
					$eSeat = $sSeat + $totalNum + $space - 1;
				}
				// 奖品ID排序随机
				$this->savePrizes ( $eventId, $plist, $totalNum, $sSeat, $eSeat, $i,$isFixed );
			}
		}
		// 清缓存
		$this->clearCache( $eventId );
	}
	/**
	 * 保存奖品位置
	 *
	 * @param unknown $eventId
	 *        	活动编号
	 * @param unknown $data
	 *        	奖品数组
	 * @param unknown $tNum
	 *        	奖品数量
	 * @param unknown $sSeat
	 *        	奖品位置最开始数字
	 * @param unknown $eSeat
	 *        	奖品位置最后数字
	 * @param unknown $batch
	 *        	第几批奖品
	 * @param unknown $isFixed
	 *         是否一定中奖       
	 */
	function savePrizes($eventId, $data, $tNum, $sSeat, $eSeat, $batch,$isFixed=0) {
		// 奖品ID排序随机
		shuffle ( $data );
		$res = [ ];
		$str = '';
		
		$save = [];
		$save ['event_id'] = $eventId;
		$save ['prize_count'] = $tNum;
		$save ['start_num'] = $sSeat;
		$save ['sort'] = $batch;
		
		for($j = 0; $j < $tNum; $j ++) {
			if ($isFixed==1){
				//一定中奖，每个位置都有奖品
				$index=$sSeat;
				$sSeat++;
			}else {
				$index = $this->getRand ( $sSeat, $eSeat, $res );
				$res [$index] = 1;
			}
			$str .= $data [$j] . ':' . $index . ',';
		}
		$str = rtrim ( $str, ',' );
		$save ['prize_list'] = $str;
		$save ['end_num'] = $eSeat;
		return $this->insertGetId ( $save );
	}
	// 获取奖品的位置，不能重复
	function getRand($start, $end, $arr) {
		$index = rand ( $start, $end );
		if (isset ( $arr [$index] ) || empty ( $index )) {
			return $this->getRand ( $start, $end, $arr );
		} else {
			return $index;
		}
	}
}