<?php
// +----------------------------------------------------------------------
// | WeiPHP [ 公众号和小程序运营管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.weiphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn> <QQ:203163051>
// +----------------------------------------------------------------------

// 检测环境是否支持可写
define('IS_WRITE', true);
// APP_MODE !== 'sae'

/**
 * 系统环境检测
 *
 * @return array 系统环境数据
 */
function check_env()
{
    $items = array(
        'os' => array(
            '操作系统',
            '不限制',
            '类Unix',
            PHP_OS,
            'success'
        ),
        'php' => array(
            'PHP版本',
            '5.6',
            '5.6+',
            PHP_VERSION,
            'success'
        ),
        'upload' => array(
            '附件上传',
            '不限制',
            '2M+',
            '未知',
            'success'
        ),
        'gd' => array(
            'GD库',
            '2.0',
            '2.0+',
            '未知',
            'success'
        )
    );

    // PHP环境检测
    if ($items['php'][3] < $items['php'][1]) {
        $items['php'][4] = 'error';
        session('error', true);
    }

    // 附件上传检测
    if (@ini_get('file_uploads'))
        $items['upload'][3] = ini_get('upload_max_filesize');

    // GD库检测
    $tmp = function_exists('gd_info') ? gd_info() : [];
    if (empty($tmp['GD Version'])) {
        $items['gd'][3] = '未安装';
        $items['gd'][4] = 'error';
        session('error', true);
    } else {
        $items['gd'][3] = $tmp['GD Version'];
    }
    unset($tmp);

    return $items;
}

/**
 * 目录，文件读写检测
 *
 * @return array 检测数据
 */
function check_dirfile()
{
    $items = array(
        array(
            'dir',
            '可写',
            'success',
            '/public/uploads'
        ),
        array(
            'dir',
            '可写',
            'success',
            '/runtime'
        ),
        array(
            'file',
            '可写',
            'success',
            '/config/database.php'
        )
    );

    foreach ($items as &$val) {
        $item = SITE_PATH . $val[3];
        if ('dir' == $val[0]) {
            if (!is_writable($item)) {
                if (is_dir($item)) {
                    $val[1] = '可读';
                    $val[2] = 'error';
                    session('error', true);
                } else {
                    if (!mkdir($item, 0777, true)) {
                        $val[1] = '不存在';
                        $val[2] = 'error';
                        session('error', true);
                    }
                }
            }
        } else {
            if (file_exists($item)) {
                if (!is_writable($item)) {
                    $val[1] = '不可写';
                    $val[2] = 'error';
                    session('error', true);
                }
            } else {
                if (!touch(dirname($item))) {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
        }
    }

    return $items;
}

/**
 * 函数检测
 *
 * @return array 检测数据
 */
function check_func()
{
    $items = array(
        array(
            'pdo',
            '支持',
            'success',
            '类'
        ),
        array(
            'pdo_mysql',
            '支持',
            'success',
            '模块'
        ),
        array(
            'file_get_contents',
            '支持',
            'success',
            '函数'
        ),
        array(
            'mb_strlen',
            '支持',
            'success',
            '函数'
        ),
        array(
            'curl_init',
            '支持',
            'success',
            '函数'
        ),
        array(
            'finfo_open',
            '支持',
            'success',
            '函数'
        ),
    );
    // array('mime_content_type', '支持', 'success'), //该函数非必须


    foreach ($items as &$val) {
        if (('类' == $val[3] && !class_exists($val[0])) || ('模块' == $val[3] && !extension_loaded($val[0])) || ('函数' == $val[3] && !function_exists($val[0]))) {
            $val[1] = '不支持';
            $val[2] = 'error';
            session('error', true);
        }
    }

    return $items;
}

/**
 * 写入配置文件
 *
 * @param array $config
 *            配置信息
 */
function write_config($config, $auth)
{
    if (is_array($config)) {
        // 读取配置内容
        $conf = file_get_contents(SITE_PATH . '/public/uploads/database.tpl');

        // 替换配置项
        foreach ($config as $name => $value) {
            if (is_array($value)) $value = var_export($value, true);
            $conf = str_replace("[{$name}]", $value, $conf);
        }

        $conf = str_replace('[auth]', $auth, $conf);

        // 写入应用配置文件
        if (!IS_WRITE) {
            return '由于您的环境不可写，请复制下面的配置文件内容覆盖到相关的配置文件，然后再登录后台。<p>' . realpath(SITE_PATH) . '/config/database.php</p>
            <textarea name="" style="width:650px;height:185px">' . $conf . '</textarea>';
        } else {
            if (file_put_contents(SITE_PATH . '/config/database.php', $conf)) {
                show_msg('配置文件写入成功');
            } else {
                show_msg('配置文件写入失败！', 'error');
                session('error', true);
            }
            return '';
        }
    }
}

/**
 * 创建数据表
 *
 * @param resource $db
 *            数据库连接资源
 */
function create_tables($db, $prefix = '')
{
    // 读取SQL文件
    $sql = file_get_contents(SITE_PATH . '/public/uploads/install.sql');
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);

    // 替换表前缀
//    $orginal = config('ORIGINAL_TABLE_PREFIX');
//    $sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);

    // 开始安装
    show_msg('开始安装数据库...');
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value))
            continue;
        //show_msg($value . '...sql');
        if (substr($value, 0, 12) == 'CREATE TABLE') {
            $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
            $msg = "创建数据表{$name}";
            if (false !== $db->execute($value)) {
                show_msg($msg . '...成功');
            } else {
                show_msg($msg . '...失败！', 'error');
                session('error', true);
            }
        } else {
            $db->execute($value);
        }
    }
