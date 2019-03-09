<?php
namespace app\sing_in;

use app\common\controller\InfoBase;

/**
 * 签到插件
 * 
 * @author 淡然
 *         QQ: 9585216
 */
class Info extends InfoBase
{

    public $info = array(
        'name' => 'SingIn',
        'title' => '签到',
        'description' => '粉丝每天签到可以获得积分。',
        'status' => 1,
        'author' => '淡然',
        'version' => '1.11',
        'has_adminlist' => 1,
        'type' => 1
    );

    public function install()
    {
        $install_sql = env('app_path') . '/sing_in/install.sql';
        if (file_exists($install_sql)) {
            execute_sql_file($install_sql);
        }
        return true;
    }

    public function uninstall()
    {
        $uninstall_sql = env('app_path') . '/sing_in/uninstall.sql';
        if (file_exists($uninstall_sql)) {
            execute_sql_file($uninstall_sql);
        }
        return true;
    }
}
