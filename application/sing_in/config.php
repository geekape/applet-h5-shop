<?php
return array(
    'random' => array( // 配置在表单中的键名 ,这个会是config[random]
        'title' => '签到积分模式:', // 表单的文字
        'type' => 'radio', // 表单的类型：text、textarea、checkbox、radio、select等
        'options' => array( // select 和radion、checkbox的子选项
            '1' => '固定积分', // 值=>文字
            '0' => '随机积分'
        ),
        'value' => '1' // 表单的默认值
    ),
    'score' => array( // 配置在表单中的键名 ,这个会是config[random]
        'title' => '固定签到积分:', // 表单的文字
        'type' => 'text', // 表单的类型：text、textarea、checkbox、radio、select等
        'value' => '1' // 表单的默认值
    ),
    'score1' => array( // 配置在表单中的键名 ,这个会是config[random]
        'title' => '随机积分下限:', // 表单的文字
        'type' => 'text', // 表单的类型：text、textarea、checkbox、radio、select等
        'value' => '1' // 表单的默认值
    ),
    'score2' => array( // 配置在表单中的键名 ,这个会是config[random]
        'title' => '随机积分上限:', // 表单的文字
        'type' => 'text', // 表单的类型：text、textarea、checkbox、radio、select等
        'value' => '2' // 表单的默认值
    ),
    'hour' => array(
        'title' => '签到开始时间(小时):', // 表单的文字
        'type' => 'text', // 表单的类型：text、textarea、checkbox、radio、select等
        'value' => '0' // 表单的默认值
    ),
    'minute' => array(
        'title' => '签到开始时间(分钟):', // 表单的文字
        'type' => 'text', // 表单的类型：text、textarea、checkbox、radio、select等
        'value' => '0' // 表单的默认值
    ),
    'continue_day' => array( // 配置在表单中的键名 ,这个会是config[random]
        'title' => '连续签到:', // 表单的文字
        'type' => 'text', // 表单的类型：text、textarea、checkbox、radio、select等
        'value' => '3' // 表单的默认值
    ),
    'continue_score' => array( // 配置在表单中的键名 ,这个会是config[random]
        'title' => '连续签到积分:', // 表单的文字
        'type' => 'text', // 表单的类型：text、textarea、checkbox、radio、select等
        'value' => '5' // 表单的默认值
    ),
    'share_score' => array( // 配置在表单中的键名 ,这个会是config[random]
        'title' => '分享积分:', // 表单的文字
        'type' => 'text', // 表单的类型：text、textarea、checkbox、radio、select等
        'value' => '1' // 表单的默认值
    ),
    'share_limit' => array( // 配置在表单中的键名 ,这个会是config[random]
        'title' => '每天分享限制:', // 表单的文字
        'type' => 'text', // 表单的类型：text、textarea、checkbox、radio、select等
        'value' => '1' // 表单的默认值
    ),
    'notstart' => array( // 配置在表单中的键名 ,这个会是config[random]
        'title' => '未开始签到回复模板:', // 表单的文字
        'type' => 'textarea', // 表单的类型：text、textarea、checkbox、radio、select等
        'value' => '亲，你起得太早了,签到从[开始时间]开始,现在才[当前时间]！' // 表单的默认值
    ),
    'done' => array( // 配置在表单中的键名 ,这个会是config[random]
        'title' => '已签到回复模板:', // 表单的文字
        'type' => 'textarea', // 表单的类型：text、textarea、checkbox、radio、select等
        'value' => '亲，今天已经签到过了，请明天再来哦，谢谢！' // 表单的默认值
    ),
    'reply' => array( // 配置在表单中的键名 ,这个会是config[random]
        'title' => '签到成功回复模板:', // 表单的文字
        'type' => 'textarea', // 表单的类型：text、textarea、checkbox、radio、select等
        'value' => "恭喜您,签到成功\n\n本次签到获得[本次积分]积分，额外赠送[赠送积分]积分\n\n当前总积分[积分余额]\n\n[签到时间]\n\n您今天是第[排名]位签到\n\n签到排行榜：\n\n[排行榜]" // 表单的默认值
    ),
    'content' => array( // 配置在表单中的键名 ,这个会是config[random]
        'title' => '积分攻略:', // 表单的文字
        'type' => 'textarea', // 表单的类型：text、textarea、checkbox、radio、select等
        'value'=>"1、连续签到2天及以上：每天积分+3；\n\n2、 连续签到中断后，则回到初始签到状态积分+1，重新累计连续签到天数。\n\n3、操作引导：领取会员卡--会员中心--签到。\n\n4、手机端签到：积分+10。\n\n"
    )
);