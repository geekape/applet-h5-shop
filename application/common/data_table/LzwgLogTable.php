<?php
/**
 * LzwgLog数据模型
 */
class LzwgLogTable {
    // 数据表模型配置
    public $config = [
      'name' => 'lzwg_log',
      'title' => '活动参与记录',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => ''
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'lzwg_id' => [
          'title' => '活动ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'follow_id' => [
          'title' => '用户ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'count' => [
          'title' => '参与次数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ]
  ];
}