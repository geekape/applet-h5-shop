<?php
/**
 * ShopAddress数据模型
 */
class ShopAddressTable {
    // 数据表模型配置
    public $config = [
      'name' => 'shop_address',
      'title' => '收货地址',
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
      'truename' => [
          'title' => '收货人姓名',
          'field' => 'varchar(100) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'uid' => [
          'title' => '用户ID',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'auto_rule' => 'get_mid',
          'auto_time' => 3,
          'auto_type' => 'function',
          'placeholder' => '请输入内容'
      ],
      'mobile' => [
          'title' => '手机号码',
          'field' => 'varchar(50) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'city' => [
          'title' => '城市',
          'field' => 'varchar(255) NULL',
          'type' => 'cascade',
          'is_show' => 1,
          'extra' => 'module=city',
          'placeholder' => '请输入内容'
      ],
      'address' => [
          'title' => '选择的地址',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'address_detail' => [
          'title' => '详细地址',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1,
          'placeholder' => '请输入内容'
      ],
      'is_use' => [
          'title' => '是否设置为默认',
          'field' => 'tinyint(2) NULL',
          'type' => 'bool',
          'is_show' => 1,
          'extra' => '0:否
1:是',
          'placeholder' => '请输入内容'
      ],
      'is_del' => [
          'title' => '是否删除',
          'type' => 'bool',
          'field' => 'tinyint(2) NULL',
          'extra' => '0:未删除
1:已删除',
          'value' => 0,
          'is_show' => 0,
          'is_must' => 0
      ]
  ];
}