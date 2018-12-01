<?php

/**
 * ShopGoodsComment数据模型
 */
class ShopGoodsCommentTable
{

    // 数据表模型配置
    public $config = [
        'name' => 'shop_goods_comment',
        'title' => '商品评论信息',
        'search_key' => '',
        'add_button' => 0,
        'del_button' => 0,
        'search_button' => 1,
        'check_all' => 0,
        'list_row' => 10,
        'addon' => 'shop'
    ];

    // 列表定义
    public $list_grid = [
        'goods_title' => [
            'title' => '商品名称',
            'field' => 'int(10) NULL',
            'type' => 'num',
            'is_show' => 1
        ],
        'uid' => [
            'title' => '用户昵称',
            'function' => 'get_username',
            'width' => '15%',
            'name' => 'uid',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'cTime' => [
            'title' => '评论时间',
            'function' => 'time_format',
            'width' => '15%',
            'name' => 'cTime',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'content' => [
            'title' => '评论内容',
            'width' => '25%',
            'name' => 'content',
            'function' => '',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'is_show' => [
            'title' => '是否显示',
            'width' => '10%',
            'name' => 'is_show',
            'function' => '',
            'is_sort' => 0,
            'raw' => 0,
            'come_from' => 0,
            'href' => []
        ],
        'urls' => [
            'title' => '操作',
            'come_from' => 1,
            'href' => [
                '1' => [
                    'title' => 'is_show:0|显示,1|隐藏',
                    'url' => 'changeShow?id=[id]&is_show=[is_show]&goods_id=[goods_id]',
                    'class' => 'ajax-get'
                ]
            ],
            'name' => 'urls',
            'function' => '',
            'width' => '',
            'is_sort' => 0,
            'raw' => 0
        ]
    ];

    // 字段定义
    public $fields = [
        'goods_id' => [
            'title' => '商品编号',
            'field' => 'int(10) NULL',
            'type' => 'num',
            'is_show' => 1
        ],
        'score' => [
            'title' => '评价',
            'field' => 'int(10) NULL',
            'type' => 'num',
            'is_show' => 1
        ],
        'content' => [
            'title' => '内容',
            'field' => 'text NULL',
            'type' => 'textarea',
            'is_show' => 1
        ],
        'order_id' => [
            'title' => '订单编号',
            'field' => 'int(10) NULL',
            'type' => 'num',
            'is_show' => 1
        ],
        'uid' => [
            'title' => '用户uid',
            'field' => 'int(10) NULL',
            'type' => 'num',
            'is_show' => 1
        ],
        'cTime' => [
            'title' => '创建时间',
            'field' => 'int(10) NULL',
            'type' => 'datetime',
            'is_show' => 1
        ],
        'is_show' => [
            'title' => '是否显示',
            'field' => 'int(10) NULL',
            'type' => 'bool',
            'is_show' => 1,
            'extra' => '1:显示
0:不显示'
        ],
        'pic' => [
            'title' => '图片',
            'field' => 'varchar(255) NULL',
            'type' => 'mult_picture'
        ],
        'wpid' => [
            'title' => 'wpid',
            'type' => 'num',
            'field' => 'int(10) NULL'
        ]
    ];
}