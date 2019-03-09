<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\home\controller;

use think\Controller;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class UserTag extends Home
{

    public $model = '';

    public function initialize()
    {
        parent::initialize();
        $this->model = $this->getModel('user_tag');
        // dump($this->model);
    }

    // 通用插件的列表模型
    public function lists()
    {
        $map['pbid'] = get_pbid();
        session('common_condition', $map);
        $this->assign('search_url', U('lists', array(
            'mdm' => isset($_GET['mdm']) ? input('mdm') : ''
        )));
        return parent::common_lists($this->model, 'lists');
    }

    // 通用插件的编辑模型
    public function edit()
    {
        $this->checkPost();
        return parent::common_edit($this->model, 0, 'common@base/edit');
    }

    // 通用插件的增加模型
    public function add()
    {
        $this->checkPost();
        return parent::common_add($this->model, 'common@base/add');
    }

    function checkPost()
    {
        if (! IS_POST) {
            return false;
        }
        
        $title = input('title');
        $id = input('id/d', 0);
        $count = D('common/UserTag')->where('title', $title)
            ->where('wpid', WPID)
            ->where('id', '<>', $id)
            ->count();
        if ($count > 0) {
            $this->error('该标签已存在');
        }
    }

    // 通用插件的删除模型
    public function del()
    {
        return parent::common_del($this->model);
    }
}
