<?php

namespace app\wei_site;

use app\common\controller\InfoBase;

/**
 * 微官网插件
 * @author 凡星
 */

class Info extends InfoBase
{

    public $info = array(
        'name'          => 'WeiSite',
        'title'         => '微官网',
        'description'   => '微3G网站、支持分类管理，文章管理、底部导航管理、微信引导信息配置，微网站统计代码部署。同时支持首页多模板切换、信息列表多模板切换、信息详情模板切换、底部导航多模板切换。并配置有详细的模板二次开发教程',
        'status'        => 1,
        'author'        => '凡星',
        'version'       => '0.1',
        'has_adminlist' => 0,
        'type'          => 1,
    );

    public function install()
    {
        $install_sql = env('app_path') . '/wei_site/install.sql';
        if (file_exists($install_sql)) {
            execute_sql_file($install_sql);
        }
        return true;
    }
    public function uninstall()
    {
        $uninstall_sql = env('app_path') . '/wei_site/uninstall.sql';
        if (file_exists($uninstall_sql)) {
            execute_sql_file($uninstall_sql);
        }
        return true;
    }

}
