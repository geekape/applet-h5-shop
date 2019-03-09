<?php
/**
 * ShopOrderGoods数据模型
 */
class ShopOrderGoodsTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_order_goods',
      'title' => '订单商品关联表',
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
      'order_id' => [
          'title' => '订单ID',
          'type' => 'num',
          'field' => 'int(10) NOT NULL',
          'is_must' => 1,
          'placeholder' => '请输入内容'
      ],
      'goods_id' => [
          'title' => '商品ID',
          'type' => 'num',
          'field' => 'int(10) NOT NULL',
          'is_must' => 1,
          'placeholder' => '请输入内容'
      ]
  ];
}