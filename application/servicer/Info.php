<?php

namespace app\servicer;

use app\common\controller\InfoBase;

/**
 * 工作授权插件
 * @author jacy
 */

class Info extends InfoBase
{

    public $info = array(
        'name'          => 'Servicer',
        'title'         => '工作授权',
        'description'   => '关注公众号后，扫描授权二维码，获取工作权限',
        'status'        => 1,
        'author'        => 'jacy',
        'version'       => '0.1',
        'has_adminlist' => 1,
    );

    public function install()
    {
        $install_sql = env('app_path') . '/servicer/install.sql';
        if (file_exists($install_sql)) {
            execute_sql_file($install_sql);
        }
        return true;
    }
    public function uninstall()
    {
        $uninstall_sql = env('app_path') . '/servicer/uninstall.sql';
        if (file_exists($uninstall_sql)) {
            execute_sql_file($uninstall_sql);
        }
        return true;
    }

}
