<?php

namespace app\draw;

use app\common\controller\InfoBase;

/**
 * 比赛抽奖插件
 * @author 凡星
 */

class Info extends InfoBase
{

    public $info = array(
        'name'          => 'Draw',
        'title'         => '抽奖游戏',
        'description'   => '功能主要有奖品设置，抽奖配置和抽奖统计',
        'status'        => 1,
        'author'        => '凡星',
        'version'       => '0.1',
        'has_adminlist' => 1,
        'type'          => 1,
    );

    public function install()
    {
        $install_sql = env('app_path') . '/draw/install.sql';
        if (file_exists($install_sql)) {
            execute_sql_file($install_sql);
        }
        return true;
    }
    public function uninstall()
    {
        $uninstall_sql = env('app_path') . '/draw/uninstall.sql';
        if (file_exists($uninstall_sql)) {
            execute_sql_file($uninstall_sql);
        }
        return true;
    }
}
