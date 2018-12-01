<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

/**
 * 微信接入验证
 * 在入口进行验证而不是放到框架里验证，主要是解决验证URL超时的问题
 */
define('SYSTEM_TOKEN', 'weiphp');

define('WORKERMAN_URL', '115.29.168.253');

if (!empty($_GET['echostr']) && !empty($_GET["signature"]) && !empty($_GET["nonce"])) {
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];

    $tmpArr = array(
        SYSTEM_TOKEN,
        $timestamp,
        $nonce
    );
    sort($tmpArr, SORT_STRING);
    $tmpStr = sha1(implode($tmpArr));

    if ($tmpStr == $signature) {
        echo $_GET["echostr"];
    }
    exit();
}

if (!is_file(__DIR__.'/uploads/install.lock')) {
    header('Location: ./install.php');
    exit ();
}
// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app')->run()->send();
