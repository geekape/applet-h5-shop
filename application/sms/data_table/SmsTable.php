<?php
/**
 * Sms数据模型
 */
class SmsTable {
    // 数据表模型配置
    public $config = [
      'name' => 'sms',
      'title' => '短信记录',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => 'sms'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'from_type' => [
          'title' => '用途',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'code' => [
          'title' => '验证码',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'smsId' => [
          'title' => '短信唯一标识',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'phone' => [
          'title' => '手机号',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'cTime' => [
          'title' => '创建时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'auto_rule' => 'time',
          'auto_time' => 3,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'status' => [
          'title' => '使用状态',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'plat_type' => [
          'title' => '平台标识',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'wpid' => [
          'title' => '公众号id',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'value' => 0,
          'is_show' => 0,
          'is_must' => 0
      ]
  ];
}