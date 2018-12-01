<?php
/**
 * GoodsStoreLink数据模型
 */
class GoodsStoreLinkTable {
    // 数据表模型配置
    public $config = [
      'name' => 'goods_store_link',
      'title' => '商品所属门店',
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
          'title' => '商品编号',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'store_id' => [
          'title' => '门店编号',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'store_num' => [
          'title' => '门店库存',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'wpid' => [
          'title' => 'wpid',
          'type' => 'num',
          'field' => 'int(10) NULL',
      ]
  ];
}