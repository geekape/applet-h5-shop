<?php
namespace app\shop\model;

use app\common\model\Base;

/**
 * Shop模型
 */
class GoodsComment extends Base
{

    protected $table = DB_PREFIX . 'shop_goods_comment';

    function getShopComment($goodsId, $update = false)
    {
		$map['is_show'] = 1;
		$map['goods_id'] = $goodsId;
		$map['wpid'] = get_wpid();
		$key = cache_key($map, $this->table);
        $data = S($key);
        if ($data === false || $update) {
            $data = $this->where(wp_where($map))
                ->order('id desc')
                ->select();
            S($key, $data);
        }
        return $data;
    }
}
