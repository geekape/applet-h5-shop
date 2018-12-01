<?php
/**
 * ShopGoodsStock数据模型
 */
class ShopGoodsStockTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_goods_stock',
      'title' => '商品库存',
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
      'stock' => [
          'title' => '物理库存',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'stock_active' => [
          'title' => '销售库存',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'remark' => '可用库存',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'lock_count' => [
          'title' => '锁定库存',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'sale_count' => [
          'title' => '销售量',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'goods_id' => [
          'title' => '商品ID',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'event_type' => [
          'title' => '商品来源',
          'type' => 'bool',
          'field' => 'tinyint(2) NULL',
          'extra' => '0:商城
1:拼团
2:秒杀
3:砍价',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'market_price' => [
          'title' => '原价',
          'type' => 'num',
          'field' => 'decimal(10,2) NULL',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'sale_price' => [
          'title' => '销售价',
          'type' => 'num',
          'field' => 'decimal(10,2) NULL',
          'remark' => '促销价，活动价',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'shop_goods_id' => [
          'title' => '商品来源',
          'type' => 'goods',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'is_must' => 1,
          'placeholder' => '请输入内容'
      ],
      'del_at' => [
          'title' => '删除时间',
          'type' => 'datetime',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'is_must' => 0
      ]
  ];
}