<?php
namespace app\shop\model;

use app\common\model\Base;

/**
 * Shop模型
 */
class Track extends Base
{

    protected $table = DB_PREFIX . 'shop_track';

    function getMyTrack($uid)
    {
        $goodsDao = D('Shop/ShopGoods');
        
        $list = $this->where('uid', $uid)
            ->where('wpid', WPID)
            ->field('goods_id,create_at')
            ->order('create_at desc')
            ->select();
        
        $goodsArr = [];
        foreach ($list as $v) {
            if (! isset($goodsArr[$v['goods_id']])) {
                $goods = $goodsDao->getInfo($v['goods_id']);
				if(empty($goods)) continue;
                
                $goodsArr[$v['goods_id']] = [
                    'id' => $goods['id'],
                    'cover' => $goods['cover'],
                    'title' => $goods['title'],
                    'market_price' => $goods['market_price'],
                    'sale_price' => $goods['sale_price'],
                    'view_count' => 1
                ];
            }
        }
        
        $res = [];
        $today = date('Y-m-d');
        foreach ($list as $v) {
            $day = date('Y-m-d', $v['create_at']);
            if ($today == $day) {
                $day = '今日';
            }
            
            if (isset($res[$day][$v['goods_id']])) {
                $res[$day][$v['goods_id']]['view_count'] += 1;
            } else {
                $res[$day][$v['goods_id']] = $goodsArr[$v['goods_id']];
            }
        }
        
        return $res;
    }

    function addToTrack($uid, $goods_id)
    {
        $data['uid'] = $uid;
        $data['goods_id'] = $goods_id;
        $data['create_at'] = NOW_TIME;
        $data['wpid'] = WPID;
        return $this->insert($data);
    }
}
