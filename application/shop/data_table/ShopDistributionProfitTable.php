<?php
/**
 * ShopDistributionProfit数据模型
 */
class ShopDistributionProfitTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_distribution_profit',
      'title' => '分销用户返利表',
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
          'field' => 'int(10) NOT NULL',
          'type' => 'string',
          'auto_rule' => 'get_wpid',
          'auto_time' => 3,
          'auto_type' => 'function'
      ],
      'uid' => [
          'title' => 'Uid',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'ctime' => [
          'title' => '返利时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime',
          'is_show' => 1
      ],
      'profit' => [
          'title' => '拥金',
          'field' => 'float(10)  NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'profit_shop' => [
          'title' => '获得佣金的店铺',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'distribution_percent' => [
          'title' => '分销比例',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'order_id' => [
          'title' => '订单id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'upper_user' => [
          'title' => '分销用户',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'upper_level' => [
          'title' => '分销用户级别',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'duser' => [
          'title' => '该用户带来的消费用户',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ]
  ];
}