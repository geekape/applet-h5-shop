<?php

namespace app\credit;

use app\common\controller\InfoBase;

/**
 * Credit应用
 */
class Info extends InfoBase
{
    public $info = array(
        'name'          => 'credit',
        'title'         => '积分等级',
        'description'   => '这是一个临时描述',
        'author'        => '无名',
        'version'       => '0.1',
        'has_adminlist' => "1",
    );

    public function reply($dataArr, $keywordArr = [])
    {
        $config = getAddonConfig('credit'); // 获取后台插件的配置参数
        //dump($config);
    }

    public function install()
    {
        $install_sql = env('app_path') . 'credit/install.sql';
        if (file_exists($install_sql)) {
            execute_sql_file($install_sql);
        }
        return true;
    }
    public function uninstall()
    {
        $uninstall_sql = env('app_path') . 'credit/uninstall.sql';
        if (file_exists($uninstall_sql)) {
            execute_sql_file($uninstall_sql);
        }
        return true;
    }
}
