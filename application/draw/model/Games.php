<?php

namespace app\draw\model;
use app\common\model\Base;


/**
 * 抽奖游戏活动模型
 */
class Games extends Base{
    protected $table = DB_PREFIX.'lottery_games';
    function initialize()
    {
    	parent::initialize();
    	$this->cacheFiledFobit='attend_num,current_draw_num,win_num_list';
    	$this->openCache=true;
    }
    function getAttendNum( $id ){
        $num=D('Draw/DrawFollowLog')->get_user_attend_count($id);
        return $num;
    }
    function updateInfo($id,$data=[]){
//     	$map['id']=$id;
    	$res=$this->save($data,['id'=>$id]);
    	if($res){
    		$this->clearCache($id);
    	}
    	return $res;
    }
    
    public function clearCache($id, $act_type = '', $uid = 0, $more_param = [])
    {
    	//更新活动配置的奖品列表
    	if (is_array($id)){
    		$dao=D('draw/LotteryGamesAwardLink');
    		foreach ($id as $ii){
    			$dao->getGamesAwardlists($ii,true);
    			$this->getInfo($ii, true);
    		}
    	}else{
    		D('draw/LotteryGamesAwardLink')->getGamesAwardlists($id,true);
    		return $this->getInfo($id, true);
    	}
    	
    }
	/**
	 * 保存抽奖排位数,并判断是否生成奖品列表，没有则生成奖品列表
	 * 
	 * @param unknown $id
	 *        	活动id
	 * @return number
	 */
	function saveCurrentDraw($id) {
		if ($id <= 0) {
			return 0;
		}
		$redis = new \think\cache\driver\Redis ();
		// 获取当前第几位抽奖
		$rediesKey = 'rdraw_lottery_' . $id;
		$cNum = $redis->get ( $rediesKey );
		$cNum = intval ( $cNum );
		if (empty ( $cNum )) {
			// 没有数字就查数据表
			$drawGame = $this->where ( 'id', $id )->field ( 'current_draw_num,win_num_list' )->find ();
			$cNum = $drawGame ['current_draw_num'];
			if ($cNum > 0) {
				$redis->set ( $rediesKey, $cNum );
			}
			// 设置已经中奖的列表
			$winList = $redis->get ( 'rdraw_winlists_' . $id );
			if (empty ( $winList ) && ! empty ( $drawGame ['win_num_list'] )) {
				$wList = explode ( ',', $drawGame ['win_num_list'] );
				addWeixinLog($wList,'wlist');
				foreach ( $wList as $vv ) {
					if ($vv >0){
						$winList [$vv] = $vv;
					}
				}
				$redis->set ( 'rdraw_winlists_' . $id, $winList );
			}
		} else {
			// 设置已经中奖的列表
			$winList = $redis->get ( 'rdraw_winlists_' . $id );
			$strList = '';
			if (! empty ( $winList )) {
				$strList = implode ( ',', $winList );
			}
			$this->where ( 'id', $id )->where ( 'current_draw_num', 'neq', $cNum )->update ( [ 
					'current_draw_num' => $cNum,
					'win_num_list' => $strList 
			] );
		}
		// 判断是否生成奖品列表，没有则生成奖品列表
		$this->saveDrawPrizeList ( $id );
	}
	//判断是否生成奖品列表，没有则生成奖品列表
	function saveDrawPrizeList($id){
		$list = D('draw/EventPrizes')->getEventPrize($id);
		if (empty($list)){
			//当前奖品
			$prizeArr = D('draw/LotteryGamesAwardLink')->getAwardCurrentNum($id);
			$info = $this->getInfo($id);
			$allNum =  D('draw/LotteryGamesAwardLink')->getAwardTotalNum($id);
			// 获取是否百分百中奖
			$isFixed = $allNum == $info ['draw_count'] ? 1 : 0;
			D ( 'draw/EventPrizes' )->setPrizeList ( $id, $prizeArr, $info ['draw_count'], $isFixed );
		}
	}
}
