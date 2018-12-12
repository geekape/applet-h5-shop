<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 凡星 <weiphp@weiphp.cn>
// +----------------------------------------------------------------------
use think\Db;

// 应用公共文件
function E($msg, $code = 0)
{
    exception($msg, $code);
}

function G($start, $end = '', $dec = 4)
{
    return debug($start, $end, $dec);
}

function L($name = null, $value = null)
{
    return lang($name, $value);
}

function I($name, $default = '', $filter = null, $datas = null)
{
    return input($name, $default, $filter);
}

function M($name = '')
{
    return db($name, [], false);
}

function D($name = '', $layer = 'model')
{

    // 兼容大写的应用名的写法
    if (strpos($name, '/')) {
        list ($module, $name) = explode('/', $name, 2);
        $module_name = parse_name($module);

        // auto_make_model($name, $module_name);

        $name = $module_name . '/' . $name;
    } else {
        // auto_make_model($name);
    }

    return model($name, $layer);
}

function check_model($name, $module_name = '')
{
    // 兼容大写的应用名的写法
    if (strpos($name, '/')) {
        list ($module, $name) = explode('/', $name, 2);
        $module_name = parse_name($module);
    }

    $name = parse_name($name, 1);
    // dump($name);
    empty($module_name) && $module_name = parse_name(MODULE_NAME);

    // 兼容没有对应的model文件的写法
    $path = SITE_PATH . '/application/' . $module_name . '/model/' . $name . '.php';
    $common = SITE_PATH . '/application/common/model/' . $name . '.php';

    if (!file_exists($path) && !file_exists($common)) {
        return false;
    } else {
        return true;
    }
}

function auto_make_model($name, $module_name = '')
{
    // dump($name);
    $name = parse_name($name, 1);
    // dump($name);
    empty($module_name) && $module_name = parse_name(MODULE_NAME);

    // 兼容没有对应的model文件的写法
    $path = SITE_PATH . '/application/' . $module_name . '/model/' . $name . '.php';
    $common = SITE_PATH . '/application/common/model/' . $name . '.php';

    if (!file_exists($path) && !file_exists($common)) {
        $content = <<<str
<?php

namespace app\\{$module_name}\model;
use app\common\model\Base;


class {$name} extends Base{

}
str;
        file_put_contents($path, $content);
    }
}

function A($name, $layer = '', $level = 0)
{
    return controller($name, $layer);
}

function R($url, $vars = [], $layer = '')
{
    return action($url, $vars, $layer);
}

function U($url = '', $vars = '', $suffix = true, $domain = true)
{
    if (strpos($url, '?pbid=') === false && strpos($url, '&pbid=') === false) {
        $pbid = defined('PBID') ? PBID : 0;
        if ($pbid == 0) {
            $pbid = input('pbid/d', 0);
        }
        if ($pbid == 0) {
            $pbid = intval(session('pbid'));
        }

        if (empty($vars)) {
            $vars = 'pbid=' . $pbid;
        } elseif (is_array($vars)) {
            isset($vars['pbid']) || $vars['pbid'] = $pbid;
        } elseif (false === strpos($vars, 'pbid=')) {
            $vars .= '&pbid=' . $pbid;
        }
    }
    return url($url, $vars, $suffix, $domain);
}

function W($name, $data = [])
{
    return widget($name, array(
        $data
    ));
}

function S($name, $value = '', $options = null)
{
    return cache($name, $value, $options);
}

// OneThink常量定义
const ONETHINK_PLUGIN_PATH = './Plugins/';

// 系统插件
/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/*
 * 格式化接口返回数据
 */
function api_return($errcode = 0, $data = [], $msg = '加载数据成功！')
{
    $msg = $errcode == 0 ? $msg : '加载数据失败！';
    $return = [
        'errcode' => $errcode,
        'msg' => $msg,
        'data' => $data
    ];
    return json_url($return);
}

/**
 * 检测用户是否登录
 *
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login()
{
    $user = session('user_auth');
    if (empty($user)) {
        $cookie_uid = cookie('user_id');
        if (!empty($cookie_uid)) {
            $uid = think_decrypt($cookie_uid);
            $userinfo = getUserInfo($uid);
            D('common/User')->autoLogin($userinfo);

            $user = session('user_auth');
        }
    }
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 检测当前用户是否为运营管理员
 *
 * @return boolean true-管理员，false-非管理员
 */
function is_admin($uid = null)
{
    $uid = is_null($uid) ? is_login() : $uid;

    // 是否为超级管理员
    $res = is_administrator($uid);
    if ($res) {
        return true;
    }

    // 是否为运营管理员
    $res = M('user_tag_link')->alias('a')
        ->join(DB_PREFIX . 'user_tag b', 'a.tag_id=b.id')
        ->where('uid', $uid)
        ->column('b.type');
    if (!empty($res) && in_array(1, $res)) {
        return true;
    }
    return false;
}

/**
 * 检测当前用户是否为系统超级管理员
 *
 * @return boolean true-管理员，false-非管理员
 */
function is_administrator($uid = null)
{
    $uid = is_null($uid) ? is_login() : $uid;
    return $uid && (intval($uid) == config('user_administrator'));
}

/*
 * public_interface
 * 设置和获取公众号接口权限
 */
function public_interface($auth, $mod = 'weixin_public', $pbid = null)
{
    $pbid = get_pbid($pbid);
    if (!$pbid) {
        return false;
    }
    // 规则
    $auth_rule = D('common/AuthRule')->getByNameMod($auth, $mod);
    // 权限列表
    $info = D('common/publics')->getInfoById($pbid);
    if (!$info) {
        return false;
    }

    $rule = S('user_tag_rule_type' . $info['type']);

    if ($rule === false) {
        $rule = M('user_tag')->where('wtype', $info['type'])->value('rule');
        $rule = explode(',', $rule);
        S('user_tag_rule_type' . $info['type'], $rule);
    }

    if (in_array($auth_rule['id'], $rule)) {
        return true;
    }
    return false;
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 *
 * @param string $str
 *            要分割的字符串
 * @param string $glue
 *            分割符
 * @return array
 */
function str2arr($str, $glue = ',')
{
    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 *
 * @param array $arr
 *            要连接的数组
 * @param string $glue
 *            分割符
 * @return string
 */
function arr2str($arr, $glue = ',')
{
    if (empty($arr)) {
        return '';
    }

    return implode($glue, $arr);
}

/**
 * 字符串截取，支持中文和其他编码
 *
 * @access public
 * @param string $str
 *            需要转换的字符串
 * @param string $start
 *            开始位置
 * @param string $length
 *            截取长度
 * @param string $charset
 *            编码格式
 * @param string $suffix
 *            截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr")) {
        $slice = mb_substr($str, $start, $length, $charset);
    } elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }

    return $suffix && $str != $slice ? $slice . '...' : $slice;
}

/**
 * 方法增强，根据$length自动判断是否应该显示...
 * 字符串截取，支持中文和其他编码
 * QQ:125682133
 *
 * @access public
 * @param string $str
 *            需要转换的字符串
 * @param string $start
 *            开始位置
 * @param string $length
 *            截取长度
 * @param string $charset
 *            编码格式
 * @param string $suffix
 *            截断显示字符
 * @return string
 */
function msubstr_local($str, $start = 0, $length, $charset = "utf-8")
{
    if (function_exists("mb_substr")) {
        $slice = mb_substr($str, $start, $length, $charset);
    } elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);

        $slice = join("", array_slice($match[0], $start, $length));
    }
    return (strlen($str) > strlen($slice)) ? $slice . '...' : $slice;
}

/**
 * 系统加密方法
 *
 * @param string $data
 *            要加密的字符串
 * @param string $key
 *            加密密钥
 * @param int $expire
 *            过期时间 单位 秒
 * @return string
 */
function think_encrypt($data, $key = '', $expire = 0)
{
    $key = md5(empty($key) ? config('DATA_AUTH_KEY') : $key);

    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }

        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1))) % 256);
    }
    return str_replace(array(
        '+',
        '/',
        '='
    ), array(
        '-',
        '_',
        ''
    ), base64_encode($str));
}

/**
 * 系统解密方法
 *
 * @param string $data
 *            要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key
 *            加密密钥
 * @return string
 */
function think_decrypt($data, $key = '')
{
    $key = md5(empty($key) ? config('DATA_AUTH_KEY') : $key);
    $data = str_replace(array(
        '-',
        '_'
    ), array(
        '+',
        '/'
    ), $data);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);

    if ($expire > 0 && $expire < time()) {
        return '';
    }
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) {
            $x = 0;
        }

        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数据签名认证
 *
 * @param array $data
 *            被认证的数据
 * @return string 签名
 */
function data_auth_sign($data)
{
    // 数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); // 排序
    $code = http_build_query($data); // url编码并生成query字符串
    $sign = sha1($code); // 生成签名
    return $sign;
}

/**
 * 对查询结果集进行排序
 *
 * @access public
 * @param array $list
 *            查询结果
 * @param string $field
 *            排序的字段名
 * @param array $sortby
 *            排序类型
 *            asc正向排序 desc逆向排序 nat自然排序
 * @return array
 *
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = [];
        foreach ($list as $i => $data) {
            $refer[$i] = isset($data[$field]) ? $data[$field] : '';
        }

        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = $list[$key];
        }

        return $resultSet;
    }
    return false;
}

/**
 * 把返回的数据集转换成Tree
 *
 * @param array $list
 *            要转换的数据集
 * @param string $pid
 *            parent标记字段
 * @param string $level
 *            level标记字段
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = [];
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = [];
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 *
 * @param array $tree
 *            原来的树
 * @param string $child
 *            孩子节点的键
 * @param string $order
 *            排序显示的键，一般是主键 升序排列
 * @param array $list
 *            过渡用的中间数组，
 * @return array 返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = [])
{
    if (is_array($tree)) {
        $refer = [];
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 树形列表
 *
 * @param array $list
 *            数据库原始数据
 * @param array $res_list
 *            返回的结果数组
 * @param int $pid
 *            上级ID
 * @param int $level
 *            当前处理的层级
 */
function list_tree($list, &$res_list, $pid = 0, $level = 0)
{
    foreach ($list as $k => $v) {
        if (intval($v['pid']) != $pid) {
            continue;
        }

        if ($level > 0) {
            $space = '';
            for ($i = 1; $i < $level; $i++) {
                $space .= '──';
            }
            $v['title'] = '├──' . $space . $v['title'];
        }

        $v['level'] = $level;
        $res_list[] = $v;
        unset($list[$k]);

        list_tree($list, $res_list, $v['id'], $level + 1);
    }
}

/**
 * 格式化字节大小
 *
 * @param number $size
 *            字节数
 * @param string $delimiter
 *            数字和单位分隔符
 * @return string 格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{
    $units = array(
        'B',
        'KB',
        'MB',
        'GB',
        'TB',
        'PB'
    );
    for ($i = 0; $size >= 1024 && $i < 5; $i++) {
        $size /= 1024;
    }

    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 */
