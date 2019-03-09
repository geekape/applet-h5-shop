<?php

namespace app\coupon;

use app\common\controller\InfoBase;

/**
 * 优惠券插件
 *
 * @author 凡星
 */
class Info extends InfoBase
{
    public $info = array(
        'name'          => 'Coupon',
        'title'         => '优惠券',
        'description'   => '配合粉丝圈子，打造粉丝互动的运营激励基础',
        'status'        => 1,
        'author'        => '凡星',
        'version'       => '0.1',
        'has_adminlist' => 1,
        'type'          => 0,
    );
    public function install()
    {
        $install_sql = env('app_path') . '/coupon/install.sql';
        if (file_exists($install_sql)) {
            execute_sql_file($install_sql);
        }
        return true;
    }
    public function uninstall()
    {
        $uninstall_sql = env('app_path') . '/coupon/uninstall.sql';
        if (file_exists($uninstall_sql)) {
            execute_sql_file($uninstall_sql);
        }
        return true;
    }
}
