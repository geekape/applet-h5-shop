<?php

namespace app\public_bind;

use app\common\controller\InfoBase;

/**
 * 一键绑定公众号插件
 * @author 凡星
 */

class Info extends InfoBase
{

    public $info = array(
        'name'          => 'PublicBind',
        'title'         => '一键绑定公众号',
        'description'   => '',
        'status'        => 1,
        'author'        => '凡星',
        'version'       => '0.1',
        'has_adminlist' => 0,
        'type'          => 1,
    );

    public function install()
    {
        $install_sql = env('app_path') . '/public_bind/install.sql';
        if (file_exists($install_sql)) {
            execute_sql_file($install_sql);
        }
        return true;
    }
    public function uninstall()
    {
        $uninstall_sql = env('app_path') . '/public_bind/uninstall.sql';
        if (file_exists($uninstall_sql)) {
            execute_sql_file($uninstall_sql);
        }
        return true;
    }
}
