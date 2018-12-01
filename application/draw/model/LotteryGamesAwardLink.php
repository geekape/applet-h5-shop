<?php

namespace app\draw\model;

use app\common\model\Base;

/**
 * 抽奖游戏活动奖品列表模型
 */
class LotteryGamesAwardLink extends Base
{

    //获取抽奖活动设置的奖品
    public function getGamesAwardlists($gamesId, $update = false)
    {
		$map['games_id'] = $gamesId;
		$map['wpid']    = get_wpid();
		$key = cache_key($map, $this->name);
        $info = S($key);
        if (empty($info) || $update ) {
            $info            = $this->where(wp_where($map))->order('sort asc')->select()->toArray();
            S($key, $info, 86400);
        }
        $awardDao        = D('draw/Award');
        foreach ($info as &$v) {
        	$award = $awardDao->getInfo($v['award_id'], true);
        	if (empty($award)){
        		$award['name']='';
        		$award['img']=0;
        		$award['score']=0;
        		$award['award_type']=0;
        		$award['price']=0;
        		$award['explain']='';
        		$award['count']=0;
        		$award['coupon_id']=0;
        		$award['money']=0;
        		$award['award_title']='';
        		$award['type_name']='';
        		$award['img_url']='';
        	}else{
        		unset($award['id']);
        		unset($award['sort']);
        	}
        	$v = array_merge($v, $award);
        }
        return $info;
    }
    /**
     * 获取活动奖品总数
     * @param unknown $gamesId
     */
    function getAwardTotalNum($gamesId) {
		$num = $this->where ( 'games_id', $gamesId )->sum ( 'num' );
		return intval ( $num );
	}
    
    /**
     * 获取活动中当前的奖品数量
     * @param unknown $eventId
     * @return unknown 返回当前活动每个奖品对应的数量，用于生成 抽奖时奖品的排位列表
     */
    function getAwardCurrentNum($eventId){
    	$wpid    = get_wpid();
    	$allNum=$this->where('games_id',$eventId)->where('wpid',$wpid)->field('award_id,num')->select();
    	
    	// 各奖品抽中发放数量
    	$hasDraw = D('Draw/LuckyFollow')->getLzwgAwardNum($eventId, $wpid);
    	
    	$awardArr = [];
    	foreach ($allNum as $vo){
    		if (isset($hasDraw[$vo['award_id']])){
    			$vo['num']=intval($vo['num'])-intval($hasDraw[$vo['award_id']]);
    		}
    		$awardArr[]=array(
    				'prize_id'=>$vo['award_id'],
    				'prize_num'=>$vo['num']
    		);
    	}
    	return $awardArr;
    }

}
