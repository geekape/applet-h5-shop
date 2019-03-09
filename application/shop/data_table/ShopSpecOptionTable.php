<?php
/**
 * ShopSpecOption数据模型
 */
class ShopSpecOptionTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_spec_option',
      'title' => '商品规格选项',
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
      'spec_id' => [
          'title' => '规格ID',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'name' => [
          'title' => '规格属性名称',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'sort' => [
          'title' => '排序号',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'remark' => '排序号越大排列越靠前',
          'is_show' => 1
      ]
  ];
}