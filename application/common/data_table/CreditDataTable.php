<?php
/**
 * CreditData数据模型
 */
class CreditDataTable {
    // 数据表模型配置
    public $config = [
      'name' => 'credit_data',
      'title' => '用户积分记录',
      'search_key' => 'uid',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'core'
  ];

    // 列表定义
    public $list_grid = [
      'uid' => [
          'title' => '用户名',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0
      ],
      'credit_title' => [
          'title' => '积分来源',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0
      ],
      'score' => [
          'title' => '积分',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0
      ],
      'cTime' => [
          'title' => '时间',
          'come_from' => 0,
          'width' => '',
          'is_sort' => 0
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'width' => '',
          'is_sort' => 0,
          'href' => [
              '0' => [
                  'title' => '删除',
                  'url' => '[DELETE]'
              ]
          ]
      ]
  ];

    // 字段定义
    public $fields = [
      'uid' => [
          'title' => '用户ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'score' => [
          'title' => '金币值',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'credit_name' => [
          'title' => '积分标识',
          'field' => 'varchar(50) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'cTime' => [
          'title' => '记录时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'auto_rule' => 'time',
          'auto_time' => 1,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'admin_uid' => [
          'title' => '操作者UID',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'placeholder' => '请输入内容'
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string',
          'auto_rule' => 'get_wpid',
          'auto_time' => 1,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'credit_title' => [
          'title' => '积分标题',
          'field' => 'varchar(50) NULL',
          'type' => 'string',
          'placeholder' => '请输入内容'
      ]
  ];
}