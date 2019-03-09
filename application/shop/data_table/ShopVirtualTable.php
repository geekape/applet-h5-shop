<?php
/**
 * ShopVirtual数据模型
 */
class ShopVirtualTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_virtual',
      'title' => '虚拟物品信息',
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
      'goods_id' => [
          'title' => '商品ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
      ],
      'account' => [
          'title' => '账号',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
      ],
      'password' => [
          'title' => '密码',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
      ],
      'is_use' => [
          'title' => '是否已经使用',
          'field' => 'char(10) NULL',
          'type' => 'radio',
          'is_show' => 1,
          'extra' => '0:未使用
1:已使用',
      ],
      'order_id' => [
          'title' => '订单号',
          'field' => 'int(10) NULL',
          'type' => 'num',
      ],
      'card_codes' => [
          'title' => '点卡序列号',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
      ],
      'wpid' => [
          'title' => 'wpid',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'is_must' => 0
      ],
      'uid' => [
          'title' => '购买用户uid',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ]
  ];
}