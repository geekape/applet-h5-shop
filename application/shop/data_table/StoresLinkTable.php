<?php
/**
 * StoresLink数据模型
 */
class StoresLinkTable {
    // 数据表模型配置
    public $config = [
      'name' => 'stores_link',
      'title' => '门店关联',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'shop'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [ ];
}