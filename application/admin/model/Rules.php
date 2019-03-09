<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: ouyangessen
// +----------------------------------------------------------------------

namespace app\admin\model;
use app\common\model\Base;

/**
 * 权限管理模型
 */
class Rules extends Base{

    public function getRoleList(){

    }

    public function getTagInfo($id = null){
        $id || $id = input('id');
        $map = array(
            'id' => $id
        );
        $tag = M( 'user_tag' )->where( wp_where($map) )->find();

        return $tag;
    }

}
