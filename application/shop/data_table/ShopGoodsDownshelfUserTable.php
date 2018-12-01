<?php
/**
 * ShopGoodsDownshelfUser数据模型
 */
class ShopGoodsDownshelfUserTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_goods_downshelf_user',
      'title' => '普通用户下架商品',
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
          'title' => '商品id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'uid' => [
          'title' => '普通用户uid',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'auto_rule' => 'get_mid',
          'auto_time' => 3,
          'auto_type' => 'function',
      ],
      'down_shelf' => [
          'title' => '是否下架',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
          'extra' => '0:下架
1:上架
',
      ],
      'wpid' => [
          'title' => 'wpid',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'is_must' => 0,
          'auto_type' => 'function',
          'auto_rule' => 'get_wpid',
          'auto_time' => 3
      ]
  ];
}