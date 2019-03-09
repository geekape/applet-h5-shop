<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星
// +----------------------------------------------------------------------
namespace app\admin\controller;

/**
 * 在线应用商店
 */
class Store extends Admin
{
    var $web_info = [];
    public function initialize()
    {
        parent::initialize();

        config('SESSION_PREFIX', 'weiphp_home');
        config('COOKIE_PREFIX', 'weiphp_home_');

        $host_url = isset($_GET['callback']) ? urldecode($_GET ['callback']) : '';
        if ($host_url) {
            $this->assign('host_url', $host_url);
            session('host_url', $host_url);
        }
    }
    function index()
    {
        $_GET ['type'] = isset($_GET['type']) ? $_GET ['type'] : '';
        switch ($_GET ['type']) {
            case 'addon':
                $remote_url = '/index.php?s=/Admin/Store/lists&type=0';
                break;
            case 'template':
                $remote_url = '/index.php?s=/Admin/Store/lists&type=1';
                break;
            case 'material':
                $remote_url = '/index.php?s=/Admin/Store/lists&type=2';
                break;
            case 'diy':
                $remote_url = '/index.php?s=/Admin/Store/lists&type=1';
                break;
            case 'developer':
                $remote_url = '/index.php?s=/home/Developer/myApps';
                break;
            case 'help':
                $remote_url = '/index.php?s=/Admin/Store/help';
                break;
            case 'home':
                $remote_url = '/index.php?s=/Admin/Store/home';
                break;
            case 'recharge':
                $remote_url = '/index.php?s=/Admin/Store/recharge';
                break;
            case 'bug':
                $remote_url = '/index.php?s=/Admin/Store/bug';
                break;
            case 'online_recharge':
                $remote_url = '/index.php?s=/Admin/Store/online_recharge';
                break;
            default:
                $remote_url = '/index.php?s=/Admin/Store/main';
        }

        $this->assign('remote_url', REMOTE_BASE_URL . $remote_url);
        return $this->fetch();
    }
}
