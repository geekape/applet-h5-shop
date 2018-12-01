<?php

namespace app\sms\controller;

use app\common\controller\WebBase;

class Sms extends WebBase
{
    function config()
    {
        $this->assign('normal_tips', '填写信息前请先到服务商平台开通账号：<a href="http://www.yuntongxun.com/" target="_blank">云之讯官网</a>，后续补充阿里云，腾讯云的发短信功能');
        return parent::config();
    }
}
