<?php

/**
 * ShopSlideshow数据模型
 */
class ShopSlideshowTable
{

    // 数据表模型配置
    public $config = [
        'name' => 'shop_slideshow',
        'title' => '幻灯片',
        'search_key' => 'title',
        'add_button' => 1,
        'del_button' => 1,
        'search_button' => 1,
        'check_all' => 1,
        'list_row' => 20,
        'addon' => 'shop'
    ];

    // 列表定义
    public $list_grid = [
        'title' => [
            'title' => '标题',
            'name' => 'title',
            'function' => '',
            'width' => '15',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'img' => [
            'title' => '图片',
            'raw' => 1,
            'name' => 'img',
            'function' => '',
            'width' => '10',
            'is_sort' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'url' => [
            'title' => '链接地址',
            'name' => 'url',
            'function' => '',
            'width' => '15',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'is_show' => [
            'title' => '显示',
            'name' => 'is_show',
            'function' => '',
            'width' => '5',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'sort' => [
            'title' => '排序',
            'name' => 'sort',
            'function' => '',
            'width' => '5',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'urls' => [
            'title' => '操作',
            'come_from' => 1,
            'href' => [
                '0' => [
                    'title' => '编辑',
                    'url' => '[EDIT]&module_id=[pid]'
                ],
                '1' => [
                    'title' => '删除',
                    'url' => '[DELETE]'
                ]
            ],
            'name' => 'urls',
            'function' => '',
            'width' => '10',
            'is_sort' => 0,
            'raw' => 0
        ]
    ];

    // 字段定义
    public $fields = [
        'title' => [
            'title' => '标题',
            'field' => 'varchar(255) NULL',
            'type' => 'string',
            'remark' => '可为空',
            'is_show' => 1
        ],
        'img' => [
            'title' => '图片',
            'field' => 'int(10) unsigned NOT NULL ',
            'type' => 'picture',
            'remark' => '最佳尺寸为800*350',
            'is_show' => 1,
            'is_must' => 1
        ],
        'url' => [
            'title' => '链接地址',
            'field' => 'varchar(255) NULL',
            'type' => 'string',
            'is_show' => 1
        ],
        'is_show' => [
            'title' => '是否显示',
            'field' => 'tinyint(2) NULL',
            'type' => 'bool',
            'is_show' => 1,
            'extra' => '0:不显示
1:显示',
            'value' => 1
        ],
        'sort' => [
            'title' => '排序',
            'field' => 'int(10) unsigned NULL ',
            'type' => 'num',
            'remark' => '值越小越靠前',
            'is_show' => 1
        ],
        'wpid' => [
            'title' => 'wpid',
            'type' => 'num',
            'field' => 'int(10) NULL',
            'auto_type' => 'function',
            'auto_rule' => 'get_wpid',
            'auto_time' => 1
        ],
        'uid' => [
            'title' => '用户id',
            'field' => 'int(10) NULL',
            'type' => 'num',
            'auto_rule' => 'get_mid',
            'auto_time' => 3,
            'auto_type' => 'function'
        ]
    ];
}