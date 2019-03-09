<?php

namespace app\draw\model;

use app\common\model\Base;


/**
 * 中奖者信息模型
 */
class LuckyFollow extends Base {
    /*
     * 获取抽奖游戏活动的中奖人列表
     * $uid 不为0 时，则获取个人用户uid 的中奖信息
     */
    function getGamesLuckyLists($gamesId,$uid=0,$state='-1',$aim_table='lottery_games',$limit=0){
        $map['wpid']=get_wpid();
        $map['aim_table']=$aim_table;
        $map['draw_id']=$gamesId;
        if ($uid !=0){
            $map['follow_id']=$uid;
        }
        if ($state != '-1'){
            $map['state']=$state;
        }
        $lists=$this->where( wp_where($map) )->order('id desc')->limit($limit)->select();
        $awardLists=D('Draw/LotteryGamesAwardLink')->getGamesAwardlists($gamesId);
        foreach ($awardLists as $a){
            $awardData[$a['award_id']]=$a;
        }
        foreach ($lists as &$v){
//             $v = $v->toArray();
//             if (isset($awardData[$v['award_id']])){
            	$v['grade']=isset($awardData[$v['award_id']]['grade'])?$awardData[$v['award_id']]['grade']:'';
            	$v['award_name']=isset($awardData[$v['award_id']]['name'])?$awardData[$v['award_id']]['name']:'';
            	$v['img']=isset($awardData[$v['award_id']]['img'])?$awardData[$v['award_id']]['img']:0;
                $v['card_id']=isset($awardData[$v['award_id']]['card_id'])?$awardData[$v['award_id']]['card_id']:0;
                $v['award_type']=isset($awardData[$v['award_id']]['award_type'])?$awardData[$v['award_id']]['award_type']:0;
                $v['coupon_id']=isset($awardData[$v['award_id']]['coupon_id'])?$awardData[$v['award_id']]['coupon_id']:0;
//             }
           /*  $address_id=intval($v['address']);
            if ($address_id){
                $address=D('common/Address' )->getInfo($address_id);
                $v['address']=$address['address'];
                $v['truename']=$address['truename'];
                $v['mobile']=$address['mobile'];
            } */
            $user=getUserInfo($v['follow_id']);
            $v['nickname']=isset($user['nickname'])?$user['nickname']:'';
            $v['headimgurl']=isset($user['headimgurl'])?$user['headimgurl']:'';
            $v['jump_url']='';
            if ($v['award_type']==1){
            	//实物
            	$v['jump_url']=U('get_prize',array('id'=>$v['id']));
            }elseif ($v['award_type']==2){
            	//优惠券
            	$v['jump_url']=U ( 'coupon/Wap/show', array (
            			'id' => $v ['coupon_id'],
            			'sn_id' => $v ['send_aim_id']
            	) );
            }
        }
        
        return $lists;
    }
    
    function getUserAward($id, $update = false, $data = []) {
		$key = cache_key('id:'.$id, DB_PREFIX.'lucky_follow');
        $info = S ( $key );
        if ($info === false || $update) {
            $info = ( array ) (empty ( $data ) ? $this->where('id', $id)->find()->toArray() : $data);
            $award=D('Draw/Award')->where('id',$info['award_id'] )->field('name,img,award_type,explain')->find();
            $info['award_name']=isset($award['name'])?$award['name']:'';
            $info['img']=isset($award['img'])?$award['img']:'';
            $info['award_type']=isset($award['award_type'])?$award['award_type']:0;
            $info['card_id'] = isset($info['card_id'])?$info['card_id']:0;
            $info['explain']=isset($award['explain'])?str_replace(chr(10), '<br/>', $award['explain']):'';
            $map2['games_id']=$info['draw_id'];
            $map2['award_id']=$info['award_id'];
            $map2['wpid']=get_wpid();
            $info['grade']=D('Draw/LotteryGamesAwardLink')->where( wp_where($map2) )->value('grade');
            S ( $key, $info, 86400 );
        }
        return $info;
    }
    
