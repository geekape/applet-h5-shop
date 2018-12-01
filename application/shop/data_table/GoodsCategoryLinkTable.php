<?php
/**
 * GoodsCategoryLink数据模型
 */
class GoodsCategoryLinkTable {
    // 数据表模型配置
    public $config = [
      'name' => 'goods_category_link',
      'title' => '商品所属分类',
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
      'wpid' => [
          'title' => 'wpid',
          'type' => 'num',
          'field' => 'int(10) NULL',
      ],
      'goods_id' => [
          'title' => '商品编号',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'sort' => [
          'title' => '排序',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'category_first' => [
          'title' => '一级分类',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'category_second' => [
          'title' => '二级分类',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ]
  ];
}