<?php

namespace app\credit\controller;
use app\common\controller\WebBase;

//PC运营管理端的控制器
class Credit extends WebBase{
    public $model = '';
    public function initialize(){
        parent::initialize();
        $this->model = $this->getModel('credit_config');
    }
    public function lists(){
        $list_data = $this->_get_model_list($this->model );
        $this->assign($list_data);
        $this->assign('add_button',0);
        $this->assign('del_button',0);
        $this->assign('search_button',0);
        $this->assign('check_all',0);
        return $this->fetch('lists');
    }
}
