<?php
namespace app\wei_site\model;

use app\common\model\Base;

/**
 * WeiSite模型
 */
class Footer extends Base
{

    protected $table = DB_PREFIX . 'weisite_footer';

    function get_list($map = [])
    {
        isset($map['wpid']) || $map['wpid'] = get_wpid();
        $list = $this->where(wp_where($map))
            ->order('pid asc, sort asc')
            ->select();
        
        return $list;
    }
}
