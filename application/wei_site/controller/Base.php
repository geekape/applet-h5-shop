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

}
