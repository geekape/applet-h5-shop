<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\home\controller;

use app\common\controller\WebBase;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class Home extends WebBase{

    /* 空操作，用于输出404页面 */
    public function _empty()
    {
        $this->redirect('Index/index');
    }
    // 初始化操作
    function initialize()
    {
        parent::initialize();
    }

    /* 用户登录检测 */
    protected function login()
    {
        /* 用户登录检测 */
        is_login() || $this->error('您还没有登录，请先登录！', U('home/User/login'));
    }
}
