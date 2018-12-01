<?php
/**
 * ShopGoodsSkuData数据模型
 */
class ShopGoodsSkuDataTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_goods_sku_data',
      'title' => '商品规格表',
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
      'cost_price' => [
          'title' => '成本价',
          'field' => 'decimal(11,2) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'goods_id' => [
          'title' => '商品ID',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'market_price' => [
          'title' => '市场价',
          'field' => 'decimal(11,2) NULL',
          'type' => 'num'
      ],
      'sale_price' => [
          'title' => '促销价',
          'field' => 'decimal(11,2) NULL',
          'type' => 'num'
      ],
      'stock' => [
          'title' => '库存量',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'sn_code' => [
          'title' => '商品编号',
          'field' => 'varchar(50) NULL',
          'type' => 'string'
      ],
      'sku_int_0' => [
          'title' => '数量规格0',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'sku_int_1' => [
          'title' => '数量规格1',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'sku_int_2' => [
          'title' => '数量规格2',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'sku_varchar_0' => [
          'title' => '文本规格0',
          'field' => 'varchar(255) NULL',
          'type' => 'string'
      ],
      'sku_varchar_1' => [
          'title' => '文本规格1',
          'field' => 'varchar(255) NULL',
          'type' => 'string'
      ],
      'sku_varchar_2' => [
          'title' => '文本规格2',
          'field' => 'varchar(255) NULL',
          'type' => 'string'
      ],
      'spec_option_ids' => [
          'title' => '规格属性ID串',
          'field' => 'varchar(100) NULL',
          'type' => 'string'
      ],
      'lock_num' => [
          'title' => '商品锁定库存',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ]
  ];
}