    //判断当天最多中奖人数
    function get_day_winners_count($gamesId,$time){
        $map['draw_id']=$gamesId;
        $map['wpid']=get_wpid();
        $map['aim_table']='lottery_games';
        $map['zjtime']=array('egt',strtotime(time_format($time,'Y-m-d')));
        $data=$this->where( wp_where($map) )->field('count(distinct follow_id) num')->select();
        $uids = $this->where( wp_where($map) )->field('follow_id')->select();
        $uidArr=[];
        foreach ($uids as $uu){
        	$uidArr[$uu['follow_id']]=$uu['follow_id'];
        }
        $return['num']=isset($data[0]['num']) ? $data[0]['num']:0;
        $return['uids']=$uidArr;
        return $return;
    }
    //每人总共中奖次数
    //$time 有值时：每人每天中奖次数
    function get_user_win_count($gamesId,$uid,$time=0){
        $map['draw_id']=$gamesId;
        $map['wpid']=get_wpid();
        $map['aim_table']='lottery_games';
        $map['follow_id']=$uid;
        if ($time !=0){
            $map['zjtime']=array('egt',strtotime(time_format($time,'Y-m-d')));
        }
        
        $data=$this->where( wp_where($map) )->field('sum( num ) totals')->select();
        return intval($data[0]['totals']);
    }
    // 获取各抽奖活动对应奖品已发放的数量
    function getLzwgAwardNum( $event_id,$wpid='',$aim_talbe='lottery_games' ) {
        if ($wpid && $aim_talbe){
            $sql="SELECT award_id, sum( num ) as num FROM `wp_lucky_follow` WHERE draw_id='$event_id' AND wpid='$wpid' and aim_table='$aim_talbe' GROUP BY award_id" ;
        }else{
            $sql="SELECT award_id, sum( num ) as num FROM `wp_lucky_follow` WHERE draw_id='$event_id' GROUP BY award_id" ;
        }
        $info = $this->query ($sql );
        foreach ( $info as $v ) {
            $i [$v ['award_id']] = $v ['num'];
        }
    
        return isset($i)?$i:[];
    }
    //
	
	// 根据奖品id，获取中奖者列表
	function getlistByAwardId($awardid, $update = false) {
		$map ['award_id'] = $awardid;
		$key = cache_key($map, DB_PREFIX.'lucky_follow');
		$info = S ( $key );
		if ($info === false || $update) {			
			$info = ( array ) $this->where ( wp_where( $map ) )->select ();
		}
		S ( $key, $info, 86400 );
		return $info;
	}
	
