<?php
/**
 * LzwgVoteLog数据模型
 */
class LzwgVoteLogTable {
    // 数据表模型配置
    public $config = [
      'name' => 'lzwg_vote_log',
      'title' => '投票记录',
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
      'vote_id' => [
          'title' => '投票标题',
		  'width' => '25%'
      ],
      'user_id' => [
          'title' => '用户',
		  'width' => '25%'
      ],
      'options' => [
          'title' => '投票选项',
		  'width' => '25%'
      ],
      'cTime' => [
          'title' => '创建时间',
          'function' => 'time_format',
		  'width' => '25%'
      ]
  ];

    // 字段定义
    public $fields = [
      'vote_id' => [
          'title' => '投票ID',
          'field' => 'int(10) unsigned NULL ',
          'type' => 'num',
          'is_show' => 1
      ],
      'user_id' => [
          'title' => '用户ID',
          'field' => 'int(10) NULL ',
          'type' => 'num',
          'is_show' => 1
      ],
      'options' => [
          'title' => '选择选项',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string'
      ],
      'cTime' => [
          'title' => '创建时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime'
      ],
      'activity_id' => [
          'title' => '活动编号',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ]
  ];
}