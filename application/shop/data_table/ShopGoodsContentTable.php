<?php
/**
 * ShopGoodsContent数据模型
 */
class ShopGoodsContentTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_goods_content',
      'title' => '商品详情',
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
      'goods_id' => [
          'title' => '商品ID',
          'type' => 'num',
          'field' => 'int(10) NOT NULL',
          'is_show' => 1,
          'is_must' => 1,
          'placeholder' => '请输入内容'
      ],
      'content' => [
          'title' => '内容',
          'type' => 'textarea',
          'field' => 'text NULL',
          'is_show' => 1,
          'is_must' => 0
      ]
  ];
}