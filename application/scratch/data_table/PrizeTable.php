<?php
/**
 * Prize数据模型
 */
class PrizeTable {
    // 数据表模型配置
    public $config = [
      'name' => 'prize',
      'title' => '奖项设置',
      'search_key' => 'title',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'scratch'
  ];

    // 列表定义
    public $list_grid = [
      'title' => [
          'title' => '奖项标题',
      ],
      'name' => [
          'title' => '奖项',
      ],
      'num' => [
          'title' => '名额数量',
      ],
      'img' => [
          'title' => '奖品图片',
          'function' => 'get_img_html',
          'raw' => 1,
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'href' => [
              '0' => [
                  'title' => '编辑',
                  'url' => '[EDIT]'
              ],
              '1' => [
                  'title' => '删除',
                  'url' => '[DELETE]'
              ]
          ]
      ]
  ];

    // 字段定义
    public $fields = [
      'title' => [
          'title' => '奖项标题',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'remark' => '如特等奖、一等奖。。。',
          'is_show' => 1
      ],
      'name' => [
          'title' => '奖项',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'remark' => '如iphone、吹风机等',
          'is_show' => 1
      ],
      'num' => [
          'title' => '名额数量',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num',
          'is_show' => 1
      ],
      'img' => [
          'title' => '奖品图片',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'picture',
          'is_show' => 1
      ],
      'sort' => [
          'title' => '排序号',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num',
          'remark' => '值越小越靠前',
          'is_show' => 1
      ],
      'addon' => [
          'title' => '来源插件',
          'field' => 'varchar(255) NULL',
          'type' => 'string'
      ],
      'target_id' => [
          'title' => '来源ID',
          'field' => 'int(10) unsigned NULL ',
          'is_show' => 1
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string',
          'auto_rule' => 'get_wpid',
          'auto_time' => 1,
          'auto_type' => 'function'
      ]
  ];
}