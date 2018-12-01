<?php
/**
 * LzwgVoteOption数据模型
 */
class LzwgVoteOptionTable {
    // 数据表模型配置
    public $config = [
      'name' => 'lzwg_vote_option',
      'title' => '投票选项',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => ''
  ];

    // 列表定义
    public $list_grid = [
      'name' => [
          'title' => '选项标题',
      ],
      'opt_count' => [
          'title' => '投票数',
      ]
  ];

    // 字段定义
    public $fields = [
      'name' => [
          'title' => '选项标题',
          'field' => 'varchar(255) NOT NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'opt_count' => [
          'title' => '当前选项投票数',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num',
          'is_show' => 1
      ],
      'order' => [
          'title' => '选项排序',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num',
          'is_show' => 1
      ],
      'image' => [
          'title' => '图片选项',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'picture',
          'is_show' => 5
      ],
      'vote_id' => [
          'title' => '投票ID',
          'field' => 'int(10) unsigned NOT NULL ',
          'type' => 'num',
          'is_show' => 4,
          'auto_rule' => '$_REQUEST[\'vote_id\']',
          'auto_time' => 3,
          'auto_type' => 'string'
      ]
  ];
}