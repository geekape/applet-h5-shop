<?php
/**
 * GoodsParamLink数据模型
 */
class GoodsParamLinkTable {
    // 数据表模型配置
    public $config = [
      'name' => 'goods_param_link',
      'title' => '商品参数表',
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
      'title' => [
          'title' => '参数名',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'wpid' => [
          'title' => 'wpid',
          'field' => 'int(10) NOT NULL',
          'type' => 'string'
      ],
      'goods_id' => [
          'title' => '商品编号',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'param_value' => [
          'title' => '参数值',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ]
  ];
}