function set_redirect_url($url)
{
    cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 *
 * @return string 跳转页URL
 */
function get_redirect_url()
{
    $url = cookie('redirect_url');
    return empty($url) ? __APP__ : $url;
}

/**
 * 处理插件钩子
 *
 * @param string $hook
 *            钩子名称
 * @param mixed $params
 *            传入参数
 * @return void
 */
function hook($hook, $params = [])
{
    // \think\facade\Hook ::listen ( $hook, $params );
}

/**
 * 获取插件类的类名
 *
 * @param strng $name
 *            插件名
 */
function get_addon_class($name)
{
    $name = parse_name($name, 0);
    $class = "app\\{$name}\\Info";
    if (!class_exists($class)) {
        // 将大写字母转为小写
        $sNameArr = preg_split('/(?=[A-Z])/', $name);
        $fdName = '';
        foreach ($sNameArr as $v) {
            if (empty($v)) {
                continue;
            }

            $fdName .= '_' . strtolower($v);
        }
        $fdName = substr($fdName, 1);
        $class = "plugins\\{$fdName}\\{$name}Addon";
    }
    return $class;
}

/**
 * 获取插件类的配置文件数组
 *
 * @param string $name
 *            插件名
 */
function get_info_config($name)
{
    $class = get_addon_class($name);

    if (class_exists($class)) {
        $addon = new $class();
        return $addon->getConfig($name);
    } else {
        return [];
    }
}

/**
 * 时间戳格式化
 *
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = null, $format = 'Y-m-d H:i')
{
    if (empty($time)) {
        return '';
    }

    $time = $time === null ? NOW_TIME : intval($time);
    return date($format, $time);
}

function day_format($time = null)
{
    return time_format($time, 'Y-m-d');
}

function hour_format($time = null)
{
    return time_format($time, 'H:i');
}

function time_offset($time = null)
{
    if (empty($time)) {
        return '00:00';
    }

    $mod = $time % 60;
    $min = ($time - $mod) / 60;

    $mod < 10 && $mod = '0' . $mod;
    $min < 10 && $min = '0' . $min;

    return $min . ':' . $mod;
}

/**
 * 友好的时间显示
 *
 * @param int $sTime
 *            待显示的时间
 * @param string $type
 *            类型. normal | mohu | full | ymd | other
 * @param string $alt
 *            已失效
 * @return string
 */
function friendlyDate($sTime, $type = 'normal', $alt = 'false')
{
    if (!$sTime) {
        return '';
    }

    // sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime = NOW_TIME;
    $dTime = $cTime - $sTime;
    $dDay = intval(date("z", $cTime)) - intval(date("z", $sTime));
    // $dDay = intval($dTime/3600/24);
    $dYear = intval(date("Y", $cTime)) - intval(date("Y", $sTime));
    // normal：n秒前，n分钟前，n小时前，日期
    if ($type == 'normal') {
        if ($dTime < 60) {
            // if ($dTime < 10) {
            return '刚刚'; // by yangjs
            // } else {
            // return intval ( floor ( $dTime / 10 ) * 10 ) . "秒前";
            // }
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
            // 今天的数据.年份相同.日期相同.
        } elseif ($dYear == 0 && $dDay == 0) {
            return intval($dTime / 3600) . "小时前";
            // return '今天' . date ( 'H:i', $sTime );
        } elseif ($dYear == 0) {
            return date("m-d H:i", $sTime);
        } else {
            return date("Y-m-d H:i", $sTime);
        }
    } elseif ($type == 'mohu') {
        if ($dTime < 60) {
            return $dTime . "秒前";
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
        } elseif ($dTime >= 3600 && $dDay == 0) {
            return intval($dTime / 3600) . "小时前";
        } elseif ($dDay > 0 && $dDay <= 7) {
            return intval($dDay) . "天前";
        } elseif ($dDay > 7 && $dDay <= 30) {
            return intval($dDay / 7) . '周前';
        } elseif ($dDay > 30) {
            return intval($dDay / 30) . '个月前';
        }
        // full: Y-m-d , H:i:s
    } elseif ($type == 'full') {
        return date("Y-m-d , H:i:s", $sTime);
    } elseif ($type == 'ymd') {
        return date("Y-m-d", $sTime);
    } else {
        if ($dTime < 60) {
            return $dTime . "秒前";
        } elseif ($dTime < 3600) {
            return intval($dTime / 60) . "分钟前";
        } elseif ($dTime >= 3600 && $dDay == 0) {
            return intval($dTime / 3600) . "小时前";
        } elseif ($dYear == 0) {
            return date("Y-m-d H:i:s", $sTime);
        } else {
            return date("Y-m-d H:i:s", $sTime);
        }
    }
}

// 获取用户信息
function getUserInfo($uid, $field = '')
{
    $info = D('common/User')->getUserInfo($uid);

    if (empty($field)) {
        return $info;
    }

    if (isset($info[$field])) {
        return $info[$field];
    } else {
        return false;
    }
}

function getUserBaseInfo($data)
{
    $user = getUserInfo($data['uid']);
    $data['headimgurl'] = $user['headimgurl'];
    $data['nickname'] = isset($user['nickname']) ? $user['nickname'] : '匿名';
    return $data;
}

/**
 * 根据用户ID获取用户名
 *
 * @param integer $uid
 *            用户ID
 * @return string 用户名
 */
function get_username($uid = 0)
{
    return get_nickname($uid);
}

function get_userface($uid = 0)
{
    return getUserInfo($uid, $field = 'headimgurl');
}

/**
 * 以下几个获取用户信息都是兼容旧系统的写法
 */
function get_nickname($uid = 0)
{
    if (empty($uid)) {
        return '';
    }

    return getUserInfo($uid, $field = 'nickname');
}

function get_truename($uid)
{
    return getUserInfo($uid, $field = 'truename');
}

function get_userinfo($uid, $field = '')
{
    return getUserInfo($uid, $field);
}

function get_followinfo($id, $field = '')
{
    return getUserInfo($id, $field);
}

function get_mult_userinfo($uid)
{
    return getUserInfo($uid);
}

function get_mult_username($uids)
{
    is_array($uids) || $uids = explode(',', $uids);

    $uids = array_filter($uids);
    if (empty($uids)) {
        return;
    }

    foreach ($uids as $uid) {
        $name = get_truename($uid);
        if ($name) {
            $nameArr[] = $name;
        }
    }

    return implode(', ', $nameArr);
}

/**
 * 获取分类信息并缓存分类
 *
 * @param integer $id
 *            分类ID
 * @param string $field
 *            要获取的字段名
 * @return string 分类信息
 */
function get_category($id, $field = null)
{
    static $list;

    /* 非法分类ID */
    if (empty($id) || !is_numeric($id)) {
        return '';
    }

    /* 读取缓存数据 */
    if (empty($list)) {
        $list = S('sys_category_list');
    }

    /* 获取分类名称 */
    if (!isset($list[$id])) {
        $cate = M('Category')->where('id', $id)->find();
        if (!$cate || 1 != $cate['status']) {
            // 不存在分类，或分类被禁用
            return '';
        }
        $list[$id] = $cate;
        S('sys_category_list', $list); // 更新缓存
    }
    return is_null($field) ? $list[$id] : $list[$id][$field];
}

/* 根据ID获取分类标识 */
function get_category_name($id)
{
    return get_category($id, 'name');
}

/* 根据ID获取分类名称 */
function get_category_title($id)
{
    return get_category($id, 'title');
}

/**
 * 获取顶级模型信息
 */
function get_top_model($model_id = null)
{
    $map['status'] = 1;
    if (!is_null($model_id)) {
        $map['id'] = array(
            'neq',
            $model_id
        );
    }
    $model = M('model')->where(wp_where($map))
        ->field(true)
        ->select();
    foreach ($model as $value) {
        $list[$value['id']] = $value;
    }
    return $list;
}

/**
 * 解析UBB数据
 *
 * @param string $data
 *            UBB字符串
 * @return string 解析为HTML的数据
 */
function ubb($data)
{
    return $data;
}

/**
 * 记录行为日志，并执行该行为的规则
 *
 * @param string $action
 *            行为标识
 * @param string $model
 *            触发行为的模型名
 * @param int $record_id
 *            触发行为的记录id
 * @param int $user_id
 *            执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null)
{

    // 参数检查
    if (empty($action) || empty($model) || empty($record_id)) {
        return '参数不能为空';
    }
    if (empty($user_id)) {
        $user_id = is_login();
    }

    // 查询行为,判断是否执行
    $action_info = M('Action')->getByName($action);
    if ($action_info['status'] != 1) {
        return '该行为被禁用或删除';
    }

    // 插入行为日志
    $data['action_id'] = $action_info['id'];
    $data['user_id'] = $user_id;
    $data['action_ip'] = ip2long(get_client_ip());
    $data['model'] = $model;
    $data['record_id'] = $record_id;
    $data['create_time'] = NOW_TIME;

    // 解析日志规则,生成日志备注
    if (!empty($action_info['log'])) {
        if (preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)) {
            $log['user'] = $user_id;
            $log['record'] = $record_id;
            $log['model'] = $model;
            $log['time'] = NOW_TIME;
            $log['data'] = array(
                'user' => $user_id,
                'model' => $model,
                'record' => $record_id,
                'time' => NOW_TIME
            );
            foreach ($match[1] as $value) {
                $param = explode('|', $value);
                if (isset($param[1])) {
                    $replace[] = call_user_func($param[1], $log[$param[0]]);
                } else {
                    $replace[] = $log[$param[0]];
                }
            }
            $data['remark'] = str_replace($match[0], $replace, $action_info['log']);
        } else {
            $data['remark'] = $action_info['log'];
        }
    } else {
        // 未定义日志规则，记录操作url
        $data['remark'] = '操作url：' . $_SERVER['REQUEST_URI'];
    }

    M('ActionLog')->insert($data);

    if (!empty($action_info['rule'])) {
        // 解析行为
        $rules = parse_action($action, $user_id);

        // 执行行为
        $res = execute_action($rules, $action_info['id'], $user_id);
    }
}

/**
 * 解析行为规则
 * 规则定义 table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 * field->要操作的字段；
 * condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 * rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 * cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 * max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 *
 * @param string $action
 *            行为id或者name
 * @param int $self
 *            替换规则里的变量为执行用户的id
 * @return boolean array: ， 成功返回规则数组
 * @author huajie <banhuajie@163.com>
 */
function parse_action($action = null, $self)
{
    if (empty($action)) {
        return false;
    }

    // 参数支持id或者name
    if (is_numeric($action)) {
        $map = array(
            'id' => $action
        );
    } else {
        $map = array(
            'name' => $action
        );
    }

    // 查询行为信息
    $info = M('Action')->where(wp_where($map))->find();
    if (!$info || $info['status'] != 1) {
        return false;
    }

    // 解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
    $rules = $info['rule'];
    $rules = str_replace('{$self}', $self, $rules);
    $rules = explode(';', $rules);
    $return = [];
    foreach ($rules as $key => &$rule) {
        $rule = explode('|', $rule);
        foreach ($rule as $k => $fields) {
            $field = empty($fields) ? [] : explode(':', $fields);
            if (!empty($field)) {
                $return[$key][$field[0]] = $field[1];
            }
        }
        // cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
        if (!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])) {
            unset($return[$key]['cycle'], $return[$key]['max']);
        }
    }

    return $return;
}

/**
 * 执行行为
 *
 * @param array $rules
 *            解析后的规则数组
 * @param int $action_id
 *            行为id
 * @param array $user_id
 *            执行的用户id
 * @return boolean false 失败 ， true 成功
 * @author huajie <banhuajie@163.com>
 */
function execute_action($rules = false, $action_id = null, $user_id = null)
{
    if (!$rules || empty($action_id) || empty($user_id)) {
        return false;
    }

    $return = true;
    foreach ($rules as $rule) {
        // 检查执行周期
        $map = array(
            'action_id' => $action_id,
            'user_id' => $user_id
        );
        $map['create_time'] = array(
            'gt',
            NOW_TIME - intval($rule['cycle']) * 3600
        );
        $exec_count = M('ActionLog')->where(wp_where($map))->count();
        if ($exec_count > $rule['max']) {
            continue;
        }

        // 执行数据库操作
        $Model = M(ucfirst($rule['table']));
        $field = $rule['field'];
        $res = $Model->where(wp_where($rule['condition']))->setField($field, array(
            'exp',
            $rule['rule']
        ));

        if (!$res) {
            $return = false;
        }
    }
    return $return;
}

// 基于数组创建目录和文件
function create_dir_or_files($files)
{
    foreach ($files as $key => $value) {
        if (substr($value, -1) == '/') {
            mkdir($value);
        } else {
            @file_put_contents($value, '');
        }
    }
}