//增加触发器
    $dbconfig = session('db_config');
    $dbms = 'mysql';     //数据库类型
    $host = $dbconfig['hostname']; //数据库主机名
    $dbName = $dbconfig['database'];    //使用的数据库
    $dbport = $dbconfig['hostport'];
    $dsn = "$dbms:host=$host;port=$dbport;dbname=$dbName";
    $new_db = new \PDO($dsn, $dbconfig['username'], $dbconfig['password']);

    $new_db->exec('DROP TRIGGER IF EXISTS `add`');
    $new_db->exec("CREATE TRIGGER `add` BEFORE INSERT ON `wp_shop_goods_stock` FOR EACH ROW set new.stock_active = new.stock - new.lock_count");
    $new_db->exec('DROP TRIGGER IF EXISTS `save`');
    $new_db->exec("CREATE TRIGGER `save` BEFORE UPDATE ON `wp_shop_goods_stock` FOR EACH ROW set new.stock_active = new.stock - new.lock_count");
}

function register_administrator($db, $prefix, $admin, $auth)
{
    show_msg('开始注册创始人帐号...');

    $sql = "INSERT INTO `[PREFIX]user` (`uid`, `nickname`, `login_name`, `password`, `reg_time`, `reg_ip`, `last_login_time`, `last_login_ip`, `status`,`is_init`,`is_audit`) VALUES " . "('1', '[NAME]', '[NAME]', '[PASS]', '[TIME]', '[IP]', 0, 0, 1,1,1)";

    $password = think_weiphp_md5($admin['password'], $auth);
    $sql = str_replace(array(
        '[PREFIX]',
        '[NAME]',
        '[PASS]',
        '[TIME]',
        '[IP]'
    ), array(
        $prefix,
        $admin['username'],
        $password,
        NOW_TIME,
        get_client_ip(1)
    ), $sql);
    // 执行sql
    show_msg($sql . '...sql222');
    $res = $db->execute($sql);
    // dump($sql);
    // dump($res);

    show_msg('创始人帐号注册完成！');
}

/**
 * 更新数据表
 *
 * @param resource $db
 *            数据库连接资源
 * @author lyq <605415184@qq.com>
 */
function update_tables($db, $prefix = '')
{
    // 读取SQL文件
    $sql = file_get_contents(SITE_PATH . '/public/uploads/update.sql');
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);

    // 替换表前缀
    $sql = str_replace(" `wp_", " `{$prefix}", $sql);

    // 开始安装
    show_msg('开始升级数据库...');
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value))
            continue;
        if (substr($value, 0, 12) == 'CREATE TABLE') {
            $name = preg_replace("/^CREATE TABLE `(\w+)` .*/s", "\\1", $value);
            $msg = "创建数据表{$name}";
            if (false !== $db->execute($value)) {
                show_msg($msg . '...成功');
            } else {
                show_msg($msg . '...失败！', 'error');
                session('error', true);
            }
        } else {
            if (substr($value, 0, 8) == 'UPDATE `') {
                $name = preg_replace("/^UPDATE `(\w+)` .*/s", "\\1", $value);
                $msg = "更新数据表{$name}";
            } else
                if (substr($value, 0, 11) == 'ALTER TABLE') {
                    $name = preg_replace("/^ALTER TABLE `(\w+)` .*/s", "\\1", $value);
                    $msg = "修改数据表{$name}";
                } else
                    if (substr($value, 0, 11) == 'INSERT INTO') {
                        $name = preg_replace("/^INSERT INTO `(\w+)` .*/s", "\\1", $value);
                        $msg = "写入数据表{$name}";
                    }
            if (($db->execute($value)) !== false) {
                show_msg($msg . '...成功');
            } else {
                show_msg($msg . '...失败！', 'error');
                session('error', true);
            }
        }
    }
}

/**
 * 及时显示提示信息
 *
 * @param string $msg
 *            提示信息
 */
function show_msg($msg, $class = '')
{
    echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
    flush();
    ob_flush();
}

/**
 * 生成系统AUTH_KEY
 */
function build_auth_key()
{
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars .= '`~!@#$%^&*()_+-=[]{};:"|,.<>/?';
    $chars = str_shuffle($chars);
    return substr($chars, 0, 40);
}
