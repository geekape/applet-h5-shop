<?php
namespace app\shop\controller;

use app\common\controller\WebBase;

class Base extends WebBase
{

    public $wpid;

    public function initialize()
    {
        parent::initialize();
        // 获取当前登录的用户的商城
        $currentShopInfo = M('shop')->where('id', WPID)->find();
        
        if (! $currentShopInfo && (ACTION_NAME != 'summary' && ACTION_NAME != 'add' && ACTION_NAME != 'config')) {
            $this->error('请先增加商城信息',U('Shop/Shop/add', $this->get_param));
        }
        
        $this->assign('wpid', WPID);
        
        $controller = strtolower(CONTROLLER_NAME);
        
        $res['title'] = '商店管理';
        $res['url'] = U('Shop/Shop/lists', $this->get_param);
        $res['class'] = ($controller == 'shop' && ACTION_NAME == "lists") ? 'current' : '';
        $nav[0] = $res;
        
        $nav = [];
        $this->assign('nav', $nav);
        
        define('CUSTOM_TEMPLATE_PATH', env('app_path') . 'shop/view/wap/template');
    }
}
