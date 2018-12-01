<?php
/**
 * PrizeAddress数据模型
 */
class PrizeAddressTable {
    // 数据表模型配置
    public $config = [
      'name' => 'prize_address',
      'title' => '奖品收货地址',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'real_prize'
  ];

    // 列表定义
    public $list_grid = [
      'prizeid' => [
          'title' => '奖品名称',
      ],
      'turename' => [
          'title' => '收货人',
      ],
      'mobile' => [
          'title' => '联系方式',
      ],
      'address' => [
          'title' => '收货地址',
      ],
      'remark' => [
          'title' => '备注',
      ],
      'urls' => [
          'title' => '操作',
          'come_from' => 1,
          'href' => [
              '0' => [
                  'title' => '编辑',
                  'url' => 'RealPrize/RealPrize/address_edit&id=[id]'
              ],
              '1' => [
                  'title' => '删除',
                  'url' => '[DELETE]'
              ]
          ]
      ]
  ];

    // 字段定义
    public $fields = [
      'address' => [
          'title' => '奖品收货地址',
          'field' => 'varchar(255) NULL',
          'type' => 'textarea',
          'is_show' => 1
      ],
      'mobile' => [
          'title' => '手机',
          'field' => 'varchar(50) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'turename' => [
          'title' => '收货人姓名',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'remark' => [
          'title' => '备注',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'uid' => [
          'title' => '用户id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'auto_rule' => 'get_mid',
          'auto_time' => 1,
          'auto_type' => 'function'
      ],
      'prizeid' => [
          'title' => '奖品编号',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 4
      ]
  ];
}