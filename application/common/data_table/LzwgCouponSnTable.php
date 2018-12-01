<?php
/**
 * LzwgCouponSn数据模型
 */
class LzwgCouponSnTable {
    // 数据表模型配置
    public $config = [
      'name' => 'lzwg_coupon_sn',
      'title' => '优惠卷序列号',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => ''
  ];

    // 列表定义
    public $list_grid = [ ];

    // 字段定义
    public $fields = [
      'coupon_id' => [
          'title' => '优惠券Id',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'sn' => [
          'title' => '优惠券sn',
          'field' => 'varchar(255) NULL',
          'type' => 'string',
          'is_show' => 1
      ],
      'is_use' => [
          'title' => '是否已领取',
          'field' => 'int(10) NULL',
          'type' => 'num',
          'is_show' => 1
      ],
      'is_get' => [
          'title' => '是否已经被领取',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ]
  ];
}