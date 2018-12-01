<?php

namespace app\draw\model;
use app\common\model\Base;


/**
 * 各场次抽奖奖品列表模型
 */
class LotteryPrizeList extends Base{
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
            $info['awardarr']=D('Draw/Award')->getInfo($info['sports_id']);
            $info['sports']=D('Sports/Sports')->getInfo($info['sports_id']);
            S ( $key, $info, 86400 );
        }
        return $info;
    }
    
    //根据场次编号获取奖品
    function getList($sportsId,$update=false){
		$map['sports_id']=$sportsId;
		$map['uid']=session('manager_id');
		 if (empty($map['uid'])){
			$map['uid']=get_mid();
		}
		$key = cache_key($map, $this->name);
        $info = S ( $key );
        if ($info === false || $update) {
            $info = ( array )  $this->where( wp_where($map) )->select();
            $awardDao=D('Draw/Award');
            if (count($info)!=0){
                foreach ($info as &$v){
                    $award=$awardDao->getInfo($v['award_id']);
                    $v['awardarr']=$award;
                    $v['award_name']=$award['name'];
                    $v['award_pic']=get_cover_url($award['img']);
                }
            }            
//             $info['awardarr']=M( 'Award' )->getInfo($info['sports_id']);
//             $info['sports']=M( 'Sports/Sports' )->getInfo($info['sports_id']);
            S ( $key, $info, 86400 );
        }
        return $info;
    }
    
    function set($sports_id,$post) {
        $opt_data ['sports_id'] =$sports_id;
        foreach ( $post ['award'] as $key => $opt ) {
            
            if (empty ( $opt ))
                continue;
            $opt_data ['award_id'] = $opt;
            $opt_data ['award_num'] = intval ($post ['order'][$key]);
            if ($key > 0) {
                // 更新选项
                $optIds [] = $map ['id'] = $key;
                $this->where ( wp_where( $map ) )->update( $opt_data );
                $this->getInfo($key,true);
            } else {
                // 增加新选项
                $optIds [] = $this->insertGetId( $opt_data );
            }
            // dump(M()->getLastSql());
        }
        // 删除旧选项
        $map2 ['id'] = array (
        		'not in',
        		$optIds
        );
        $map2 ['sports_id'] = $opt_data ['sports_id'];
        $this->where ( wp_where( $map2 ) )->delete ();
        $this->getList($sports_id,true);
    }
    
    function getCountInfo($sports_id,$award_id, $update = false, $data = []) {
		$map['sports_id']=$sports_id;
		$map['award_id']=$award_id;
		$key = cache_key($map, $this->name);
    	$info = S ( $key );
    	if ($info === false || $update) {
    		$info = ( array ) (empty ( $data ) ? $this->where( wp_where($map) )->find () : $data);
//     		$info['awardarr']=D('Award')->getInfo($info['sports_id']);
//     		$info['sports']=D('Sports/Sports')->getInfo($info['sports_id']);
    		S ( $key, $info, 86400 );
    	}
    	return $info;
    }
    //兑换时奖品数量-1
    function setCount($sports_id,$award_id){
    	$map['sports_id']=$sports_id;
    	$map['award_id']=$award_id;
    	$info=$this->getCountInfo($sports_id, $award_id);
    	$res=$this->where( wp_where($map) )->setDec('award_num');
    	if ($res){
    		$this->getInfo($info['id'],true);
    		$this->getList($sports_id,true);
    		$this->getCountInfo($sports_id, $award_id,true);
    	}
    	return $res;
    }
    //添加数据
    function delayAddData($datas) {
    	$res = $this->insertAll ( $datas);
    	return $res;
    }
    
   
}
