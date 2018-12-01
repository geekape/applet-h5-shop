<?php
/**
 * ShopCashoutAccount数据模型
 */
class ShopCashoutAccountTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_cashout_account',
      'title' => '提现账号',
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
      'uid' => [
          'title' => 'uid',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1,
      ],
      'wpid' => [
          'title' => 'wpid',
          'type' => 'num',
          'field' => 'int(10) NULL',
          'is_show' => 1,
          'is_must' => 0
      ],
      'type' => [
          'title' => '提现方式',
          'field' => 'char(50) NULL',
          'type' => 'radio',
          'is_show' => 1,
          'extra' => '0:支付宝
1:银行',
      ],
      'name' => [
          'title' => '姓名',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
      ],
      'account' => [
          'title' => '提现账号',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
      ]
  ];
}