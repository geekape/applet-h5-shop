<?php

namespace app\real_prize\model;

use app\common\model\Base;

/**
 * RealPrize模型
 */
class RealPrize extends Base
{
    // 素材相关
    function getSucaiList($search = '')
    {
        $map['wpid'] = get_wpid();
        $map['uid'] = intval(session('mid_'.get_pbid()));
        empty($search) || $map ['title'] = array (
                'like',
                "%$search%"
        );
        
        $data_list = $this->where(wp_where($map))->field('id,prize_name')->order('id desc')->paginate();
        $data_list = dealPage($data_list); // paginate
        foreach ($data_list ['list_data'] as &$v) {
            $v ['title'] = $v ['prize_name'];
        }
        
        return $data_list;
    }
    function getPackageData($id)
    {
        $param ['prizeid'] = $id;
        $return ['service_info'] = $info = get_pbid_appinfo();
        $param ['publicid'] = $info ['id'];
        $return ['data'] = $data = $this->getInfo($id);
        // 设置奖品页面领取对应的跳转链接
        $prizetype = $data ['prize_type'];
        if ($prizetype == '0') {
            $return ['jumpurl'] = U("RealPrize/RealPrize/save_address", $param);
        } else {
            $return ['jumpurl'] = U("RealPrize/RealPrize/address", $param);
        }
        
        // 获取奖品类型名称，方便显示
        $return ['tname'] = $prizetype == '0' ? '虚拟物品' : '实体物品';
        // 服务号信息
        $return ['template'] = $template = $data ['template'] == "" ? "default" : $data ['template'];
        
        return $return;
    }
    
    function updatePrizeCount($id, $update = false)
    {
//      $key = cache_key('id:'.$id, DB_PREFIX.'real_prize');
//      $cache = S ( $key );
//      dump($cache);
//      if ($cache===false){
//          $cache = 0;
//      }
//      $info = $this->getInfo ( $id );
//      if (! $cache || $cache >= 2 || $update) {
//          // 更新数据库
//          $this->where( wp_where(array('id'=>$id) ))->setField('prize_count',$info['prize_count']);
//          $cache = 1;
//      } else {
//          // 更新缓存
//          $info ['prize_count'] -= 1;
//          $cache += 1;
//      }
//      S ( $key, $cache);
//      $this->getInfo ( $id, true, $info );
        $info=$this->getInfo($id);
        $info['prize_count']-=1;
        $res=$this->where(wp_where(array('id'=>$id)))->setField('prize_count', $info['prize_count']);
        if ($res) {
            $this->getInfo($id, true);
        }
        return $res;
    }
}
