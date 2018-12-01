<?php
/**
 * VisitLog数据模型
 */
class VisitLogTable {
    // 数据表模型配置
    public $config = [
      'name' => 'visit_log',
      'title' => '网站访问日志',
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
    public $fields = [ ];
}