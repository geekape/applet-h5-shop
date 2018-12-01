<?php
/**
 * MaterialText数据模型
 */
class MaterialTextTable {
    // 数据表模型配置
    public $config = [
      'name' => 'material_text',
      'title' => '文本素材',
      'search_key' => 'content:请输入文本内容搜索',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'core'
  ];

    // 列表定义
    public $list_grid = [
      'id' => [
          'title' => '编号',
          'come_from' => 0,
          'width' => 10,
          'is_sort' => 0
      ],
      'content' => [
          'title' => '文本内容',
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
                  'url' => 'text_edit?id=[id]'
              ],
              '1' => [
                  'title' => '删除',
                  'url' => 'text_del?id=[id]'
              ]
          ]
      ]
  ];

    // 字段定义
    public $fields = [
      'content' => [
          'title' => '文本内容',
          'field' => 'text NULL',
          'type' => 'textarea',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'pbid' => [
          'title' => 'pbid',
          'field' => 'varchar(50) NULL',
          'type' => 'string',
          'auto_rule' => 'get_pbid',
          'auto_time' => 1,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'uid' => [
          'title' => 'uid',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'auto_rule' => 'get_mid',
          'auto_time' => 1,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'admin_uid' => [
          'title' => 'uid',
          'field' => 'int(10) NULL',
          'type' => 'user',
          'placeholder' => '请输入内容'
      ],
      'is_use' => [
          'title' => '可否使用',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'extra' => '0:不可用
1:可用',
          'placeholder' => '请输入内容'
      ],
      'aim_id' => [
          'title' => '添加来源标识id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'aim_table' => [
          'title' => '来源表名',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ]
  ];
}