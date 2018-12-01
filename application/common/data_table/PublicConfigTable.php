<?php
/**
 * PublicConfig数据模型
 */
class PublicConfigTable {
    // 数据表模型配置
    public $config = [
      'name' => 'public_config',
      'title' => '公共配置信息',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'core'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'pbid' => [
          'title' => 'pbid',
          'field' => 'varchar(50) ',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ],
      'pkey' => [
          'title' => '配置规则名',
          'field' => 'varchar(30) NULL',
          'type' => 'string',
          'remark' => '配置规则名',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'pvalue' => [
          'title' => '配置值',
          'field' => 'text NULL',
          'type' => 'textarea',
          'remark' => 'json格式的配置值',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'mtime' => [
          'title' => '设置时间',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '设置时间',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ]
  ];
}