	// 判断粉丝在同一场次抽奖时抽取到同一个奖品时，奖品数加1
	function getLuckyFollow($sports_id, $award_id, $follow_id, $update = false) {
		$map ['sport_id'] = $sports_id;
		$map ['award_id'] = $award_id;
		$map ['follow_id'] = $follow_id;
		$key = cache_key($map, DB_PREFIX.'lucky_follow');
		$info = S ( $key );
		if ($info === false || $update) {
			$info = $this->where ( wp_where( $map ) )->find ();
		}
		S ( $key, $info, 86400 );
		return $info;
	}
	function setNum( $data ) {
		$info = $this->getLuckyFollow ( $data ['sport_id'], $data ['award_id'], $data ['follow_id'] );
		if (! empty ( $info )) {
			$id = $info ['id'];
			$info ['num'] = $info ['num'] + 1;
			$info ['zjtime'] = NOW_TIME;
			$map ['sport_id'] = $data ['sport_id'];
			$map ['award_id'] = $data ['award_id'];
			$map ['follow_id'] = $data ['follow_id'];
			$res = $this->where ( wp_where( $map ) )->update( $info );
			
			$f ['truename'] = $data ['truename'];
			$f ['mobile'] = $data ['mobile'];
			$followDao = D('common/Follow' );
			$followDao->updateInfo ( $data ['follow_id'], $f );
			// 奖品数量减1
			// D('LotteryPrizeList')->setCount($data['sport_id'],$data['award_id']);
			if ($res!==false) {
				$this->getInfo ( $id, true );
				$this->getlistByAwardId ( $data ['award_id'], true );
				$this->getLuckyFollow ( $data ['sport_id'], $data ['award_id'], $data ['follow_id'], true );
				$this->getUserPrizeData ( $data ['sport_id'], $data ['follow_id'], true );
			}
			return $res;
		}
		return $info;
	}
	function delayAddData($data, $delay = 10) {
		$res = $this->delayAdd ( $data, $delay );
		// $followDao= D('common/Follow' );
		
		// if($data['truename']&&$data['mobile']){
		// $f['truename']=$data['truename'];
		// $f['mobile']=$data['mobile'];
		// $followDao->updateInfo ($data['follow_id'],$f);
		// }else{
		// $award=D('Award')->getInfo($data['award_id']);
		// $followInfo=get_followinfo( $data ['follow_id']);
		// $new_score=$followInfo['score']+intval($award['score']);
		// D('common/Follow' )->updateField($data ['follow_id'],'score',$new_score);
		// //奖品数量减1
		// D('LotteryPrizeList')->setCount($data['sport_id'],$data['award_id']);
		
		// }
		$this->getlistByAwardId ( $data ['award_id'], true );
		$this->getLuckyFollow ( $data ['sport_id'], $data ['award_id'], $data ['follow_id'], true );
		$this->getUserPrizeData ( $data ['sport_id'], $data ['follow_id'], true );
		if(!empty($data['draw_id'])){
			$this->getLzwgUserPrizeData($data['draw_id'], $data['follow_id'],true);
			$this->getLzwgUserAllPrizeData($data['follow_id'],true);
			
		}
		
		return $res;
	}
	function updateInfo($id, $data = []) {
		$map ['id'] = $id;
		$res = $this->where ( wp_where( $map ) )->update( $data );
		if ($res!==false) {
			$info = $this->getInfo ( $id );
			if ($info['draw_id']){
				$this->getLzwgLuckyFollowInfo($id,true);
				$this->getLzwgUserPrizeData($info['draw_id'], $info ['follow_id'],true);
				$this->getLzwgUserAllPrizeData($info['follow_id'],true);
			}else {
				$this->getInfo ( $info ['id'], true );
				$this->getlistByAwardId ( $info ['prizeid'], true );
				$this->getLuckyFollow ( $info ['sportsid'], $info ['prizeid'], $info ['follow_id'], true );
				$this->getUserPrizeData ( $info ['sportsid'], $info ['follow_id'], true );
			}
			
		}
		return $res;
	}
	
	// 获取各活动场次对应奖品已发放的数量
	function getAwardNum( $sport_id ) {
		$info = $this->query ( "SELECT award_id, count(num) as num FROM `wp_lucky_follow` WHERE sport_id='$sport_id' GROUP BY award_id" );
		foreach ( $info as $v ) {
			$i [$v ['award_id']] = $v ['num'];
		}
		
		return $i;
	}
	
	// 获取粉丝在每场活动的擂鼓数
	function getDrumCount($follow_id) {
		$list = $this->query ( "SELECT sports_id, sum( drum_count ) as num FROM `wp_sports_drum` WHERE follow_id='$follow_id' GROUP BY sports_id" );
		foreach ( $list as $v ) {
			$countArr [$v ['sports_id']] = $v ['num'];
		}
		return $countArr;
	}
	// 获取粉丝每场活动中奖数
	function getUserPrizeData($sport_id, $follow_id, $update = false) {
		$map ['follow_id'] = $follow_id;
		$map ['sport_id'] = $sport_id;
		$map['uid']=session('manager_id');
		if (empty($map['uid'])){
			$map['uid']=get_mid();
		}
		$key = cache_key($map, DB_PREFIX.'lucky_follow', 'award_id,state');
		$info = S ( $key );
		if ($info === false || $update) {
			$info = $this->where ( wp_where( $map ) )->field ( 'award_id,state' )->select ();
			$awardDao = D ( 'Draw/Award' );
			foreach ( $info as &$i ) {
				$award = $awardDao->getInfo ( $i ['award_id'] );
				$i = array_merge ( $i, $award );
			}
		}
		S ( $key, $info, 86400 );
		return $info;
	}
	
	
	
	
}
