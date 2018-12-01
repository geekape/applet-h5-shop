<?php

/**
 * GoodsParamTemp数据模型
 */
class GoodsParamTempTable
{

    // 数据表模型配置
    public $config = [
        'name' => 'goods_param_temp',
        'title' => '商品参数模板',
        'search_key' => '',
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
            'title' => '模板名称',
            'come_from' => 0,
            'width' => '',
            'is_sort' => 0
        ],
        'urls' => [
            'title' => '操作',
            'come_from' => 1,
            'width' => '',
            'is_sort' => 0,
            'href' => [
                '0' => [
                    'title' => '编辑',
                    'url' => '[EDIT]'
                ],
                '1' => [
                    'title' => '复制模板',
                    'url' => '[EDIT]&type=copy'
                ],
                '2' => [
                    'title' => '删除',
                    'url' => '[DELETE]'
                ]
            
            ]
        ]
    ];

    // 字段定义
    public $fields = [
        'title' => [
            'title' => '模板名称',
            'type' => 'string',
            'field' => 'varchar(30) NOT NULL',
            'is_show' => 1,
            'is_must' => 1,
            'placeholder' => '请输入内容'
        ],
        'wpid' => [
            'title' => 'wpid',
            'type' => 'num',
            'field' => 'int(10) NULL',
            'placeholder' => '请输入内容'
        ],
        'param' => [
            'title' => 'param',
            'type' => 'textarea',
            'field' => 'text NULL',
            'placeholder' => '请输入内容'
        ]
    ];
}