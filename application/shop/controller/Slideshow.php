<?php
namespace app\shop\controller;

use app\shop\controller\Base;

class Slideshow extends Base
{

    var $model;

    function initialize()
    {
        $this->model = $this->getModel('shop_slideshow');
        parent::initialize();
    }

    // 通用插件的列表模型
    public function lists()
    {
        $map['wpid'] = get_wpid();
        session('common_condition', $map);
        $list_data = $this->_get_model_list($this->model);        
        $this->assign($list_data);
        // dump ( $list_data );
        return $this->fetch();
    }

    // 通用插件的编辑模型
    public function edit()
    {
        $data = [];
        if (request()->isPost()) {
            $data = input('post.');
            $data['wpid'] = WPID;
            if (input('post.url')) {
                $res = strstr(input('post.url'), 'http://');
                if (! $res) {
                    $res = strstr(input('post.url'), 'https://');
                }
                if (! $res) {
                    $data['url'] = 'http://' . input('post.url');
                }
            }
        }
        
        return parent::common_edit($this->model, 0, '', $data);
    }

    // 通用插件的增加模型
    public function add()
    {
        $data = [];
        if (request()->isPost()) {
            $data = input('post.');
            $data['wpid'] = WPID;
            if (input('post.url')) {
                $res = strstr(input('post.url'), 'http://');
                if (! $res) {
                    $res = strstr(input('post.url'), 'https://');
                }
                if (! $res) {
                    $data['url'] = 'http://' . input('post.url');
                }
            }
        }
        // dump($this->model);
        return parent::common_add($this->model, '', $data);
    }

    // 通用插件的删除模型
    public function del()
    {
        return parent::common_del($this->model);
    }
}
