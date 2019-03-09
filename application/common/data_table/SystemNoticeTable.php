<?php
/**
 * SystemNotice数据模型
 */
class SystemNoticeTable {
    // 数据表模型配置
    public $config = [
      'name' => 'system_notice',
      'title' => '系统公告表',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'core'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'title' => [
          'title' => '公告标题',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'content' => [
          'title' => '公告内容',
          'field' => 'text  NULL',
          'type' => 'editor',
          'is_show' => 1
      ],
      'create_time' => [
          'title' => '发布时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'is_show' => 4
      ]
  ];
}