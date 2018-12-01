<?php
/**
 * SignInLog数据模型
 */
class SignInLogTable {
    // 数据表模型配置
    public $config = [
      'name' => 'SignIn_Log',
      'title' => '签到记录',
      'search_key' => 'uid',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'sing_in'
  ];

    // 列表定义
    public $list_grid = [
      'uid' => [
          'title' => '用户ID',
      ],
      'nickname' => [
          'title' => '呢称',
      ],
      'sTime' => [
          'title' => '签到时间',
          'function' => 'time_format',
      ],
      'score' => [
          'title' => '积分',
      ]
  ];

    // 字段定义
    public $fields = [
      'score' => [
          'title' => '积分',
          'field' => 'int(10) NOT NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'varchar(255) NOT NULL',
          'type' => 'string',
          'auto_rule' => 'get_wpid',
          'auto_time' => 1,
          'auto_type' => 'function'
      ],
      'sTime' => [
          'title' => '签到时间',
          'field' => 'int(10) UNSIGNED NOT NULL',
          'type' => 'datetime',
          'auto_rule' => 'time',
          'auto_time' => 1,
          'auto_type' => 'function'
      ],
      'uid' => [
          'title' => '用户ID',
          'field' => 'varchar(255) NOT NULL',
          'type' => 'textarea',
          'is_show' => 1
      ]
  ];
}