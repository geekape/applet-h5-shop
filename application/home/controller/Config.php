<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\home\controller;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class Config extends Home
{

    public function index()
    {
        if (request()->isPost()) {
            $flag = D('common/PublicConfig')->setConfig(MODULE_NAME, I('post.'));

            if ($flag !== false) {
                $this->success('保存成功', cookie('__forward__'));
            } else {
                $this->error('保存失败');
            }
        }

        return $this->fetch();
    }
}
