<?php
namespace app\shop\model;

use app\common\model\Base;

/**
 * Shop模型
 */
class GoodsStoreLink extends Base
{

    protected $table = DB_PREFIX . 'goods_store_link';

    // 保存商品门店
    function set_store($goods_id, $event_type, $post)
    {
        if (! isset($post['goods_store_title']))
            return false;
        
        foreach ($post['goods_store_title'] as $key => $opt) {
            if (empty($opt))
                continue;
            $opt_data['goods_id'] = $goods_id;
            $opt_data['wpid'] = get_wpid();
            $opt_data['store_id'] = $opt;
            $opt_data['event_type'] = $event_type;
            if ($key > 0) {
                // 更新选项
                $optIds[] = $key;
                $this->where('id', $key)->update($opt_data);
            } else {
                // 增加新选项
                $optIds[] = $this->insertGetId($opt_data);
            }
            unset($opt_data);
            // dump(M()->getLastSql());
        }
        if (! empty($optIds)) {
            // 删除旧选项
            $map2['id'] = array(
                'not in',
                $optIds
            );
            $map2['goods_id'] = $goods_id;
            $map2['event_type'] = $event_type;
            $this->where(wp_where($map2))->delete();
        }
    }

    function get_store($goods_id, $event_type)
    {
        $stores = $this->where('goods_id', $goods_id)
            ->where('event_type', $event_type)
            ->column('id');
        return $stores;
    }

    function getOrderStores($data)
    {
        $goods_ids = [];
        foreach ($data['lists'] as $type => $goods_lists) {
            if ($type == 1)
                continue;
            
            foreach ($goods_lists as $goods) {
                $goods_ids[$goods['id']] = $goods['id'];
            }
        }
        if (empty($goods_ids)) {
            return '';
        }
        
        if ($data['event_type'] == SECKILL_EVENT_TYPE) {
            $goods_config = D('seckill/SeckillGoods')->whereIn('id', $goods_ids)->column('is_all_store', 'id');
        } elseif ($data['event_type'] == COLLAGE_EVENT_TYPE) {
            $goods_config = D('collage/CollageGoods')->whereIn('id', $goods_ids)->column('is_all_store', 'id');
        } elseif ($data['event_type'] == HAGGLE_EVENT_TYPE) {
            $goods_config = D('haggle/Haggle')->whereIn('id', $goods_ids)->column('is_all_store', 'id');
        } else {
            $goods_config = D('shop/ShopGoods')->whereIn('id', $goods_ids)->column('is_all_store', 'id');
        }
        
        foreach ($goods_config as $id => $is_all_store) {
            if ($is_all_store == 0) { // 全部门店
                unset($goods_ids[$id]);
            }
        }
        
        if (empty($goods_ids)) {
            return '-1'; // 展示全部门店
        }
        
        $store_lists = $this->whereIn('goods_id', $goods_ids)
            ->where('wpid', WPID)
            ->where('event_type', $data['event_type'])
            ->field('goods_id,store_id')
            ->select();
        
        $arr = [];
        foreach ($store_lists as $s) {
            $arr[$s['goods_id']][] = $s['store_id'];
        }
        
        $inArr = array_pop($arr);
        
        if (! empty($arr)) {
            foreach ($arr as $gid => $a) {
                $inArr = array_intersect($inArr, $a);
            }
        }
        
        if (empty($inArr)) {
            return '';
        } else {
            return implode('_', $inArr);
        }
    }
}
