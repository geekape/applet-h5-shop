<?php
namespace app\shop\model;

use app\common\model\Base;

/**
 * Shop模型
 */
class ShopSlideshow extends Base
{

    protected $table = DB_PREFIX . 'shop_slideshow';

    function updateById($id, $data)
    {
        $map['id'] = $id;
        $res = $this->where(wp_where($map))->update($data);
        if ($res) {
            $this->getInfo($id, true);
        }
    }

    function getShopList($wpid)
    {
        $map['wpid'] = WPID;
        $map['is_show'] = 1;
        $list = $this->where(wp_where($map))->select();
        return $list;
    }

    function getShopBanner($wpid)
    {
        $map['wpid'] = WPID;
        $map['is_show'] = 1;
        $list = $this->where(wp_where($map))->select();
        foreach ($list as $k => &$v) {
            $v = $v->toArray();
            $v['img'] = get_cover_url($v['img'], 600, 400);
        }
        return $list;
    }
}
