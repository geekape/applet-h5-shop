<?php
namespace app\shop\model;

use app\common\model\Base;

/**
 * Shop模型
 */
class Slideshow extends Base
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
        $list = $this->where(wp_where($map))->order('sort asc,id asc')->field('img,title,url')->select();
        foreach ($list as &$vo) {
            $vo['img'] = get_cover_url($vo['img']);
        }
        return $list;
    }
}
