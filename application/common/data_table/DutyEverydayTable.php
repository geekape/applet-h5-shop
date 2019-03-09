<?php
/**
 * DutyEveryday数据模型
 */
class DutyEverydayTable {
    // 数据表模型配置
    public $config = [
      'name' => 'duty_everyday',
      'title' => '每日任务',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => ''
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'duty' => [
          'title' => '每日任务设置',
          'field' => 'text  NULL',
          'type' => 'editor',
          'is_show' => 1
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string'
      ]
  ];
}