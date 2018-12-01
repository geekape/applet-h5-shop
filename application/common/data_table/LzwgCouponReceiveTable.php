<?php
/**
 * LzwgCouponReceive数据模型
 */
class LzwgCouponReceiveTable {
    // 数据表模型配置
    public $config = [
      'name' => 'lzwg_coupon_receive',
      'title' => '优惠券领取',
      'search_key' => '',
      'add_button' => 1,
      'del_button' => 1,
      'search_button' => 1,
      'check_all' => 1,
      'list_row' => 20,
      'addon' => ''
  ];

    // 列表定义
    public $list_grid = [
      'follow_id' => [
          'title' => '领取人',
          'function' => 'get_nickname',
      ],
      'cTime' => [
          'title' => '领取时间',
          'function' => 'time_format',
      ],
      'coupon_title' => [
          'title' => '优惠券名称',
      ],
      'sn' => [
          'title' => '序列号',
      ],
      'is_use' => [
          'title' => '是否使用',
      ]
  ];

    // 字段定义
    public $fields = [
      'sn_id' => [
          'title' => '序列号',
          'field' => 'varchar(100) NULL',
          'type' => 'num'
      ],
      'follow_id' => [
          'title' => '用户ID',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'coupon_id' => [
          'title' => '优惠券ID',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'cTime' => [
          'title' => '领取时间',
          'field' => 'int(10) NULL',
          'type' => 'datetime'
      ],
      'aim_id' => [
          'title' => '活动编号',
          'field' => 'int(10) NULL',
          'type' => 'num'
      ],
      'aim_table' => [
          'title' => '活动表名',
          'field' => 'varchar(255) NULL',
          'type' => 'string'
      ]
  ];
}