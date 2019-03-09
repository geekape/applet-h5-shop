<?php
namespace app\shop\model;

use app\common\model\Base;

/**
 * Shop模型
 */
class Collect extends Base
{

    protected $table = DB_PREFIX . 'shop_collect';

    function getMyCollect($uid, $update = false)
    {
		$key = cache_key('uid:'.$uid, $this->table, 'goods_id,cTime');
        $res = S($key);
        if ($res === false || $update) {
            $info = $this->where('uid', $uid)
                ->field('goods_id,cTime')
                ->order('cTime desc')
                ->select();
            $goodsDao = D('Shop/ShopGoods');
            $res = [];
            foreach ($info as $v) {
                $goods = $goodsDao->getInfo($v['goods_id']);
                if(empty($goods)) continue;

                $goods['collect_time'] = $v['cTime'];
                $res[] = $goods;
            }
            S($key, $res);
        }
        
        return $res;
    }

    function addToCollect($uid, $goods_id)
    {
        $data['uid'] = intval($uid);
        $data['goods_id'] = $goods_id;
        
        $oldData = $this->where(wp_where($data))->find();
        if ($oldData) {
            $this->where(wp_where($data))->delete();
            $res = 0;
        } else {
            $data['cTime'] = NOW_TIME;
            $this->insert($data);
            $res = 1;
        }
        if ($res) {
            $this->getMyCollect($uid, true);
        }
        return $res;
    }
}
