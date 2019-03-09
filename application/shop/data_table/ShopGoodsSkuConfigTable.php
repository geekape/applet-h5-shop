<?php
/**
 * ShopGoodsSkuConfig数据模型
 */
class ShopGoodsSkuConfigTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_goods_sku_config',
      'title' => '商品规格配置',
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
          'type' => 'num'
      ],
      'spec_id' => [
          'title' => '规格ID',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'option_id' => [
          'title' => '属性ID',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'img' => [
          'title' => '属性加图',
          'field' => 'int(10) UNSIGNED NULL',
          'type' => 'picture'
      ]
  ];
}