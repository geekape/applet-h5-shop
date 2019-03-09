<?php

/**
 * SnCode数据模型
 */
class SnCodeTable
{

    // 数据表模型配置
    public $config = [
        'name' => 'sn_code',
        'title' => 'SN码',
        'search_key' => 'sn',
        'add_button' => 1,
        'del_button' => 1,
        'search_button' => 1,
        'check_all' => 1,
        'list_row' => 20,
        'addon' => 'core'
    ];

    // 列表定义
    public $list_grid = [
        'sn' => [
            'title' => 'SN码'
        ],
        'uid' => [
            'title' => '昵称',
            'function' => 'get_nickname'
        ],
        'prize_title' => [
            'title' => '奖项'
        ],
        'cTime' => [
            'title' => '创建时间',
            'function' => 'time_format'
        ],
        'is_use' => [
            'title' => '是否已使用'
        ],
        'use_time' => [
            'title' => '使用时间',
            'function' => 'time_format'
        ],
        'urls' => [
            'title' => '操作',
            'come_from' => 1,
            'href' => [
                '0' => [
                    'title' => 'is_use:0|已使用,1|未使用',
                    'url' => 'set_use?id=[id]',
                    'class' => 'ajax-get'
                ]
            ]
        ]
    ];

    // 字段定义
    public $fields = [
        'prize_title' => [
            'title' => '奖项',
            'field' => 'varchar(255) NULL',
            'type' => 'string',
            'is_show' => 1
        ],
        'sn' => [
            'title' => 'SN码',
            'field' => 'varchar(255) NULL',
            'type' => 'string',
            'auto_rule' => 'uniqid',
            'auto_time' => 1,
            'auto_type' => 'function'
        ],
        'uid' => [
            'title' => '粉丝UID',
            'field' => 'int(10) NULL',
            'type' => 'num',
            'auto_rule' => 'get_mid',
            'auto_time' => 1,
            'auto_type' => 'function'
        ],
        'cTime' => [
            'title' => '创建时间',
            'field' => 'int(10) NULL',
            'type' => 'datetime',
            'auto_rule' => 'time',
            'auto_time' => 3,
            'auto_type' => 'function'
        ],
        'is_use' => [
            'title' => '是否已使用',
            'field' => 'tinyint(2) NULL',
            'type' => 'bool',
            'extra' => '0:未使用
1:已使用'
        ],
        'use_time' => [
            'title' => '使用时间',
            'field' => 'int(10) NULL',
            'type' => 'datetime'
        ],
        'addon' => [
            'title' => '来自的插件',
            'field' => 'varchar(255) NULL',
            'type' => 'string',
            'is_show' => 4
        ],
        'target_id' => [
            'title' => '来源ID',
            'field' => 'int(10) unsigned NULL ',
            'type' => 'num',
            'is_show' => 4
        ],
        'prize_id' => [
            'title' => '奖项ID',
            'field' => 'int(10) unsigned NULL ',
            'type' => 'num'
        ],
        'wpid' => [
            'title' => 'wpid',
            'field' => 'int(10) NOT NULL',
            'type' => 'string',
            'auto_rule' => 'get_wpid',
            'auto_time' => 1,
            'auto_type' => 'function'
        ],
        'can_use' => [
            'title' => '是否可用',
            'field' => 'tinyint(2) NULL',
            'type' => 'bool',
            'extra' => '0:不可用
1:可用'
        ],
        'server_addr' => [
            'title' => '服务器IP',
            'field' => 'varchar(50) NULL',
            'type' => 'string',
            'is_show' => 1
        ],
        'admin_uid' => [
            'title' => '核销管理员ID',
            'field' => 'int(10) NULL',
            'type' => 'num'
        ]
    ];
}