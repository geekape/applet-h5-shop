<?php

namespace app\shop\model;

use app\common\model\Base;
 

/**
 * 分销数据模型
 */
class Distribution extends Base {
	protected $table = DB_PREFIX. 'shop_distribution_user';
	
	// 获取分销商用户信息
	function getDistributionUser($uid, $update = false, $data = []) {
		$map ['is_delete'] = 0;
		// $map['level'] = array('gt',0);
		$map ['wpid'] = get_wpid ();
		$map ['uid'] = $uid;
		$key = cache_key($map, $this->table);
		$info = S ( $key );
		if ($info === false || $update) {
			if (empty ( $data )) {
				$info = $this->where ( wp_where( $map ) )->find ();
			} else {
				$info = $data;
			}
			
			S ( $key, $info );
		}
		return $info;
	}
	//修改信息
	function do_update($uid,$data){
		$map['uid']=$uid;
		$map['wpid'] = get_wpid();
		$res = $this->where ( wp_where( $map ) )->update ($data);
		if ($res){
			$this->getDistributionUser($uid,true);
		}
	}
	//统计用户的成员数 
	function get_duser_member($uid,$isCount=1,$is_down=0){
		$duser = $this->getDistributionUser ( $uid );
		$lmap ['uid|upper_user'] = $uid;
		$lmap ['level'] = array (
				'neq',
				0 
		);
		$uids = M( 'shop_user_level_link' )->where ( wp_where( $lmap ) )->column( 'uid' );
		if ($duser ['level'] == 1 && $is_down ==0) {
			if (!empty($uids)){
				$lmap ['uid|upper_user'] = array (
						'in',
						$uids
				);
				$uids = M( 'shop_user_level_link' )->where ( wp_where( $lmap ) )->column( 'uid' );
			}
		}
		// 排除用户本身
		foreach ( $uids as $k => $u ) {
			if ($u != $duser ['uid']) {
				$uidArr [] = intval($u);
			}
		}
		
		return $isCount == 1 ? count ( $uidArr ) : $uidArr;
	}
	//获取分销商用户带来的客户
	function get_follow_member($uid,$isCount=1){
		//获取客户
		$sfMap['duid'] = $uid;
		$sfMap['wpid'] = get_wpid();
		$sfData = M( 'shop_statistics_follow' )->where( wp_where($sfMap) )->field('uid,ctime,openid,wpid')->select();
		return $isCount == 1? count ($sfData) :$sfData;
	}
		// 获取分销商所获的佣金
	function get_duser_profit($uid, $level = 0) {
		$userArr = $this->get_duser_member ( $uid, 0 );
		$userArr [] = $uid;
		if ($level != 0) {
			$map ['upper_level'] = $level;
		}
		$data = null;
		if (! empty ( $userArr )) {
			$map ['upper_user'] = array (
					'in',
					$userArr 
			);
			$data = M( 'shop_distribution_profit' )->where ( wp_where( $map ) )->group ( 'upper_user' )->column ( "sum( profit ) profit", 'upper_user');
		}
		return $data;
	}
		// 统计用户收益来源
	function get_total_profit_from( $uid ) {
		$map ['upper_user'] = $uid;
		$map ['wpid'] = get_wpid ();
		$data = M( 'shop_distribution_profit' )->where ( wp_where( $map ) )->select ();
		$self_profit = 0;
		$other_profit = 0;
		foreach ( $data as $vo ) {
			if ($vo ['duser'] == $uid) {
				$self_profit += $vo ['profit'];
			} else {
				$other_profit += $vo ['profit'];
			}
		}
		$totals = $self_profit + $other_profit;
		$returnData ['coustom_profit'] =  $self_profit ;
		$returnData ['team_profit'] =  $other_profit ;
		$returnData ['total_profit'] =  $totals ;
		return $returnData;
	}
	//获取已提现金额
	function get_duser_cashout($uid){
		$map['uid']=$uid;
		$map['wpid']=get_wpid();
		$map['cashout_status']=array('neq',2);//0:处理中 1:成功 2：失败
		$data=M( 'shop_cashout_log' )->where( wp_where($map) )->field('sum( cashout_amount ) amount')->select();
		
		return floatval($data[0]['amount']);
	}
	//获取分销用户的分销订单
	function get_duser_order($uid){
		$map['upper_user'] = $uid;
		$map['wpid'] = get_wpid();
		$myUser = $this ->getDistributionUser($uid);
		$data = M( 'shop_distribution_profit' ) ->where( wp_where($map) )->select();
		$levelName = $this->_get_level_name();
		$orderDao = D ( 'Shop/Order');
		$orderCount =[];
		$totalProfit =0;
		$config = get_info_config ( 'Shop' );
		$lev [1] = strstr ( $config ['level1'], '%' ) ? floatval ( $config ['level1'] ) * 0.01 : $config ['level1'];
		$lev [2] = strstr ( $config ['level2'], '%' ) ? floatval ( $config ['level2'] ) * 0.01 : $config ['level2'];
		$lev [3] = strstr ( $config ['level3'], '%' ) ? floatval ( $config ['level3'] ) * 0.01 : $config ['level3'];
		
		foreach ($data as &$v){
			$goodsArr=[];
			$totalProfit += $v['profit'];
			$duser = $this ->getDistributionUser($v['duser']);
			$v['duser_order'] = $levelName[$duser['level']];
			$v['duser_name']=get_userinfo($duser['uid'],'truename');
			
			if (isset($orderCount[$duser['level']])){
				$orderCount[$duser['level']]++;
			}else{
				$orderCount[$duser['level']]=1;
			}
			
			$orderInfo = $orderDao ->getInfo($v['order_id']);
			$goods = $orderInfo['goods'];
			foreach ($goods as $gInfo){
				$gg=[];
				$gg['goods_profit'] = $gInfo['distribution_price'] * $gInfo['num'] * $lev[$myUser['level']];
				$gg['goods_img'] = $gInfo['imgs_url'][0];
				$gg['goods_title'] =$gInfo['title'];
				if (!empty ( $gInfo ['spec_option_ids'] )){
				    $pricedata = $gInfo ['sku_data'] [$gInfo ['spec_option_ids']];
				    if (floatval ( $pricedata ['sale_price'] ) > 0) {
				       $gg['goods_price'] = $pricedata['sale_price'];
				    } else {
				       $gg['goods_price']= $pricedata['market_price'];
				    }
				}else{
					if (floatval($gInfo['sale_price']) >0){
						$gg['goods_price'] = $gInfo['sale_price'];
					}else{
						$gg['goods_price']= $gInfo['market_price'];
					}
				}
				$goodsArr[]=$gg;
			}
			$v['order_number']=$orderInfo['order_number'];
			$v['goods_list']=$goodsArr;
			$v['total_price'] = $orderInfo ['total_price'];
			$v['follow_name'] = get_username($v['uid']);
		}
		$returnData['datas']=$data;
		$returnData['total_profit']=$totalProfit;
		$returnData['order_count']=$orderCount;
		$returnData['total_count']=count($data);
		return $returnData;
	}
	
