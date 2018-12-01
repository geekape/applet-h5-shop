<?php
/**
 * CreditConfig数据模型
 */
class CreditConfigTable {
    // 数据表模型配置
    public $config = [
      'name' => 'credit_config',
      'title' => '积分配置',
      'search_key' => 'title',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'core'
  ];

    // 列表定义
    public $list_grid = [
      'title' => [
          'title' => '积分描述',
      ],
      'name' => [
          'title' => '积分标识',
      ],
      'score' => [
          'title' => '金币值',
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'href' => [
              '0' => [
                  'title' => '配置',
                  'url' => '[EDIT]'
              ]
          ]
      ]
  ];

    // 字段定义
    public $fields = [
      'name' => [
          'title' => '积分标识',
          'field' => 'varchar(50) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'title' => [
          'title' => '积分描述',
          'field' => 'varchar(255) NOT NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'score' => [
          'title' => '积分值',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '可以是正数，也可以是负数，如 -10 表示减10个金币值',
          'is_show' => 1
      ],
      'mTime' => [
          'title' => '修改时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'auto_rule' => 'time',
          'auto_time' => 3,
          'auto_type' => 'function'
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string'
      ]
  ];
}