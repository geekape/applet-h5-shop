<?php

namespace app\weixin\controller;

use app\common\controller\WebBase;
use think\Controller;


class Wecome extends WebBase
{

    public function initialize()
    {
        parent::initialize();
        $strtip = "常用配置地址：<br/>
&lt;a href=&quot;[follow]&quot;&gt;绑定帐号&lt;/a&gt;<br/>
&lt;a href=&quot;[website]&quot;&gt;微首页&lt;/a&gt;<br/>
&lt;a href=&quot;http://xxxx?quot;&gt;Token&lt;/a&gt;<br/>
&lt;a href=&quot;http://xxxx?opneid=['openid']&quot;&gt;OpenId&lt;/a&gt;";

//         $this->assign('normal_tips', $strtip);
    }
}
