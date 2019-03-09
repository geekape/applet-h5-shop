<?php
/**
 * CreditCash数据模型
 */
class CreditCashTable {
    // 数据表模型配置
    public $config = [
      'name' => 'credit_cash',
      'title' => '兑换商品',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 10,
      'addon' => ''
  ];

    // 列表定义
    public $list_grid = [
      'id' => [
          'title' => '商品ID',
      ],
      'name' => [
          'title' => '商品名',
      ],
      'score' => [
          'title' => '兑换积分',
      ],
      'num' => [
          'title' => '总数',
      ],
      'surplus' => [
          'title' => '剩余数量',
      ],
      'status' => [
          'title' => '兑换记录',
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'href' => [
              '0' => [
                  'title' => '编辑',
                  'url' => '[EDIT]'
              ],
              '1' => [
                  'title' => '删除',
                  'url' => '[DEL]'
              ]
          ]
      ]
  ];

    // 字段定义
    public $fields = [
      'name' => [
          'title' => '商品名称',
          'field' => 'text NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'describe' => [
          'title' => '商品描述',
          'field' => 'text NULL',
          'type' => 'textarea',
          'is_show' => 1
      ],
      'num' => [
          'title' => '商品个数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'img' => [
          'title' => '商品图片',
          'field' => 'int(10) UNSIGNED NULL',
          'type' => 'picture',
          'is_show' => 1
      ],
      'fail' => [
          'title' => '兑换失败提示',
          'field' => 'text NULL',
          'type' => 'textarea',
          'remark' => '用户兑换失败后看到，或没有领取成功的提示',
          'is_show' => 1
      ],
      'score' => [
          'title' => '兑换所需积分',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string'
      ],
      'surplus' => [
          'title' => '剩余',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'status' => [
          'title' => '状态',
          'field' => 'char(50) NULL',
          'type' => 'select',
          'extra' => '0:未上线
1:已上线'
      ]
  ];
}