if (!function_exists('array_column')) {

    function array_column(array $input, $columnKey, $indexKey = null)
    {
        $result = [];
        if (null === $indexKey) {
            if (null === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
        return $result;
    }
}

/**
 * 获取属性信息并缓存
 */
function get_model_attribute($model)
{
    $obj = D('common/Models')->getFileInfo($model);
    if ($obj == false) return [];
    return $obj->fields;
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5'); 调用Admin模块的User接口
 *
 * @param string $name
 *            格式 [模块名]/接口名/方法名
 * @param array|string $vars
 *            参数
 */
function api($name, $vars = [])
{
    $array = explode('/', $name);
    $method = array_pop($array);
    $classname = array_pop($array);
    $module = $array ? array_pop($array) : 'app\common';
    $callback = '\\' . $module . '\\api\\' . $classname . '::' . $method;

    if (is_string($vars)) {
        parse_str($vars, $vars);
    }
    return call_user_func_array($callback, $vars);
}

/**
 * 根据条件字段获取指定表的数据
 *
 * @param mixed $value
 *            条件，可用常量或者数组
 * @param string $condition
 *            条件字段
 * @param string $field
 *            需要返回的字段，不传则返回整个数据
 * @param string $table
 *            需要查询的表
 * @author huajie <banhuajie@163.com>
 */
function get_table_field($value = null, $condition = 'id', $field = null, $table = null)
{
    if (empty($value) || empty($table)) {
        return false;
    }

    // 拼接参数
    $map[$condition] = $value;
    $info = M(ucfirst($table))->where(wp_where($map));
    if (empty($field)) {
        $info = $info->field(true)->find();
    } else {
        $info = $info->value($field);
    }
    return $info;
}

/**
 * 获取文档封面图片
 *
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据 或者 指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_cover($cover_id, $field = null)
{
    if (empty($cover_id)) {
        return false;
    }
    $map['status'] = 1;
    $map['id'] = $cover_id;

    $key = cache_key($map, DB_PREFIX . 'picture');
    $picture = S($key);
    if (!$picture) {
        $picture = M('Picture')->where(wp_where($map))->find();
        S($key, $picture, 86400);
    }

    if (empty($picture)) {
        return '';
    }

    return empty($field) ? $picture : $picture[$field];
}

function get_cover_url($cover_id, $width = '', $height = '')
{
    $info = get_cover($cover_id);
    if ($width || $height) {
        $path = '';
        if ($info['url']) {
            $path = mk_rule_image($info['url'], $width, $height);
        } else {
            if (empty($info['path'])) {
                return '';
            }

            $path = mk_rule_image($info['path'], $width, $height);
        }
        return $path;
    } else {
        if (isset($info['url']) && $info['url']) {
            return $info['url'];
        }

        $url = isset($info['path']) ? $info['path'] : '';
        if (empty($url)) {
            return '';
        }

        $url = SITE_URL . $url;
        $url = str_replace('public./', '', $url);
        return $url;
    }
}

function get_square_url($cover_id, $width = '')
{
    $info = get_cover($cover_id);

    if ($width > 0) {
        $thumb = "?imageView2/1/w/{$width}";
    }
    if ($info['url']) {
        return $info['url'] . $thumb;
    }

    $url = $info['path'];
    if (empty($url)) {
        return '';
    }

    return SITE_URL . $url . $thumb;
}

// 兼容旧方法
function get_picture_url($cover_id)
{
    return get_cover_url($cover_id);
}

function get_img_html($cover_id)
{
    $url = get_cover_url($cover_id);

    return url_img_html($url, $cover_id);
}

function url_img_html($url, $cover_id = '')
{
    if (empty($url)) {
        return '';
    }

    return '<img class="list_img" src="' . $url . '" data-id="' . $cover_id . '" >';
}

function get_file_info($id)
{
    if (empty($id)) {
        return false;
    }

    $key = cache_key('id:' . $id, DB_PREFIX . 'file');
    $file = S($key);

    if (!$file) {
        $file = M('File')->where([
            'id' => $id
        ])->find();
        S($key, $file, 86400);
    }

    if (empty($file)) {
        return false;
    }

    if (isset($file['url']) && $file['url']) {
        $url = $file['url'];
    } elseif (empty($file['savepath'])) {
        $url = '';
    } else {
        $url = SITE_URL . '/uploads/download/' . $file['savepath'] . $file['savename'];
    }

    $file['url'] = $url;

    return $file;
}

/* 根据id获取fiel路径 */
function get_file_url($id)
{
    $info = get_file_info($id);
    if (empty($info)) {
        return '';
    }

    return $info['url'];
}

function get_file_html($id)
{
    $info = get_file_info($id);
    if (empty($info)) {
        return '';
    }

    $url = $info['url'];
    return "<a href='{$url}' >{$info['name']}</a>";
}

/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 *
 * @param number $pos
 *            推荐位的值
 * @param number $contain
 *            指定推荐位
 * @return boolean true 包含 ， false 不包含
 * @author huajie <banhuajie@163.com>
 */
function check_document_position($pos = 0, $contain = 0)
{
    if (empty($pos) || empty($contain)) {
        return false;
    }

    // 将两个参数进行按位与运算，不为0则表示$contain属于$pos
    $res = $pos & $contain;
    if ($res !== 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * 获取数据的所有子孙数据的id值
 *
 * @author 朱亚杰 <xcoolcc@gmail.com>
 */
function get_stemma($pids, &$model, $field = 'id')
{
    $collection = [];

    // 非空判断
    if (empty($pids)) {
        return $collection;
    }

    if (is_array($pids)) {
        $pids = trim(implode(',', $pids), ',');
    }
    $result = $model->field($field)
        ->whereIn('pid', $pids)
        ->select();
    $child_ids = array_column((array)$result, 'id');

    while (!empty($child_ids)) {
        $collection = array_merge($collection, $result);
        $result = $model->field($field)
            ->whereIn('pid', $child_ids)
            ->select();
        $child_ids = array_column((array)$result, 'id');
    }
    return $collection;
}

/**
 * 判断关键词是否唯一
 *
 * @author weiphp
 */
function keyword_unique($keyword)
{
    if (empty($keyword)) {
        return false;
    }

    $map['keyword'] = $keyword;
    $info = M('keyword')->where(wp_where($map))->find();
    return empty($info);
}

// 分析枚举类型配置值 格式 a:名称1,b:名称2
// weiphp 该函数是从admin的function的文件里提取这到里
function parse_config_attr($string)
{
    $array = preg_split('/[\s;\r\n]+/', trim($string, ",;\s
"));
    if (strpos($string, ':')) {
        $value = [];
        foreach ($array as $val) {
            list ($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    foreach ($value as &$vo) {
        $vo = clean_hide_attr($vo);
    }
    // dump($value);
    return $value;
}

function clean_hide_attr($str)
{
    $arr = explode('|', $str);
    return isset($arr[0]) ? $arr[0] : '';
}

function get_hide_attr($str)
{
    $arr = explode('|', $str);
    return isset($arr[1]) ? $arr[1] : '';
}

// 分析枚举类型字段值 格式 a:名称1,b:名称2
// 暂时和 parse_config_attr功能相同
// 但请不要互相使用，后期会调整
function parse_field_attr($string)
{
    if (0 === strpos($string, ':')) {
        // 采用函数定义
        return eval(substr($string, 1) . ';');
    }
    $array = array_filter(preg_split('/[;\r\n]+/', $string));
    // dump($array);
    if (strpos($string, ':')) {
        $value = [];
        foreach ($array as $val) {
            list ($k, $v) = explode(':', $val);
            empty($v) && $v = $k;
            $k = clean_hide_attr($k);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    // dump($value);
    return $value;
}

/**
 * 取一个二维数组中的每个数组的固定的键知道的值来形成一个新的一维数组
 *
 * @param $pArray 一个二维数组
 * @param $pKey 数组的键的名称
 * @return 返回新的一维数组
 */
function getSubByKey($pArray, $pKey)
{
    if (is_object($pArray)) {
        $pArray = $pArray->toArray();
    }

    $result = [];
    if (is_array($pArray)) {
        foreach ($pArray as $temp_array) {
            if (is_object($temp_array)) {
                $temp_array = $temp_array->toArray();
            }

            $result[] = isset($temp_array[$pKey]) ? $temp_array[$pKey] : "";
        }
    }

    return $result;
}

// 判断是否是在微信浏览器里
function isWeixinBrowser($from = 0)
{
    if ((!$from && defined('IN_WEIXIN') && IN_WEIXIN) || isset($_GET['is_stree'])) {
        return true;
    }

    $agent = $_SERVER['HTTP_USER_AGENT'];
    if (!strpos($agent, "icroMessenger")) {
        return false;
    }
    return true;
}

// php获取当前访问的完整url地址
function GetCurUrl()
{
    $url = HTTP_PREFIX;
    if ($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443') {
        $url .= $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
    } else {
        $url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    // 兼容后面的参数组装
    if (stripos($url, '?') === false) {
        $url .= '?t=' . time();
    }
    return $url;
}

// 获取当前用户的OpenId
function get_openid($openid = null)
{
    $pbid = get_pbid();
    $request_openid = input('openid');
    if ($openid !== null && $openid != '-1' && $openid != '-2') {
        session('openid_' . $pbid, $openid);
    } elseif (!empty($request_openid) && $request_openid != '-1' && $request_openid != '-2') {
        session('openid_' . $pbid, $request_openid);
    }
    $openid = session('openid_' . $pbid);
    $isWeixinBrowser = isWeixinBrowser();
    if ((empty($openid) || $openid == '-1') && $isWeixinBrowser && $request_openid != '-2' && request()->isGet() && !request()->isAjax()) {
        $callback = GetCurUrl();
        $openid = OAuthWeixin($callback, $pbid, true);
        if ($openid != false && $openid != '-2') {
            session('openid_' . $pbid, $openid);
        }
    }
    if (empty($openid)) {
        return '-1';
    }
    return $openid;
}

/*
 * 获取支付的appid的openid
 * 微信支付和红包使用
 */
function getPaymentOpenid($appId = "", $serect = "")
{
    if (empty($appId) || empty($serect)) {
        $openid = get_openid();
        return $openid;
        exit();
    }
    $callback = GetCurUrl();

    $param = $appId . ':' . $serect;
    $openid = OAuthWeixin($callback, $param, true);

    return $openid;
}

// 获取当前用户的UID,方便在模型里的自动填充功能使用
function get_mid()
{
    return session('mid_' . get_pbid());
}

function get_wpid($wpid = '')
{
    if (defined('WPID')) {
        return WPID;
    } else {
        return 0;
    }
}

function get_pbid()
{
    if (defined('PBID')) {
        return intval(PBID);
    } else {
        return 0;
    }
}

// 通过openid获取微信用户基本信息,此功能只有认证的服务号才能用
function getWeixinUserInfo($openid)
{
    /*
     * if (! config('USER_BASE_INFO')) {
     * return [];
     * }
     */
    $access_token = get_access_token();
    if (empty($access_token)) {
        return [];
    }

    $param2['access_token'] = $access_token;
    $param2['openid'] = $openid;
    $param2['lang'] = 'zh_CN';

    $url = 'https://api.weixin.qq.com/cgi-bin/user/info?' . http_build_query($param2);

    $content = str_replace("\r\n", '\n', wp_file_get_contents($url));
    $content = json_decode($content, true);
    return $content;
}

// 获取公众号的信息
function get_pbid_appinfo($pbid = '', $field = '')
{
    empty($pbid) && $pbid = get_pbid();
    $info = [];
    if ($pbid != 'gh_3c884a361561') {
        $info = D('common/Publics')->getInfoById($pbid, $field);
    }
    return $info;
}

function get_pbid_by_token($token)
{
    $wpid = D('common/Publics')->get_pbid_by_token($token);
    if (empty($wpid) && defined('WPID')) {
        $wpid = WPID;
    }
    return $wpid;
}

// 兼容旧方法
function get_service_info($field = '')
{
    return get_pbid_appinfo('', $field);
}

// 判断公众号的类型：是订阅号还是服务号
function get_wpid_type($pbid = '')
{
    $info = get_pbid_appinfo($pbid);
    return intval($info['type']);
}

// 获取access_token，自动带缓存功能
function get_access_token($pbid = '', $update = false)
{
    empty($pbid) && $pbid = get_pbid();

    $info = get_pbid_appinfo($pbid);
    if (!$info) {
        return false;
    }

    // 微信开放平台一键绑定
    if ($pbid == 'gh_3c884a361561' || $info['is_bind']) {
        $access_token = get_authorizer_access_token($info['appid'], $info['authorizer_refresh_token'], $update);
    } else {
        $access_token = get_access_token_by_apppid($info['appid'], $info['secret'], $update);
    }

    // 自动判断access_token是否已失效，如失效自动获取新的
    if ($update == false) {
        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=' . $access_token;
        $res = wp_file_get_contents($url);
        $res = json_decode($res, true);
        if (isset($res['errcode']) && $res['errcode'] == '40001') {
            $access_token = get_access_token($pbid, true);
        }
    }

    return $access_token;
}

function get_authorizer_access_token($appid, $refresh_token, $update)
{
    if (empty($appid)) {
        return 0;
    }

    $key = 'authorizer_access_token_' . $appid;
    $res = S($key);
    if ($res !== false && !$update) {
        return $res;
    }

    $dao = D('public_bind/PublicBind');
    if (empty($refresh_token)) {
        $auth_code = $dao->_get_pre_auth_code();
        $info = $dao->getAuthInfo($auth_code);
        if (!isset($info['authorization_info']['authorizer_access_token'])) {
            return false;
        }
        $authorizer_access_token = $info['authorization_info']['authorizer_access_token'];
    } else {
        $info = $dao->refreshToken($appid, $refresh_token);
        if (!isset($info['authorizer_access_token'])) {
            return false;
        }
        $authorizer_access_token = $info['authorizer_access_token'];
    }

    if (!empty($authorizer_access_token)) {
        S($key, $authorizer_access_token, $info['expires_in'] - 200);
        return $authorizer_access_token;
    } else {
        addWeixinLog($info, 'get_authorizer_access_token_fail_' . $appid);
        return 0;
    }
}

function get_access_token_by_apppid($appid, $secret, $update = false)
{
    if (empty($appid) || empty($secret)) {
        return 0;
    }

    $key = 'access_token_apppid_' . $appid . '_' . $secret;
    $res = S($key);
    if ($res !== false && !$update) {
        return $res;
    }

    $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&secret=' . $secret . '&appid=' . $appid;
    $tempArr = json_decode(wp_file_get_contents($url), true);
    if (@array_key_exists('access_token', $tempArr)) {
        S($key, $tempArr['access_token'], $tempArr['expires_in']);
        return $tempArr['access_token'];
    } else {
        return 0;
    }
}

function OAuthWeixin($callback, $pbid = '', $is_return = false)
{
    if ((defined('IN_WEIXIN') && IN_WEIXIN) || isset($_GET['is_stree']) || !config('USER_OAUTH')) {
        return false;
    }

    $isWeixinBrowser = isWeixinBrowser();
    if (!$isWeixinBrowser) {
        return false;
    }
    $callback = urldecode($callback);
    if (strpos($callback, '?') === false) {
        $callback .= '?';
    } else {
        $callback .= '&';
    }
    if (!empty($pbid) && strpos($pbid, ':') !== false) {
        $arr = explode(':', $pbid);
        $info['appid'] = $arr[0];
        $info['secret'] = $arr[1];
    } else {
        $info = get_pbid_appinfo($pbid);
    }

    if (empty($info['appid'])) {
        return $is_return ? -2 : redirect($callback . 'openid=-2');
    }
    $param['appid'] = $info['appid'];
    $_GET['state'] = I('state','');
    if (!isset($_GET['state']) || $_GET['state'] != 'weiphp') {
        $param['redirect_uri'] = $callback;
        $param['response_type'] = 'code';
        $param['scope'] = 'snsapi_base';
        $param['state'] = 'weiphp';
        $info['is_bind'] && $param['component_appid'] = config('COMPONENT_APPID');
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?' . http_build_query($param) . '#wechat_redirect';
        // return redirect($url);
        header('Location: ' . $url);
        exit();
    } elseif (isset($_GET['state']) && $_GET['state'] == 'weiphp') {
        if (empty($_GET['code'])) {
            exit('code获取失败');
        }
        //code只能使用一次
        $param['code'] = I('code');
        $skey = 'openid_' . $param['code'];
        $content['openid'] = S($skey);
        if (empty ($content ['openid'])) {
            $param ['grant_type'] = 'authorization_code';
            if ($info ['is_bind']) {
                $param ['appid'] = I('appid');
                $param ['component_appid'] = config('COMPONENT_APPID');
                $param ['component_access_token'] = D('public_bind/PublicBind')->_get_component_access_token();

                $url = 'https://api.weixin.qq.com/sns/oauth2/component/access_token?' . http_build_query($param);
            } else {
                $param ['secret'] = $info ['secret'];

                $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?' . http_build_query($param);
            }
            $content = $res = wp_file_get_contents($url);
            $content = json_decode($content, true);
            if (!isset ($content ['openid'])) {
                add_debug_log($res, 'OAuthWeixin_' . $param ['code']);
                add_debug_log($content, 'OAuthWeixin2_' . $param ['code']);
                exception($res);
            }
            S($skey, $content['openid'], 300);
        }

        if ($is_return) {
            return $content['openid'];
        } else {
            return redirect($callback . 'openid=' . $content['openid']);
        }
    }
}

/**
 * 执行SQL文件
 */
function execute_sql_file($sql_path)
{
    // 读取SQL文件
    $sql = wp_file_get_contents($sql_path);
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);

    // 替换表前缀
    $orginal = 'wp_';
    $prefix = DB_PREFIX;
    $sql = str_replace("{$orginal}", "{$prefix}", $sql);

    // 开始安装
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) {
            continue;
        }

        $res = M()->execute($value);
        // dump($res);
        // dump(M()->getLastSql());
    }
}

// 设置微信关联聊天中用到的用户状态
function set_user_status($addon, $keywordArr = [])
{
    // 设置用户状态
    $user_status['addon'] = $addon;
    $user_status['keywordArr'] = $keywordArr;

    $openid = get_openid();
    return S('user_status_' . $openid, $user_status);
}

// 截取内容
function getShort($str, $length = 40, $ext = '')
{
    $str = filter_line_tab($str);
    $str = htmlspecialchars($str);
    $str = strip_tags($str);
    $str = htmlspecialchars_decode($str);
    $strlenth = 0;
    $out = '';
    preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/", $str, $match);

    $output = '';
    foreach ($match[0] as $v) {
        preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $v, $matchs);
        if (!empty($matchs[0])) {
            $strlenth += 1;
        } elseif (is_numeric($v)) {
            $strlenth += 0.5; // 字符字节长度比例 汉字为1
        } else {
            $strlenth += 0.5; // 字符字节长度比例 汉字为1
        }

        if ($strlenth > $length) {
            $output .= $ext;
            break;
        }

        $output .= $v;
    }
    return $output;
}

// 过滤非法html标签 去掉换行符
function filter_line_tab($text)
{
    $text = str_replace(array(
        "\r\n",
        "\r",
        "\n",
        " "
    ), '', $text);
    // 过滤标签
    $text = nl2br($text);
    $text = real_strip_tags($text);
    $text = addslashes($text);
    $text = trim($text);
    return addslashes($text);
}

function real_strip_tags($str, $allowable_tags = "")
{
    $str = stripslashes(htmlspecialchars_decode($str));
    return strip_tags($str, $allowable_tags);
}

// 防超时的file_get_contents改造函数
function wp_file_get_contents($url)
{
    if (empty($url)) {
        return '';
    }

    $context = stream_context_create(array(
        'http' => array(
            'timeout' => 30
        ),
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false
        )
    )); // 超时时间，单位为秒

    return file_get_contents($url, 0, $context);
}

// 全局的安全过滤函数
function safe($text, $type = 'html')
{
    // 无标签格式
    $text_tags = '';
    // 只保留链接
    $link_tags = '<a>';
    // 只保留图片
    $image_tags = '<img>';
    // 只存在字体样式
    $font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
    // 标题摘要基本格式
    $base_tags = $font_tags . '<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike><section><header><footer><article><nav><audio><video>';
    // 兼容Form格式
    $form_tags = $base_tags . '<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
    // 内容等允许HTML的格式
    $html_tags = $base_tags . '<meta><ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
    // 全HTML格式
    $all_tags = $form_tags . $html_tags . '<!DOCTYPE><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
    // 过滤标签
    $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    $text = strip_tags($text, ${$type . '_tags'});

    // 过滤攻击代码
    if ($type != 'all') {
        // 过滤危险的属性，如：过滤on事件lang js
        while (preg_match('/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
        while (preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
    }
    return $text;
}

// 创建多级目录
function mkdirs($dir)
{
    if (!is_dir($dir)) {
        if (!mkdirs(dirname($dir))) {
            return false;
        }
        if (!mkdir($dir, 0777)) {
            return false;
        }
    }
    return true;
}

// 组装查询条件
function getIdsForMap($ids, $map = [], $field = 'id')
{
    $ids = safe($ids);
    $ids = preg_split('/[\s,;]+/', $ids); // 支持以空格tab逗号分号分割ID
    $ids = array_filter($ids);
    if (empty($ids)) {
        return $map;
    }

    $map[] = array(
        $field,
        'in',
        $ids
    );

    return $map;
}

// 获取通用分类级联菜单的标题，改方法仅支持级联的数据源配置成数据表common_category的情况，其它情况需要使用下面的getCascadeTitle方法
function getCommonCategoryTitle($ids)
{
    $extra = 'type=db&table=common_category';

    return getCascadeTitle($ids, $extra);
}

// 获取级联菜单的标题的通用处理方法
function getCascadeTitle($ids, $extra)
{
    $idArr = explode(',', $ids);
    $idArr = array_filter($idArr);
    if (empty($idArr)) {
        return '';
    }

    parse_str($extra, $arr);
    if ($arr['type'] == 'db') {
        $table = $arr['table'];
        unset($arr['type'], $arr['table']);

        $arr['wpid'] = get_wpid();
        $list = M($table)->where(wp_where($arr))
            ->whereIn('id', $idArr)
            ->field('title')
            ->select();
        $titleArr = getSubByKey($list, 'title');
    } else {
        $str = str_replace('，', ',', $extra);
        $str = str_replace('【', '[', $str);
        $str = str_replace('】', ']', $str);
        $str = str_replace('：', ':', $str);

        $arr = StringToArray($str);
        $str = '';
        foreach ($arr as $v) {
            if ($v == '[' || $v == ']' || $v == ',') {
                if ($str) {
                    $block = explode(':', trim($str));
                    if (in_array($block[0], $idArr)) {
                        $titleArr[] = isset($block[1]) ? $block[1] : $block[0];
                    }
                }
                $str = '';
            } else {
                $str .= $v;
            }
        }
    }
    return implode(' > ', $titleArr);
}

// 把字符串转成数组，支持汉字，只能是utf-8格式的
function StringToArray($str)
{
    $result = [];
    $len = strlen($str);
    $i = 0;
    while ($i < $len) {
        $chr = ord($str[$i]);
        if ($chr == 9 || $chr == 10 || (32 <= $chr && $chr <= 126)) {
            $result[] = substr($str, $i, 1);
            $i += 1;
        } elseif (192 <= $chr && $chr <= 223) {
            $result[] = substr($str, $i, 2);
            $i += 2;
        } elseif (224 <= $chr && $chr <= 239) {
            $result[] = substr($str, $i, 3);
            $i += 3;
        } elseif (240 <= $chr && $chr <= 247) {
            $result[] = substr($str, $i, 4);
            $i += 4;
        } elseif (248 <= $chr && $chr <= 251) {
            $result[] = substr($str, $i, 5);
            $i += 5;
        } elseif (252 <= $chr && $chr <= 253) {
            $result[] = substr($str, $i, 6);
            $i += 6;
        }
    }
    return $result;
}

/**
 *
 * @param string $name
 *            积分标识名
 * @param array $param
 *            自定义金币值，格式：array('uid'=>'用户ID','score'=>积分值,'title'=>'积分项名称');为空时默认取管理中心积分管理里的配置值
 * @param int $lock_time
 *            解锁时间，即多长时间内才能重复加积分，为0时不作控制
 * @param int $admin_uid
 *            管理员UID，用于管理员给用户手工加积分时的场景
 */
function add_credit($name, $param = [], $lock_time = 5, $admin_uid = 0)
{
    if (empty($name)) {
        return false;
    }

    if ($lock_time > 0) {
        $key = 'credit_lock_' . get_wpid() . '_' . get_openid() . '_' . $name;
        if (S($key)) {
            return false;
        }

        S($key, 1, $lock_time);
    }

    $data['credit_name'] = $name;
    $data['admin_uid'] = $admin_uid;
    $data = array_merge($data, $param);

    $credit = D('common/Credit')->addCredit($data);

    return $credit;
}

/**
 * 增加用户余额函数
 *
 * @param int $uid
 *            用户UID
 * @param float $money
 *            充值金额
 * @param string $log
 *            recharge_log 表需要的数据
 */
function add_money($uid, $money, $log = [])
{
    if (empty($uid) || empty($money)) {
        return false;
    }

    return D('Card/Card')->addMoney($uid, $money, $log);
}

// 判断用户最大可创建的公众号数
function getPublicMax($uid)
{
    $map['uid'] = $uid;
    $public_count = M('user')->where(wp_where($map))->value('public_count');
    if ($public_count === null) {
        $public_count = config('DEFAULT_PUBLIC_CREATE_MAX_NUMB');
    }
    return intval($public_count);
}

function diyPage($keyword)
{
    $map['keyword'] = $keyword;
    $map['wpid'] = get_wpid();
    $page = M('diy')->where(wp_where($map))->find();

    if (!$page) {
        $map['wpid'] = '0';
        $page = M('diy')->where(wp_where($map))->find();
    }
    // dump($page);
    if (!$page) {
        return false;
    }

    $model = A('Diy/Diy');
    // dump($model);exit;
    $model->show($page['id']);
}

// 各插件获取关联抽奖活动的地址 暂只支持刮刮卡
function event_url($addon_title, $id = '0')
{
    // $map ['wpid'] = get_wpid ();
    // $map ['addon_condition'] = array (
    // 'exp',
    // "='[{$addon_title}:*]' or addon_condition='[{$addon_title}:{$id}]'"
    // );

    // $event = M( 'Scratch' )->where ( wp_where( $map ) )->order ( 'id desc' )->find ();
    $event_url = '';
    // if ($event) {
    // $param ['wpid'] = get_wpid ();
    // $param ['openid'] = get_openid ();
    // $param ['id'] = $event ['id'];
    // $event_url = U ( 'Scratch/Scratch/show', $param );
    // }
    return $event_url;
}

// 抽奖或者优惠券领取的插件条件判断
function addon_condition_check($addon_condition)
{
    if (empty($addon_condition)) {
        return true;
    }

    preg_match_all("/\[([\s\S]*):([\*,\d]*)\]/i", $addon_condition, $match);
    if (empty($match[1][0]) || empty($match[2][0])) {
        return true;
    }

    $conditon['wpid'] = get_wpid();
    $conditon['uid'] = get_mid();
    switch ($match[1][0]) {
        case '投票':
            $match[2][0] != '*' && $match[2][0] > 0 && $conditon['vote_id'] = $match[2][0];
            $conditon['user_id'] = get_mid();
            unset($conditon['uid']);
            $res = M('vote_log')->where(wp_where($conditon))->find();
            break;
        case '通用表单':
            $match[2][0] != '*' && $match[2][0] > 0 && $conditon['forms_id'] = $match[2][0];
            $res = M('forms_value')->where(wp_where($conditon))->find();
            break;
        case '微考试':
            $match[2][0] != '*' && $match[2][0] > 0 && $conditon['exam_id'] = $match[2][0];
            $res = M('exam_answer')->where(wp_where($conditon))->find();
            break;
        case '微测试':
            $match[2][0] != '*' && $match[2][0] > 0 && $conditon['test_id'] = $match[2][0];
            $res = M('test_answer')->where(wp_where($conditon))->find();
            break;
        case '微调研':
            $match[2][0] != '*' && $match[2][0] > 0 && $conditon['survey_id'] = $match[2][0];
            $res = M('survey_answer')->where(wp_where($conditon))->find();
            break;
        default:
            $match[2][0] != '*' && $match[2][0] > 0 && $conditon['id'] = $match[2][0];
            $res = M($match[1][0])->where(wp_where($conditon))->find();
    }
    // dump ( $res );
    // dump ( M()->getLastSql () );

    return !empty($res);
}

// 抽奖或者优惠券领取的插件条件提示
function condition_tips($addon_condition)
{
    if (empty($addon_condition)) {
        return '';
    }

    preg_match_all("/\[([\s\S]*):([\*,\d]*)\]/i", $addon_condition, $match);
    if (empty($match[1][0]) || empty($match[2][0])) {
        return '';
    }

    $conditon['wpid'] = get_wpid();
    $conditon['id'] = $match[2][0];
    $title = '';
    $has_title = $conditon['id'] != '*' && $conditon['id'] > 0;

    switch ($match[1][0]) {
        case '投票':
            $has_title && $title = M('vote')->where(wp_where($conditon))->value('title');
            break;
        case '通用表单':
            $has_title && $title = M('forms')->where(wp_where($conditon))->value('title');
            break;
        case '微考试':
            $has_title && $title = M('exam')->where(wp_where($conditon))->value('title');
            break;
        case '微测试':
            $has_title && $title = M('test')->where(wp_where($conditon))->value('title');
            break;
        case '微调研':
            $has_title && $title = M('survey')->where(wp_where($conditon))->value('title');
            break;
        default:
            $has_title && $title = M($match[1][0])->where(wp_where($conditon))->value('title');
    }
    $result = '需要参与' . $title . $match[1][0] . '后才能领取';

    return $result;
}

function lastsql()
{
    dump(M()->getLastSql());
}

// 商业代码解密
function code_decode($text)
{
    $key = substr(config('WEIPHP_STORE_LICENSE'), 0, 5);
    return think_decrypt($text, $key);
}

function outExcel($dataArr, $fileName = '', $sheet = false)
{
    require_once env('root_path') . 'vendor/' . 'download-xlsx.php';
    export_csv($dataArr, $fileName, $sheet);
    unset($sheet);
    unset($dataArr);
}

// 获取通用分类表的分类标题
function category_title($cate_id)
{
    static $_category_title = [];
    if (isset($_category_title[$cate_id])) {
        return $_category_title[$cate_id];
    }

    $map['wpid'] = get_wpid();
    $list = M('common_category')->where(wp_where($map))
        ->field('id,title')
        ->select();
    foreach ($list as $v) {
        $_category_title[$v['id']] = $v['title'];
    }
    if (!isset($_category_title[$cate_id])) {
        $_category_title[$cate_id] = '';
    }
    return $_category_title[$cate_id];
}

function get_lecturer_name($lecturer_id)
{
    static $_lecturer_name = [];
    if (isset($_lecturer_name[$lecturer_id])) {
        return $_lecturer_name[$lecturer_id];
    }

    $map['wpid'] = get_wpid();
    $list = M('classes_lecturer')->where(wp_where($map))
        ->field('id,name')
        ->select();
    foreach ($list as $v) {
        $_lecturer_name[$v['id']] = $v['name'];
    }
    if (!isset($_lecturer_name[$lecturer_id])) {
        $_lecturer_name[$lecturer_id] = '';
    }
    return $_lecturer_name[$lecturer_id];
}

function check_TOKEN_purview($table, $id, $field = 'wpid')
{
    $wpid = get_wpid();
    $map['id'] = $id;
    $info = M($table)->where(wp_where($map))
        ->field($field)
        ->find();
    if ($info === false || $info[$field] == $wpid) {
        return true;
    }
    // 没有这个字段或者没有这个记录直接返回

    exit('非法访问');
}

// weiphp专用分割函数，同时支持常见的按空格、逗号、分号、换行进行分割
function wp_explode($string, $delimiter = "\s,;\r\n")
{
    if (empty($string)) {
        return [];
    }

    // 转换中文符号
    // $string = iconv ( 'utf-8', 'gbk', $string );
    // $string = preg_replace ( '/\xa3([\xa1-\xfe])/e', 'chr(ord(\1)-0x80)', $string );
    // $string = iconv ( 'gbk', 'utf-8', $string );

    $arr = preg_split('/[' . $delimiter . ']+/', $string);
    return array_unique(array_filter($arr));
}

function get_code_img($qr_code)
{
    if (!$qr_code) {
        return '';
    }

    $html = '<img src="' . $qr_code . '" width="50" height="50" />';
    return $html;
}

function get_file_title($attach_ids)
{
    $ids = wp_explode($attach_ids);
    if (empty($ids)) {
        return '';
    }

    $names = M('file')->whereIn('id', $ids)->column('name');

    return implode(', ', $names);
}

// 阿拉伯数字转中文表述，如101转成一百零一
function num2cn($number)
{
    $number = intval($number);
    $capnum = array(
        "零",
        "一",
        "二",
        "三",
        "四",
        "五",
        "六",
        "七",
        "八",
        "九"
    );
    $capdigit = array(
        "",
        "十",
        "百",
        "千",
        "万"
    );

    $data_arr = str_split($number);
    $count = count($data_arr);
    for ($i = 0; $i < $count; $i++) {
        $d = $capnum[$data_arr[$i]];
        $arr[] = $d != '零' ? $d . $capdigit[$count - $i - 1] : $d;
    }
    $cncap = implode("", $arr);

    $cncap = preg_replace("/(零)+/", "0", $cncap); // 合并连续“零”
    $cncap = trim($cncap, '0');
    $cncap = str_replace("0", "零", $cncap); // 合并连续“零”
    $cncap == '一十' && $cncap = '十';
    $cncap == '' && $cncap = '零';
    // echo ( $data.' : '.$cncap.' <br/>' );
    return $cncap;
}

function week_name($number = null)
{
    if ($number === null) {
        $number = date('w');
    }

    $arr = array(
        "日",
        "一",
        "二",
        "三",
        "四",
        "五",
        "六"
    );

    return '星期' . $arr[$number];
}

// 日期转换成星期几
function daytoweek($day = null)
{
    $day === null && $day = date('Y-m-d');
    if (empty($day)) {
        return '';
    }

    $number = date('w', strtotime($day));

    return week_name($number);
}

/**
 * select返回的数组进行整数映射转换
 *
 * @param array $map
 *            映射关系二维数组 array(
 *            '字段名1'=>array(映射关系数组),
 *            '字段名2'=>array(映射关系数组),
 *            ......
 *            )
 * @author 朱亚杰 <zhuyajie@topthink.net>
 * @return array array(
 *         array('id'=>1,'title'=>'标题','status'=>'1','status_text'=>'正常')
 *         ....
 *         )
 *
 */
function int_to_string(&$data, $map = array('status' => array(1 => '正常', -1 => '删除', 0 => '禁用', 2 => '未审核', 3 => '草稿')))
{
    if ($data === false || $data === null) {
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row) {
        foreach ($map as $col => $pair) {
            if (isset($row[$col]) && isset($pair[$row[$col]])) {
                $data[$key][$col . '_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}

function importFormExcel($attach_id, $column, $dateColumn = [])
{
    $attach_id = intval($attach_id);
    $res = array(
        'status' => 0,
        'data' => ''
    );
    if (empty($attach_id) || !is_numeric($attach_id)) {
        $res['data'] = '上传文件ID无效！';
        return $res;
    }
    $file = M('file')->where('id=' . $attach_id)->find();
    $root = config('DOWNLOAD_UPLOAD.rootPath');
    $filename = SITE_PATH . '/public/uploads/download/' . $file['savepath'] . $file['savename'];
    trace($filename, 'error');
    if (!file_exists($filename)) {
        $res['data'] = '上传的文件失败';
        return $res;
    }
    $extend = $file['ext'];
    if (!($extend == 'xls' || $extend == 'xlsx' || $extend == 'csv')) {
        $res['data'] = '文件格式不对，请上传xls,xlsx格式的文件';
        return $res;
    }

    require_once env('vendor_path') . 'PHPExcel.php';
    require_once env('vendor_path') . 'PHPExcel/IOFactory.php';
    require_once env('vendor_path') . 'PHPExcel/Reader/Excel5.php';

    switch (strtolower($extend)) {
        case 'csv':
            $format = 'CSV';
            $objReader = \PHPExcel_IOFactory::createReader($format)->setDelimiter(',')
                ->setInputEncoding('GBK')
                ->setEnclosure('"')
                ->setLineEnding("\r\n")
                ->setSheetIndex(0);
            break;
        case 'xls':
            $format = 'Excel5';
            $objReader = \PHPExcel_IOFactory::createReader($format);
            break;
        default:
            $format = 'excel2007';
            $objReader = \PHPExcel_IOFactory::createReader($format);
    }

    $objPHPExcel = $objReader->load($filename);
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getSheet(0);
    $highestRow = $sheet->getHighestRow(); // 取得总行数
    $result = [];
    for ($j = 2; $j <= $highestRow; $j++) {
        $addData = [];
        foreach ($column as $k => $v) {
            if ($dateColumn) {
                foreach ($dateColumn as $d) {
                    if ($k == $d) {
                        $addData[$v] = gmdate("Y-m-d H:i:s", PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()
                            ->getCell("$k$j")
                            ->getValue()));
                    } else {
                        $addData[$v] = trim((string)$objPHPExcel->getActiveSheet()
                            ->getCell($k . $j)
                            ->getValue());
                    }
                }
            } else {
                $addData[$v] = trim((string)$objPHPExcel->getActiveSheet()
                    ->getCell($k . $j)
                    ->getValue());
            }
        }

        $isempty = true;
        foreach ($column as $v) {
            $isempty && $isempty = empty($addData[$v]);
        }

        if (!$isempty) {
            $result[$j] = $addData;
        }
    }
    $res['status'] = 1;
    $res['data'] = $result;
    return $res;
}

function showNewIcon($time, $day = 3)
{
    $img = '';
    if (NOW_TIME < ($time + 86400 * $day)) {
        $img = '<img src="' . config('TMPL_PARSE_STRING.__IMG__') . '/new.png"/>';
    }
    return $img;
}

function replace_url($content)
{
    $param['wpid'] = get_wpid();
    $param['openid'] = get_openid();

    $sreach = array(
        '[follow]',
        '[website]',
        '[wpid]',
        '[openid]'
    );
    $replace = array(
        U('weixin/UserCenter/bind', $param),
        U('wei_site/Wap/index', $param),
        $param['wpid'],
        $param['openid']
    );
    $content = str_replace($sreach, $replace, $content);

    return $content;
}

/**
 * 验证分类是否允许发布内容
 *
 * @param integer $id
 *            分类ID
 * @return boolean true-允许发布内容，false-不允许发布内容
 */
function check_category($id)
{
    if (is_array($id)) {
        $id['type'] = !empty($id['type']) ? $id['type'] : 2;
        $type = get_category($id['category_id'], 'type');
        $type = explode(",", $type);
        return in_array($id['type'], $type);
    } else {
        $publish = get_category($id, 'allow_publish');
        return $publish ? true : false;
    }
}

/**
 * 检测分类是否绑定了指定模型
 *
 * @param array $info
 *            模型ID和分类ID数组
 * @return boolean true-绑定了模型，false-未绑定模型
 */
function check_category_model($info)
{
    $cate = get_category($info['category_id']);
    $array = explode(',', $info['pid'] ? $cate['model_sub'] : $cate['model']);
    return in_array($info['model_id'], $array);
}

// 获取随机的字符串，用于TOKEN，EncodingAESKey等的生成
function get_rand_char($length = 6)
{
    $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $strLength = 61;

    for ($i = 0; $i < $length; $i++) {
        $res .= $str[rand(0, $strLength)];
    }

    return $res;
}

/**
 * 根据两点间的经纬度计算距离
 *
 * @param float $lat
 *            纬度值
 * @param float $lng
 *            经度值
 */
function getDistance($lat1, $lng1, $lat2, $lng2)
{
    $earthRadius = 6367000; // approximate radius of earth in meters

    // Convert these degrees to radians to work with the formula
    $lat1 = ($lat1 * pi()) / 180;
    $lng1 = ($lng1 * pi()) / 180;

    $lat2 = ($lat2 * pi()) / 180;
    $lng2 = ($lng2 * pi()) / 180;

    // Using the Haversine formula http://en.wikipedia.org/wiki/Haversine_formula calculate the distance

    $calcLongitude = $lng2 - $lng1;
    $calcLatitude = $lat2 - $lat1;
    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
    $calculatedDistance = $earthRadius * $stepTwo;

    return round($calculatedDistance);
}

function getMyDistance($shopGPS)
{
    $arr = explode(',', $shopGPS);
    if (empty($arr[0]) || empty($arr[1]) || !empty($_SESSION['my_location_' . $GLOBALS['mid']])) {
        return '';
    }
    // 无法计算

    $my = explode(',', $_SESSION['my_location_' . $GLOBALS['mid']]);
    return getDistance($arr[0], $arr[1], $my[0], $my[1]);
}

function GPS2Address($location)
{
    $url = 'http://api.map.baidu.com/geocoder/v2/?ak=' . BAIDU_GPS_AK . '&coordtype=wgs84ll&location=' . $location . '&output=json&pois=0';
    $res = wp_file_get_contents($url);
    // dump ( $url );
    $res = json_decode($res, true);
    // dump ( $res );
    return $res['result']['formatted_address'];
}

// 兼容旧版本
function xml_to_array($xml)
{
    return FromXml($xml);
}

// 兼容旧版本
function xmltoarray($xml)
{
    return FromXml($xml);
}

/**
 * ************************************************************
 *
 * 使用特定function对数组中所有元素做处理
 *
 * @param
 *            string &$array 要处理的字符串
 * @param string $function
 *            要执行的函数
 * @return boolean $apply_to_keys_also 是否也应用到key上
 * @access public
 *
 *         ***********************************************************
 */
function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
{
    static $recursive_counter = 0;
    if (++$recursive_counter > 1000) {
        die('possible deep recursion attack');
    }
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            arrayRecursive($array[$key], $function, $apply_to_keys_also);
        } else {
            $array[$key] = $function($value);
        }

        if ($apply_to_keys_also && is_string($key)) {
            $new_key = $function($key);
            if ($new_key != $key) {
                $array[$new_key] = $array[$key];
                unset($array[$key]);
            }
        }
    }
    $recursive_counter--;
}

/**
 * ************************************************************
 *
 * 将数组转换为JSON字符串（兼容中文）
 *
 * @param array $array
 *            要转换的数组
 * @return string 转换得到的json字符串
 * @access public
 *
 *         ***********************************************************
 */
function json_url($array)
{
    $json = json_encode($array, JSON_UNESCAPED_UNICODE);
    return $json;
}

/**
 * 短链接功能
 *
 * @param float $long_url
 *            长链接
 * @return string 如果没有微信短链接接口权限或者不成功，就原样返回长链接，否则返回短链接
 */
function short_url($long_url)
{
    $access_token = get_access_token();
    if (empty($access_token)) {
        return $long_url;
    }

    $url = 'https://api.weixin.qq.com/cgi-bin/shorturl?access_token=' . $access_token;

    $data['action'] = 'long2short';
    $data['long_url'] = $long_url;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    curl_close($ch);
    $res = json_decode($res, true);
    if ($res['errcode'] == 0) {
        return $res['short_url'];
    } else {
        return $long_url;
    }
}

function makeKeyVal($list, $val = 'title', $key = 'id')
{
    foreach ($list as $v) {
        $arr[$v[$key]] = $v[$val];
    }
    return $arr;
}

/**
 * 检查是否是以手机浏览器进入(IN_MOBILE)
 */
function isMobile()
{
    $mobile = [];
    static $mobilebrowser_list = 'Mobile|iPhone|Android|WAP|NetFront|JAVA|OperasMini|UCWEB|WindowssCE|Symbian|Series|webOS|SonyEricsson|Sony|BlackBerry|Cellphone|dopod|Nokia|samsung|PalmSource|Xphone|Xda|Smartphone|PIEPlus|MEIZU|MIDP|CLDC';
    // note 获取手机浏览器
    if (preg_match("/$mobilebrowser_list/i", $_SERVER['HTTP_USER_AGENT'], $mobile)) {
        return true;
    } else {
        if (preg_match('/(mozilla|chrome|safari|opera|m3gate|winwap|openwave)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return false;
        } else {
            if ($_GET['mobile'] === 'yes') {
                return true;
            } else {
                return false;
            }
        }
    }
}

function isiPhone()
{
    return strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false;
}

function isiPad()
{
    return strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false;
}

function isiOS()
{
    return isiPhone() || isiPad();
}

function isAndroid()
{
    return strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false;
}

// 通过服务号获取用户UID
function get_uid_by_openid($init = true, $openid = '')
{
    $info = get_pbid_appinfo();

    empty($openid) && $openid = get_openid();
    if (!$openid || is_numeric($openid)) {
        return 0;
    }

    $map['openid'] = $openid;
    $map['pbid'] = isset($info['id']) ? $info['id'] : '';
    $uid = M('public_follow')->where(wp_where($map))->value('uid');

    if ($uid) {
        return $uid;
    }

    if (!$init) {
        return 0;
    }

    // 不存在就初始化
    $uid = D('common/Follow')->init_follow($openid, $map['pbid']);
    return $uid;
}

/**
 * 用SHA1算法生成安全签名
 */
function getSHA1($array)
{
    // 排序
    sort($array, SORT_STRING);
    $str = implode($array);
    return sha1($str);
}

function getModelByName($name)
{
    $name = parse_name($name);

    $key = cache_key('name:' . $name, DB_PREFIX . 'model');
    $model = S($key);
    if (!$model) {
        $model = M('model')->getByName($name);
        S($key, $model, 86400);
    }

    return $model;
}

// 复制目录，目前用于生成素材
function copydir($strSrcDir, $strDstDir)
{
    $dir = opendir($strSrcDir);
    if (!$dir) {
        return false;
    }
    if (!is_dir($strDstDir)) {
        if (!mkdir($strDstDir)) {
            return false;
        }
    }
    while (false !== ($file = readdir($dir))) {
        if ($file == '.' || $file == '..' || $file == '.svn' || $file == '.DS_Store' || $file == '__MACOSX' || $file == 'Thumbs.db' || $file == 'info.php') {
            continue;
        }
        if (is_dir($strSrcDir . '/' . $file)) {
            if (!copydir($strSrcDir . '/' . $file, $strDstDir . '/' . $file)) {
                return false;
            }
        } else {
            if (!copy($strSrcDir . '/' . $file, $strDstDir . '/' . $file)) {
                return false;
            }
        }
    }
    closedir($dir);
    return true;
}

/**
 * 获取插件的配置数组
 */
function getAddonConfig($name, $wpid = '')
{
    return D('common/PublicConfig')->getConfig($name, '', $wpid);
}

// 删除目录及目录下的所有子目录和文件
function rmdirr($dirname)
{
    if (!file_exists($dirname)) {
        return false;
    }
    if (is_file($dirname) || is_link($dirname)) {
        return @unlink($dirname);
    }
    $dir = dir($dirname);
    if ($dir) {
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
        }
    }
    $dir->close();
    return rmdir($dirname);
}

function wp_money_format($number, $decimals = '2')
{
    return number_format($number, $decimals, ".", "");
}

// 以GET方式获取数据，替代file_get_contents
function get_data($url, $timeout = 5)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
}

// 以POST方式提交数据
function post_data($url, $param = [], $type = 'json', $return_array = true, $useCert = [])
{
    $has_json = false;
    if ($type == 'json' && is_array($param)) {
        $has_json = true;
        $param = json_encode($param, JSON_UNESCAPED_UNICODE);
    } elseif ($type == 'xml' && is_array($param)) {
        $param = ToXml($param);
    }
    add_debug_log($url, 'post_data');

    // 初始化curl
    $ch = curl_init();
    if ($type != 'file') {
        add_debug_log($param, 'post_data');
        // 设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    } else {
        // 设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
    }

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // 设置header
    if ($type == 'file') {
        $header[] = "content-type: multipart/form-data; charset=UTF-8";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    } elseif ($type == 'xml') {
        curl_setopt($ch, CURLOPT_HEADER, false);
    } elseif ($has_json) {
        $header[] = "content-type: application/json; charset=UTF-8";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }

    // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    // dump($param);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
    // 要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // 使用证书：cert 与 key 分别属于两个.pem文件
    if (isset($useCert['certPath']) && isset($useCert['keyPath'])) {
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLCERT, $useCert['certPath']);
        curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
        curl_setopt($ch, CURLOPT_SSLKEY, $useCert['keyPath']);
    }

    $res = curl_exec($ch);
    if ($type != 'file') {
        add_debug_log($res, 'post_data');
    }
    // echo $res;die;
    $flat = curl_errno($ch);

    $msg = '';
    if ($flat) {
        $msg = curl_error($ch);
    }
    // add_request_log($url, $param, $res, $flat, $msg);
    if ($flat) {
        return [
            'curl_erron' => $flat,
            'curl_error' => $msg
        ];
    } else {
        if ($return_array && !empty($res)) {
            $res = $type == 'json' ? json_decode($res, true) : FromXml($res);
        }

        return $res;
    }
}

/**
 * 输出xml字符
 */
function ToXml($arr = [])
{
    if (!is_array($arr) || count($arr) <= 0) {
        exception("数组数据异常！");
    }

    $xml = "<xml>";
    foreach ($arr as $key => $val) {
        if (is_numeric($val)) {
            $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
        } else {
            $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
    }
    $xml .= "</xml>";
    return $xml;
}

/**
 * 将xml转为array
 */
function FromXml($xml)
{
    if (!$xml) {
        exception("xml数据异常！");
    }
    file_log($xml, 'FromXml');

    // 解决部分json数据误入的问题
    $arr = json_decode($xml, true);
    if (is_array($arr) && !empty($arr)) {
        return $arr;
    }
    // 将XML转为array
    $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    return $arr;
}

// 生成签名
function make_sign($paraMap = [], $partner_key = '')
{
    $buff = "";
    ksort($paraMap);
    $paraMap['key'] = $partner_key;
    foreach ($paraMap as $k => $v) {
        if (null != $v && "null" != $v && '' != $v && "sign" != $k) {
            $buff .= $k . "=" . $v . "&";
        }
    }
    $reqPar = '';
    if (strlen($buff) > 0) {
        $reqPar = substr($buff, 0, strlen($buff) - 1);
    }

    return strtoupper(md5($reqPar));
}

// //////靓妆//////////
/*
 * 分时分段的抽奖算法 prizeArr 奖品数据，结构是：array(array(prize_id=>1,prize_num=>2),array(prize_id=>2,prize_num=>4),...); start_time 开始的时间戳 end_time 结束的时间戳 event_id 抽奖的活动ID，以确保每个抽奖不冲突
 */
function get_lottery1($prizeArr, $start_time, $end_time, $event_id = 0, $uid = 0, $update = false, $wpid = '')
{
    if (empty($uid) && $wpid) {
        // $uid = get_mid();
        $key = 'function_lottery_' . $wpid . '_' . $event_id;
    }
    if (!empty($uid)) {
        $key = 'function_lottery_' . $uid . '_' . $event_id;
    }
    $res = S($key);
    if ($res === false || $update) {
        $res = false;
        // 奖品ID组处理
        $count = 0;
        foreach ($prizeArr as $p) {
            for ($i = 0; $i < $p['prize_num']; $i++) {
                $rand[] = $p['prize_id'];
                $count += 1;
            }
        }
        // 奖品ID排序随机
        shuffle($rand);

        // 时间分段
        $start_time < NOW_TIME && $start_time = NOW_TIME; // 如果活动已经开始，以当前时间为开始时间

        $total_time = $end_time - $start_time;

        $section_time = floor($total_time / $count);

        // 生成中奖的数组 结构是：array('中奖的随机时间点'=>'中奖的奖品ID');
        $stime = $start_time;
        for ($j = 0; $j < $count; $j++) {
            $etime = $stime + $section_time;
            $index = rand($stime, $etime);
            $res[$index] = $rand[$j];

            $stime = $etime;
        }
        S($key, $res);
    }

    return $res;
}

function del_lottery1($index, $event_id = 0, $uid = 0, $wpid = '')
{
    if (empty($uid) && $wpid) {
        $key = 'function_lottery_' . $wpid . '_' . $event_id;
    }
    if (!empty($uid)) {
        $key = 'function_lottery_' . $uid . '_' . $event_id;
    }

    $res = S($key);
    if ($res === false) {
        return false;
    }

    unset($res[$index]);
    S($key, $res);
}

// ////////////sports//////////////
/*
 * 分时分段的抽奖算法 prizeArr 奖品数据，结构是：array(array(prize_id=>1,prize_num=>2),array(prize_id=>2,prize_num=>4),...); start_time 开始的时间戳 end_time 结束的时间戳 event_id 抽奖的活动ID，以确保每个抽奖不冲突
 */
function get_lottery($prizeArr, $start_time, $end_time, $event_id = 0, $update = false)
{
    $key = 'function_lottery_' . $event_id;
    $res = S($key);
    if ($res === false || $update) {
        $res = false;
        // 奖品ID组处理
        $count_shiwu = 0;
        foreach ($prizeArr['shiwu'] as $p) {
            for ($i = 0; $i < $p['prize_num']; $i++) {
                $rand_shiwu[] = $p['prize_id'];
                $count_shiwu += 1;
            }
        }
        // $count_xuni = 0;
        // foreach ( $prizeArr ['xuni'] as $p ) {
        // for($i = 0; $i < $p ['prize_num']; $i ++) {
        // $rand_xuni [] = $p ['prize_id'];
        // $count_xuni += 1;
        // }
        // }
        // 奖品ID排序随机
        shuffle($rand_shiwu);
        // shuffle ( $rand_xuni );

        // 时间分段
        $start_time < NOW_TIME && $start_time = NOW_TIME; // 如果活动已经开始，以当前时间为开始时间

        $total_time = $end_time - $start_time;
        $section_time = floor($total_time / $count_shiwu);
        // 生成中奖的数组 结构是：array('中奖的随机时间点'=>'中奖的奖品ID');
        $stime = $start_time;
        for ($j = 0; $j < $count_shiwu; $j++) {
            $etime = $stime + $section_time;
            // $index = rand ( $stime, $etime );
            // $res [$index] = $rand_shiwu [$j];
            $shiwu_prize[$etime] = $rand_shiwu[$j];
            $stime = $etime;
        }
        $res['shiwu'] = $shiwu_prize;
        // $res ['xuni'] = $rand_xuni;
        S($key, $res);
    }

    return $res;
}

// 根据概率随机抽取积分奖品
function get_jifen_lottery($proArr)
{
    $allPro = array_sum($proArr);
    $result = 0;
    foreach ($proArr as $k => $p) {
        $randNum = mt_rand(1, $allPro);
        if ($randNum <= $p) {
            $result = $k;
        } else {
            $allPro -= $p;
        }
    }
    unset($proArr);
    return $result;
}

function del_lottery($index, $event_id = 0, $delkeyname = 'shiwu')
{
    $key = 'function_lottery_' . $event_id;
    $res = S($key);
    if ($res === false) {
        return false;
    }

    unset($res[$delkeyname][$index]);
    S($key, $res);
}

function lists_msubstr($str)
{
    return msubstr($str, 0, 30);
}

function parseComment($comment, $file = 'lzwg', $width = '40')
{
    preg_match_all('/\[[a-zA-Z0-9]+\]/', $comment, $res);
    $faceData = $res[0];
    if (count($faceData) != 0) {
        foreach ($faceData as $v) {
            $faceName = substr($v, 1, strlen($v) - 2);
            $faceUrl = '<img width=' . $width . ' src="' . SITE_URL . '/static/face/' . $file . '/' . $faceName . '.png"/>';
            $replaceArr[$v] = $faceUrl;
        }
    }
    if ($replaceArr) {
        $comment = strtr($comment, $replaceArr);
    }
    return $comment;
}

/**
 * 系统非常规MD5加密方法
 *
 * @param string $str
 *            要加密的字符串
 * @return string
 */
function think_weiphp_md5($str, $key = '')
{
    if (empty($key)) {
        $conf = config('database.');
        $key = $conf['data_auth_key'];
    }
    return '' === $str ? '' : md5(sha1($str) . $key);
}

// 微信端的错误码转中文解释
function error_msg($return, $more_tips = '')
{
    $msg = array(
        '-1' => '系统繁忙，此时请开发者稍候再试',
        '0' => '请求成功',
        '40001' => '获取access_token时AppSecret错误，或者access_token无效。请开发者认真比对AppSecret的正确性，或查看是否正在为恰当的公众号调用接口',
        '40002' => '不合法的凭证类型',
        '40003' => '不合法的OpenID，请开发者确认OpenID（该用户）是否已关注公众号，或是否是其他公众号的OpenID',
        '40004' => '不合法的媒体文件类型',
        '40005' => '不合法的文件类型',
        '40006' => '不合法的文件大小',
        '40007' => '不合法的媒体文件id',
        '40008' => '不合法的消息类型',
        '40009' => '不合法的图片文件大小',
        '40010' => '不合法的语音文件大小',
        '40011' => '不合法的视频文件大小',
        '40012' => '不合法的缩略图文件大小',
        '40013' => '不合法的AppID，请开发者检查AppID的正确性，避免异常字符，注意大小写',
        '40014' => '不合法的access_token，请开发者认真比对access_token的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口',
        '40015' => '不合法的菜单类型',
        '40016' => '不合法的按钮个数',
        '40017' => '不合法的按钮个数',
        '40018' => '不合法的按钮名字长度',
        '40019' => '不合法的按钮KEY长度',
        '40020' => '不合法的按钮URL长度',
        '40021' => '不合法的菜单版本号',
        '40022' => '不合法的子菜单级数',
        '40023' => '不合法的子菜单按钮个数',
        '40024' => '不合法的子菜单按钮类型',
        '40025' => '不合法的子菜单按钮名字长度',
        '40026' => '不合法的子菜单按钮KEY长度',
        '40027' => '不合法的子菜单按钮URL长度',
        '40028' => '不合法的自定义菜单使用用户',
        '40029' => '不合法的oauth_code',
        '40030' => '不合法的refresh_token',
        '40031' => '不合法的openid列表',
        '40032' => '不合法的openid列表长度',
        '40033' => '不合法的请求字符，不能包含\uxxxx格式的字符',
        '40035' => '不合法的参数',
        '40038' => '不合法的请求格式',
        '40039' => '不合法的URL长度',
        '40050' => '不合法的分组id',
        '40051' => '分组名字不合法',
        '40117' => '分组名字不合法',
        '40118' => 'media_id大小不合法',
        '40119' => 'button类型错误',
        '40120' => 'button类型错误',
        '40121' => '不合法的media_id类型',
        '40132' => '微信号不合法',
        '40137' => '不支持的图片格式',
        '41001' => '缺少access_token参数',
        '41002' => '缺少appid参数',
        '41003' => '缺少refresh_token参数',
        '41004' => '缺少secret参数',
        '41005' => '缺少多媒体文件数据',
        '41006' => '缺少media_id参数',
        '41007' => '缺少子菜单数据',
        '41008' => '缺少oauth code',
        '41009' => '缺少openid',
        '42001' => 'access_token超时，请检查access_token的有效期，请参考基础支持-获取access_token中，对access_token的详细机制说明',
        '42002' => 'refresh_token超时',
        '42003' => 'oauth_code超时',
        '43001' => '需要GET请求',
        '43002' => '需要POST请求',
        '43003' => '需要HTTPS请求',
        '43004' => '需要接收者关注',
        '43005' => '需要好友关系',
        '44001' => '多媒体文件为空',
        '44002' => 'POST的数据包为空',
        '44003' => '图文消息内容为空',
        '44004' => '文本消息内容为空',
        '45001' => '多媒体文件大小超过限制',
        '45002' => '消息内容超过限制',
        '45003' => '标题字段超过限制',
        '45004' => '描述字段超过限制',
        '45005' => '链接字段超过限制',
        '45006' => '图片链接字段超过限制',
        '45007' => '语音播放时间超过限制',
        '45008' => '图文消息超过限制',
        '45009' => '接口调用超过限制',
        '45010' => '创建菜单个数超过限制',
        '45015' => '回复时间超过限制',
        '45016' => '系统分组，不允许修改',
        '45017' => '分组名字过长',
        '45018' => '分组数量超过上限',
        '46001' => '不存在媒体数据',
        '46002' => '不存在的菜单版本',
        '46003' => '不存在的菜单数据',
        '46004' => '不存在的用户',
        '47001' => '解析JSON/XML内容错误',
        '48001' => 'api功能未授权，请确认公众号已获得该接口，可以在公众平台官网-开发者中心页中查看接口权限',
        '50001' => '用户未授权该api',
        '50002' => '用户受限，可能是违规后接口被封禁',
        '61451' => '参数错误(invalid parameter)',
        '61452' => '无效客服账号(invalid kf_account)',
        '61453' => '客服帐号已存在(kf_account exsited)',
        '61454' => '客服帐号名长度超过限制(仅允许10个英文字符，不包括@及@后的公众号的微信号)(invalid kf_acount length)',
        '61455' => '客服帐号名包含非法字符(仅允许英文+数字)(illegal character in kf_account)',
        '61456' => '客服帐号个数超过限制(10个客服账号)(kf_account count exceeded)',
        '61457' => '无效头像文件类型(invalid file type)',
        '61450' => '系统错误(system error)',
        '61500' => '日期格式错误',
        '61501' => '日期范围错误',
        '9001001' => 'POST数据参数不合法',
        '9001002' => '远端服务不可用',
        '9001003' => 'Ticket不合法',
        '9001004' => '获取摇周边用户信息失败',
        '9001005' => '获取商户信息失败',
        '9001006' => '获取OpenID失败',
        '9001007' => '上传文件缺失',
        '9001008' => '上传素材的文件类型不合法',
        '9001009' => '上传素材的文件尺寸不合法',
        '9001010' => '上传失败',
        '9001020' => '帐号不合法',
        '9001021' => '已有设备激活率低于50%，不能新增设备',
        '9001022' => '设备申请数不合法，必须为大于0的数字',
        '9001023' => '已存在审核中的设备ID申请',
        '9001024' => '一次查询设备ID数量不能超过50',
        '9001025' => '设备ID不合法',
        '9001026' => '页面ID不合法',
        '9001027' => '页面参数不合法',
        '9001028' => '一次删除页面ID数量不能超过10',
        '9001029' => '页面已应用在设备中，请先解除应用关系再删除',
        '9001030' => '一次查询页面ID数量不能超过50',
        '9001031' => '时间区间不合法',
        '9001032' => '保存设备与页面的绑定关系参数错误',
        '9001033' => '门店ID不合法',
        '9001034' => '设备备注信息过长',
        '9001035' => '设备申请参数不合法',
        '9001036' => '查询起始值begin不合法'
    );

    if ($more_tips) {
        $res = $more_tips . ': ';
    } else {
        $res = '';
    }
    if (isset($msg[$return['errcode']])) {
        $res .= $msg[$return['errcode']];
    } else {
        $res .= $return['errmsg'];
    }

    $res .= ', 返回码：' . $return['errcode'];

    return $res;
}

/* yqx */
function virifylocal()
{
    define('VIRIFY', true);
    define('HTML_VESION', 'index3_6');
    config('JS_VISION', 3.6);
}

/**
 * 获取随机字符串
 *
 * @param number $length
 * @return string
 */
function createNonceStr($length = 16)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

function randorderno($length = 10, $type = 0)
{
    $arr = array(
        1 => "3425678934567892345678934567892",
        2 => "ABCDEFGHJKLMNPQRSTUVWXY"
    );
    $code = '';
    if ($type == 0) {
        array_pop($arr);
        $string = implode("", $arr);
    } else {
        if ($type == "-1") {
            $string = implode("", $arr);
        } else {
            $string = $arr[$type];
        }
    }
    $count = strlen($string) - 1;
    for ($i = 0; $i < $length; $i++) {
        $str[$i] = $string[rand(0, $count)];
        $code .= $str[$i];
    }
    return $code;
}

function aboutaa()
{
    virifylocal();
}

// 泛域名情况下的域名替换
function chang_domain($url, $domain)
{
    if (!config('DIV_DOMAIN') || is_numeric($domain) || SITE_DOMAIN == 'localhost' || empty($domain)) {
        return $url;
    }

    $arr = explode('.', SITE_DOMAIN);
    if (count($arr) < 3) {
        // 顶级域名
        $new_site_domain = $domain . '.' . SITE_DOMAIN;
    } else {
        $arr[0] = $domain;
        $new_site_domain = implode('.', $arr);
    }

    $url = str_replace(SITE_DOMAIN, $new_site_domain, $url);

    return $url;
}

// 获取当前网址的顶级域名
function top_domain()
{
    if (strpos(SITE_DOMAIN, 'weiphp.cn') !== false) {
        return '.weiphp.cn';
    } else {
        return '.oftenchat.cn';
    }
}

// 从微信下载临时图片文件
// $media_id 媒体文件ID
function down_media($media_id)
{
    $savePath = SITE_PATH . '/public/uploads/picture/' . time_format(NOW_TIME, 'Y-m-d');
    mkdirs($savePath);
    // 获取图片URL

    $url = 'http://api.weixin.qq.com/cgi-bin/media/get?access_token=' . get_access_token() . '&media_id=' . $media_id;
    $picContent = wp_file_get_contents($url);
    $picjson = json_decode($picContent, true);
    if (isset($picjson['errcode']) && $picjson['errcode'] != 0) {
        return 0;
    }

    $picName = $media_id . '.jpg';
    $picPath = $savePath . '/' . $picName;
    $res = file_put_contents($picPath, $picContent);
    $cover_id = 0;
    if ($res) {
        // 保存记录，添加到picture表里，获取coverid
        $url = U('home/File/upload_picture', array(
            'session_id' => session_id()
        ));
        $_FILES['download'] = array(
            'name' => $picName,
            'type' => 'application/octet-stream',
            'tmp_name' => $picPath,
            'size' => $res,
            'error' => 0
        );
        $Picture = D('home/Picture');
        $pic_driver = config('PICTURE_UPLOAD_DRIVER');
        $files = request()->file();
        $info = $Picture->upload($files, config('PICTURE_UPLOAD'), config('PICTURE_UPLOAD_DRIVER'), config("UPLOAD_{$pic_driver}_CONFIG"));
        $cover_id = $info['download']['id'];
        @unlink($picPath);
    }
    return $cover_id;
}

function outputCurl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

// 上传多媒体文体
function upload_media($path, $type = 'image')
{
    $url = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token=' . get_access_token();
    $param = upload_param_by_curl($path);
    $param['type'] = $type;

    $res = post_data($url, $param, 'file');
    if (isset($res['errcode']) && $res['errcode'] != 0) {
        $this->error(error_msg($res, '图片上传'));
        exit();
    }
    return $res['media_id'];
}

// 下载永久素材
function do_down_image($media_id, $picUrl = '')
{
    $savePath = SITE_PATH . '/public/uploads/picture/' . time_format(NOW_TIME, 'Y-m-d');
    mkdirs($savePath);
    if (empty($picUrl)) {
        // 获取图片URL
        $url = 'https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=' . get_access_token();
        $param['media_id'] = $media_id;
        $picContent = post_data($url, $param, 'json', false);
        $picjson = json_decode($picContent, true);
        if (isset($picjson['errcode']) && $picjson['errcode'] != 0) {
            return 0;
        }
        // dump($picContent);
        // dump($picjson);
        // if ($picContent){
        $picName = NOW_TIME . uniqid() . '.jpg';
        $picPath = $savePath . '/' . $picName;
        $res = file_put_contents($picPath, $picContent);
        // }
    } else {
        $content = wp_file_get_contents($picUrl);
        // 获取图片扩展名

        $picName = NOW_TIME . uniqid() . '.jpg';
        $picPath = $savePath . '/' . $picName;
        $res = file_put_contents($picPath, $content);
        if (!$res) {
            // $this->error ( '远程图片下载失败' );
            // exit ();
            return 0;
            exit();
        }
    }
    $cover_id = 0;
    if ($res) {
        // 保存记录，添加到picture表里，获取coverid
        $url = U('home/File/upload_picture', array(
            'session_id' => session_id()
        ));
        $_FILES['download'] = array(
            'name' => $picName,
            'type' => 'application/octet-stream',
            'tmp_name' => $picPath,
            'size' => $res,
            'error' => 0
        );
        $Picture = D('home/Picture');
        $pic_driver = config('PICTURE_UPLOAD_DRIVER');
        $files = request()->file();
        $info = $Picture->upload($files, config('PICTURE_UPLOAD'), config('PICTURE_UPLOAD_DRIVER'), config("UPLOAD_{$pic_driver}_CONFIG"));
        $cover_id = $info['download']['id'];
        @unlink($picPath);
    }
    return $cover_id;
}

// 下载临时语言、视频素材
function down_file_media($media_id, $type = 'voice')
{
    $savePath = SITE_PATH . '/public/uploads/download/' . time_format(NOW_TIME, 'Y-m-d');
    mkdirs($savePath);
    // 获取图片URL
    $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . get_access_token() . '&media_id=' . $media_id;
    $picContent = outputCurl($url);
    $picjson = json_decode($picContent, true);
    if (isset($picjson['errcode']) && $picjson['errcode'] != 0) {
        return 0;
    }
    if ($type == 'voice') {
        $ext = 'mp3';
    } else {
        if ($type == 'video') {
            $ext = 'mp4';
        }
    }
    if ($picContent) {
        $picName = NOW_TIME . uniqid() . '.' . $ext;
        $picPath = $savePath . '/' . $picName;
        $res = file_put_contents($picPath, $picContent);
    }
    $cover_id = 0;
    if ($res) {
        // 保存记录，添加到picture表里，获取coverid
        $_FILES['download'] = array(
            'name' => $picName,
            'type' => 'application/octet-stream',
            'tmp_name' => $picPath,
            'size' => $res,
            'error' => 0
        );
        $File = D('home/File');
        $file_driver = config('DOWNLOAD_UPLOAD_DRIVER');
        $info = $File->upload($_FILES, config('DOWNLOAD_UPLOAD'), config('DOWNLOAD_UPLOAD_DRIVER'), config("UPLOAD_{$file_driver}_CONFIG"));
        $cover_id = $info['download']['id'];
        @unlink($picPath);
    }
    return $cover_id;
}

// 二维数组根据键排序
function array_sort($arr, $keys, $type = 'desc')
{
    $keysvalue = $new_array = [];
    foreach ($arr as $k => $v) {
        $keysvalue[$k] = $v[$keys];
    }
    if ($type == 'asc') {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}

function test_mem_cache()
{
    $key = 'test_mem_cache';
    $res = S($key);
    if ($res !== false) {
        return $res;
    }

    S($key, NOW_TIME, 7200);
    return NOW_TIME;
}

// 转换得到含emoji表情的代码 注意引入css文件
function parseHtmlemoji($text)
{
    require_once env('vendor_path') . "emoji.php";
    $tmpStr = json_encode($text);
    $tmpStr = preg_replace_callback("#(\\\ue[0-9a-f]{3})#i", function ($r) {
        return addslashes('\\1');
    }, $tmpStr);

    $text = json_decode($tmpStr);
    preg_match_all("#u([0-9a-f]{4})+#iUs", $text, $rs);
    if (empty($rs[1])) {
        return $text;
    }
    foreach ($rs[1] as $v) {
        $test_iphone = '0x' . trim(strtoupper($v));
        $test_iphone = $test_iphone + 0;
        $utbytes = utf8_bytes($test_iphone);
        $emji = emoji_softbank_to_unified($utbytes);
        $t = emoji_unified_to_html($emji);
        $text = str_replace("\u$v", $t, $text);
    }
    return $text;
}

function utf8_bytes($cp)
{
    if ($cp > 0x10000) {
        // 4 bytes
        return chr(0xF0 | (($cp & 0x1C0000) >> 18)) . chr(0x80 | (($cp & 0x3F000) >> 12)) . chr(0x80 | (($cp & 0xFC0) >> 6)) . chr(0x80 | ($cp & 0x3F));
    } else {
        if ($cp > 0x800) {
            // 3 bytes
            return chr(0xE0 | (($cp & 0xF000) >> 12)) . chr(0x80 | (($cp & 0xFC0) >> 6)) . chr(0x80 | ($cp & 0x3F));
        } else {
            if ($cp > 0x80) {
                // 2 bytes
                return chr(0xC0 | (($cp & 0x7C0) >> 6)) . chr(0x80 | ($cp & 0x3F));
            } else {
                // 1 byte
                return chr($cp);
            }
        }
    }
}

function curl_post($url, $data = null)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); // 设置访问的url地址
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.87 Safari/537.36');
    /*
     * if($data){
     * curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
     * curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
     * curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
     *
     * }
     */
    $data && curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);
    if (curl_errno($ch)) {
        return curl_error($ch);
    }
    curl_close($ch);
    return $tmpInfo;
}

function matchImages($content = '')
{
    $src = [];
    preg_match_all('/<img.*src=\s*[\'"](.*)[\s>\'"]/isU', $content, $src);
    if (count($src[1]) > 0) {
        foreach ($src[1] as $v) {
            $images[] = trim($v, "\"'"); // 删除首尾的引号 ' "
        }
        return $images;
    } else {
        return false;
    }
}

function getEditorImages($content)
{
    preg_match_all('/<img.*src=\s*[\'"](.*)[\s>\'"]/isU', $content, $matchs);
    $image = '';
    foreach ($matchs[1] as $match) {
        $isFace = strpos($match, '/emotion/') === false ? false : true;
        if ($isFace) {
            continue;
        }
        if (preg_match('/http:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is', $match) && !$isFace) {
            $image = $match;
        } else {
            if (!$isFace) {
                $url = implode('/', array_slice(explode('/', $match), -4));
                // $image = getImageUrl($url, 200, 200, true);
            }
        }
        break;
    }

    return $image;
}

function matchReplaceImages($content = '')
{
    $image = preg_replace_callback('/<img.*src=\s*[\'"](.*)[\s>\'"]/isU', "matchReplaceImagesOnce", $content);
    return $image;
}

function matchReplaceImagesOnce($matches)
{
    $matches[1] = str_replace('"', '', $matches[1]);
    return sprintf("<a class='thickbox'  href='%s'>%s</a>", $matches[1], $matches[0]);
}

/**
 * 获取字符串的长度
 *
 * 计算时, 汉字或全角字符占1个长度, 英文字符占0.5个长度
 *
 * @param string $str
 * @param boolean $filter
 *            是否过滤html标签
 * @return int 字符串的长度
 */
function get_str_length($str, $filter = false)
{
    if ($filter) {
        $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
        $str = strip_tags($str);
    }
    return (strlen($str) + mb_strlen($str, 'UTF8')) / 4;
}

/**
 * 检查表单是否已锁定
 *
 * @return boolean 表单已锁定时返回true, 否则返回false
 */
function isSubmitLocked()
{
    return isset($_SESSION['LOCK_SUBMIT_TIME']) && intval($_SESSION['LOCK_SUBMIT_TIME']) > time();
}

/**
 * 锁定表单
 *
 * @param int $life_time
 *            表单锁的有效时间(秒). 如果有效时间内未解锁, 表单锁自动失效.
 * @return boolean 成功锁定时返回true, 表单锁已存在时返回false
 */
function lockSubmit($life_time = null)
{
    if (isset($_SESSION['LOCK_SUBMIT_TIME']) && intval($_SESSION['LOCK_SUBMIT_TIME']) > time()) {
        return false;
    } else {
        $life_time = $life_time ? $life_time : 10;
        $_SESSION['LOCK_SUBMIT_TIME'] = time() + intval($life_time);
        return true;
    }
}

/**
 * 表单解锁
 *
 * @return void
 */
function unlockSubmit()
{
    unset($_SESSION['LOCK_SUBMIT_TIME']);
}

/**
 * 记录自动检测过程数据
 */
function addAutoCheckLog($na = '', $msg = '', $wpid = '')
{
    $data['na'] = empty($na) ? I('na') : $na;
    $data['msg'] = $msg;
    $data['wpid'] = empty($wpid) ? get_wpid() : $wpid;

    // dump ( $data );
    $res = M('public_check')->insertGetId($data);
    // dump ( M( 'public_check' )->getLastSql() );
}

/**
 * 通用奖品选择器
 * 显示奖品信息
 */
function get_prize_detail($prizeValue)
{
    $data = [];
    $prizeData = explode(',', $prizeValue);
    foreach ($prizeData as $key => $value) {
        $keyArr = explode(':', $value);
        if (empty($keyArr[0])) {
            continue;
        }

        $title = '';
        $typeName = '';
        $imgurl = '';
        $total_count = 0;
        $num = $keyArr[2];
        if ($keyArr[0] == 'coupon') {
            $pdata = D('Coupon/Coupon')->getInfo($keyArr[1]);
            $typeName = '优惠卷';
            $title = $pdata['title'];
            $imgurl = $pdata['background'];
            $total_count = $pdata['num'];
        } elseif ($keyArr[0] == 'realPrize') {
            $pdata = D('RealPrize/RealPrize')->getInfo($keyArr[1]);
            $typeName = '实物奖励';
            $title = $pdata['prize_name'];
            $imgurl = $pdata['prize_image'];
            $total_count = $pdata['prize_count'];
        } elseif ($keyArr[0] == 'cardVouchers') {
            $pdata = D('CardVouchers/CardVouchers')->getInfo($keyArr[1]);
            $typeName = '微信卡卷';
            $title = $pdata['title'];
            $imgurl = $pdata['background'];
        } elseif ($keyArr[0] == 'redBag') {
            $pdata = D('Redbag/Redbag')->getInfo($keyArr[1]);
            $typeName = '微信红包';
            $title = $pdata['act_name'];
            $total_count = $pdata['total_num'];
        } elseif ($keyArr[0] == 'points') {
            $typeName = '积分';
            $title = '积分';
            $num = $keyArr[3];
            $total_count = $keyArr[2]; // 奖励的积分数
        }

        $data['title'][$key] = $title;
        $data['id'][$key] = $keyArr[1];
        $data['typeName'][$key] = $typeName;
        $data['img'][$key] = get_cover_url($imgurl);
        $data['num'][$key] = $num;
        $data['total_count'][$key] = $total_count;
        $data['type'][$key] = $keyArr[0];
    }
    return $data;
}

// 文件名
/**
 * 获取缩略图
 *
 * @param unknown_type $filename
 *            原图路劲、url
 * @param unknown_type $width
 *            宽度
 * @param unknown_type $height
 *            高
 * @param unknown_type $cut
 *            是否切割 默认不切割
 * @return string
 */
function getThumbImage($filename, $width = 100, $height = 'auto', $cut = false, $replace = false, $redirect = false)
{
    return $filename; // 待完善
}

// 判断应用插件是否已经安装
function is_install($addon_name)
{
    $list = D('home/Addons')->getList();
    $addon_name = parse_name($addon_name);
    return isset($list[$addon_name]);
}

/**
 * 获取客户端IP地址
 *
 * @param integer $type
 *            返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv
 *            是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false)
{
    $type = $type ? 1 : 0;
    static $ip = null;
    if ($ip !== null) {
        return $ip[$type];
    }

    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }

            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array(
        $ip,
        $long
    ) : array(
        '0.0.0.0',
        0
    );
    return $ip[$type];
}

function get_server_ip()
{
    if (isset($_SERVER['SERVER_ADDR'])) {
        $server_ip = $_SERVER['SERVER_ADDR'];
    } elseif (isset($_SERVER["SERVER_NAME"])) {
        $server_ip = gethostbyname($_SERVER["SERVER_NAME"]);
    } elseif (isset($_SERVER['LOCAL_ADDR'])) {
        $server_ip = $_SERVER['LOCAL_ADDR'];
    } else {
        $server_ip = getenv('SERVER_ADDR');
    }
    return $server_ip;
}

/**
 * 字符串命名风格转换
 * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 *
 * @param string $name
 *            字符串
 * @param integer $type
 *            转换类型
 * @return string
 */
function parse_name($name, $type = 0)
{
    if ($type) {
        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function ($match) {
            return strtoupper($match[1]);
        }, $name));
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}

//给parse_name定义别名，好理解些
function parse_name_upper($name)
{
    return parse_name($name, 0);
}

function parse_name_lower($name)
{
    return parse_name($name, 1);
}

/*
 * 上传图片到微信获取url
 * 上传图文消息内的图片获取URL 请注意，本接口所上传的图片不占用公众号的素材库中图片数量的5000个的限制。
 * 图片仅支持jpg/png格式，大小必须在1MB以下。
 */
function uploadimg($path)
{
    if (preg_match('#^(http|https)://mmbiz.qpic.cn/#i', $path)) {
        return $path;
    }
    $filePath = '';
    if (!file_exists($path)) {
        $filePath = './uploads/' . think_weiphp_md5($path) . '.jpg';
        getImg($path, $filePath);
        $path = $filePath;
    }
    $url = 'https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=' . get_access_token();
    $param = upload_param_by_curl($path);
    $param['type'] = 'image';

    $res = post_data($url, $param, 'file');
    @unlink($filePath); // 删除本地创建的图片
    return empty($res['url']) ? '' : $res['url'];
}

/*
 * @通过curl方式获取指定的图片到本地
 * @ 完整的图片地址
 * @ 要存储的文件名
 */
function getImg($url = "", $filename = "")
{
    // 去除URL连接上面可能的引号
    // $url = preg_replace( '/(?:^['"]+|['"/]+$)/', '', $url );
    $hander = curl_init();
    $fp = fopen($filename, 'wb');
    curl_setopt($hander, CURLOPT_URL, $url);
    curl_setopt($hander, CURLOPT_FILE, $fp);
    curl_setopt($hander, CURLOPT_HEADER, 0);
    curl_setopt($hander, CURLOPT_FOLLOWLOCATION, 1);
    // curl_setopt($hander,CURLOPT_RETURNTRANSFER,false);//以数据流的方式返回数据,当为false是直接显示出来
    curl_setopt($hander, CURLOPT_TIMEOUT, 10);
    curl_exec($hander);
    curl_close($hander);
    fclose($fp);
    return true;
}

// 获取显示确定规格图片
/*
 * http://img.baidu.com/hi/jx2/j_0002.gif
 * http://img1.gtimg.com/auto/pics/hv1/156/84/2125/138199701.jpg
 * /uploads/Editor/gh_dd85ac50d2dd/2016-08-26/57bfa4a23fba5.png
 */
function mk_rule_image($imgurl, $w, $h)
{
    if (preg_match('#^/uploads/picture/#i', $imgurl)) {
        // 内部图片
        $img = $imgurl;
        $imgurl = '.' . $img;
        $filename = basename($imgurl);
        $filename_ex = explode('.', $filename);
        $dirname = dirname($imgurl);
        $dirname_new = $dirname . '/' . $filename_ex[0] . "_$w" . "X$h." . $filename_ex[1];
        // dump($dirname_new);
        if (file_exists($dirname_new)) {
            return str_replace('./uploads', SITE_URL . '/uploads', $dirname_new);
        }

        file_exists($imgurl) && $imginfo = getimagesize($imgurl); // 图片存在并获取到信息

        $imginfo = isset($imginfo) ? $imginfo : '';
        if ($imginfo) {
            // 规格图片存在
            if ($imginfo[0] > $w || $imginfo['1'] > $h) {
                isset($img_model) || $img_model = \think\Image::open($imgurl);
                // 生成缩略图

                $res = $img_model->thumb($w, $h)->save($dirname_new);
            } else {
                return SITE_URL . $img;
            }
        }
        return str_replace('./uploads', SITE_URL . '/uploads', $dirname_new);
    }
    if (preg_match('#^(http|https)://#i', $imgurl)) {
        // 外部
        $imgurl1 = $imgurl;
        $imginfo = getimagesize($imgurl); // 图片存在并获取到信息
        // dump($imginfo);
        $url_info = parse_url($imgurl);
        $filename = basename($url_info['path']);
        $filename_ex = explode('.', $filename);
        $dirname = './uploads/picture';
        $dirname_new = $dirname . '/' . think_weiphp_md5($filename_ex[0] . $url_info['query']) . "_$w" . "X$h." . 'jpg'; // $filename_ex[1];
        $imgurl = SITE_URL . '/uploads/picture/' . think_weiphp_md5($filename_ex[0] . $url_info['query']) . "_$w" . "X$h." . 'jpg';
        if (file_exists($dirname_new)) {
            return $imgurl;
        }
        if ($imginfo) {
            // 规格图片存在
            if ($imginfo[0] > $w || $imginfo[1] > $h) {
                $save_filename = './uploads/picture/' . $filename;
                isset($img_model) || $img_model = \think\Image::open($save_filename);
                $res = getImg($imgurl1, $save_filename);

                // $re = $img_model->open($save_filename);

                $res = $img_model->thumb($w, $h)->save($dirname_new);
                @unlink($save_filename);
            } else {
                getImg($imgurl1, $dirname_new);
                $imgurl = SITE_URL . $dirname_new;
            }
        }
        return $imgurl;
    }
    return SITE_URL . $imgurl;
}

function dealPage($page_data)
{
    if (empty($page_data)) {
        $list['list_data'] = [];
        $list['count'] = 0;
        $list['_page'] = '';
    } else {
        $page = $page_data->render();
        $page_data = $page_data->toArray();
        $list['list_data'] = $page_data['data'];
        $list['count'] = $page_data['total'];
        $list['_page'] = $page;
    }
    return $list;
}

function check_auth($rule_name, $mod = 'common', $uid = 0)
{
    // 获取用户权限合集
    $user_rule = getUserInfo($uid, 'rule');
    if (empty($user_rule)) {
        return false; // 用户没有任何权限直接返回false
    }

    // 获取权限列表
    static $auth_rule;
    if (empty($auth_rule)) {
        $auth_rule = D('common/AuthRule')->getList();
    }
    if (empty($auth_rule)) {
        return false; // 规则为空不判断,直接返回false
    }

    if (!isset($auth_rule[$mod . ':' . $rule_name])) {
        return false; // 没有配置的规则，默认是false,开发者也可以根据需要设置为true
    }

    $rule_id = $auth_rule[$mod . ':' . $rule_name];
    return isset($user_rule[$rule_id]) ? true : false;
}

function add_admin_log($content, $mod = 'admin')
{
    $log['uid'] = $GLOBALS['mid'];
    $log['ip'] = get_client_ip();
    $log['content'] = is_array($content) ? serialize($content) : $content;
    $log['mod'] = $mod;
    $log['cTime'] = NOW_TIME;
    M('admin_log')->insert($log);
}

function add_request_log($url, $param = [], $res = [], $error_code = '', $msg = '')
{
    $log['url'] = $url;
    $log['param'] = is_array($param) ? serialize($param) : $param;
    $log['md5'] = md5($log['url'] . $log['param']);
    $log['error_code'] = $error_code;
    $log['msg'] = $msg;
    $log['res'] = is_array($res) ? serialize($res) : $res;
    $log['server_ip'] = get_client_ip();
    $log['cTime'] = NOW_TIME;
    M('request_log')->insert($log);

    // 自动删除48小时以前正常的日志，错误的日志需要手工删除
    $key = 'clean_request_log_' . date('Ymd');
    $day_lock = S($key);
    if ($day_lock !== false) {
        return true; // 一天只清一次
    } else {
        S($key, 1, 86400);
    }

    $map['error_code'] = '0';
    $map['cTime'] = [
        'lt',
        NOW_TIME - 172800
    ];
    M('request_log')->where(wp_where($map))->delete();
}

function add_debug_log($data, $data_post = '')
{
    $log['cTime'] = time();
    $log['cTime_format'] = date('Y-m-d H:i:s', $log['cTime']);
    $log['data'] = is_array($data) ? serialize($data) : $data;
    $log['data_post'] = is_array($data_post) ? serialize($data_post) : $data_post;

    M('debug_log')->insert($log);
}

function addWeixinLog($data, $data_post = '')
{
    add_debug_log($data, $data_post);
}

/**
 * 上传文件(单个)
 *
 * @param 文件信息数组 $files
 *            ，通常是 $_FILES数组
 */
function upload_files($setting = '', $driver = '', $config = '', $type = 'picture')
{
    $return['msg'] = '';

    $files = request()->file();
    // dump($_FILES);
    // dump($files);//dump($rr);
    if (count($files) <= 0) {
        $return['msg'] = '找不到上传文件';
    }
    if ($return['msg'] != '') {
        return $return;
    }

    $key = key($files);
    $file = isset($files[$key]) ? $files[$key] : [];
    $rootpath = './uploads/' . $type . '/';
    $saveName = time_format(time(), 'Ymd') . '/' . uniqid();

    if (isset($setting['rootPath'])) {
        unset($setting['rootPath']);
    }

    // 检测上传根目录
    if (empty($return['msg'])) {
        if (!is_dir($rootpath) && function_exists('mkdirs')) {
            mkdirs($rootpath);
        }

        if (!(is_dir($rootpath) && is_writable($rootpath))) {
            $return['msg'] = '上传根目录不存在！请尝试手动创建:' . $rootpath;
        }
    }
    if (empty($return['msg'])) {
        $info = $file->isTest(false)
            ->rule('uniqid')
            ->move($rootpath, DIRECTORY_SEPARATOR . $saveName);
        if ($info) {
            $return['mime'] = $info->getMime();
            $return['name'] = $info->getFilename();
            $return['key'] = $key;
            $return['ext'] = $info->getExtension();
            $return['savename'] = str_replace('\\', '/', $info->getSaveName());
            $return['md5'] = $info->md5();
            $return['sha1'] = $info->sha1();
            $return['code'] = 1;
            $of = $info->getInfo();
            isset($of['name']) || $of['name'] = $return['name'];
            $return['old_name'] = $of['name'];
            $return['size'] = isset($of['size']) ? $of['size'] : 0;
            $return['rootPath'] = $rootpath;
        } else {
            $return['msg'] = $file->getError();
            $return['code'] = 0;
        }
    }
    $redata[$key] = $return;
    return $redata;
}

// api return
function api_success($data = [], $is_json = true)
{
    if (isset($data['status']) || isset($data['msg'])) {
        $return['status'] = 1;
        $return['msg'] = '';
        $return['data'] = $data;
        return $is_json ? json_url($return) : $return;
    } else {
        $data['status'] = 1;
        $data['msg'] = '';
        return $is_json ? json_url($data) : $data;
    }
}

function api_error($msg = '', $is_json = true)
{
    $return['status'] = 0;
    $return['msg'] = $msg;
    return $is_json ? json_url($return) : $return;
}

// 表单里初始化数据
function initValue($field, $data = [], $defalut = '')
{
    return isset($data[$field]) ? $data[$field] : $defalut;
}

function wp_where($field)
{
    if (!is_array($field)) {
        return $field;
    }

    $res = [];
    foreach ($field as $key => $value) {
        if (is_numeric($key) || (is_array($value) && count($value) == 3)) {
            if (strtolower($value[1]) == 'exp' && !is_object($value[2])) {
                $value[2] = Db::raw($value[2]);
            }
            $res[] = $value;
        } elseif (is_array($value)) {
            if (strtolower($value[0]) == 'exp' && !is_object($value[1])) {
                $value[1] = Db::raw($value[1]);
            }
            $res[] = [
                $key,
                $value[0],
                $value[1]
            ];
        } else {
            $res[] = [
                $key,
                '=',
                $value
            ];
        }
    }

    return $res;
}

function file_log($content, $file_name = "debug", $title = '')
{
    if (isset($_GET['debug'])) {
        empty($title) || dump('title:' . $title);
        dump($content);
        return false;
    }

    if (is_array($content)) {
        $content = "\r\n" . var_export($content, true);
    }
    if (!empty($title)) {
        $content = $title . $content;
    }
    $file_name = $file_name != "" ? $file_name . "_" : "";
    $logPath = env('runtime_path') . "log/" . $file_name . date("Y-m-d") . ".log";

    error_log("\r\n" . date("[Y-m-d H:i:s]") . " : " . $content, 3, $logPath);
}

// 判断是否为管理员用户,true:是管理员，false:不是管理员
function is_manager($uid)
{
    return $uid == config('app.user_administrator') ? true : false;
}

// 不区分大小写的in_array实现
function in_array_case($value, $array)
{
    return in_array(strtolower($value), array_map('strtolower', $array));
}

function chinese_number($number)
{
    if ($number > 9) {
        return $number;
    }

    $arr = [
        "零",
        "一",
        "二",
        "三",
        "四",
        "五",
        "六",
        "七",
        "八",
        "九",
        "十"
    ];
    return $arr[$number + 1];
}

/**
 * +----------------------------------------------------------
 * 将一个字符串部分字符用*替代隐藏
 * +----------------------------------------------------------
 *
 * @param string $string
 *            待转换的字符串
 * @param int $bengin
 *            起始位置，从0开始计数，当$type=4时，表示左侧保留长度
 * @param int $len
 *            需要转换成*的字符个数，当$type=4时，表示右侧保留长度
 * @param int $type
 *            转换类型：0，从左向右隐藏；1，从右向左隐藏；2，从指定字符位置分割前由右向左隐藏；3，从指定字符位置分割后由左向右隐藏；4，保留首末指定字符串
 * @param string $glue
 *            分割符
 *            +----------------------------------------------------------
 * @return string 处理后的字符串
 *         +----------------------------------------------------------
 */
function hideStr($string, $bengin = 0, $len = 4, $type = 2, $glue = "@")
{
    if (empty($string)) {
        return false;
    }

    $array = [];
    if ($type == 0 || $type == 1 || $type == 4) {
        $strlen = $length = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string, 0, 1, "utf8");
            $string = mb_substr($string, 1, $strlen, "utf8");
            $strlen = mb_strlen($string);
        }
    }
    if ($type == 0) {
        for ($i = $bengin; $i < ($bengin + $len); $i++) {
            if (isset($array[$i])) {
                $array[$i] = "*";
            }
        }
        $string = implode("", $array);
    } else {
        if ($type == 1) {
            $array = array_reverse($array);
            for ($i = $bengin; $i < ($bengin + $len); $i++) {
                if (isset($array[$i])) {
                    $array[$i] = "*";
                }
            }
            $string = implode("", array_reverse($array));
        } else {
            if ($type == 2) {
                $array = explode($glue, $string);
                $array[0] = hideStr($array[0], $bengin, $len, 1);
                $string = implode($glue, $array);
            } else {
                if ($type == 3) {
                    $array = explode($glue, $string);
                    $array[1] = hideStr($array[1], $bengin, $len, 0);
                    $string = implode($glue, $array);
                } else {
                    if ($type == 4) {
                        $left = $bengin;
                        $right = $len;
                        $tem = [];
                        for ($i = 0; $i < ($length - $right); $i++) {
                            if (isset($array[$i])) {
                                $tem[] = $i >= $left ? "*" : $array[$i];
                            }
                        }
                        $array = array_chunk(array_reverse($array), $right);
                        $array = array_reverse($array[0]);
                        for ($i = 0; $i < $right; $i++) {
                            $tem[] = $array[$i];
                        }
                        $string = implode("", $tem);
                    }
                }
            }
        }
    }
    return $string;
}

// cache_key('搜索字段，格式：uid:10,wpid:200 或者：['uid'=>10,'wpid'=>200]','数据表名','缓存字段，可为空，如：id,name','扩展名，可为空')
function cache_key($map_field, $table_name, $data_field = '', $extra = '')
{
    $table_name = parse_name($table_name);
    $pre = substr($table_name, 0, strlen(DB_PREFIX));
    if ($pre != DB_PREFIX) {
        $table_name = DB_PREFIX . $table_name;
    }
    // dump($map_field);
    $key = $key_rule = 'wpcache_' . $table_name . '_';
    if (is_string($map_field)) {
        $fields = wp_explode($map_field, ',');
        $map_field = [];
        foreach ($fields as $vo) {
            list ($k, $val) = explode(':', $vo, 2);
            $k = trim($k);
            $val = trim($val);
            $map_field[$k] = $val;
        }
    }

    foreach ($map_field as $f => $v) {
        $key .= $f . '-' . $v . '_';
        $key_rule .= $f . '-[' . $f . ']_';
    }

    $key = rtrim($key, '_');
    $key_rule = rtrim($key_rule, '_');
    if (!empty($extra)) {
        $key .= '_' . $extra;
        $key_rule .= '_' . $extra;
    }

    // 实现$key_rule自动注册
    $list_keys = keys_list($table_name);
    // dump($list_keys);
    $table_keys = [];
    foreach ($list_keys as $vo) {
        $table_keys[$vo['key_rule']] = 1;
    }

    // dump($table_keys);
    // exit();
    if (!isset($table_keys[$key_rule])) {
        M('cache_keys')->insert([
            'table_name' => $table_name,
            'key_rule' => $key_rule,
            'map_field' => json_encode($map_field),
            'data_field' => is_array($data_field) ? implode(',', $data_field) : $data_field,
            'extra' => $extra
        ]);
        S('keyscache_' . $table_name, null);
    }
    // dump($key);
    return $key;
}

function keys_list($table_name)
{
    // dump($table_name);
    $list_keys = S('keyscache_' . $table_name);
    // dump($list_keys);
    if ($list_keys === false) {
        $list_keys = M('cache_keys')->where('table_name', $table_name)->select();
        S('keyscache_' . $table_name, $list_keys);
    }
    return $list_keys;
}

function get_cert_pem($config)
{
    if (!isset($config['cert_pem']) || !isset($config['key_pem'])) {
        return false;
    }

    $fileData = M('file')->whereIn('id', [
        $config['cert_pem'],
        $config['key_pem']
    ])->column('*', 'id');

    if (empty($fileData)) {
        return false;
    }
    $path = SITE_PATH . '/public/uploads/download';
    $useCert['keyPath'] = $path . $fileData[$config['key_pem']]['savepath'] . $fileData[$config['key_pem']]['savename'];
    $useCert['certPath'] = $path . $fileData[$config['cert_pem']]['savepath'] . $fileData[$config['cert_pem']]['savename'];

    return $useCert;
}

function upload_param_by_curl($path)
{
    $path = realpath($path);

    $postname = null;
    $pathinfo = pathinfo($path);
    if (isset($pathinfo['basename']) && !empty($pathinfo['basename'])) {
        $postname = $pathinfo['basename'];
    }

    if (class_exists('\CURLFile')) { // 关键是判断curlfile,官网推荐php5.5或更高的版本使用curlfile来实例文件
        $param['media'] = new \CURLFile($path, null, $postname);
    } else {
        $param['media'] = '@' . $path;
    }
    return $param;
}

//TODO判断处理重定向跳转redirect
function deal_redirect($url)
{
    //$url='http://localhost/yi/public/card/api/me/pbid/72';
    $wApp = input('PHPSESSID', '');
    if (!empty($wApp)) {
        //dump($url);
        //http://localhost/yi/public/index.php/card/api/me/pbid/72
        $url = str_replace(SITE_URL . '/', '', $url);
        //dump($url);
        //index.php/card/api/me/pbid/72
        $arr = explode('/', $url);
        //dump($arr);
        $index = 0;
        if (isset($arr[0]) && $arr[0] == 'index.php') {
            $index = 1;
        }
        $page = isset($arr[$index]) ? $arr[$index] : '';
        $fun = isset($arr[$index + 2]) ? $arr[$index + 2] : '';
        $turnPage = $page == '' || $fun == '' ? '' : '/pages/' . $page . '/' . $fun . '/main';
        $rdata['redirect_code'] = 1;
        $rdata['redirect_page'] = $turnPage;
        return $rdata;

    } else {
        return redirect($url);
    }
}