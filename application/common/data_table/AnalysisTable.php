<?php
/**
 * Analysis数据模型
 */
class AnalysisTable {
    // 数据表模型配置
    public $config = [
      'name' => 'analysis',
      'title' => '统计分析',
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
      'sports_id' => [
          'title' => 'sports_id',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'type' => [
          'title' => 'type',
          'field' => 'varchar(30) NULL',
          'type' => 'string'
      ],
      'time' => [
          'title' => 'time',
          'field' => 'varchar(50) NULL',
          'type' => 'string'
      ],
      'total_count' => [
          'title' => 'total_count',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'follow_count' => [
          'title' => 'follow_count',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'aver_count' => [
          'title' => 'aver_count',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ]
  ];
}