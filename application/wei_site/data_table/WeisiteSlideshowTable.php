<?php
/**
 * WeisiteSlideshow数据模型
 */
class WeisiteSlideshowTable
{
    // 数据表模型配置
    public $config = [
        'name'          => 'weisite_slideshow',
        'title'         => '幻灯片',
        'search_key'    => 'title',
        'add_button'    => 1,
        'del_button'    => 1,
        'search_button' => 1,
        'check_all'     => 1,
        'list_row'      => 20,
        'addon' => 'wei_site',
    ];

    // 列表定义
    public $list_grid = [
        'title'   => [
            'title' => '标题',
        ],
        'img'     => [
            'title' => '图片',
            'raw'   => 1,
        ],
        'url'     => [
            'title' => '链接地址',
        ],
        'is_show' => [
            'title' => '显示',
        ],
        'sort'    => [
            'title' => '排序',
        ],
        'urls'    => [
            'title'     => '操作',
            'come_from' => 1,
            'href'      => [
                '0' => [
                    'title' => '编辑',
                    'url'   => '[EDIT]&module_id=[pid]',
                ],
                '1' => [
                    'title' => '删除',
                    'url'   => '[DELETE]',
                ],
            ],
        ],
    ];

    // 字段定义
    public $fields = [
        'title'   => [
            'title'   => '标题',
            'field'   => 'varchar(255) NULL',
            'type'    => 'string',
            'remark'  => '可为空',
            'is_show' => 1,
        ],
        'img'     => [
            'title'   => '图片',
            'field'   => 'int(10) unsigned NOT NULL ',
            'type'    => 'picture',
            'is_show' => 1,
            'remark' => '建议上传800*300的图片',
        ],
        'url'     => [
            'title'   => '链接地址',
            'field'   => 'varchar(255) NULL',
            'type'    => 'string',
            'is_show' => 1,
        ],
        'is_show' => [
            'title'   => '是否显示',
            'field'   => 'tinyint(2) NULL',
            'type'    => 'bool',
            'is_show' => 1,
            'extra'   => '0:不显示
1:显示',
        ],
        'sort'    => [
            'title'   => '排序',
            'field'   => 'int(10) NULL ',
            'type'    => 'num',
            'remark'  => '值越小越靠前',
            'is_show' => 1,
        ],
        'wpid'   => [
            'title'     => 'wpid',
            'field'     => 'varchar(100) NULL',
            'type'      => 'string',
            'auto_rule' => 'get_wpid',
            'auto_time' => 1,
            'auto_type' => 'function',
        ],
    ];
}
