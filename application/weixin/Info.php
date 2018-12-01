<?php
namespace app\weixin;

use app\common\controller\InfoBase;

/**
 * Weixin应用
 */
class Info extends InfoBase
{

    public $info = array(
        'name'          => 'weixin',
        'title'         => '微信核心模块',
        'description'   => '微信管理模块',
        'author'        => '无名',
        'version'       => '0.1',
        'has_adminlist' => 0,
    );

    public $auth_rule = array(
        'preview' => '活动预览',
        'join'    => '参与抽奖',
        'cat1'    => '预览文件1',
        'cat2'    => '预览文件2',
    );

    public $init_url = [
        [
            'title' => '自定义菜单',
            'url'   => 'weixin/CustomMenu/lists',
        ],
        [
            'title' => '欢迎语',
            'url'   => 'weixin/Wecome/config',
        ],
    ];

    public function reply($dataArr, $keywordArr = [])
    {
        $config = getAddonConfig('Weixin'); // 获取后台插件的配置参数
        // dump($config);
    }

    public function install()
    {
        $install_sql = env('app_path') . 'weixin/install.sql';
        if (file_exists($install_sql)) {
            execute_sql_file($install_sql);
        }
        return true;
    }

    public function uninstall()
    {
        $uninstall_sql = env('app_path') . 'weixin/uninstall.sql';
        if (file_exists($uninstall_sql)) {
            execute_sql_file($uninstall_sql);
        }
        return true;
    }
}
