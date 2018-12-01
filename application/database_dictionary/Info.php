<?php

namespace app\database_dictionary;

use app\common\controller\InfoBase;

/**
 * DatabaseDictionary应用
 */
class Info extends InfoBase
{
    public $info = array(
        'name'          => 'database_dictionary',
        'title'         => '数据库字典',
        'description'   => '自动生成数据库字典',
        'author'        => '凡星',
        'version'       => '0.1',
        'has_adminlist' => 1,
    );

    //自定义权限规则
    public $auth_rule = [];

    //自定义积分规则
    public $credit_config = [];

    public function reply($dataArr, $keywordArr = [])
    {
        $config = getAddonConfig('database_dictionary'); // 获取后台应用的配置参数
        //dump($config);
    }

    public function install()
    {
        $install_sql = env('app_path') . 'database_dictionary/install.sql';
        if (file_exists($install_sql)) {
            execute_sql_file($install_sql);
        }
        return true;
    }
    public function uninstall()
    {
        $uninstall_sql = env('app_path') . 'database_dictionary/uninstall.sql';
        if (file_exists($uninstall_sql)) {
            execute_sql_file($uninstall_sql);
        }
        return true;
    }
}
