<?php
/**
 * ShopOrderLog数据模型
 */
class ShopOrderLogTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_order_log',
      'title' => '订单跟踪',
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
      'order_id' => [
          'title' => '订单ID',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'status_code' => [
          'title' => '状态码',
          'field' => 'char(50) NULL',
          'type' => 'select',
          'extra' => '0:待支付
1:待商家确认
2:待发货
3:配送中
4:确认已收货
5:确认已收款
6:待评价
7:已评价'
      ],
      'remark' => [
          'title' => '备注内容',
          'field' => 'varchar(255) NULL',
          'type' => 'string'
      ],
      'cTime' => [
          'title' => '时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime'
      ],
      'extend' => [
          'title' => '扩展信息',
          'field' => 'varchar(255) NULL',
          'type' => 'string'
      ]
  ];
}