<?php
namespace app\material\controller;

use app\common\controller\WapBase;

/**
 * 素材管理控制器
 */
class Wap extends WapBase
{

    public function news_detail()
    {
        $map['id'] = I('id');
        $info = M('material_news')->where(wp_where($map))->find();
        $this->assign('info', $info);
        
        return $this->fetch();
    }
}
