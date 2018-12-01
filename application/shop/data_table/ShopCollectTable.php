<?php
/**
 * ShopCollect数据模型
 */
class ShopCollectTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_collect',
      'title' => '商品收藏',
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
          'title' => '使用UID',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'goods_id' => [
          'title' => '商品ID',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'cTime' => [
          'title' => '收藏时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime'
      ]
  ];
}