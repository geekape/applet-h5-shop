<?php
/**
 * ShopCart数据模型
 */
class ShopCartTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_cart',
      'title' => '购物车',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => 'shop'
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'uid' => [
          'title' => '用户ID',
          'field' => 'int(10) UNSIGNED NOT NULL',
          'type' => 'num',
          'auto_rule' => 'get_mid',
          'auto_time' => 1,
          'auto_type' => 'function',
      ],
      'wpid' => [
          'title' => 'wpid',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 0,
          'is_must' => 0
      ],
      'goods_id' => [
          'title' => '商品id',
          'field' => 'varchar(255) NOT NULL',
          'type' => 'string',
      ],
      'num' => [
          'title' => '数量',
          'field' => 'int(10) UNSIGNED NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'price' => [
          'title' => '单价',
          'field' => 'varchar(30) NULL',
          'type' => 'num',
      ],
      'goods_type' => [
          'title' => '商品类型',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'is_show' => 1,
      ],
      'openid' => [
          'title' => 'openid',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'auto_rule' => 'get_openid',
          'auto_time' => 3,
          'auto_type' => 'function',
      ],
      'spec_option_ids' => [
          'title' => '商品SKU',
          'field' => 'varchar(50) NULL',
          'type' => 'string',
      ],
      'cTime' => [
          'title' => '创建时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
      ],
      'lock_rid_num' => [
          'title' => '释放库存数',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ]
  ];
}