	//处理分销用户获取的拥金  (新 分销方法)
	function do_distribution_profit($id) {
		$orderinfo = D ( 'Shop/Order' )->getInfo ( $id );
		// 带来的粉丝必须是关注公众号的
		if (! empty ( $orderinfo )) {
			$wpid = get_wpid ();
			// $price = $orderinfo['total_price'];
			$price = 0; // 可分佣价格
			          // 获取分佣价格
			          // dump($orderinfo);
			$goodsData = $orderinfo ['goods'];
			foreach ( $goodsData as $gg ) {
				$price += $gg ['distribution_price'] * $gg['num'] ;
			}
			if ($price <= 0) {
				return false;
			}
			$config = get_info_config ( 'Shop' );
			//分销商与购买用户是否固定关系
			if ($config ['is_fixed_shopper'] == 1) {
				$sfMap ['uid'] = $orderinfo ['uid'];
				$sfMap ['wpid'] = get_wpid ();
				$managerId = M( 'shop_statistics_follow' )->where ( wp_where( $sfMap ) )->value ( 'duid' );
				$duser = $this->getDistributionUser ( $managerId );
			} else {
				// 分销用户
				$duser = $this->getDistributionUser ( $orderinfo ['manager_id'] );
			}
			if (empty ( $duser ) || $duser ['level'] <= 0 || $duser ['is_audit'] != 1) {
				return false;
			}
			// 三级分销 ：父shopid
			$data ['uid'] = $orderinfo ['uid'];
			$data ['wpid'] = $wpid;
			$data ['ctime'] = NOW_TIME;
			$data ['order_id'] = $id;
			$data ['profit_shop'] = $orderinfo ['wpid'];
			$data ['duser'] =$duser['uid'];
			if ($config ['need_distribution'] == 1) {
				$maxProfit = $config ['max_money'];
				$level = $config ['level'];
				// 获取三级分销比例
				$lev [0] = strstr ( $config ['level1'], '%' ) ? floatval ( $config ['level1'] ) * 0.01 : $config ['level1'];
				$lev [1] = strstr ( $config ['level2'], '%' ) ? floatval ( $config ['level2'] ) * 0.01 : $config ['level2'];
				$lev [2] = strstr ( $config ['level3'], '%' ) ? floatval ( $config ['level3'] ) * 0.01 : $config ['level3'];
				// 获取分销用户关联的分销商
				$lmap ['uid'] = $duser ['uid'];
				$lmap ['wpid'] = $wpid;
				$lmap ['level'] = array (
						'neq',
						0 
				);
				$userLink = M( 'shop_user_level_link' )->where ( wp_where( $lmap ) )->find ();
				if ($duser ['level'] == 3) {
					$upmap ['uid'] = $userLink ['upper_user'];
					$upmap ['level'] = 2;
					$upUser = M( 'shop_user_level_link' )->where ( wp_where( $upmap ) )->value ( 'upper_user' );
					$userArr [] = $upUser; // 一级分销商
					$userArr [] = $userLink ['upper_user']; // 二级
					$userArr [] = $duser ['uid']; // 三级
				} else if ($duser ['level'] == 2) {
					$userArr [] = $userLink ['upper_user'];
					$userArr [] = $userLink ['uid'];
				} else {
					$userArr [] = $userLink ['uid'];
				}
				switch ($level) {
					case '2' :
						// 二级分销
						if (count ( $userArr ) == 3) {
							$userArr [0] = $userArr [1];
							$userArr [1] = $userArr [2];
							unset ( $userArr [2] );
						}
						foreach ( $userArr as $key => $v ) {
							if (! empty ( $lev [$key] ) && $v) {
								$data ['distribution_percent'] = $lev [$key];
								$data ['profit'] = $price * $lev [$key];
								if ($maxProfit) {
									$data ['profit'] > $maxProfit && $data ['profit'] = $maxProfit;
								}
								$data ['upper_user'] = $v;
								$data ['upper_level'] = $key + 1;
								$datas [] = $data;
								$moneyData [$v] = $data ['profit'];
							}
						}
						break;
					case '1' :
						$data ['distribution_percent'] = $lev [0];
						$data ['profit'] = $price * $lev [0];
						if ($maxProfit) {
							$data ['profit'] > $maxProfit && $data ['profit'] = $maxProfit;
						}
						$data ['upper_user'] = $duser ['uid'];
						$data ['upper_level'] = $duser ['level'];
						$datas [] = $data;
						$moneyData [$duser ['uid']] = $data ['profit'];
						break;
					default :
						// 三级分销
						foreach ( $userArr as $key => $v ) {
							if (! empty ( $lev [$key] ) && $v) {
								$data ['distribution_percent'] = $lev [$key];
								$data ['profit'] = $price * $lev [$key];
								if ($maxProfit) {
									$data ['profit'] > $maxProfit && $data ['profit'] = $maxProfit;
								}
								$data ['upper_user'] = $v;
								$data ['upper_level'] = $key + 1;
								$datas [] = $data;
								$moneyData [$v] = $data ['profit'];
							}
						}
						break;
				}
				if (!empty($datas)){
					M( 'shop_distribution_profit' )->insertAll($datas);
					foreach ($moneyData as $k => $pro){
						$uu = $this->getDistributionUser($k);
						$save['profit_money'] = $pro + $uu['profit_money'];
						$this->do_update($k, $save);
					}
				}
				
			}
		}
	}
	
	//获取分销级别类型名称
	function _get_level_name(){
		$config=get_info_config('Shop');
	
		$typeName=[];
		switch ($config['level']){
			case 1:
				if ($config['level_name_1']){
					$typeName[1]=$config['level_name_1'];
				}else{
					$typeName[1]='一级分销商';
				}
				break;
			case 2:
				if ($config['level_name_1']){
					$typeName[1]=$config['level_name_1'];
				}else{
					$typeName[1]='一级分销商';
				}
				if ($config['level_name_2']){
					$typeName[2]=$config['level_name_2'];
				}else{
					$typeName[2]='二级分销商';
				}
				break;
			case 3:
				if ($config['level_name_1']){
					$typeName[1]=$config['level_name_1'];
				}else{
					$typeName[1]='一级分销商';
				}
				if ($config['level_name_2']){
					$typeName[2]=$config['level_name_2'];
				}else{
					$typeName[2]='二级分销商';
				}
				if ($config['level_name_3']){
					$typeName[3]=$config['level_name_3'];
				}else{
					$typeName[3]='三级分销商';
				}
				break;
			default:
				$typeName=null;
				break;
		}
		return $typeName;
	}
	
}
