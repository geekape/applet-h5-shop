<?php
/**
 * Chat数据模型
 */
class ChatTable {
    // 数据表模型配置
    public $config = [
      'name' => 'chat',
      'title' => '客户记录',
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
      'uid' => [
          'title' => 'uid',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'placeholder' => '请输入内容'
      ],
      'to_uid' => [
          'title' => 'to_uid',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'wpid' => [
          'title' => 'wpid',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'content' => [
          'title' => '内容',
          'type' => 'textarea',
          'field' => 'text NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'create_at' => [
          'title' => '时间',
          'type' => 'datetime',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'is_read' => [
          'title' => '已读',
          'type' => 'bool',
          'field' => 'tinyint(2) NULL',
          'extra' => '0:未读
1:已读',
          'value' => 0,
          'is_show' => 1,
          'is_must' => 0
      ]
  ];
}