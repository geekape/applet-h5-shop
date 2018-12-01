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
    public $fields = [
      'keyword' => [
          'title' => '关键词',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'keyword_type' => [
          'title' => '关键词类型',
          'field' => 'tinyint(2) NULL',
          'type' => 'select',
          'is_show' => 1,
          'extra' => '0:完全匹配
1:左边匹配
2:右边匹配
3:模糊匹配
4:正则匹配
5:随机匹配'
      ],
      'mult_ids' => [
          'title' => '多图文ID',
          'field' => 'varchar(255) NULL',
          'type' => 'string'
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