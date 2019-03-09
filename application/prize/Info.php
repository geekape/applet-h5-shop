<?php
namespace app\prize;

use app\common\controller\InfoBase;

/**
 * 奖品库应用
 */
class Info extends InfoBase
{

    public $info = array(
        'name'          => 'prize',
        'title'         => '奖品库',
        'description'   => '支持的奖品有优惠券，现金红包，实物，中奖码，积分，微信卡券',
        'author'        => '凡星',
        'version'       => '0.1',
        'has_adminlist' => "",
    );

    public $auth_rule = [];

    public function install()
    {
        $install_sql = env('app_path') . 'prize/install.sql';
        if (file_exists($install_sql)) {
            execute_sql_file($install_sql);
        }
        return true;
    }

    public function uninstall()
    {
        $uninstall_sql = env('app_path') . 'prize/uninstall.sql';
        if (file_exists($uninstall_sql)) {
            execute_sql_file($uninstall_sql);
        }
        return true;
    }
}
