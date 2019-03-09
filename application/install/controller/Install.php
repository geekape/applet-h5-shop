<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------
namespace app\install\controller;

use app\common\controller\Base;

class Install extends Base
{

    public function initialize()
    {
        parent::initialize();

        if (file_exists(SITE_PATH . '/public/uploads/install.lock')) {
            $this->error('已经成功安装了WeiPHP，请不要重复安装!');
        }
    }
    function index(){
        return $this->fetch();
    }
    // 安装第一步，检测运行所需的环境设置
    public function step1()
    {
        session('error', false);

        // 环境检测
        $env = check_env();

        // 目录文件读写检测
        if (IS_WRITE) {
            $dirfile = check_dirfile();
            $this->assign('dirfile', $dirfile);
        }

        // 函数检测
        $func = check_func();

        session('step', 1);

        $this->assign('env', $env);
        $this->assign('func', $func);
        return $this->fetch();
    }

    // 安装第二步，创建数据库
    public function step2($db = null, $admin = null)
    {
        if (request()->isPost()) {
            // 检测管理员信息
            if (!is_array($admin) || empty($admin[0]) || empty($admin[1])) {
                $this->error('请填写完整管理员信息');
            } else
                if ($admin[1] != $admin[2]) {
                    $this->error('确认密码和密码不一致');
                } else {
                    $info = [];
                    list ($info['username'], $info['password'], $info['repassword']) = $admin;
                    // 缓存管理员信息
                    session('admin_info', $info);
                }

            // 检测数据库配置
            if (!is_array($db) || empty($db[0]) || empty($db[1]) || empty($db[2]) || empty($db[4])) {
                $this->error('请填写完整的数据库配置');
            } else {
                $DB = [];
                list ($DB['hostname'], $DB['database'], $DB['username'], $DB['password'], $DB['hostport']) = $db;
                $DB['params'] = array(
                    \PDO::ATTR_CASE => \PDO::CASE_NATURAL
                );
                // 缓存数据库配置
                $DB['type'] = 'mysql';
                $DB['prefix'] = 'wp_';
                session('db_config', $DB);

                // 创建数据库
                $dbname = $DB['database'];
                unset($DB['database']);
                $dbbase = db('', $DB);
                $sql = "CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8mb4";
                $res = $dbbase->execute($sql);
                if (!$res)
                    $this->error('该数据库已存在，创建失败');
            }

            // 跳转到数据库安装页面
            $this->redirect('step3');
        } else {
            $config = config('database.');

            $this->assign('config', $config);

            if (session('update')) {
                session('step', 2);
                return $this->fetch('update');
            } else {
                session('error') && $this->error('环境检测没有通过，请调整环境后重试！');

                $step = session('step');
                if ($step != 1 && $step != 2) {
                    $this->redirect('step1');
                }

                session('step', 2);

                return $this->fetch();
            }
        }
    }

    // 安装第三步，安装数据表，创建配置文件
    public function step3()
    {
        if (session('step') != 2) {
            $this->redirect('step2');
        }

        $content = $this->fetch();
        echo $content;
        flush();
        ob_flush();

        $this->doInstall();
    }

    public function doInstall()
    {
		if(function_exists('set_time_limit')){
			set_time_limit(0);
		}
        if (session('update')) {
            $db = db();
            // 更新数据表
            update_tables($db, DB_PREFIX);
        } else {
            // 连接数据库
            $dbconfig = session('db_config');
            // dump($dbconfig);exit;
            $db = db('', $dbconfig);
            // 创建数据表
            create_tables($db, $dbconfig['prefix']);
            // 注册创始人帐号
            $auth = build_auth_key();
            $admin = session('admin_info');
            register_administrator($db, $dbconfig['prefix'], $admin, $auth);

            // 创建配置文件
            $conf = write_config($dbconfig, $auth);
            session('config_file', $conf);
        }

        if (session('error')) {
            show_msg(session('error'));
        } else {
            session('step', 3);
            $url = U('install/Index/complete');
            echo "<script type=\"text/javascript\">window.location.href='{$url}'</script>";
        }
    }
}
