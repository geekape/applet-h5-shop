<?php

namespace app\wei_site\controller;

use app\common\controller\WebBase;

class Base extends WebBase
{
    protected $config;
    public function initialize()
    {
        parent::initialize();
        $this->assign('nav', null);
//         $this->getNavs();
        
        $config = getAddonConfig('wei_site');
        if (!empty($config)) {
            $config['cover_url']      = get_cover_url($config['cover']);
            $config['background_arr'] = explode(',', $config['background']);
            $config['background_id']  = $config['background_arr'][0];
            $config['background']     = get_cover_url($config['background_id']);
        } else {
            $config['cover_url']      = "";
            $config['background_arr'] = "";
            $config['background_id']  = "";
            $config['background']     = "";
        }
        $this->config = $config;
        $this->assign('config', $config);
        // dump($config);
        // dump(get_wpid());

        // 定义模板常量
        $act   = strtolower(ACTION_NAME);
        $temp  = isset($config['template_' . $act]) ? $config['template_' . $act] : '';
        $act   = ucfirst($act);
        $title = isset($config['title']) ? $config['title'] : '';
        $this->assign('page_title', $title);
        define('CUSTOM_TEMPLATE_PATH', __ROOT__ . '/wei_site/template');
    }

    function getNavs(){
        $controller = strtolower(CONTROLLER_NAME);
    	$res ['title'] = '微信回复';
    	$res ['url'] = U('wei_site/WeiSite/config');
    	$res ['class'] =  $controller == 'weisite' ? 'current' : '';
    	$nav [] = $res;
    	$res ['title'] = '首页配置';
    	$res ['url'] = U('wei_site/Template/index');
    	$res ['class'] = $controller == 'template' ? 'current' : '';
    	$nav [] = $res;
    	$this->assign('nav', $nav);
    }
}
