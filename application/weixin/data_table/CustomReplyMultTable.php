<?php
/**
 * CustomReplyMult数据模型
 */
class CustomReplyMultTable {
    // 数据表模型配置
    public $config = [
      'name' => 'custom_reply_mult',
      'title' => '多图文配置',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'weixin'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [ ];
}