<?php
/**
 * Test数据模型
 */
class TestTable {
    // 数据表模型配置
    public $config = [
      'name' => 'test',
      'title' => 'test-modelname',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'shop'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'keyword' => [
          'title' => '关键词',
          'field' => 'varchar(100) NOT NULL',
          'type' => 'string',
          'is_must' => 1
      ],
      'keyword_type' => [
          'title' => '关键词匹配类型',
          'field' => 'tinyint(2) NOT NULL',
          'type' => 'string',
          'is_must' => 1,
          'value' => 0
      ],
      'title' => [
          'title' => '问卷标题',
          'field' => 'varchar(255) NOT NULL',
          'type' => 'string',
          'is_must' => 1
      ],
      'intro' => [
          'title' => '封面简介',
          'field' => 'text NOT NULL',
          'type' => 'string',
          'is_must' => 1
      ],
      'mTime' => [
          'title' => '修改时间',
          'field' => 'int(10) NOT NULL',
          'type' => 'string',
          'is_must' => 1
      ],
      'cover' => [
          'title' => '封面图片',
          'field' => 'int(10) unsigned NOT NULL',
          'type' => 'string',
          'is_must' => 1
      ],
      'finish_tip' => [
          'title' => '评论语',
          'field' => 'text NOT NULL',
          'type' => 'string',
          'is_must' => 1
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string',
          'is_must' => 1,
          'value' => 0
      ]
  ];
}